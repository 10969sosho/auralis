<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->enum('type', ['percentage', 'fixed_amount']);
            $table->decimal('value', 10, 2);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('usage_quota');
            $table->integer('used_count')->default(0);
            $table->foreignId('route_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('ticket_class', ['vip', 'regular', 'all'])->nullable()->default('all');
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_apply')->default(false);
            $table->integer('min_passengers')->nullable();
            $table->integer('max_passengers')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
