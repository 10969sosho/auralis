<?php

namespace App\Filament\Widgets\Concerns;

use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Builder;

/**
 * Shared filter state passed down from the Reports page filter bar.
 * ponytail: schedule_id/status filter the schedule scope, date_from/date_to
 * filter bookings by created_at and schedules by departure_time.
 */
trait AppliesReportFilters
{
    public array $filters = [];

    protected function reportSchedules(): Builder
    {
        return Schedule::query()
            ->when($this->filters['schedule_id'] ?? null, fn ($query, $id) => $query->whereKey($id))
            ->when($this->filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($this->filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('departure_time', '>=', $date))
            ->when($this->filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('departure_time', '<=', $date));
    }

    protected function reportBookings(): Builder
    {
        return Booking::query()
            ->when($this->filters['schedule_id'] ?? null, fn ($query, $id) => $query->where('schedule_id', $id))
            ->when($this->filters['status'] ?? null, fn ($query, $status) => $query->whereIn(
                'schedule_id',
                Schedule::query()->where('status', $status)->select('id')
            ))
            ->when($this->filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($this->filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
    }
}
