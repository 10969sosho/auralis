<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_passenger_id')->constrained()->cascadeOnDelete();
            $table->enum('ticket_class', ['vip', 'regular']);
            $table->text('qr_token');
            $table->string('ticket_number')->unique();
            $table->enum('ticket_status', ['active', 'used', 'expired', 'cancelled', 'refunded'])->default('active');
            $table->timestamp('boarded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
