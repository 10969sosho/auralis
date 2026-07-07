<?php

namespace Database\Seeders;

use App\Models\AgeCategory;
use App\Models\Promo;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\ScheduleAgePrice;
use App\Models\User;
use App\Models\Vessel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);
        $this->call(AgeCategorySeeder::class);

        // ─── Users ────────────────────────────────────────────
        $admin = User::create([
            'name' => 'Admin System',
            'email' => 'admin@auralis8.com',
            'password' => Hash::make('password'),
            'phone' => '0190000000',
            'nationality' => 'Malaysian',
            'passport_number' => 'A12345678',
            'birth_date' => '1990-01-15',
            'gender' => 'male',
        ]);
        $admin->assignRole('admin');

        $boardingOfficer = User::create([
            'name' => 'Boarding Officer 1',
            'email' => 'boarding@auralis8.com',
            'password' => Hash::make('password'),
            'phone' => '0191111111',
            'nationality' => 'Malaysian',
            'gender' => 'male',
        ]);
        $boardingOfficer->assignRole('boarding_officer');

        $ticketCounter = User::create([
            'name' => 'Ticket Counter 1',
            'email' => 'counter@auralis8.com',
            'password' => Hash::make('password'),
            'phone' => '0192222222',
            'nationality' => 'Malaysian',
            'gender' => 'female',
        ]);
        $ticketCounter->assignRole('ticket_counter_officer');

        $deportationOfficer = User::create([
            'name' => 'Deportation Officer 1',
            'email' => 'deportation@auralis8.com',
            'password' => Hash::make('password'),
            'phone' => '0193333333',
        ]);
        $deportationOfficer->assignRole('deportation_officer');

        $passenger = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'phone' => '0194444444',
            'nationality' => 'Filipino',
            'passport_number' => 'P98765432',
            'birth_date' => '1995-06-20',
            'gender' => 'male',
        ]);
        $passenger->assignRole('passenger');

        // ─── Vessel ───────────────────────────────────────────
        $vessel = Vessel::create([
            'name' => 'Auralis 8',
            'capacity' => 280,
            'vip_capacity' => 40,
            'regular_capacity' => 240,
            'free_baggage' => 10,
            'status' => 'active',
        ]);

        // ─── Routes ───────────────────────────────────────────
        $route1 = Route::create([
            'origin_port' => 'Bongao, Tawi-Tawi (Philippines)',
            'destination_port' => 'Lahad Datu, Sabah (Malaysia)',
            'estimated_duration' => 120,
            'active' => true,
        ]);

        $route2 = Route::create([
            'origin_port' => 'Lahad Datu, Sabah (Malaysia)',
            'destination_port' => 'Bongao, Tawi-Tawi (Philippines)',
            'estimated_duration' => 120,
            'active' => true,
        ]);

        // ─── Schedules ────────────────────────────────────────
        $schedule1 = Schedule::create([
            'vessel_id' => $vessel->id,
            'route_id' => $route1->id,
            'departure_time' => Carbon::now()->addDays(3)->setHour(8)->setMinute(0)->setSecond(0),
            'arrival_time' => Carbon::now()->addDays(3)->setHour(10)->setMinute(0)->setSecond(0),
            'vip_price' => 150.00,
            'regular_price' => 80.00,
            'vip_remaining' => $vessel->vip_capacity,
            'regular_remaining' => $vessel->regular_capacity,
            'status' => 'scheduled',
            'is_active' => true,
        ]);

        $schedule2 = Schedule::create([
            'vessel_id' => $vessel->id,
            'route_id' => $route2->id,
            'departure_time' => Carbon::now()->addDays(5)->setHour(14)->setMinute(0)->setSecond(0),
            'arrival_time' => Carbon::now()->addDays(5)->setHour(16)->setMinute(0)->setSecond(0),
            'vip_price' => 150.00,
            'regular_price' => 80.00,
            'vip_remaining' => $vessel->vip_capacity,
            'regular_remaining' => $vessel->regular_capacity,
            'status' => 'scheduled',
            'is_active' => true,
        ]);

        $schedule3 = Schedule::create([
            'vessel_id' => $vessel->id,
            'route_id' => $route1->id,
            'departure_time' => Carbon::now()->addDays(7)->setHour(8)->setMinute(0)->setSecond(0),
            'arrival_time' => Carbon::now()->addDays(7)->setHour(10)->setMinute(0)->setSecond(0),
            'vip_price' => 150.00,
            'regular_price' => 80.00,
            'vip_remaining' => $vessel->vip_capacity,
            'regular_remaining' => $vessel->regular_capacity,
            'status' => 'scheduled',
            'is_active' => true,
        ]);

        // ─── Age Pricing ──────────────────────────────────────
        $infant = AgeCategory::where('name', 'Infant')->first();
        $child = AgeCategory::where('name', 'Child')->first();
        $adult = AgeCategory::where('name', 'Adult')->first();

        foreach ([$schedule1, $schedule2, $schedule3] as $s) {
            if ($infant) {
                ScheduleAgePrice::create([
                    'schedule_id' => $s->id,
                    'age_category_id' => $infant->id,
                    'price' => 0,
                ]);
            }
            if ($child) {
                ScheduleAgePrice::create([
                    'schedule_id' => $s->id,
                    'age_category_id' => $child->id,
                    'price' => 50.00,
                ]);
            }
            if ($adult) {
                ScheduleAgePrice::create([
                    'schedule_id' => $s->id,
                    'age_category_id' => $adult->id,
                    'price' => $s->regular_price,
                ]);
            }
        }

        // ─── Promos ───────────────────────────────────────────
        Promo::create([
            'name' => 'Early Bird 20%',
            'code' => 'EARLY20',
            'type' => 'percentage',
            'value' => 20,
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(30),
            'usage_quota' => 100,
            'used_count' => 0,
            'is_active' => true,
            'auto_apply' => false,
            'ticket_class' => 'regular',
        ]);

        Promo::create([
            'name' => 'RM10 Off Regular',
            'code' => 'SAVE10',
            'type' => 'fixed_amount',
            'value' => 10,
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(14),
            'usage_quota' => 50,
            'used_count' => 0,
            'is_active' => true,
            'auto_apply' => false,
            'ticket_class' => 'regular',
        ]);
    }
}
