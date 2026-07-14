<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['schedule_id']);

            // Make schedule_id nullable
            $table->unsignedBigInteger('schedule_id')->nullable()->change();

            // Re-add foreign key as nullable
            $table->foreign('schedule_id')
                ->references('id')
                ->on('schedules')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // Add route and vessel info for deportation bookings
            $table->string('route_text')->nullable()->after('shelter_fee');
            $table->string('vessel_text')->nullable()->after('route_text');
            $table->decimal('route_vip_price', 10, 2)->nullable()->after('vessel_text');
            $table->decimal('route_regular_price', 10, 2)->nullable()->after('route_vip_price');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);

            $table->unsignedBigInteger('schedule_id')->nullable(false)->change();

            $table->foreign('schedule_id')
                ->references('id')
                ->on('schedules')
                ->cascadeOnDelete();

            $table->dropColumn([
                'route_text',
                'vessel_text',
                'route_vip_price',
                'route_regular_price',
            ]);
        });
    }
};
