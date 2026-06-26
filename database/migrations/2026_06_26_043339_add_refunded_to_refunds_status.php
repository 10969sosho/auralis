<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE refunds MODIFY COLUMN refund_status ENUM('requested', 'approved', 'rejected', 'refunded', 'processed') DEFAULT 'requested'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE refunds MODIFY COLUMN refund_status ENUM('requested', 'approved', 'rejected', 'processed') DEFAULT 'requested'");
    }
};
