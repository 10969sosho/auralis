<?php

namespace App\Http\Controllers;

use App\Models\AgeCategory;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\DeportationAnalytics;
use App\Models\DeportationManifest;
use App\Models\DeportationPassenger;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\User;
use App\Services\ToyibPayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class DeportationController extends Controller
{
    /**
     * Show deportation user dashboard.
     */
    public function dashboard()
    {
        $user = auth()->user();

        if (!$user->isDeportation()) {
            return redirect()->route('home')->with('error', 'Akses terhad untuk akaun deportasi sahaja.');
        }

        $bookings = $user->bookings()
            ->where('is_deportation', true)
            ->with(['passengers.ticket', 'payment'])
            ->latest()
            ->take(10)
            ->get();

        return view('deportation.dashboard', compact('bookings', 'user'));
    }

    /**
     * Show deportation registration form.
     */
    public function showRegister()
    {
        return view('deportation.register');
    }

    /**
     * Handle deportation registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'nationality' => ['nullable', 'string', 'max:50'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'shelter_point' => ['required', 'in:tawau,sandakan,kinabalu_papar,kinabalu_menggatal'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'passport_number' => $validated['passport_number'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'account_type' => 'deportation',
            'shelter_point' => $validated['shelter_point'],
        ]);

        $user->assignRole('passenger');

        // Log analytics
        DeportationAnalytics::create([
            'event_type' => 'registration',
            'user_id' => $user->id,
            'shelter_point' => $validated['shelter_point'],
            'payload' => [
                'name' => $user->name,
                'email' => $user->email,
                'nationality' => $user->nationality,
            ],
        ]);

        auth()->login($user);

        return redirect()->route('deportation.dashboard')
            ->with('success', 'Akaun deportasi berjaya didaftarkan. Selamat menggunakan perkhidmatan Auralis8.');
    }

    /**
     * Show deportation booking page (ship tickets only).
     */
    public function showBooking()
    {
        $user = auth()->user();

        if (!$user->isDeportation()) {
            return redirect()->route('home')->with('error', 'Akses terhad untuk akaun deportasi sahaja.');
        }

        // Show routes with their latest schedule prices (no dates/times)
        $routes = Schedule::with(['vessel', 'route'])
            ->where('status', 'scheduled')
            ->where('is_active', true)
            ->where('departure_time', '>', now())
            ->orderBy('departure_time')
            ->get()
            ->groupBy(fn($s) => $s->route_id . '_' . $s->vessel_id)
            ->map(fn($group) => $group->first())
            ->values();

        $ageCategories = AgeCategory::where('is_active', true)->orderBy('sort_order')->get();

        return view('deportation.booking', compact('routes', 'ageCategories', 'user'));
    }

    /**
     * Store deportation booking.
     */
    public function storeBooking(Request $request)
    {
        $user = auth()->user();

        if (!$user->isDeportation()) {
            return redirect()->route('home')->with('error', 'Akses terhad untuk akaun deportasi sahaja.');
        }

        $validated = $request->validate([
            'schedule_id' => ['required', 'exists:schedules,id'],
            'passengers' => ['required', 'array', 'min:1'],
            'passengers.*.full_name' => ['required', 'string', 'max:255'],
            'passengers.*.gender' => ['required', 'in:male,female,other'],
            'passengers.*.birth_date' => ['required', 'date', 'before:today'],
            'passengers.*.nationality' => ['required', 'string', 'max:50'],
            'passengers.*.passport_number' => ['required', 'string', 'max:50'],
            'passengers.*.phone_number' => ['nullable', 'string', 'max:20'],
            'passengers.*.ticket_class' => ['required', 'in:vip,regular'],
        ]);

        $schedule = Schedule::with('vessel', 'route')->findOrFail($validated['schedule_id']);

        if ($schedule->status !== 'scheduled' || !$schedule->is_active) {
            return back()->with('error', 'Perkhidmatan ini tidak lagi tersedia.');
        }

        // Calculate base ticket price
        $totalAmount = 0;
        foreach ($validated['passengers'] as $p) {
            $birthDate = Carbon::parse($p['birth_date']);
            $age = $birthDate->diffInYears(now());
            $price = $schedule->getPassengerPrice($age, $p['ticket_class']);
            $totalAmount += $price;
        }

        // Add shelter/bus fee (fixed per booking)
        $shelterFee = $user->shelter_fee;

        // Add insurance RM 10 per passenger
        $insuranceTotal = count($validated['passengers']) * 10;
        $totalAmount += $insuranceTotal + $shelterFee;

        $booking = DB::transaction(function () use ($validated, $schedule, $totalAmount, $user, $shelterFee) {
            $booking = Booking::create([
                'user_id' => $user->id,
                'schedule_id' => null, // Not tied to a specific schedule - open ticket
                'booking_code' => strtoupper('DEP-'.date('Ymd').'-'.substr(uniqid(), -5)),
                'total_passengers' => count($validated['passengers']),
                'total_amount' => $totalAmount,
                'discount_amount' => 0,
                'booking_status' => 'pending_payment',
                'payment_status' => 'pending',
                'locked_at' => now(),
                'expires_at' => now()->addHours(24),
                'is_deportation' => true,
                'shelter_point' => $user->shelter_point,
                'shelter_fee' => $shelterFee,
                'route_text' => $schedule->route->origin_port . ' → ' . $schedule->route->destination_port,
                'vessel_text' => $schedule->vessel->name,
                'route_vip_price' => $schedule->vip_price,
                'route_regular_price' => $schedule->regular_price,
            ]);

            foreach ($validated['passengers'] as $passengerData) {
                $birthDate = Carbon::parse($passengerData['birth_date']);
                $age = $birthDate->diffInYears(now());
                $category = AgeCategory::detectCategory($age);

                $booking->passengers()->create([
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

            return $booking;
        });

        // Log analytics
        DeportationAnalytics::create([
            'event_type' => 'booking',
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'shelter_point' => $user->shelter_point,
            'payload' => [
                'schedule_id' => $schedule->id,
                'passengers' => count($validated['passengers']),
                'total_amount' => $totalAmount,
                'shelter_fee' => $shelterFee,
            ],
        ]);

        // Create ToyibPay bill
        try {
            $toyibPay = app(ToyibPayService::class);
            $result = $this->createDeportationToyibPayBill($booking, $toyibPay);

            $totalWithFee = $totalAmount + 1.00;

            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $totalWithFee,
                'payment_method' => 'toyibpay',
                'payment_status' => 'pending',
                'transaction_id' => $result['bill_code'],
                'payment_meta' => [
                    'bill_code' => $result['bill_code'],
                    'payment_url' => $result['payment_url'],
                    'base_amount' => $totalAmount,
                    'fee_amount' => 1.00,
                ],
            ]);

            return redirect()->away($result['payment_url']);
        } catch (\Exception $e) {
            Log::error('Deportation ToyibPay createBill failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $totalAmount,
                'payment_status' => 'pending',
            ]);

            return redirect()->route('deportation.payment', $booking->booking_code)
                ->with('error', 'Gateway pembayaran tidak tersedia. Sila cuba lagi.');
        }
    }

    /**
     * Create ToyibPay bill for deportation booking.
     */
    private function createDeportationToyibPayBill(Booking $booking, ToyibPayService $toyibPay): array
    {
        $booking->loadMissing('passengers');

        $passengerNames = $booking->passengers->pluck('full_name')->join(', ');
        $route = $booking->route_display;
        $billName = substr('DEP_'.$booking->booking_code, 0, 30);
        $billDescription = substr('Deportasi '.$route, 0, 100);

        $feeInCents = 100;
        $amountInCents = (int) round($booking->total_amount * 100) + $feeInCents;

        $data = [
            'userSecretKey' => $toyibPay->secretKey ?? config('toyibpay.secret_key'),
            'categoryCode' => $toyibPay->categoryCode ?? config('toyibpay.category_code'),
            'billName' => $billName,
            'billDescription' => $billDescription,
            'billPriceSetting' => 1,
            'billPayorInfo' => 1,
            'billAmount' => $amountInCents,
            'billReturnUrl' => route('deportation.toyibpay-return', $booking->booking_code),
            'billCallbackUrl' => route('deportation.toyibpay-callback'),
            'billExternalReferenceNo' => $booking->booking_code,
            'billTo' => $passengerNames,
            'billEmail' => $booking->user?->email ?? '',
            'billPhone' => $booking->passengers->first()?->phone_number ?? '',
            'billExpiryDate' => $booking->expires_at->format('d-m-Y H:i:s'),
            'billPaymentChannel' => '2',
            'enableDuitNowQR' => '1',
            'chargeDuitNowQR' => '1',
        ];

        $baseUrl = rtrim(config('toyibpay.base_url'), '/');

        $response = \Illuminate\Support\Facades\Http::asForm()
            ->timeout(30)
            ->post($baseUrl.'/index.php/api/createBill', $data);

        if (!$response->successful()) {
            throw new \RuntimeException('ToyibPay API error: HTTP '.$response->status());
        }

        $result = $response->json();

        if (!is_array($result) || !isset($result[0]['BillCode'])) {
            throw new \RuntimeException('ToyibPay API unexpected response: '.$response->body());
        }

        return [
            'bill_code' => $result[0]['BillCode'],
            'payment_url' => $baseUrl.'/'.$result[0]['BillCode'],
        ];
    }

    /**
     * Show payment page for deportation booking.
     */
    public function showPayment($code)
    {
        $booking = Booking::where('booking_code', $code)
            ->where('is_deportation', true)
            ->where('user_id', auth()->id())
            ->with(['passengers', 'payment'])
            ->firstOrFail();

        if ($booking->expires_at && $booking->expires_at->isPast() && $booking->payment_status === 'pending') {
            $booking->update(['booking_status' => 'cancelled', 'payment_status' => 'expired']);
            return view('deportation.expired', compact('booking'));
        }

        if (in_array($booking->payment_status, ['paid', 'approved'])) {
            return redirect()->route('deportation.success', $booking->booking_code);
        }

        $billCode = $booking->payment?->payment_meta['bill_code'] ?? null;
        if ($billCode && $booking->payment_status === 'pending') {
            $toyibPay = app(ToyibPayService::class);
            return redirect()->away($toyibPay->getPaymentUrl($billCode));
        }

        return view('deportation.payment', compact('booking'));
    }

    /**
     * Process manual transfer payment for deportation.
     */
    public function processPayment(Request $request, $code)
    {
        $booking = Booking::where('booking_code', $code)
            ->where('is_deportation', true)
            ->where('user_id', auth()->id())
            ->with('payment')
            ->firstOrFail();

        if (!in_array($booking->payment_status, ['pending', 'rejected'])) {
            return back()->with('error', 'Pembayaran sudah diproses.');
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
        });

        return redirect()->route('deportation.payment', $booking->booking_code)
            ->with('success', 'Bukti pembayaran berjaya dimuat naik. Sila tunggu pengesahan admin.');
    }

    /**
     * Show success page after payment.
     */
    public function success($code)
    {
        $booking = Booking::where('booking_code', $code)
            ->where('is_deportation', true)
            ->where('user_id', auth()->id())
            ->with(['passengers.ticket', 'payment'])
            ->firstOrFail();

        return view('deportation.success', compact('booking'));
    }

    /**
     * Show deportation QR / ticket page.
     */
    public function showTicket($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)
            ->where('is_deportation', true)
            ->with(['booking.user', 'passenger'])
            ->firstOrFail();

        if ($ticket->booking->user_id !== auth()->id()) {
            abort(403);
        }

        return view('deportation.ticket', compact('ticket'));
    }

    /**
     * Check ToyibPay payment status for deportation.
     */
    public function checkPaymentStatus($code)
    {
        $booking = Booking::where('booking_code', $code)
            ->where('is_deportation', true)
            ->where('user_id', auth()->id())
            ->with(['payment', 'passengers', 'schedule'])
            ->firstOrFail();

        $response = [
            'booking_status' => $booking->booking_status,
            'payment_status' => $booking->payment_status,
        ];

        if (in_array($booking->payment_status, ['paid', 'approved', 'failed', 'expired'])) {
            $response['done'] = true;
            if (in_array($booking->payment_status, ['paid', 'approved'])) {
                $response['redirect'] = route('deportation.success', $booking->booking_code);
            }
            return response()->json($response);
        }

        $billCode = $booking->payment->payment_meta['bill_code'] ?? null;
        if ($billCode && $booking->payment->payment_method === 'toyibpay') {
            try {
                $toyibPay = app(ToyibPayService::class);
                $tx = $this->checkToyibPayBill($toyibPay, $billCode);

                if ($tx && ($tx['billpaymentStatus'] ?? '') === '1') {
                    $this->markDeportationPaymentPaid($booking, $tx);
                    $response['booking_status'] = 'paid';
                    $response['payment_status'] = 'paid';
                    $response['done'] = true;
                    $response['redirect'] = route('deportation.success', $booking->booking_code);
                }
            } catch (\Exception $e) {
                Log::warning('Deportation checkPaymentStatus error', ['booking' => $code, 'error' => $e->getMessage()]);
            }
        }

        if ($booking->expires_at && $booking->expires_at->isPast() && $booking->payment_status === 'pending') {
            $booking->update(['booking_status' => 'cancelled', 'payment_status' => 'expired']);
            $response['booking_status'] = 'cancelled';
            $response['payment_status'] = 'expired';
            $response['done'] = true;
        }

        $response['done'] = $response['done'] ?? false;

        return response()->json($response);
    }

    /**
     * ToyibPay return URL for deportation.
     */
    public function toyibpayReturn($code)
    {
        $booking = Booking::where('booking_code', $code)
            ->where('is_deportation', true)
            ->with(['payment', 'passengers', 'schedule'])
            ->firstOrFail();

        if ($booking->user_id && $booking->user_id !== auth()->id()) {
            abort(403);
        }

        if (in_array($booking->payment_status, ['paid', 'approved'])) {
            return redirect()->route('deportation.success', $booking->booking_code);
        }

        $billCode = $booking->payment->payment_meta['bill_code'] ?? null;
        if ($billCode) {
            try {
                $toyibPay = app(ToyibPayService::class);
                $tx = $this->checkToyibPayBill($toyibPay, $billCode);

                if ($tx && ($tx['billpaymentStatus'] ?? '') === '1') {
                    $this->markDeportationPaymentPaid($booking, $tx);
                    return redirect()->route('deportation.success', $booking->booking_code);
                }
            } catch (\Exception $e) {
                Log::warning('Deportation toyibpayReturn error', ['booking' => $code, 'error' => $e->getMessage()]);
            }
        }

        return view('deportation.payment', compact('booking'));
    }

    /**
     * ToyibPay callback for deportation.
     */
    public function toyibpayCallback(Request $request)
    {
        $data = $request->all();
        Log::info('Deportation ToyibPay callback', $data);

        $toyibPay = app(ToyibPayService::class);

        if (!$toyibPay->verifyCallback($data)) {
            Log::warning('Deportation ToyibPay callback hash verification failed', $data);
            return response('Invalid hash', 400);
        }

        $orderId = $data['order_id'] ?? null;
        $status = (int) ($data['status'] ?? 0);

        if (!$orderId) {
            return response('Missing order_id', 400);
        }

        $booking = Booking::where('booking_code', $orderId)
            ->where('is_deportation', true)
            ->with(['payment', 'passengers', 'schedule'])
            ->first();

        if (!$booking) {
            return response('Booking not found', 404);
        }

        $refno = $data['refno'] ?? null;
        if ($refno) {
            $processedRefnos = $booking->payment->payment_meta['processed_refnos'] ?? [];
            if (in_array($refno, $processedRefnos)) {
                return response('OK (already processed)');
            }
        }

        if ($status === 1) {
            if (!in_array($booking->payment_status, ['paid', 'approved'])) {
                $this->markDeportationPaymentPaid($booking, $data);
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
        }

        AuditLog::log(
            action: 'deportation_toyibpay_callback',
            entityType: 'payment',
            entityId: $booking->payment?->id,
            payload: ['booking_code' => $booking->booking_code, 'status' => $status, 'refno' => $refno, 'data' => $data],
            ipAddress: request()->ip(),
        );

        return response('OK');
    }

    /**
     * Check ToyibPay bill status directly via API.
     */
    private function checkToyibPayBill(ToyibPayService $toyibPay, string $billCode): ?array
    {
        $baseUrl = rtrim(config('toyibpay.base_url'), '/');
        $response = \Illuminate\Support\Facades\Http::asForm()
            ->timeout(30)
            ->post($baseUrl.'/index.php/api/getBillTransactions', [
                'billCode' => $billCode,
                'billpaymentStatus' => '1',
            ]);

        if (!$response->successful()) {
            return null;
        }

        $result = $response->json();
        if (!is_array($result) || empty($result)) {
            return null;
        }

        foreach ($result as $tx) {
            if (($tx['billpaymentStatus'] ?? '') === '1') {
                return $tx;
            }
        }

        return null;
    }

    /**
     * Mark deportation payment as paid and generate open tickets.
     */
    private function markDeportationPaymentPaid(Booking $booking, array $txData): void
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

            // Generate deportation open tickets (no expiry date)
            foreach ($booking->passengers as $passenger) {
                if (!$passenger->ticket) {
                    $passenger->ticket()->create([
                        'booking_id' => $booking->id,
                        'ticket_class' => $passenger->ticket_class,
                        'qr_token' => Ticket::generateQrToken(),
                        'ticket_number' => Ticket::generateTicketNumber(),
                        'ticket_status' => 'active',
                        'expiry_date' => null, // Open ticket - no expiry
                        'is_deportation' => true,
                    ]);
                }
            }

            // Log analytics
            DeportationAnalytics::create([
                'event_type' => 'payment',
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'shelter_point' => $booking->shelter_point,
                'payload' => [
                    'amount' => $booking->total_amount,
                    'toyibpay_refno' => $txData['refno'] ?? null,
                ],
            ]);
        });

        // Notify user
        if ($booking->user_id) {
            Notification::create([
                'user_id' => $booking->user_id,
                'type' => 'payment_success',
                'channel' => 'database',
                'title' => 'Pembayaran Deportasi Berjaya',
                'body' => 'Pembayaran untuk tiket deportasi #'.$booking->booking_code.' telah diterima. Tiket terbuka anda siap sedia!',
                'sent_at' => now(),
            ]);
        }

        AuditLog::log(
            action: 'deportation_payment_paid',
            entityType: 'booking',
            entityId: $booking->id,
            payload: [
                'booking_code' => $booking->booking_code,
                'amount' => $booking->total_amount,
                'toyibpay_refno' => $txData['refno'] ?? null,
            ],
            ipAddress: request()->ip(),
        );
    }

    /**
     * Show deportation QR scanner for boarding officer.
     */
    public function scanner()
    {
        return view('deportation.scanner');
    }

    /**
     * Scan deportation QR code for boarding.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'qr_data' => ['required', 'string'],
        ]);

        $qrData = json_decode($request->qr_data, true);

        if (!$qrData || !isset($qrData['ticket_id'])) {
            return response()->json([
                'success' => false,
                'status' => 'invalid',
                'message' => 'Kod QR tidak sah.',
            ]);
        }

        $ticket = Ticket::where('id', $qrData['ticket_id'])
            ->where('is_deportation', true)
            ->with(['passenger', 'booking.user', 'booking.schedule.vessel', 'booking.schedule.route'])
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'status' => 'invalid',
                'message' => 'Tiket deportasi tidak dijumpai.',
            ]);
        }

        if ($ticket->ticket_status === 'used') {
            return response()->json([
                'success' => false,
                'status' => 'used',
                'message' => 'Tiket deportasi ini telah digunakan.',
                'passenger_name' => $ticket->passenger->full_name,
                'ticket_number' => $ticket->ticket_number,
                'boarded_at' => $ticket->boarded_at?->format('d M Y, H:i'),
                'type' => 'red_warning',
            ]);
        }

        if ($ticket->ticket_status === 'cancelled') {
            return response()->json([
                'success' => false,
                'status' => 'invalid',
                'message' => 'Tiket deportasi ini telah dibatalkan.',
                'type' => 'red_rejection',
            ]);
        }

        DB::transaction(function () use ($ticket) {
            $ticket->update([
                'ticket_status' => 'used',
                'boarded_at' => now(),
            ]);

            $booking = $ticket->booking;
            $allUsed = $booking->tickets()->where('ticket_status', '!=', 'used')->doesntExist();
            if ($allUsed) {
                $booking->update(['booking_status' => 'used']);
                if ($booking->payment) {
                    $booking->payment->update(['payment_status' => 'completed']);
                }
            }

            // Log analytics
            DeportationAnalytics::create([
                'event_type' => 'boarding',
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'ticket_id' => $ticket->id,
                'shelter_point' => $booking->shelter_point,
                'payload' => [
                    'officer_id' => auth()->id(),
                    'officer_name' => auth()->user()->name,
                    'passenger_name' => $ticket->passenger->full_name,
                    'ticket_number' => $ticket->ticket_number,
                ],
            ]);
        });

        $schedule = $ticket->booking->schedule;

        return response()->json([
            'success' => true,
            'status' => 'valid',
            'message' => 'Boarding deportasi berjaya!',
            'type' => 'green_success',
            'passenger_name' => $ticket->passenger->full_name,
            'ticket_number' => $ticket->ticket_number,
            'ticket_class' => ucfirst($ticket->ticket_class),
            'passenger_type' => $ticket->passenger->passenger_type,
            'shelter_point' => $ticket->booking->user->shelter_point_name ?? '—',
            'route' => $schedule->route->origin_port.' → '.$schedule->route->destination_port,
            'vessel' => $schedule->vessel->name,
            'departure' => $schedule->departure_time->format('d M Y, H:i'),
        ]);
    }

    /**
     * Show deportation history for the user.
     */
    public function history()
    {
        $user = auth()->user();

        if (!$user->isDeportation()) {
            return redirect()->route('home');
        }

        $bookings = $user->bookings()
            ->where('is_deportation', true)
            ->with(['passengers.ticket', 'payment'])
            ->latest()
            ->paginate(10);

        return view('deportation.history', compact('bookings'));
    }

    // ========================
    // Existing Officer Methods
    // ========================

    public function index()
    {
        $manifests = DeportationManifest::with(['schedule.vessel', 'schedule.route', 'officer', 'passengers'])
            ->latest()
            ->paginate(10);

        return view('deportation.index', compact('manifests'));
    }

    public function create()
    {
        $schedules = Schedule::with('vessel', 'route')
            ->where('status', 'scheduled')
            ->where('departure_time', '>', now())
            ->get();

        return view('deportation.create', compact('schedules'));
    }

    public function storeManifest(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => ['required', 'exists:schedules,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $manifest = DeportationManifest::create([
            'schedule_id' => $validated['schedule_id'],
            'officer_id' => auth()->id(),
            'manifest_code' => DeportationManifest::generateManifestCode(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('deportation.manifest.show', $manifest->manifest_code);
    }

    public function showManifest($code)
    {
        $manifest = DeportationManifest::where('manifest_code', $code)
            ->with(['schedule.vessel', 'schedule.route', 'officer', 'passengers'])
            ->firstOrFail();

        return view('deportation.manifest', compact('manifest'));
    }

    public function addPassenger(Request $request, DeportationManifest $manifest)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'nationality' => ['required', 'string', 'max:50'],
            'passport_number' => ['required', 'string', 'max:50'],
        ]);

        DB::transaction(function () use ($manifest, $validated) {
            $passenger = $manifest->passengers()->create([
                'full_name' => $validated['full_name'],
                'gender' => $validated['gender'],
                'nationality' => $validated['nationality'],
                'passport_number' => $validated['passport_number'],
                'qr_token' => DeportationPassenger::generateQrToken(),
            ]);

            $manifest->updateTotalPassengers();
        });

        return back()->with('success', 'Passenger added to deportation manifest.');
    }

    public function boardingScan(Request $request)
    {
        $request->validate([
            'qr_data' => ['required', 'string'],
        ]);

        $qrData = json_decode($request->qr_data, true);

        if (!$qrData || !isset($qrData['passenger_id'])) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Invalid QR code.',
            ]);
        }

        $passenger = DeportationPassenger::with('manifest.schedule.vessel', 'manifest.schedule.route')
            ->find($qrData['passenger_id']);

        if (!$passenger) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Passenger not found.',
            ]);
        }

        if ($passenger->boarding_status === 'boarded') {
            return response()->json([
                'status' => 'used',
                'message' => 'Passenger already boarded.',
            ]);
        }

        $passenger->update([
            'boarding_status' => 'boarded',
            'boarded_at' => now(),
        ]);

        return response()->json([
            'status' => 'valid',
            'message' => 'Deportation boarding successful!',
            'passenger' => [
                'name' => $passenger->full_name,
                'nationality' => $passenger->nationality,
            ],
        ]);
    }
}
