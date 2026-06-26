<?php

namespace App\Filament\Widgets;

use App\Http\Controllers\AdminReportController;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class ReportsStatsOverviewWidget extends StatsOverviewWidget
{
    public function getColumns(): int | array | null
    {
        return 4;
    }

    protected function getStats(): array
    {
        $controller = new AdminReportController();

        $allSchedules = Schedule::where('status', 'scheduled')
            ->where('departure_time', '>', now())
            ->count();

        $todayBookings = Booking::whereDate('created_at', today())->count();
        $totalRevenueToday = Booking::whereDate('created_at', today())
            ->where('booking_status', 'paid')
            ->sum('total_amount');

        $totalTickets = Booking::sum('total_passengers');
        $totalPaidPassengers = Booking::whereIn('booking_status', ['paid', 'used'])
            ->sum('total_passengers');

        $totalPending = Booking::where('booking_status', 'pending_payment')->count();
        $totalRefunded = Booking::where('booking_status', 'refunded')->count();
        $totalCancelled = Booking::whereIn('booking_status', ['cancelled', 'expired'])->count();
        $totalBoarded = Ticket::where('ticket_status', 'used')->count();

        $totalBookings = Booking::count();

        return [
            Stat::make('Live Bookings Today', $todayBookings)
                ->description($totalRevenueToday > 0 ? '+ MYR '.number_format($totalRevenueToday, 0) : 'no revenue yet')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 5, 8, 12, 9, 15, $todayBookings])
                ->color('info'),

            Stat::make('Schedules Active', $allSchedules)
                ->description('Upcoming departures')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('gray'),

            Stat::make('Total Revenue', 'MYR '.number_format(
                Booking::whereIn('booking_status', ['paid', 'used'])->sum('total_amount'), 0
            ))
                ->description('Across all schedules')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([65, 78, 90, 85, 110, 95, 120])
                ->color('success'),

            Stat::make('Occupancy Rate', $totalTickets > 0
                ? round(($totalPaidPassengers / max($totalTickets, 1)) * 100, 1).'%'
                : '0%')
                ->description('Paid / Total passengers')
                ->descriptionIcon('heroicon-m-users')
                ->chart([45, 52, 48, 55, 60, 58, 65])
                ->color('warning'),

            Stat::make('Passengers', $totalTickets)
                ->description(number_format($totalPaidPassengers).' paid · '.number_format($totalPending).' pending')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Paid', $totalPaidPassengers)
                ->description(number_format($totalBookings).' total bookings')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Boarded', $totalBoarded)
                ->description(number_format($totalPaidPassengers).' paid passengers')
                ->descriptionIcon('heroicon-m-arrow-right-circle')
                ->color('info'),

            Stat::make('Pending', $totalPending)
                ->description(number_format($totalRefunded).' refunded · '.number_format($totalCancelled).' cancelled')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Refunds', $totalRefunded)
                ->description(number_format($totalCancelled).' cancelled transactions')
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->color('danger'),
        ];
    }
}
