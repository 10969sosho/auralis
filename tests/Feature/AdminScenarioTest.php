<?php

namespace Tests\Feature;

use App\Filament\Resources\Schedules\Pages\CreateSchedule;
use App\Models\AuditLog;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vessel;
use Carbon\Carbon;
use Database\Seeders\AgeCategorySeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class AdminScenarioTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Vessel $vessel;

    protected Route $route;

    protected Schedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AgeCategorySeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

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

        $this->schedule = Schedule::create([
            'vessel_id' => $this->vessel->id,
            'route_id' => $this->route->id,
            'departure_time' => Carbon::now()->addDays(3)->setHour(8)->setMinute(0)->setSecond(0),
            'arrival_time' => Carbon::now()->addDays(3)->setHour(10)->setMinute(0)->setSecond(0),
            'vip_price' => 150.00,
            'regular_price' => 80.00,
            'vip_remaining' => 40,
            'regular_remaining' => 240,
            'status' => 'scheduled',
            'is_active' => true,
        ]);
    }

    public function test_admin_login_page_loads(): void
    {
        $this->get('/admin/login')
            ->assertStatus(200);
    }

    public function test_admin_can_login_to_filament(): void
    {
        $this->post('/login', [
            'email' => $this->admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_admin_login_is_audited(): void
    {
        $this->post('/login', [
            'email' => $this->admin->email,
            'password' => 'password',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($this->admin);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'login.post',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_non_admin_cannot_access_filament(): void
    {
        $passenger = User::factory()->create();
        $passenger->assignRole('passenger');

        $this->post('/login', [
            'email' => $passenger->email,
            'password' => 'password',
        ]);

        $this->get('/admin')->assertForbidden();
    }

    public function test_admin_creates_schedule_in_filament(): void
    {
        $departure = Carbon::now()->addDays(4)->setHour(9)->setMinute(0)->setSecond(0)->setMicrosecond(0);
        $arrival = Carbon::now()->addDays(4)->setHour(11)->setMinute(0)->setSecond(0)->setMicrosecond(0);

        $this->actingAs($this->admin);

        Livewire::test(CreateSchedule::class)
            ->fillForm([
                'is_active' => true,
                'status' => 'scheduled',
                'vessel_id' => $this->vessel->id,
                'route_id' => $this->route->id,
                'departure_time' => $departure->format('Y-m-d H:i:s'),
                'arrival_time' => $arrival->format('Y-m-d H:i:s'),
                'vip_price' => 200,
                'regular_price' => 120,
            ])
            ->call('create');

        $this->assertDatabaseHas('schedules', [
            'vessel_id' => $this->vessel->id,
            'route_id' => $this->route->id,
            'vip_price' => 200,
            'regular_price' => 120,
            'status' => 'scheduled',
            'is_active' => true,
        ]);

        $created = Schedule::where('vip_price', 200)->firstOrFail();
        $this->assertTrue($created->departure_time->equalTo($departure));
    }

    public function test_admin_views_reports_with_filters(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/report-list');
        $response->assertStatus(200);
        $response->assertSee('Auralis 8');

        $filtered = $this->get('/admin/report-list?status=scheduled&date_from='.$this->schedule->departure_time->copy()->subDay()->toDateString().'&date_to='.$this->schedule->departure_time->copy()->addDay()->toDateString());
        $filtered->assertStatus(200);
        $filtered->assertSee('Auralis 8');

        $empty = $this->get('/admin/report-list?status=cancelled');
        $empty->assertStatus(200);
        $empty->assertDontSee('Bongao, Tawi-Tawi (Philippines)');

        $this->get('/admin/reports')->assertStatus(200);
        $this->get('/admin/exports/csv')->assertStatus(200);
    }

    public function test_admin_can_read_audit_log(): void
    {
        $passenger = User::factory()->create();
        $passenger->assignRole('passenger');

        $this->actingAs($passenger)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post('/booking', [
                'schedule_id' => $this->schedule->id,
                'passengers' => [[
                    'full_name' => 'Audited Passenger',
                    'gender' => 'male',
                    'birth_date' => '1990-01-01',
                    'nationality' => 'Malaysian',
                    'passport_number' => 'A00001',
                    'ticket_class' => 'regular',
                    'passport_file' => UploadedFile::fake()->create('passport.pdf', 5, 'application/pdf'),
                ]],
            ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'booking.store',
            'user_id' => $passenger->id,
        ]);

        $log = AuditLog::where('action', 'booking.store')->firstOrFail();
        $this->assertSame($passenger->id, $log->user_id);
        $this->assertNotNull($log->ip_address);

        $this->actingAs($this->admin)
            ->get('/admin/audit-logs')
            ->assertStatus(200);
    }
}
