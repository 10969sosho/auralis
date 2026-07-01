<?php

namespace App\Http\Controllers;

use App\Events\SeatAvailabilityUpdated;
use App\Models\AgeCategory;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PassengerProfile;
use App\Models\Promo;
use App\Models\Refund;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Services\ToyibPayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        try {
            $toyibPay = app(ToyibPayService::class);
            $result = $toyibPay->createBill($booking);

            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $totalAfterDiscount,
                'payment_method' => 'toyibpay',
                'payment_status' => 'pending',
                'transaction_id' => $result['bill_code'],
                'payment_meta' => [
                    'bill_code' => $result['bill_code'],
                    'payment_url' => $result['payment_url'],
                ],
            ]);

            return redirect()->away($result['payment_url']);
        } catch (\Exception $e) {
            Log::error('ToyibPay createBill failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            // Fallback: create payment record as pending and show payment page
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $totalAfterDiscount,
                'payment_status' => 'pending',
            ]);

            return redirect()->route('booking.payment', $booking->booking_code)
                ->with('error', 'Payment gateway is temporarily unavailable. Please try again or use manual transfer.');
        }
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

        // If bill_code exists, redirect to existing ToyibPay payment URL (reuse, don't create new)
        $billCode = $booking->payment?->payment_meta['bill_code'] ?? null;
        if ($billCode && $booking->payment_status === 'pending') {
            $toyibPay = app(ToyibPayService::class);
            return redirect()->away($toyibPay->getPaymentUrl($billCode));
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

        // Auto-cancel expired payments
        if ($booking->expires_at && $booking->expires_at->isPast() && $booking->payment_status === 'pending') {
            DB::transaction(function () use ($booking) {
                $booking->update(['booking_status' => 'cancelled', 'payment_status' => 'expired']);
                event(new SeatAvailabilityUpdated($booking->schedule));
            });
            $booking->refresh();
        }

        return view('booking.detail', compact('booking'));
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

    /**
     * AJAX endpoint: check ToyibPay payment status.
     */
    public function checkPaymentStatus($code)
    {
        $booking = Booking::where('booking_code', $code)
            ->with(['payment', 'passengers', 'schedule'])
            ->firstOrFail();

        // Owner check
        if ($booking->user_id && $booking->user_id !== auth()->id()) {
            abort(403);
        }

        $response = [
            'booking_status' => $booking->booking_status,
            'payment_status' => $booking->payment_status,
        ];

        // If already terminal state, return immediately
        if (in_array($booking->payment_status, ['paid', 'approved', 'failed', 'expired'])) {
            $response['done'] = true;

            if (in_array($booking->payment_status, ['paid', 'approved'])) {
                $response['redirect'] = route('booking.success', $booking->booking_code);
            }

            return response()->json($response);
        }

        // Check ToyibPay API for pending ToyibPay payments
        $billCode = $booking->payment->payment_meta['bill_code'] ?? null;
        if ($billCode && $booking->payment->payment_method === 'toyibpay') {
            try {
                $toyibPay = app(ToyibPayService::class);
                $tx = $toyibPay->getBillTransactions($billCode);

                if ($tx && ($tx['billpaymentStatus'] ?? '') === '1') {
                    $this->markPaymentPaid($booking, $tx);
                    $response['booking_status'] = 'paid';
                    $response['payment_status'] = 'paid';
                    $response['done'] = true;
                    $response['redirect'] = route('booking.success', $booking->booking_code);
                }
            } catch (\Exception $e) {
                Log::warning('checkPaymentStatus API error', [
                    'booking' => $code,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Check expiry
        if ($booking->expires_at && $booking->expires_at->isPast() && $booking->payment_status === 'pending') {
            DB::transaction(function () use ($booking) {
                $booking->update(['booking_status' => 'cancelled', 'payment_status' => 'expired']);
                event(new SeatAvailabilityUpdated($booking->schedule));
            });
            $response['booking_status'] = 'cancelled';
            $response['payment_status'] = 'expired';
            $response['done'] = true;
        }

        $response['done'] = $response['done'] ?? false;

        return response()->json($response);
    }

    /**
     * Handle ToyibPay return URL (user redirected back after payment).
     */
    public function toyibpayReturn($code)
    {
        $booking = Booking::where('booking_code', $code)
            ->with(['payment', 'passengers', 'schedule'])
            ->firstOrFail();

        // If already paid, redirect to success
        if (in_array($booking->payment_status, ['paid', 'approved'])) {
            return redirect()->route('booking.success', $booking->booking_code);
        }

        // Auto-cancel if expired
        if ($booking->expires_at && $booking->expires_at->isPast() && $booking->payment_status === 'pending') {
            DB::transaction(function () use ($booking) {
                $booking->update(['booking_status' => 'cancelled', 'payment_status' => 'expired']);
                event(new SeatAvailabilityUpdated($booking->schedule));
            });

            return view('booking.expired', compact('booking'));
        }

        // Check payment status via ToyibPay API
        $billCode = $booking->payment->payment_meta['bill_code'] ?? null;
        if ($billCode) {
            $toyibPay = app(ToyibPayService::class);
            $tx = $toyibPay->getBillTransactions($billCode);

            if ($tx && ($tx['billpaymentStatus'] ?? '') === '1') {
                $this->markPaymentPaid($booking, $tx);
                return redirect()->route('booking.success', $booking->booking_code);
            }
        }

        // Payment not yet confirmed, show waiting page
        return view('booking.payment', compact('booking'));
    }

    /**
     * Handle ToyibPay server callback (POST from ToyibPay).
     * This endpoint must be public (no auth, no CSRF).
     */
    public function toyibpayCallback(Request $request)
    {
        $data = $request->all();

        Log::info('ToyibPay callback received', $data);

        $toyibPay = app(ToyibPayService::class);

        // Verify hash
        if (!$toyibPay->verifyCallback($data)) {
            Log::warning('ToyibPay callback hash verification failed', $data);
            return response('Invalid hash', 400);
        }

        $orderId = $data['order_id'] ?? null;
        $status = (int) ($data['status'] ?? 0);

        if (!$orderId) {
            Log::warning('ToyibPay callback missing order_id', $data);
            return response('Missing order_id', 400);
        }

        $booking = Booking::where('booking_code', $orderId)
            ->with(['payment', 'passengers', 'schedule'])
            ->first();

        if (!$booking) {
            Log::warning('ToyibPay callback booking not found', ['order_id' => $orderId]);
            return response('Booking not found', 404);
        }

        // Idempotent check: skip if already processed
        $refno = $data['refno'] ?? null;
        if ($refno) {
            $processedRefnos = $booking->payment->payment_meta['processed_refnos'] ?? [];
            if (in_array($refno, $processedRefnos)) {
                Log::info('ToyibPay callback already processed, skipping', ['refno' => $refno, 'booking' => $orderId]);
                return response('OK (already processed)');
            }
        }

        // Status: 1 = success, 2 = pending, 3 = fail
        if ($status === 1) {
            if (!in_array($booking->payment_status, ['paid', 'approved'])) {
                $this->markPaymentPaid($booking, $data);
            }
        } elseif ($status === 3) {
            if ($booking->payment_status === 'pending') {
                DB::transaction(function () use ($booking, $refno) {
                    $booking->payment->update([
                        'payment_status' => 'failed',
                        'payment_meta' => array_merge($booking->payment->payment_meta ?? [], [
                            'processed_refnos' => array_unique(array_merge(
                                $booking->payment->payment_meta['processed_refnos'] ?? [],
                                $refno ? [$refno] : []
                            )),
                        ]),
                    ]);
                    $booking->update(['payment_status' => 'failed']);
                });
            }

            // Notify user if exists
            if ($booking->user_id) {
                Notification::create([
                    'user_id' => $booking->user_id,
                    'type' => 'payment_failed',
                    'channel' => 'database',
                    'title' => 'Payment Failed',
                    'body' => 'Your payment for booking #' . $booking->booking_code . ' has failed. Please try again.',
                    'sent_at' => now(),
                ]);
            }
        }

        // Audit log the webhook
        AuditLog::log(
            action: 'toyibpay_callback',
            entityType: 'payment',
            entityId: $booking->payment?->id,
            payload: [
                'booking_code' => $booking->booking_code,
                'status' => $status,
                'refno' => $refno,
                'data' => $data,
            ],
            ipAddress: request()->ip(),
        );

        return response('OK');
    }

    /**
     * Mark a booking as paid and generate tickets.
     */
    private function markPaymentPaid(Booking $booking, array $txData): void
    {
        DB::transaction(function () use ($booking, $txData) {
            $booking->payment->update([
                'payment_status' => 'paid',
                'transaction_id' => $txData['billpaymentInvoiceNo'] ?? $booking->payment->transaction_id,
                'paid_at' => now(),
                'payment_meta' => array_merge($booking->payment->payment_meta ?? [], [
                    'toyibpay_refno' => $txData['refno'] ?? null,
                    'toyibpay_channel' => $txData['billpaymentChannel'] ?? null,
                    'toyibpay_amount' => $txData['billpaymentAmount'] ?? null,
                    'toyibpay_date' => $txData['billPaymentDate'] ?? null,
                    'processed_refnos' => array_unique(array_merge(
                        $booking->payment->payment_meta['processed_refnos'] ?? [],
                        ($txData['refno'] ?? null) ? [$txData['refno']] : []
                    )),
                ]),
            ]);

            $booking->update([
                'booking_status' => 'paid',
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            // Generate tickets
            foreach ($booking->passengers as $passenger) {
                if (!$passenger->ticket) {
                    $passenger->ticket()->create([
                        'booking_id' => $booking->id,
                        'ticket_class' => $passenger->ticket_class,
                        'qr_token' => Ticket::generateQrToken(),
                        'ticket_number' => Ticket::generateTicketNumber(),
                        'ticket_status' => 'active',
                        'expiry_date' => $booking->schedule->departure_time->startOfDay(),
                    ]);
                }
            }

            event(new SeatAvailabilityUpdated($booking->schedule));
        });

        // Notify user
        if ($booking->user_id) {
            Notification::create([
                'user_id' => $booking->user_id,
                'type' => 'payment_success',
                'channel' => 'database',
                'title' => 'Payment Successful',
                'body' => 'Your payment for booking #' . $booking->booking_code . ' has been received. Tickets are ready!',
                'sent_at' => now(),
            ]);
        }

        // Audit log
        AuditLog::log(
            action: 'toyibpay_payment_paid',
            entityType: 'booking',
            entityId: $booking->id,
            payload: [
                'booking_code' => $booking->booking_code,
                'amount' => $booking->total_amount,
                'toyibpay_refno' => $txData['refno'] ?? null,
                'toyibpay_invoice' => $txData['billpaymentInvoiceNo'] ?? null,
            ],
            ipAddress: request()->ip(),
        );
    }
}
