<?php

namespace App\Filament\Pages;

use App\Models\Schedule;
use Filament\Pages\Page;

class Reports extends Page
{
    public string $scheduleId = '';

    public string $status = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public function getView(): string
    {
        return 'filament.pages.reports';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-chart-bar';
    }

    public static function getNavigationLabel(): string
    {
        return 'Reports';
    }

    public function getTitle(): string
    {
        return 'Schedule Reports & Analytics';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Analytics';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public function getScheduleList(): array
    {
        return Schedule::with('vessel', 'route')
            ->orderBy('departure_time', 'desc')
            ->get()
            ->toArray();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}
