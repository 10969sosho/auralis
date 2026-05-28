<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $passengerPermissions = [
            'search schedule', 'booking ticket', 'upload document',
            'make payment', 'download e-ticket', 'request refund',
            'view booking history',
        ];

        $adminPermissions = [
            'manage vessels', 'manage routes', 'manage schedules',
            'manage pricing', 'manage promo', 'manage booking',
            'approve refund', 'reject refund', 'view reports',
            'broadcast notification', 'manage passenger data',
            'export manifest', 'manage users', 'view audit logs',
            'access admin panel',
        ];

        $boardingOfficerPermissions = [
            'scan qr ticket', 'validate passenger', 'view ticket status',
            'boarding check-in', 'view manifest', 'reject invalid boarding',
        ];

        $ticketCounterPermissions = [
            'create manual booking', 'receive cash payment',
            'print ticket', 'search booking', 'create passenger data',
        ];

        $deportationOfficerPermissions = [
            'create deportation manifest', 'add deportation passenger',
            'validate deportation identity', 'boarding without payment',
            'generate deportation report',
        ];

        $allPermissions = array_merge(
            $passengerPermissions,
            $adminPermissions,
            $boardingOfficerPermissions,
            $ticketCounterPermissions,
            $deportationOfficerPermissions,
        );

        foreach (array_unique($allPermissions) as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $passengerRole = Role::firstOrCreate(['name' => 'passenger', 'guard_name' => 'web']);
        $passengerRole->syncPermissions($passengerPermissions);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($adminPermissions);

        $boardingOfficerRole = Role::firstOrCreate(['name' => 'boarding_officer', 'guard_name' => 'web']);
        $boardingOfficerRole->syncPermissions($boardingOfficerPermissions);

        $ticketCounterRole = Role::firstOrCreate(['name' => 'ticket_counter_officer', 'guard_name' => 'web']);
        $ticketCounterRole->syncPermissions($ticketCounterPermissions);

        $deportationOfficerRole = Role::firstOrCreate(['name' => 'deportation_officer', 'guard_name' => 'web']);
        $deportationOfficerRole->syncPermissions($deportationOfficerPermissions);
    }
}
