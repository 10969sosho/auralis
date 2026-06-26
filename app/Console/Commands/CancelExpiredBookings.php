<?php

namespace App\Console\Commands;

use App\Events\SeatAvailabilityUpdated;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CancelExpiredBookings extends Command
{
    protected $signature = 'bookings:cancel-expired';
    protected $description = 'Cancel all bookings where payment time has expired';

    public function handle(): void
    {
        $expiredBookings = Booking::where('booking_status', 'pending_payment')
            ->where('payment_status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expiredBookings as $booking) {
            DB::transaction(function () use ($booking) {
                $booking->update([
                    'booking_status' => 'cancelled',
                    'payment_status' => 'expired',
                ]);
                event(new SeatAvailabilityUpdated($booking->schedule));
            });
            $count++;
        }

        $this->info("Cancelled {$count} expired booking(s).");
    }
}
