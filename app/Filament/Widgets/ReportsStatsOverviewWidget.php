<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\AppliesReportFilters;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReportsStatsOverviewWidget extends StatsOverviewWidget
{
    use AppliesReportFilters;

    public function getColumns(): int|array|null
    {
        return 4;
    }

    protected function getStats(): array
    {
        $allSchedules = $this->reportSchedules()
            ->when(empty($this->filters['status']), fn ($query) => $query->where('status', 'scheduled'))
            ->where('departure_time', '>', now())
            ->count();

        $todayBookings = $this->reportBookings()->whereDate('created_at', today())->count();
        $totalRevenueToday = $this->reportBookings()->whereDate('created_at', today())
            ->where('booking_status', 'paid')
            ->sum('total_amount');

        $totalTickets = $this->reportBookings()->sum('total_passengers');
        $totalPaidPassengers = $this->reportBookings()->whereIn('booking_status', ['paid', 'used'])
            ->sum('total_passengers');

        $totalPending = $this->reportBookings()->where('booking_status', 'pending_payment')->count();
        $totalRefunded = $this->reportBookings()->where('booking_status', 'refunded')->count();
        $totalCancelled = $this->reportBookings()->whereIn('booking_status', ['cancelled', 'expired'])->count();
        $totalBoarded = Ticket::whereIn('booking_id', $this->reportBookings()->select('id'))
            ->where('ticket_status', 'used')->count();

        $totalBookings = $this->reportBookings()->count();

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
                $this->reportBookings()->whereIn('booking_status', ['paid', 'used'])->sum('total_amount'), 0
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
