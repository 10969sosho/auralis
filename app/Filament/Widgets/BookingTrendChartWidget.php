<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;

class BookingTrendChartWidget extends LineChartWidget
{
    protected int | string | array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return 'Booking Trend';
    }

    public function getDescription(): ?string
    {
        return 'Last 7 days';
    }

    public function getFilters(): ?array
    {
        return [
            '7d' => '7 Days',
            '30d' => '30 Days',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $days = ($this->filter === '30d') ? 30 : 7;

        $data = collect(range($days - 1, 0))->map(function ($i) {
            $date = Carbon::today()->subDays($i);

            return [
                'label' => $date->format('d M'),
                'total' => Booking::whereDate('created_at', $date)->count(),
                'paid' => Booking::whereDate('created_at', $date)
                    ->whereIn('booking_status', ['paid', 'used'])
                    ->count(),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Total Bookings',
                    'data' => $data->pluck('total')->toArray(),
                ],
                [
                    'label' => 'Paid',
                    'data' => $data->pluck('paid')->toArray(),
                ],
            ],
            'labels' => $data->pluck('label')->toArray(),
        ];
    }
}
