<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('birth_date');
            $table->string('nationality');
            $table->string('passport_number');
            $table->string('phone_number', 20)->nullable();
            $table->enum('passenger_type', ['child', 'adult'])->default('adult');
            $table->enum('ticket_class', ['vip', 'regular']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_passengers');
    }
};
