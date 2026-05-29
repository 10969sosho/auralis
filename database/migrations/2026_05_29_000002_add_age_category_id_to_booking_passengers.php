<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_passengers', function (Blueprint $table) {
            $table->foreignId('age_category_id')->nullable()->after('ticket_class')->constrained('age_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('booking_passengers', function (Blueprint $table) {
            $table->dropForeign(['age_category_id']);
            $table->dropColumn('age_category_id');
        });
    }
};
