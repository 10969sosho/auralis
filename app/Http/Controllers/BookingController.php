<?php

namespace App\Http\Controllers;

use App\Events\SeatAvailabilityUpdated;
use App\Models\AgeCategory;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PassengerProfile;
use App\Models\Promo;
use App\Models\Refund;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function search(Request $request)
    {
        $validated = $request->validate([
            'origin_port' => ['nullable', 'string'],
            'destination_port' => ['nullable', 'string'],
            'departure_date' => ['nullable', 'date'],
            'passenger_count' => ['nullable', 'integer', 'min:1', 'max:8'],
        ]);

        $routes = Route::where('active', true)->get();

        $schedules = Schedule::with(['vessel', 'route'])
            ->where('status', 'scheduled')
            ->where('departure_time', '>', now())
            ->when($request->origin_port, fn ($q) => $q->whereHas('route', fn ($r) => $r->where('origin_port', $request->origin_port)))
            ->when($request->destination_port, fn ($q) => $q->whereHas('route', fn ($r) => $r->where('destination_port', $request->destination_port)))
            ->when($request->departure_date, fn ($q) => $q->whereDate('departure_time', $request->departure_date))
            ->orderBy('departure_time')
            ->get();

        $schedules->each(function ($schedule) {
            $schedule->loadMissing(['vessel', 'route']);
        });

        $autoPromos = Promo::where('is_active', true)
            ->where('auto_apply', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereColumn('used_count', '<', 'usage_quota')
            ->get();

        return view('booking.search', compact('schedules', 'routes', 'autoPromos'));
    }

    public function show(Schedule $schedule, Request $request)
    {
        $schedule->load('vessel', 'route', 'agePrices.ageCategory');

        if ($schedule->isH6Passed || $schedule->status !== 'scheduled') {
            return back()->with('error', 'This schedule is no longer available for booking.');
        }

        $autoPromos = Promo::where('is_active', true)
            ->where('auto_apply', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereColumn('used_count', '<', 'usage_quota')
            ->get()
            ->filter(fn ($promo) => $promo->isApplicableToSchedule($schedule, (int) ($request->passenger_count ?? 1), 'regular'));

        $ageCategories = AgeCategory::where('is_active', true)->orderBy('sort_order')->get();
        $userProfile = auth()->user() ? [
            'name' => auth()->user()->name,
            'birth_date' => auth()->user()->birth_date?->format('Y-m-d'),
            'gender' => auth()->user()->gender,
            'phone' => auth()->user()->phone,
            'passport_number' => auth()->user()->passport_number,
            'nationality' => auth()->user()->nationality,
        ] : null;

        $savedProfiles = auth()->user()
            ? auth()->user()->passengerProfiles()->get()
            : collect();

        $passengerCount = (int) ($request->passenger_count ?? 1);

        return view('booking.create', compact(
            'schedule', 'autoPromos', 'ageCategories',
            'userProfile', 'savedProfiles', 'passengerCount'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => ['required', 'exists:schedules,id'],
            'passengers' => ['required', 'array', 'min:1', 'max:8'],
            'passengers.*.full_name' => ['required', 'string', 'max:255'],
            'passengers.*.gender' => ['required', 'in:male,female,other'],
            'passengers.*.birth_date' => ['required', 'date', 'before:today'],
            'passengers.*.nationality' => ['required', 'string', 'max:50'],
            'passengers.*.passport_number' => ['required', 'string', 'max:50'],
            'passengers.*.phone_number' => ['nullable', 'string', 'max:20'],
            'passengers.*.ticket_class' => ['required', 'in:vip,regular'],
            'passengers.*.passport_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'passengers.*.travel_permit' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'promo_code' => ['nullable', 'string'],
        ]);

        $schedule = Schedule::with('vessel')->findOrFail($validated['schedule_id']);

        if ($schedule->isH6Passed || $schedule->status !== 'scheduled') {
            return back()->with('error', 'This schedule is no longer available.');
        }

        $vipCount = collect($validated['passengers'])->where('ticket_class', 'vip')->count();
        $regularCount = collect($validated['passengers'])->where('ticket_class', 'regular')->count();

        $vipBooked = (int) $schedule->vipBooked;
        $regularBooked = (int) $schedule->regularBooked;

        if (($vipBooked + $vipCount) > $schedule->vessel->vip_capacity) {
            return back()->with('error', 'Not enough VIP seats available.');
        }

        if (($regularBooked + $regularCount) > $schedule->vessel->regular_capacity) {
            return back()->with('error', 'Not enough Regular seats available.');
        }

        $totalAmount = 0;
        $passengerPrices = [];
        foreach ($validated['passengers'] as $p) {
            $birthDate = Carbon::parse($p['birth_date']);
            $age = $birthDate->diffInYears(now());
            $price = $schedule->getPassengerPrice($age, $p['ticket_class']);
            $totalAmount += $price;
            $passengerPrices[] = $price;
        }

        $promo = null;
        $discountAmount = 0;
        if ($request->promo_code) {
            $promo = Promo::where('code', $request->promo_code)
                ->where('is_active', true)
                ->first();

            if ($promo && $promo->isApplicableToSchedule($schedule, count($validated['passengers']), 'regular')) {
                $discountAmount = $promo->calculateDiscount($totalAmount);
            }
        }

        $totalAfterDiscount = max(0, $totalAmount - $discountAmount);

        $booking = DB::transaction(function () use ($validated, $schedule, $totalAfterDiscount, $discountAmount, $promo) {
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'schedule_id' => $schedule->id,
                'booking_code' => strtoupper('BK-'.date('Ymd').'-'.substr(uniqid(), -5)),
                'total_passengers' => count($validated['passengers']),
                'total_amount' => $totalAfterDiscount,
                'discount_amount' => $discountAmount,
                'promo_id' => $promo?->id,
                'booking_status' => 'pending_payment',
                'payment_status' => 'pending',
                'locked_at' => now(),
                'expires_at' => now()->addMinutes(30),
            ]);

            foreach ($validated['passengers'] as $index => $passengerData) {
                $birthDate = Carbon::parse($passengerData['birth_date']);
                $age = $birthDate->diffInYears(now());
                $category = AgeCategory::detectCategory($age);

                $passenger = $booking->passengers()->create([
                    'full_name' => $passengerData['full_name'],
                    'gender' => $passengerData['gender'],
                    'birth_date' => $passengerData['birth_date'],
                    'nationality' => $passengerData['nationality'],
                    'passport_number' => $passengerData['passport_number'],
                    'phone_number' => $passengerData['phone_number'] ?? null,
                    'passenger_type' => $category ? $category->name : ($age <= 12 ? 'Child' : 'Adult'),
                    'ticket_class' => $passengerData['ticket_class'],
                    'age_category_id' => $category?->id,
                ]);

                $passportFile = $passengerData['passport_file'] ?? null;
                if ($passportFile) {
                    $path = $passportFile->store('documents/passports', 'public');
                    $passenger->documents()->create([
                        'type' => 'passport',
                        'file_path' => $path,
                        'file_name' => $passportFile->getClientOriginalName(),
                        'mime_type' => $passportFile->getMimeType(),
                        'file_size' => $passportFile->getSize(),
                        'uploaded_at' => now(),
                    ]);
                }

                $travelPermit = $passengerData['travel_permit'] ?? null;
                if ($travelPermit) {
                    $path = $travelPermit->store('documents/permits', 'public');
                    $passenger->documents()->create([
                        'type' => 'travel_permit',
                        'file_path' => $path,
                        'file_name' => $travelPermit->getClientOriginalName(),
                        'mime_type' => $travelPermit->getMimeType(),
                        'file_size' => $travelPermit->getSize(),
                        'uploaded_at' => now(),
                    ]);
                }
            }

            if ($promo) {
                $promo->increment('used_count');
            }

            return $booking;
        });

        Payment::create([
            'booking_id' => $booking->id,
            'amount' => $totalAfterDiscount,
            'payment_status' => 'pending',
        ]);

        return redirect()->route('booking.payment', $booking->booking_code);
    }

    public function showPayment($code)
    {
        $booking = Booking::where('booking_code', $code)
            ->with(['passengers', 'payment', 'schedule.vessel', 'schedule.route', 'promo'])
            ->firstOrFail();

        if ($booking->expires_at && $booking->expires_at->isPast() && $booking->payment_status === 'pending') {
            $booking->update(['booking_status' => 'expired', 'payment_status' => 'expired']);

            return view('booking.expired', compact('booking'));
        }

        return view('booking.payment', compact('booking'));
    }

    public function processPayment(Request $request, $code)
    {
        $booking = Booking::where('booking_code', $code)
            ->with('payment')
            ->firstOrFail();

        if ($booking->payment_status !== 'pending') {
            return back()->with('error', 'Payment already processed.');
        }

        if ($booking->expires_at && $booking->expires_at->isPast()) {
            $booking->update(['booking_status' => 'expired', 'payment_status' => 'expired']);

            return back()->with('error', 'Payment time has expired.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:fpx,ewallet,online_banking'],
        ]);

        $payment = $booking->payment;

        DB::transaction(function () use ($booking, $payment, $validated) {
            $payment->update([
                'payment_method' => $validated['payment_method'],
                'transaction_id' => 'TXN-'.strtoupper(uniqid()),
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            $booking->update([
                'booking_status' => 'paid',
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            foreach ($booking->passengers as $passenger) {
                $ticket = $passenger->ticket()->create([
                    'booking_id' => $booking->id,
                    'ticket_class' => $passenger->ticket_class,
                    'qr_token' => Ticket::generateQrToken(),
                    'ticket_number' => Ticket::generateTicketNumber(),
                    'ticket_status' => 'active',
                    'expiry_date' => $booking->schedule->departure_time->startOfDay(),
                ]);
            }

            event(new SeatAvailabilityUpdated($booking->schedule));
        });

        return redirect()->route('booking.success', $booking->booking_code);
    }

    public function success($code)
    {
        $booking = Booking::where('booking_code', $code)
            ->with(['passengers.ticket', 'schedule.vessel', 'schedule.route', 'payment'])
            ->firstOrFail();

        return view('booking.success', compact('booking'));
    }

    public function history(Request $request)
    {
        $bookings = auth()->user()->bookings()
            ->with(['schedule.vessel', 'schedule.route', 'passengers', 'payment'])
            ->when($request->status, fn ($q, $status) => $q->where('booking_status', $status))
            ->latest()
            ->paginate(10);

        return view('booking.history', compact('bookings'));
    }

    public function showBooking($code)
    {
        $booking = Booking::where('booking_code', $code)
            ->with(['passengers.ticket', 'passengers.documents', 'schedule.vessel', 'schedule.route', 'payment', 'refund'])
            ->firstOrFail();

        return view('booking.detail', compact('booking'));
    }

    public function refundRequest(Request $request, $code)
    {
        $booking = Booking::where('booking_code', $code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($booking->booking_status !== 'paid') {
            return back()->with('error', 'This booking is not eligible for refund.');
        }

        $schedule = $booking->schedule;
        if ($schedule->isH6Passed) {
            return back()->with('error', 'Refund window has closed (H-6).');
        }

        $existingRefund = Refund::where('booking_id', $booking->id)->first();
        if ($existingRefund) {
            return back()->with('error', 'A refund request already exists for this booking.');
        }

        $validated = $request->validate([
            'refund_reason' => ['required', 'string', 'max:500'],
        ]);

        $refundAmount = round($booking->total_amount * 0.25, 2);

        DB::transaction(function () use ($booking, $validated, $refundAmount) {
            Refund::create([
                'booking_id' => $booking->id,
                'refund_amount' => $refundAmount,
                'refund_reason' => $validated['refund_reason'],
                'refund_status' => 'requested',
            ]);

            $booking->update(['booking_status' => 'refund_requested']);

            event(new SeatAvailabilityUpdated($booking->schedule));
        });

        return back()->with('success', 'Refund request submitted successfully. Admin will process via WhatsApp.');
    }
}
