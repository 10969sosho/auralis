<?php

namespace Database\Seeders;

use App\Models\AgeCategory;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Payment;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vessel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. DELETE ALL TRANSACTIONAL DATA ─────────────────────────
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('boarding_logs')->delete();
        DB::table('tickets')->delete();
        DB::table('booking_passengers')->delete();
        DB::table('payments')->delete();
        DB::table('refunds')->delete();
        DB::table('bookings')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('All transactional data cleared.');

        // Clean up old test users
        User::where('email', 'like', 'testuser%@example.com')->delete();

        // ─── 2. ENSURE MASTER DATA EXISTS ─────────────────────────────
        // Vessel
        $vessel = Vessel::first();
        if (!$vessel) {
            $vessel = Vessel::create([
                'name' => 'KM Test',
                'capacity' => 100,
                'vip_capacity' => 30,
                'regular_capacity' => 70,
                'free_baggage' => 10,
                'status' => 'active',
            ]);
        }

        // Route
        $route = Route::first();
        if (!$route) {
            $route = Route::create([
                'origin_port' => 'Batu Pahat',
                'destination_port' => 'Muara',
                'estimated_duration' => 90,
                'active' => true,
            ]);
        }

        // Schedule (tomorrow)
        $schedule = Schedule::first();
        if (!$schedule) {
            $schedule = Schedule::create([
                'vessel_id' => $vessel->id,
                'route_id' => $route->id,
                'departure_time' => Carbon::tomorrow()->setHour(8)->setMinute(0),
                'arrival_time' => Carbon::tomorrow()->setHour(10)->setMinute(0),
                'vip_price' => 150.00,
                'regular_price' => 80.00,
                'vip_remaining' => 30,
                'regular_remaining' => 70,
                'status' => 'scheduled',
            ]);
        }

        // Age categories
        if (AgeCategory::count() === 0) {
            AgeCategory::create(['name' => 'Adult', 'min_age' => 12, 'max_age' => 120, 'sort_order' => 1]);
            AgeCategory::create(['name' => 'Child', 'min_age' => 2, 'max_age' => 11, 'sort_order' => 2]);
            AgeCategory::create(['name' => 'Infant', 'min_age' => 0, 'max_age' => 1, 'sort_order' => 3]);
        }

        // ─── 3. CREATE 10 TEST USERS ──────────────────────────────────
        $users = [];
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => "Test User {$i}",
                'email' => "testuser{$i}@example.com",
                'password' => Hash::make('password'),
                'phone' => '0812345678' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'nationality' => 'Malaysian',
                'passport_number' => 'A' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'birth_date' => '1990-01-15',
                'gender' => $i % 2 === 0 ? 'female' : 'male',
                'is_active' => true,
            ]);
            if (!\Spatie\Permission\Models\Role::where('name', 'customer')->exists()) {
                \Spatie\Permission\Models\Role::create(['name' => 'customer', 'guard_name' => 'web']);
            }
            $user->assignRole('customer');
            $users[] = $user;
        }

        // ─── 4. CREATE 10 BOOKINGS WITH VARIOUS STATUSES ─────────────
        $statusConfigs = [
            // user_idx, booking_status,   payment_status,  amount,  has_tickets, tickets_used, has_payment, has_proof
            [0, 'paid',             'paid',              200.00, true,  false, true,  true],
            [1, 'paid',             'paid',              250.00, true,  false, true,  true],
            [2, 'used',             'completed',         180.00, true,  true,  true,  true],  // boarded
            [3, 'used',             'completed',         300.00, true,  true,  true,  true],  // boarded
            [4, 'pending_payment',  'pending',           150.00, false, false, false, false],
            [5, 'awaiting_approval','awaiting_approval', 220.00, false, false, true,  true],  // uploaded proof, waiting ACC
            [6, 'awaiting_approval','awaiting_approval', 175.00, false, false, true,  true],  // uploaded proof, waiting ACC
            [7, 'cancelled',        'expired',           0,      false, false, false, false],
            [8, 'refunded',         'paid',              190.00, true,  false, true,  true],
            [9, 'expired',          'expired',           0,      false, false, false, false],
        ];

        $ageCategory = AgeCategory::first();

        foreach ($statusConfigs as $idx => $config) {
            [$userIdx, $bookingStatus, $paymentStatus, $amount, $hasTickets, $ticketsUsed, $hasPayment, $hasProof] = $config;

            $user = $users[$userIdx];

            $booking = Booking::create([
                'user_id' => $user->id,
                'schedule_id' => $schedule->id,
                'booking_code' => 'TST-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                'total_passengers' => $hasTickets || in_array($bookingStatus, ['paid', 'used']) ? 2 : 0,
                'total_amount' => $amount,
                'discount_amount' => 0,
                'booking_status' => $bookingStatus,
                'payment_status' => $paymentStatus === 'completed' ? 'paid' : $paymentStatus,
                'expires_at' => Carbon::now()->addHours(2),
                'paid_at' => in_array($bookingStatus, ['paid', 'used']) ? Carbon::now()->subHours(3) : null,
            ]);

            // Create passengers
            if ($hasTickets || in_array($bookingStatus, ['paid', 'used'])) {
                $passenger1 = BookingPassenger::create([
                    'booking_id' => $booking->id,
                    'full_name' => $user->name . ' (Adult)',
                    'gender' => $user->gender,
                    'birth_date' => '1990-06-15',
                    'nationality' => 'Malaysian',
                    'passport_number' => $user->passport_number,
                    'passenger_type' => 'Adult',
                    'ticket_class' => $idx % 2 === 0 ? 'vip' : 'regular',
                    'age_category_id' => $ageCategory?->id,
                ]);

                $passenger2 = BookingPassenger::create([
                    'booking_id' => $booking->id,
                    'full_name' => $user->name . ' (Child)',
                    'gender' => 'male',
                    'birth_date' => '2018-06-15',
                    'nationality' => 'Malaysian',
                    'passport_number' => 'C' . str_pad($idx + 1, 8, '0', STR_PAD_LEFT),
                    'passenger_type' => 'Child',
                    'ticket_class' => 'regular',
                    'age_category_id' => AgeCategory::skip(1)->first()?->id ?? $ageCategory?->id,
                ]);

                // Create tickets
                foreach ([$passenger1, $passenger2] as $pIdx => $passenger) {
                    $ticketStatus = 'active';
                    $boardedAt = null;
                    if ($ticketsUsed && $pIdx === 0) {
                        $ticketStatus = 'used';
                        $boardedAt = Carbon::now()->subHour();
                    }

                    $ticket = Ticket::create([
                        'booking_id' => $booking->id,
                        'booking_passenger_id' => $passenger->id,
                        'ticket_class' => $passenger->ticket_class,
                        'qr_token' => hash('sha256', uniqid('qr_', true)),
                        'ticket_number' => Ticket::generateTicketNumber(),
                        'ticket_status' => $ticketStatus,
                        'boarded_at' => $boardedAt,
                        'expiry_date' => $schedule->departure_time->startOfDay(),
                    ]);
                }
            }

            // Create payment
            if ($hasPayment) {
                $payment = Payment::create([
                    'booking_id' => $booking->id,
                    'payment_method' => 'transfer',
                    'transaction_id' => 'TXN-' . str_pad($idx + 1, 6, '0', STR_PAD_LEFT),
                    'proof_of_transfer' => $hasProof ? 'settings/qr/payment.jpg' : null,
                    'amount' => $amount,
                    'payment_status' => $paymentStatus,
                    'paid_at' => in_array($paymentStatus, ['paid', 'completed']) ? Carbon::now()->subHours(2) : null,
                    'approved_by' => in_array($paymentStatus, ['paid', 'completed']) ? 1 : null,
                    'approved_at' => in_array($paymentStatus, ['paid', 'completed']) ? Carbon::now()->subHours(2) : null,
                ]);
            }
        }

        $this->command->info('10 dummy bookings created with various statuses.');
        $this->command->info('');
        $this->command->info('=== SUMMARY ===');
        $this->command->info('2x Paid (lunas, belum boarding)');
        $this->command->info('2x Completed (lunas & sudah boarding)');
        $this->command->info('1x Pending Payment (belum bayar)');
        $this->command->info('2x Awaiting Approval (upload bukti, menunggu ACC)');
        $this->command->info('1x Cancelled');
        $this->command->info('1x Refunded');
        $this->command->info('1x Expired');
        $this->command->info('');
        $this->command->info('Schedule used: ' . $schedule->route->origin_port . ' → ' . $schedule->route->destination_port);
        $this->command->info('Capacity: VIP ' . $vessel->vip_capacity . ', Regular ' . $vessel->regular_capacity);
    }
}
