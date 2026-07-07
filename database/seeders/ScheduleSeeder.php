<?php

namespace Database\Seeders;

use App\Models\AgeCategory;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\ScheduleAgePrice;
use App\Models\Vessel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $vessel = Vessel::where('status', 'active')->first();
        if (!$vessel) {
            $this->command->warn('No active vessel found. Run DatabaseSeeder first.');
            return;
        }

        $route1 = Route::where('origin_port', 'like', '%Bongao%')->first();
        $route2 = Route::where('origin_port', 'like', '%Lahad%')->first();

        if (!$route1 || !$route2) {
            $this->command->warn('Routes not found. Run DatabaseSeeder first.');
            return;
        }

        $infant = AgeCategory::where('name', 'Infant')->first();
        $child = AgeCategory::where('name', 'Child')->first();
        $adult = AgeCategory::where('name', 'Adult')->first();

        $schedules = [
            [
                'route_id' => $route1->id,
                'departure_time' => Carbon::now()->addHours(2)->setMinute(0)->setSecond(0),
                'arrival_time' => Carbon::now()->addHours(4)->setMinute(0)->setSecond(0),
                'vip_price' => 200.00,
                'regular_price' => 100.00,
            ],
            [
                'route_id' => $route2->id,
                'departure_time' => Carbon::now()->addHours(6)->setMinute(0)->setSecond(0),
                'arrival_time' => Carbon::now()->addHours(8)->setMinute(0)->setSecond(0),
                'vip_price' => 200.00,
                'regular_price' => 100.00,
            ],
            [
                'route_id' => $route1->id,
                'departure_time' => Carbon::tomorrow()->setHour(8)->setMinute(0)->setSecond(0),
                'arrival_time' => Carbon::tomorrow()->setHour(10)->setMinute(0)->setSecond(0),
                'vip_price' => 200.00,
                'regular_price' => 100.00,
            ],
            [
                'route_id' => $route2->id,
                'departure_time' => Carbon::tomorrow()->setHour(14)->setMinute(0)->setSecond(0),
                'arrival_time' => Carbon::tomorrow()->setHour(16)->setMinute(0)->setSecond(0),
                'vip_price' => 200.00,
                'regular_price' => 100.00,
            ],
            [
                'route_id' => $route1->id,
                'departure_time' => Carbon::now()->addDays(2)->setHour(8)->setMinute(0)->setSecond(0),
                'arrival_time' => Carbon::now()->addDays(2)->setHour(10)->setMinute(0)->setSecond(0),
                'vip_price' => 200.00,
                'regular_price' => 100.00,
            ],
        ];

        foreach ($schedules as $data) {
            $schedule = Schedule::create([
                'vessel_id' => $vessel->id,
                'route_id' => $data['route_id'],
                'departure_time' => $data['departure_time'],
                'arrival_time' => $data['arrival_time'],
                'vip_price' => $data['vip_price'],
                'regular_price' => $data['regular_price'],
                'vip_remaining' => $vessel->vip_capacity,
                'regular_remaining' => $vessel->regular_capacity,
                'status' => 'scheduled',
                'is_active' => true,
            ]);

            // Age pricing
            if ($infant) {
                ScheduleAgePrice::create([
                    'schedule_id' => $schedule->id,
                    'age_category_id' => $infant->id,
                    'price' => 0,
                ]);
            }
            if ($child) {
                ScheduleAgePrice::create([
                    'schedule_id' => $schedule->id,
                    'age_category_id' => $child->id,
                    'price' => 50.00,
                ]);
            }
            if ($adult) {
                ScheduleAgePrice::create([
                    'schedule_id' => $schedule->id,
                    'age_category_id' => $adult->id,
                    'price' => $data['regular_price'],
                ]);
            }

            $route = $schedule->route;
            $this->command->info("Schedule: {$route->origin_port} → {$route->destination_port} @ {$data['departure_time']->format('d M Y H:i')}");
        }

        $this->command->info('');
        $this->command->info('5 active schedules created successfully!');
    }
}
