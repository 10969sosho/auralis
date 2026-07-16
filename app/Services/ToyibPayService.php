<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToyibPayService
{
    protected string $secretKey;
    protected string $categoryCode;
    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = Setting::getValue('toyibpay_secret_key', config('toyibpay.secret_key'));
        $this->categoryCode = Setting::getValue('toyibpay_category_code', config('toyibpay.category_code'));
        $this->baseUrl = rtrim(config('toyibpay.base_url'), '/');
    }

    /**
     * Create a bill on ToyibPay with all payment channels (FPX, CC, DuitNow QR).
     * Includes RM 1 flat processing fee.
     * Returns ['bill_code' => '...', 'payment_url' => '...'] or throws.
     */
    public function createBill(Booking $booking): array
    {
        $booking->loadMissing('schedule.route', 'passengers');

        $passengerNames = $booking->passengers->pluck('full_name')->join(', ');
        $route = $booking->schedule->route->origin_port . ' → ' . $booking->schedule->route->destination_port;
        $billName = substr('Ticket_' . $booking->booking_code, 0, 30);
        $billDescription = substr($route . ' | ' . $booking->schedule->departure_time->format('d M H:i'), 0, 100);

        // Add RM 1 flat processing fee
        $feeInCents = 100;
        $amountInCents = (int) round($booking->total_amount * 100) + $feeInCents;

        $data = [
            'userSecretKey' => $this->secretKey,
            'categoryCode' => $this->categoryCode,
            'billName' => $billName,
            'billDescription' => $billDescription,
            'billPriceSetting' => 1,                    // fixed amount
            'billPayorInfo' => 1,                       // require payer info
            'billAmount' => $amountInCents,
            'billReturnUrl' => route('booking.toyibpay-return', $booking->booking_code),
            'billCallbackUrl' => route('booking.toyibpay-callback'),
            'billExternalReferenceNo' => $booking->booking_code,
            'billTo' => $passengerNames,
            'billEmail' => $booking->user?->email ?? '',
            'billPhone' => $booking->passengers->first()?->phone_number ?? '',
            'billExpiryDate' => $booking->expires_at->format('d-m-Y H:i:s'),
            // All payment channels, charge to customer
            'billPaymentChannel' => '2',                 // 0=FPX, 1=CC, 2=Both
            'enableDuitNowQR' => '1',                    // Enable DuitNow QR
            'chargeDuitNowQR' => '1',                    // charge to customer
            'billChargeToCustomer' => '0',               // charge FPX to customer
        ];

        Log::info('ToyibPay createBill request', $data);

        $response = Http::asForm()
            ->timeout(30)
            ->post($this->baseUrl . '/index.php/api/createBill', $data);

        Log::info('ToyibPay createBill response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('ToyibPay API error: HTTP ' . $response->status());
        }

        $result = $response->json();

        if (!is_array($result) || !isset($result[0]['BillCode'])) {
            Log::error('ToyibPay createBill unexpected response', ['response' => $response->body()]);
            throw new \RuntimeException('ToyibPay API returned unexpected response: ' . $response->body());
        }

        $billCode = $result[0]['BillCode'];

        return [
            'bill_code' => $billCode,
            'payment_url' => $this->baseUrl . '/' . $billCode,
        ];
    }

    /**
     * Check bill transaction status.
     * Returns array with bill details or null.
     */
    public function getBillTransactions(string $billCode): ?array
    {
        $data = [
            'billCode' => $billCode,
            'billpaymentStatus' => '1',
        ];

        $response = Http::asForm()
            ->timeout(30)
            ->post($this->baseUrl . '/index.php/api/getBillTransactions', $data);

        Log::info('ToyibPay getBillTransactions', [
            'billCode' => $billCode,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if (!$response->successful()) {
            return null;
        }

        $result = $response->json();

        if (!is_array($result) || empty($result)) {
            return null;
        }

        // If a successful transaction exists, return the first one
        foreach ($result as $tx) {
            if (($tx['billpaymentStatus'] ?? '') === '1') {
                return $tx;
            }
        }

        return null;
    }

    /**
     * Verify the callback hash from ToyibPay.
     */
    public function verifyCallback(array $data): bool
    {
        $status = $data['status'] ?? '';
        $orderId = $data['order_id'] ?? '';
        $refno = $data['refno'] ?? '';
        $receivedHash = $data['hash'] ?? '';

        $expectedHash = md5($this->secretKey . $status . $orderId . $refno . 'ok');

        return hash_equals($expectedHash, $receivedHash);
    }

    /**
     * Get the payment URL for a bill code.
     */
    public function getPaymentUrl(string $billCode): string
    {
        return $this->baseUrl . '/' . $billCode;
    }

    /**
     * Create a new category. Usually only needed once.
     */
    public function createCategory(string $name, string $description): ?string
    {
        $data = [
            'userSecretKey' => $this->secretKey,
            'catname' => $name,
            'catdescription' => $description,
        ];

        $response = Http::asForm()
            ->timeout(30)
            ->post($this->baseUrl . '/index.php/api/createCategory', $data);

        if (!$response->successful()) {
            return null;
        }

        $result = $response->json();

        return $result[0]['CategoryCode'] ?? null;
    }
}
