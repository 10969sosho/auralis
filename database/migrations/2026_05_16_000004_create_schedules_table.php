<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->decimal('vip_price', 10, 2);
            $table->decimal('regular_price', 10, 2);
            $table->integer('vip_remaining')->nullable();
            $table->integer('regular_remaining')->nullable();
            $table->enum('status', ['scheduled', 'departed', 'cancelled', 'completed'])->default('scheduled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
