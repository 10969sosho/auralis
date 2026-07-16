<?php

namespace App\Http\Controllers;

use App\Events\SeatAvailabilityUpdated;
use App\Helpers\MailHelper;
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
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function search(Request $request)
    {
        $validated = $request->validate([
            'origin_port' => ['nullable', 'string'],
            'destination_port' => ['nullable', 'string'],
            'passenger_count' => ['nullable', 'integer', 'min:1', 'max:8'],
        ]);

        $routes = Route::where('active', true)->get();

        // Always show all upcoming schedules, with optional filters
        $schedules = Schedule::with(['vessel', 'route'])
            ->where('status', 'scheduled')
            ->where('is_active', true)
            ->where('departure_time', '>', now())
            ->when($request->origin_port, fn ($q) => $q->whereHas('route', fn ($r) => $r->where('origin_port', $request->origin_port)))
            ->when($request->destination_port, fn ($q) => $q->whereHas('route', fn ($r) => $r->where('destination_port', $request->destination_port)))
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

        // Prepare destination ports based on selected origin for linked dropdown
        $destinationPorts = Route::where('active', true)
            ->when($request->origin_port, fn ($q) => $q->where('origin_port', $request->origin_port))
            ->pluck('destination_port')
            ->unique()
            ->values();

        return view('booking.search', compact('schedules', 'routes', 'autoPromos', 'destinationPorts'));
    }

    public function show(Schedule $schedule, Request $request)
    {
        $schedule->load('vessel', 'route', 'agePrices.ageCategory');

        if ($schedule->isH6Passed || $schedule->status !== 'scheduled' || !$schedule->is_active) {
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
        $rules = [
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
        ];

        // Require guest_email for unauthenticated users
        if (!auth()->check()) {
            $rules['guest_email'] = ['required', 'email', 'max:255'];
        }

        $validated = $request->validate($rules);

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

        // Add RM 10 insurance per passenger
        $insuranceTotal = count($validated['passengers']) * 10;
        $totalAmount += $insuranceTotal;

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

        $isGuest = !auth()->check();
        $guestToken = $isGuest ? Str::random(40) : null;

        $booking = DB::transaction(function () use ($validated, $schedule, $totalAfterDiscount, $discountAmount, $promo, $isGuest, $guestToken) {
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'guest_email' => $isGuest ? $validated['guest_email'] : null,
                'guest_token' => $guestToken,
                'schedule_id' => $schedule->id,
                'booking_code' => strtoupper('BK-'.date('Ymd').'-'.substr(uniqid(), -5)),
                'total_passengers' => count($validated['passengers']),
                'total_amount' => $totalAfterDiscount,
                'discount_amount' => $discountAmount,
                'promo_id' => $promo?->id,
                'booking_status' => 'pending_payment',
                'payment_status' => 'pending',
                'locked_at' => now(),
                'expires_at' => now()->addMinutes(10),
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

        if ($isGuest) {
            MailHelper::sendBookingGuest($booking);
        } else {
            MailHelper::sendBookingPending($booking);
        }

        return redirect()->route('booking.payment', $booking->booking_code);
    }

    public function showPayment($code)
    {
        $booking = Booking::where('booking_code', $code)
            ->with(['passengers', 'payment', 'schedule.vessel', 'schedule.route', 'promo'])
            ->firstOrFail();

        if ($booking->expires_at && $booking->expires_at->isPast() && $booking->payment_status === 'pending') {
            DB::transaction(function () use ($booking) {
                $booking->update(['booking_status' => 'cancelled', 'payment_status' => 'expired']);
                event(new SeatAvailabilityUpdated($booking->schedule));
            });

            return view('booking.expired', compact('booking'));
        }

        // If payment is awaiting approval, show waiting page
        if ($booking->payment_status === 'awaiting_approval') {
            return view('booking.payment', compact('booking'));
        }

        // If payment is already approved/paid, redirect to success
        if (in_array($booking->payment_status, ['paid', 'approved'])) {
            return redirect()->route('booking.success', $booking->booking_code);
        }

        return view('booking.payment', compact('booking'));
    }

    public function processPayment(Request $request, $code)
    {
        $booking = Booking::where('booking_code', $code)
            ->with('payment')
            ->firstOrFail();

        if (!in_array($booking->payment_status, ['pending', 'rejected'])) {
            return back()->with('error', 'Payment already processed.');
        }

        if ($booking->expires_at && $booking->expires_at->isPast()) {
            DB::transaction(function () use ($booking) {
                $booking->update(['booking_status' => 'cancelled', 'payment_status' => 'expired']);
                event(new SeatAvailabilityUpdated($booking->schedule));
            });

            return back()->with('error', 'Payment time has expired. Booking has been cancelled.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:manual_transfer'],
            'proof_of_transfer' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $payment = $booking->payment;

        $proofPath = $request->file('proof_of_transfer')->store('payments/proofs', 'public');

        DB::transaction(function () use ($booking, $payment, $validated, $proofPath) {
            $payment->update([
                'payment_method' => $validated['payment_method'],
                'transaction_id' => 'TXN-'.strtoupper(uniqid()),
                'proof_of_transfer' => $proofPath,
                'payment_status' => 'awaiting_approval',
                'rejection_reason' => null,
                'approved_by' => null,
                'approved_at' => null,
            ]);

            $booking->update([
                'booking_status' => 'awaiting_approval',
                'payment_status' => 'awaiting_approval',
            ]);

            event(new SeatAvailabilityUpdated($booking->schedule));
        });

        return redirect()->route('booking.payment', $booking->booking_code)
            ->with('success', 'Proof of transfer uploaded successfully. Please wait for admin confirmation.');
    }

    public function success($code)
    {
        $booking = Booking::where('booking_code', $code)
            ->with(['passengers.ticket', 'schedule.vessel', 'schedule.route', 'payment'])
            ->firstOrFail();

        $isGuestAccess = $booking->guest_token !== null && !auth()->check();

        return view('booking.success', compact('booking', 'isGuestAccess'));
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

    public function showBooking($code, Request $request)
    {
        $query = Booking::where('booking_code', $code)
            ->with(['passengers.ticket', 'passengers.documents', 'schedule.vessel', 'schedule.route', 'payment', 'refund']);

        // If a guest token is provided, verify against it
        $guestToken = $request->query('token');
        if ($guestToken) {
            $query->where('guest_token', $guestToken);
        }

        $booking = $query->firstOrFail();

        // Auto-cancel expired payments
        if ($booking->expires_at && $booking->expires_at->isPast() && $booking->payment_status === 'pending') {
            DB::transaction(function () use ($booking) {
                $booking->update(['booking_status' => 'cancelled', 'payment_status' => 'expired']);
                event(new SeatAvailabilityUpdated($booking->schedule));
            });
            $booking->refresh();
        }

        $isGuestAccess = $guestToken !== null;

        return view('booking.detail', compact('booking', 'isGuestAccess'));
    }

    public function cancelExpired($code)
    {
        $booking = Booking::where('booking_code', $code)->firstOrFail();

        if ($booking->payment_status === 'pending' && $booking->expires_at && $booking->expires_at->isPast()) {
            DB::transaction(function () use ($booking) {
                $booking->update(['booking_status' => 'cancelled', 'payment_status' => 'expired']);

                event(new SeatAvailabilityUpdated($booking->schedule));
            });

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Booking cannot be cancelled.']);
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
