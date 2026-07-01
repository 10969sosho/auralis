<?php

namespace App\Http\Controllers;

use App\Events\SeatAvailabilityUpdated;
use App\Models\AgeCategory;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\Refund;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CounterController extends Controller
{
    public function dashboard()
    {
        $schedules = Schedule::with(['vessel', 'route'])
            ->where('is_active', true)
            ->where('departure_time', '>', now())
            ->orderBy('departure_time')
            ->get();

        return view('counter.dashboard', compact('schedules'));
    }

    public function newBooking(Schedule $schedule)
    {
        $schedule->load('vessel', 'route', 'agePrices.ageCategory');

        if ($schedule->isH6Passed || !$schedule->is_active) {
            return back()->with('error', 'This schedule is no longer available.');
        }

        $ageCategories = AgeCategory::where('is_active', true)->orderBy('sort_order')->get();

        $vipBooked = (int) $schedule->vipBooked;
        $regularBooked = (int) $schedule->regularBooked;
        $vipAvailable = $schedule->vessel->vip_capacity - $vipBooked;
        $regularAvailable = $schedule->vessel->regular_capacity - $regularBooked;

        return view('counter.create', compact('schedule', 'ageCategories', 'vipAvailable', 'regularAvailable'));
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
            'payment_method' => ['required', 'in:cash,card'],
            'amount_received' => ['required', 'numeric', 'min:0'],
        ]);

        $schedule = Schedule::with('vessel')->findOrFail($validated['schedule_id']);

        if ($schedule->isH6Passed || !$schedule->is_active) {
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
        foreach ($validated['passengers'] as $p) {
            $birthDate = Carbon::parse($p['birth_date']);
            $age = $birthDate->diffInYears(now());
            $price = $schedule->getPassengerPrice($age, $p['ticket_class']);
            $totalAmount += $price;
        }

        // Add RM 10 insurance per passenger
        $insuranceTotal = count($validated['passengers']) * 10;
        $totalAmount += $insuranceTotal;

        $amountReceived = (float) $validated['amount_received'];
        $changeAmount = max(0, $amountReceived - $totalAmount);

        if ($amountReceived < $totalAmount) {
            return back()->with('error', 'Amount received is less than total amount. Need MYR ' . number_format($totalAmount - $amountReceived, 2) . ' more.');
        }

        $booking = DB::transaction(function () use ($validated, $schedule, $totalAmount) {
            $booking = Booking::create([
                'user_id' => null,
                'schedule_id' => $schedule->id,
                'booking_code' => strtoupper('BK-'.date('Ymd').'-'.substr(uniqid(), -5)),
                'total_passengers' => count($validated['passengers']),
                'total_amount' => $totalAmount,
                'discount_amount' => 0,
                'promo_id' => null,
                'booking_status' => 'paid',
                'payment_status' => 'paid',
                'locked_at' => now(),
                'expires_at' => now()->addMinutes(30),
                'paid_at' => now(),
            ]);

            foreach ($validated['passengers'] as $passengerData) {
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
            }

            foreach ($booking->passengers as $passenger) {
                $passenger->ticket()->create([
                    'booking_id' => $booking->id,
                    'ticket_class' => $passenger->ticket_class,
                    'qr_token' => Ticket::generateQrToken(),
                    'ticket_number' => Ticket::generateTicketNumber(),
                    'ticket_status' => 'active',
                    'expiry_date' => $schedule->departure_time->startOfDay(),
                ]);
            }

            return $booking;
        });

        Payment::create([
            'booking_id' => $booking->id,
            'amount' => $totalAmount,
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'paid',
            'transaction_id' => 'OFF-'.strtoupper(uniqid()),
            'paid_at' => now(),
        ]);

        event(new SeatAvailabilityUpdated($schedule));

        return redirect()->route('counter.success', [
            'code' => $booking->booking_code,
            'change' => $changeAmount,
        ]);
    }

    public function success(Request $request)
    {
        $booking = Booking::where('booking_code', $request->code)
            ->with(['passengers.ticket', 'schedule.vessel', 'schedule.route'])
            ->firstOrFail();

        $changeAmount = (float) $request->change;

        return view('counter.success', compact('booking', 'changeAmount'));
    }

    public function search(Request $request)
    {
        $request->validate(['query' => ['required', 'string', 'min:3']]);

        $query = $request->input('query');

        $bookings = Booking::whereNull('user_id')
            ->where(function ($q) use ($query) {
                $q->where('booking_code', 'like', '%'.$query.'%')
                    ->orWhereHas('passengers', function ($pq) use ($query) {
                        $pq->where('full_name', 'like', '%'.$query.'%')
                            ->orWhere('passport_number', 'like', '%'.$query.'%');
                    });
            })
            ->with(['passengers.ticket', 'schedule.vessel', 'schedule.route', 'payment'])
            ->latest()
            ->limit(20)
            ->get();

        return view('counter.search', compact('bookings'));
    }

    public function history(Request $request)
    {
        $bookings = Booking::whereNull('user_id')
            ->with(['schedule.vessel', 'schedule.route', 'passengers', 'payment'])
            ->when($request->status, fn ($q, $status) => $q->where('booking_status', $status))
            ->latest()
            ->paginate(10);

        return view('counter.history', compact('bookings'));
    }

    public function detail($code)
    {
        $booking = Booking::where('booking_code', $code)
            ->with(['passengers.ticket', 'passengers.documents', 'schedule.vessel', 'schedule.route', 'payment', 'refund'])
            ->firstOrFail();

        return view('counter.detail', compact('booking'));
    }

    public function refundRequest(Request $request, $code)
    {
        $booking = Booking::where('booking_code', $code)
            ->firstOrFail();

        $request->validate([
            'refund_reason' => ['required', 'string', 'max:1000'],
        ]);

        if ($booking->booking_status !== 'paid') {
            return back()->with('error', 'Only paid bookings can be refunded.');
        }

        if ($booking->schedule->isH6Passed) {
            return back()->with('error', 'Refund period has passed (H-6 before departure).');
        }

        if ($booking->refund) {
            return back()->with('error', 'A refund request already exists for this booking.');
        }

        $refundAmount = $booking->total_amount * 0.25;

        Refund::create([
            'booking_id' => $booking->id,
            'requested_by' => auth()->id(),
            'refund_amount' => $refundAmount,
            'refund_reason' => $request->refund_reason,
            'refund_status' => 'pending',
        ]);

        $booking->update(['booking_status' => 'refund_requested']);

        return back()->with('success', 'Refund request submitted successfully.');
    }
}
