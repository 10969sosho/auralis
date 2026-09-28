<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\AppliesReportFilters;
use App\Http\Controllers\AdminReportController;
use App\Models\Schedule;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ScheduleTableWidget extends TableWidget
{
    use AppliesReportFilters;

    protected int|string|array $columnSpan = ['default' => 1, 'xl' => 3];

    public function getTableHeading(): string|Htmlable|null
    {
        return 'Schedule Analytics';
    }

    public function getDescription(): ?string
    {
        return 'Detailed per-schedule performance metrics';
    }

    protected function getTableQuery(): Builder
    {
        return $this->reportSchedules()->with('vessel', 'route')
            ->orderBy('departure_time', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('vessel.name')
                ->label('Vessel')
                ->searchable()
                ->sortable(),
            TextColumn::make('route_label')
                ->label('Route')
                ->state(fn (Schedule $record) => $record->route->origin_port.' → '.$record->route->destination_port),
            TextColumn::make('departure_time')
                ->label('Departure')
                ->dateTime('d M Y, H:i')
                ->sortable()
                ->size('xs'),
            TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'scheduled' => 'info',
                    'departed' => 'success',
                    'cancelled' => 'danger',
                    'completed' => 'gray',
                    default => 'gray',
                }),
            TextColumn::make('capacity')
                ->label('Capacity')
                ->state(fn (Schedule $record) => ($record->vessel->vip_capacity + $record->vessel->regular_capacity))
                ->numeric(),
            TextColumn::make('booked')
                ->label('Booked')
                ->state(fn (Schedule $record) => ((int) $record->vipBooked + (int) $record->regularBooked))
                ->numeric(),
            TextColumn::make('paid')
                ->label('Paid')
                ->state(fn (Schedule $record) => $this->getMetric($record, 'total_payment_success'))
                ->numeric(),
            TextColumn::make('boarded')
                ->label('Boarded')
                ->state(fn (Schedule $record) => $this->getMetric($record, 'total_boarded'))
                ->numeric(),
            TextColumn::make('available')
                ->label('Available')
                ->state(fn (Schedule $record) => max(0, ($record->vessel->vip_capacity + $record->vessel->regular_capacity) - ((int) $record->vipBooked + (int) $record->regularBooked)))
                ->numeric()
                ->color('success'),
            TextColumn::make('revenue')
                ->label('Revenue')
                ->money('MYR')
                ->state(fn (Schedule $record) => $this->getMetric($record, 'total_revenue')),
            TextColumn::make('refund')
                ->label('Refund')
                ->state(fn (Schedule $record) => $this->getMetric($record, 'total_refund'))
                ->numeric()
                ->color('danger'),
            TextColumn::make('occupancy')
                ->label('Occupancy')
                ->state(fn (Schedule $record) => $this->getMetric($record, 'occupancy_percentage').'%'),
        ];
    }

    private function getMetric(Schedule $schedule, string $key): int|float
    {
        static $cache = [];
        $cacheKey = $schedule->id;

        if (! isset($cache[$cacheKey])) {
            $controller = new AdminReportController;
            $cache[$cacheKey] = $controller->getMetrics($schedule);
        }

        return $cache[$cacheKey][$key] ?? 0;
    }

    protected function getTableActions(): array
    {
        return [];
    }

    protected function getTableBulkActions(): array
    {
        return [];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50];
    }

    protected function getTablePollingInterval(): ?string
    {
        return '30s';
    }
}
