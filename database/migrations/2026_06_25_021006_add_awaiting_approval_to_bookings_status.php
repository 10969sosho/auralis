<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY COLUMN booking_status ENUM('pending_payment', 'awaiting_approval', 'paid', 'used', 'expired', 'cancelled', 'refund_requested', 'refunded') DEFAULT 'pending_payment'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY COLUMN booking_status ENUM('pending_payment', 'paid', 'used', 'expired', 'cancelled', 'refund_requested', 'refunded') DEFAULT 'pending_payment'");
    }
};
