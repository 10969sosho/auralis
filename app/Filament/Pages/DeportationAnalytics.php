<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\Ticket;
use App\Models\User;
use Filament\Pages\Page;

class DeportationAnalytics extends Page
{
    public function getView(): string
    {
        return 'filament.pages.deportation-analytics';
    }

    public function getTitle(): string
    {
        return 'Deportation Analytics';
    }

    public static function getNavigationLabel(): string
    {
        return 'Deportation';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Analytics';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-user-group';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    // ── Stats ──

    public function getTotalUsers(): int
    {
        return User::where('account_type', 'deportation')->count();
    }

    public function getTotalBookings(): int
    {
        return Booking::where('is_deportation', true)->count();
    }

    public function getTotalPaid(): int
    {
        return Booking::where('is_deportation', true)
            ->whereIn('payment_status', ['paid', 'approved'])
            ->count();
    }

    public function getTotalBoarded(): int
    {
        return Ticket::where('is_deportation', true)
            ->where('ticket_status', 'used')
            ->count();
    }

    // ── Lists ──

    public function getRegisteredUsers(): array
    {
        return User::where('account_type', 'deportation')
            ->withCount(['bookings' => fn ($q) => $q->where('is_deportation', true)])
            ->latest()
            ->take(20)
            ->get()
            ->toArray();
    }

    public function getBookingPayments(): array
    {
        return Booking::where('is_deportation', true)
            ->with(['user', 'payment', 'passengers'])
            ->latest()
            ->take(20)
            ->get()
            ->toArray();
    }

    public function getBoardedPassengers(): array
    {
        return Ticket::where('is_deportation', true)
            ->where('ticket_status', 'used')
            ->with(['passenger', 'booking.user', 'booking'])
            ->latest('boarded_at')
            ->take(20)
            ->get()
            ->toArray();
    }
}
