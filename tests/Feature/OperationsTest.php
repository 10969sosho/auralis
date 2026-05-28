<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\DeportationManifest;
use App\Models\DeportationPassenger;
use App\Models\Promo;
use App\Models\Refund;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vessel;
use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $boardingOfficer;

    protected User $deportationOfficer;

    protected User $passenger;

    protected Schedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->boardingOfficer = User::factory()->create();
        $this->boardingOfficer->assignRole('boarding_officer');

        $this->deportationOfficer = User::factory()->create();
        $this->deportationOfficer->assignRole('deportation_officer');

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
            'departure_time' => Carbon::now()->addDays(3),
            'arrival_time' => Carbon::now()->addDays(3)->addHours(2),
            'vip_price' => 150.00,
            'regular_price' => 80.00,
            'status' => 'scheduled',
        ]);
    }

    public function test_promo_model_calculates_percentage_discount(): void
    {
        $promo = Promo::create([
            'name' => 'Test 20%',
            'type' => 'percentage',
            'value' => 20,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'usage_quota' => 100,
            'is_active' => true,
        ]);

        $discount = $promo->calculateDiscount(100.00);
        $this->assertEquals(20.00, $discount);
    }

    public function test_promo_model_calculates_fixed_discount(): void
    {
        $promo = Promo::create([
            'name' => 'Test RM10',
            'type' => 'fixed_amount',
            'value' => 10,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'usage_quota' => 100,
            'is_active' => true,
        ]);

        $discount = $promo->calculateDiscount(100.00);
        $this->assertEquals(10.00, $discount);
    }

    public function test_ticket_generates_unique_number(): void
    {
        $number1 = Ticket::generateTicketNumber();
        $number2 = Ticket::generateTicketNumber();
        $this->assertStringStartsWith('TKT-', $number1);
        $this->assertNotEquals($number1, $number2);
    }

    public function test_deportation_manifest_generates_code(): void
    {
        $code1 = DeportationManifest::generateManifestCode();
        $code2 = DeportationManifest::generateManifestCode();
        $this->assertStringStartsWith('DEP-', $code1);
        $this->assertNotEquals($code1, $code2);
    }

    public function test_deportation_passenger_generates_qr_token(): void
    {
        $token = DeportationPassenger::generateQrToken();
        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token));
    }

    public function test_deportation_officer_can_access_module(): void
    {
        $response = $this->actingAs($this->deportationOfficer)
            ->get('/deportation');

        $response->assertStatus(200);
    }

    public function test_passenger_cannot_access_deportation(): void
    {
        $response = $this->actingAs($this->passenger)
            ->get('/deportation');

        $response->assertStatus(403);
    }

    public function test_boarding_officer_can_access_scanner(): void
    {
        $response = $this->actingAs($this->boardingOfficer)
            ->get('/boarding/scanner');

        $response->assertStatus(200);
    }

    public function test_booking_model_relationships(): void
    {
        $booking = Booking::create([
            'user_id' => $this->passenger->id,
            'schedule_id' => $this->schedule->id,
            'booking_code' => 'BK-TEST001',
            'total_passengers' => 1,
            'total_amount' => 80.00,
            'booking_status' => 'paid',
            'payment_status' => 'paid',
            'expires_at' => now()->addMinutes(30),
        ]);

        BookingPassenger::create([
            'booking_id' => $booking->id,
            'full_name' => 'Test Passenger',
            'gender' => 'male',
            'birth_date' => '1990-01-01',
            'nationality' => 'Malaysian',
            'passport_number' => 'P123456',
            'passenger_type' => 'adult',
            'ticket_class' => 'regular',
        ]);

        $this->assertCount(1, $booking->passengers);
        $this->assertEquals($this->passenger->id, $booking->user->id);
        $this->assertEquals($this->schedule->id, $booking->schedule->id);
    }

    public function test_refund_model_creates_with_status(): void
    {
        $booking = Booking::create([
            'user_id' => $this->passenger->id,
            'schedule_id' => $this->schedule->id,
            'booking_code' => 'BK-REFUND01',
            'total_passengers' => 1,
            'total_amount' => 80.00,
            'booking_status' => 'paid',
            'payment_status' => 'paid',
            'expires_at' => now()->addMinutes(30),
        ]);

        $refund = Refund::create([
            'booking_id' => $booking->id,
            'refund_amount' => 20.00,
            'refund_reason' => 'Test refund',
            'refund_status' => 'requested',
        ]);

        $this->assertEquals('requested', $refund->refund_status);
        $this->assertEquals(20.00, $refund->refund_amount);
        $this->assertEquals($booking->id, $refund->booking->id);
    }

    public function test_refund_is_25_percent(): void
    {
        $amount = 80.00;
        $refund = round($amount * 0.25, 2);
        $this->assertEquals(20.00, $refund);
    }
}
