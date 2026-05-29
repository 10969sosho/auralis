<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE booking_passengers MODIFY passenger_type VARCHAR(50) DEFAULT 'Adult'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE booking_passengers MODIFY passenger_type ENUM('child', 'adult') DEFAULT 'adult'");
    }
};
