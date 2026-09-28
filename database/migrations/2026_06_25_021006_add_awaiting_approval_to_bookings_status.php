<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('booking_status', ['pending_payment', 'awaiting_approval', 'paid', 'used', 'expired', 'cancelled', 'refund_requested', 'refunded'])->default('pending_payment')->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('booking_status', ['pending_payment', 'paid', 'used', 'expired', 'cancelled', 'refund_requested', 'refunded'])->default('pending_payment')->change();
        });
    }
};
