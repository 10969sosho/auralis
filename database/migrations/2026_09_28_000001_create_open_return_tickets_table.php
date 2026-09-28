<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('open_return_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->date('return_date');
            $table->enum('status', ['open', 'assigned', 'used', 'cancelled'])->default('open');
            $table->timestamps();

            $table->index(['return_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('open_return_tickets');
    }
};
