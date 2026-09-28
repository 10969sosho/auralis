<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_ships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('capacity');
            $table->timestamps();
        });

        if (DB::table('master_ships')->count() === 0) {
            DB::table('master_ships')->insert([
                'name' => 'Auralis Ferry',
                'capacity' => 280,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('master_ships');
    }
};
