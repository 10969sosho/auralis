<?php

namespace Tests\Feature;

use App\Models\Route;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vessel;
use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $passenger;

    protected Schedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(RoleAndPermissionSeeder::class);

        $this->passenger = User::factory()->create();
        $this->passenger->assignRole('passenger');

        $vessel = Vessel::create([
            'name' => 'Auralis 8',
            'capacity' => 280,
            'vip_capacity' => 40,
            'regular_capacity' => 240,
            'free_baggage' => 10,
            'status' => 'active',
        ]);

        $route = Route::create([
            'origin_port' => 'Bongao, Tawi-Tawi (Philippines)',
            'destination_port' => 'Lahad Datu, Sabah (Malaysia)',
            'estimated_duration' => 120,
            'active' => true,
        ]);

        $this->schedule = Schedule::create([
            'vessel_id' => $vessel->id,
            'route_id' => $route->id,
            'departure_time' => Carbon::now()->addDays(3)->setHour(8, 0),
            'arrival_time' => Carbon::now()->addDays(3)->setHour(10, 0),
            'vip_price' => 150.00,
            'regular_price' => 80.00,
            'status' => 'scheduled',
        ]);
    }

    public function test_home_page_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_schedule_search_page_loads(): void
    {
        $response = $this->get('/schedules');
        $response->assertStatus(200);
        $response->assertSee('Auralis 8');
    }

    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_logout(): void
    {
        $response = $this->actingAs($this->passenger)
            ->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_create_booking(): void
    {
        $this->actingAs($this->passenger);

        $response = $this->get('/booking/'.$this->schedule->id.'?passenger_count=1');
        $response->assertStatus(200);
        $response->assertSee('Auralis 8');
    }

    public function test_schedule_model_calculates_h6_correctly(): void
    {
        $this->assertFalse($this->schedule->isH6Passed);

        $scheduleClose = Schedule::create([
            'vessel_id' => $this->schedule->vessel_id,
            'route_id' => $this->schedule->route_id,
            'departure_time' => Carbon::now()->addHours(2),
            'arrival_time' => Carbon::now()->addHours(4),
            'vip_price' => 150.00,
            'regular_price' => 80.00,
            'status' => 'scheduled',
        ]);

        $this->assertTrue($scheduleClose->isH6Passed);
    }

    public function test_schedule_model_calculates_boarding_closed(): void
    {
        $this->assertFalse($this->schedule->isBoardingClosed);

        $scheduleClose = Schedule::create([
            'vessel_id' => $this->schedule->vessel_id,
            'route_id' => $this->schedule->route_id,
            'departure_time' => Carbon::now()->addMinutes(15),
            'arrival_time' => Carbon::now()->addMinutes(135),
            'vip_price' => 150.00,
            'regular_price' => 80.00,
            'status' => 'scheduled',
        ]);

        $this->assertTrue($scheduleClose->isBoardingClosed);
    }

    public function test_guest_redirected_to_login_for_booking(): void
    {
        $response = $this->post('/booking', [
            'schedule_id' => $this->schedule->id,
            'passengers' => [],
        ]);

        $response->assertRedirect('/login');
    }

    public function test_boarding_scanner_requires_auth(): void
    {
        $response = $this->get('/boarding/scanner');
        $response->assertRedirect('/login');
    }

    public function test_boarding_scanner_requires_role(): void
    {
        $response = $this->actingAs($this->passenger)
            ->get('/boarding/scanner');

        $response->assertStatus(403);
    }
}
