<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('proof_of_transfer')->nullable()->after('transaction_id');
            $table->foreignId('approved_by')->nullable()->after('proof_of_transfer')->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable()->after('approved_by');
            $table->timestamp('approved_at')->nullable()->after('rejection_reason');
        });

        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_status ENUM('pending', 'awaiting_approval', 'paid', 'approved', 'rejected', 'failed', 'expired') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_status ENUM('pending', 'paid', 'failed', 'expired') DEFAULT 'pending'");

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['proof_of_transfer', 'approved_by', 'rejection_reason', 'approved_at']);
        });
    }
};
