<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('age_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('min_age')->comment('Minimum age (inclusive)');
            $table->integer('max_age')->comment('Maximum age (inclusive)');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('schedule_age_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('age_category_id')->constrained('age_categories')->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->unique(['schedule_id', 'age_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_age_prices');
        Schema::dropIfExists('age_categories');
    }
};
