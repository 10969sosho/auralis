<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('nationality', 50)->nullable()->after('phone');
            $table->string('passport_number', 50)->nullable()->after('nationality');
            $table->date('birth_date')->nullable()->after('passport_number');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birth_date');
            $table->boolean('is_active')->default(true)->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'nationality', 'passport_number', 'birth_date', 'gender', 'is_active']);
        });
    }
};
