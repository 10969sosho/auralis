<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boarding_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('validated_by')->constrained('users')->cascadeOnDelete();
            $table->enum('validation_result', ['valid', 'used', 'invalid', 'expired', 'cancelled', 'refunded']);
            $table->string('device_info')->nullable();
            $table->string('scan_method')->default('qr');
            $table->timestamp('validated_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boarding_logs');
    }
};
