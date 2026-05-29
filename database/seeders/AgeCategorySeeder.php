<?php

namespace Database\Seeders;

use App\Models\AgeCategory;
use Illuminate\Database\Seeder;

class AgeCategorySeeder extends Seeder
{
    public function run(): void
    {
        AgeCategory::firstOrCreate(
            ['name' => 'Infant'],
            ['min_age' => 0, 'max_age' => 2, 'is_active' => true, 'sort_order' => 1]
        );

        AgeCategory::firstOrCreate(
            ['name' => 'Child'],
            ['min_age' => 3, 'max_age' => 12, 'is_active' => true, 'sort_order' => 2]
        );

        AgeCategory::firstOrCreate(
            ['name' => 'Adult'],
            ['min_age' => 13, 'max_age' => 150, 'is_active' => true, 'sort_order' => 3]
        );
    }
}
