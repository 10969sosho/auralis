<?php

namespace Database\Seeders;

use App\Models\Promo;
use App\Models\Route;
use App\Models\Schedule;
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

        $admin = User::create([
            'name' => 'Admin System',
            'email' => 'admin@shipticketing.com',
            'password' => Hash::make('password'),
            'phone' => '0190000000',
        ]);
        $admin->assignRole('admin');

        $boardingOfficer = User::create([
            'name' => 'Boarding Officer 1',
            'email' => 'boarding@shipticketing.com',
            'password' => Hash::make('password'),
            'phone' => '0191111111',
        ]);
        $boardingOfficer->assignRole('boarding_officer');

        $ticketCounter = User::create([
            'name' => 'Ticket Counter 1',
            'email' => 'counter@shipticketing.com',
            'password' => Hash::make('password'),
            'phone' => '0192222222',
        ]);
        $ticketCounter->assignRole('ticket_counter_officer');

        $deportationOfficer = User::create([
            'name' => 'Deportation Officer 1',
            'email' => 'deportation@shipticketing.com',
            'password' => Hash::make('password'),
            'phone' => '0193333333',
        ]);
        $deportationOfficer->assignRole('deportation_officer');

        $passenger = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'phone' => '0194444444',
        ]);
        $passenger->assignRole('passenger');

        $vessel = Vessel::create([
            'name' => 'Auralis 8',
            'capacity' => 280,
            'vip_capacity' => 40,
            'regular_capacity' => 240,
            'free_baggage' => 10,
            'status' => 'active',
        ]);

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

        $schedule1 = Schedule::create([
            'vessel_id' => $vessel->id,
            'route_id' => $route1->id,
            'departure_time' => Carbon::now()->addDays(3)->setHour(8)->setMinute(0)->setSecond(0),
            'arrival_time' => Carbon::now()->addDays(3)->setHour(10)->setMinute(0)->setSecond(0),
            'vip_price' => 150.00,
            'regular_price' => 80.00,
            'status' => 'scheduled',
        ]);

        $schedule2 = Schedule::create([
            'vessel_id' => $vessel->id,
            'route_id' => $route2->id,
            'departure_time' => Carbon::now()->addDays(5)->setHour(14)->setMinute(0)->setSecond(0),
            'arrival_time' => Carbon::now()->addDays(5)->setHour(16)->setMinute(0)->setSecond(0),
            'vip_price' => 150.00,
            'regular_price' => 80.00,
            'status' => 'scheduled',
        ]);

        $schedule3 = Schedule::create([
            'vessel_id' => $vessel->id,
            'route_id' => $route1->id,
            'departure_time' => Carbon::now()->addDays(7)->setHour(8)->setMinute(0)->setSecond(0),
            'arrival_time' => Carbon::now()->addDays(7)->setHour(10)->setMinute(0)->setSecond(0),
            'vip_price' => 150.00,
            'regular_price' => 80.00,
            'status' => 'scheduled',
        ]);

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
