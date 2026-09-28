<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\OpenReturnTicket;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vessel;
use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class OpenReturnTest extends TestCase
{
    use RefreshDatabase;

    protected User $passenger;

    protected Vessel $vessel;

    protected Route $route;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $this->seed(RoleAndPermissionSeeder::class);

        $this->passenger = User::factory()->create();
        $this->passenger->assignRole('passenger');

        $this->vessel = Vessel::create([
            'name' => 'Auralis 8',
            'capacity' => 280,
            'vip_capacity' => 40,
            'regular_capacity' => 240,
            'free_baggage' => 10,
            'status' => 'active',
        ]);

        $this->route = Route::create([
            'origin_port' => 'Bongao, Tawi-Tawi (Philippines)',
            'destination_port' => 'Lahad Datu, Sabah (Malaysia)',
            'estimated_duration' => 120,
            'active' => true,
        ]);
    }

    private function makeSchedule(string $day): Schedule
    {
        return Schedule::create([
            'vessel_id' => $this->vessel->id,
            'route_id' => $this->route->id,
            'departure_time' => Carbon::parse($day)->setHour(8, 0),
            'arrival_time' => Carbon::parse($day)->setHour(10, 0),
            'vip_price' => 150.00,
            'regular_price' => 80.00,
            'status' => 'scheduled',
        ]);
    }

    private function bookReturn(string $returnDate): Booking
    {
        $this->actingAs($this->passenger)->post('/booking', [
            'return_date' => $returnDate,
            'passengers' => [[
                'full_name' => 'Return Passenger',
                'gender' => 'male',
                'birth_date' => '1990-01-01',
                'nationality' => 'Malaysian',
                'passport_number' => 'R000001',
                'ticket_class' => 'regular',
                'passport_file' => UploadedFile::fake()->create('passport.pdf', 10, 'application/pdf'),
            ]],
        ])->assertRedirect();

        return Booking::latest('id')->firstOrFail();
    }

    public function test_open_return_is_assigned_when_a_sailing_exists(): void
    {
        $returnDate = Carbon::now()->addDays(5)->toDateString();
        $this->makeSchedule($returnDate);

        $booking = $this->bookReturn($returnDate);

        $this->assertNotNull($booking->openReturnTicket);
        $this->assertSame('assigned', $booking->openReturnTicket->status);
        $this->assertSame($returnDate, $booking->openReturnTicket->return_date->toDateString());
    }

    public function test_open_return_stays_open_without_a_sailing(): void
    {
        $booking = $this->bookReturn(Carbon::now()->addDays(7)->toDateString());

        $this->assertSame('open', $booking->openReturnTicket->status);
        $this->assertNull($booking->openReturnTicket->schedule());
    }

    public function test_schedule_creation_assigns_pending_open_returns(): void
    {
        $returnDate = Carbon::now()->addDays(6)->toDateString();
        $booking = $this->bookReturn($returnDate);

        $this->assertSame('open', $booking->openReturnTicket->status);

        $this->makeSchedule($returnDate);

        $this->assertSame('assigned', $booking->fresh()->openReturnTicket->status);
    }

    public function test_capacity_full_leaves_open_returns_unassigned(): void
    {
        \DB::table('master_ships')->update(['capacity' => 0]);

        $returnDate = Carbon::now()->addDays(8)->toDateString();
        $this->makeSchedule($returnDate);

        $booking = $this->bookReturn($returnDate);

        $this->assertSame('open', $booking->openReturnTicket->status);
        $this->assertSame(0, OpenReturnTicket::where('status', 'assigned')->count());
    }
}
