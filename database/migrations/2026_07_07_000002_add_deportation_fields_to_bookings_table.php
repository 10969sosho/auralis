<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('is_deportation')->default(false)->after('payment_status');
            $table->string('shelter_point')->nullable()->after('is_deportation');
            $table->decimal('shelter_fee', 10, 2)->default(0)->after('shelter_point');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['is_deportation', 'shelter_point', 'shelter_fee']);
        });
    }
};
