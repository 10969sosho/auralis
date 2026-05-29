<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;

class RevenueChartWidget extends LineChartWidget
{
    protected int | string | array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return 'Revenue Trend';
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
        $days = match ($this->filter) {
            '30d' => 30,
            default => 7,
        };

        $data = collect(range($days - 1, 0))->map(function ($i) {
            $date = Carbon::today()->subDays($i);

            return [
                'label' => $date->format('d M'),
                'revenue' => (float) Booking::whereIn('booking_status', ['paid', 'used'])
                    ->whereDate('created_at', $date)
                    ->sum('total_amount'),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (MYR)',
                    'data' => $data->pluck('revenue')->toArray(),
                ],
            ],
            'labels' => $data->pluck('label')->toArray(),
        ];
    }
}
