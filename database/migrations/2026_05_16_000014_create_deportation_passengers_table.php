<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deportation_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manifest_id')->constrained('deportation_manifests')->cascadeOnDelete();
            $table->string('full_name');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('nationality');
            $table->string('passport_number');
            $table->text('qr_token')->nullable();
            $table->enum('boarding_status', ['pending', 'boarded', 'rejected'])->default('pending');
            $table->timestamp('boarded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deportation_passengers');
    }
};
