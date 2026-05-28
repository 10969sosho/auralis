<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $fillable = [
        'vessel_id', 'route_id', 'departure_time', 'arrival_time',
        'vip_price', 'regular_price', 'vip_remaining', 'regular_remaining', 'status',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'vip_price' => 'decimal:2',
        'regular_price' => 'decimal:2',
        'vip_remaining' => 'integer',
        'regular_remaining' => 'integer',
    ];

    public function vessel(): BelongsTo
    {
        return $this->belongsTo(Vessel::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function deportationManifests(): HasMany
    {
        return $this->hasMany(DeportationManifest::class);
    }

    public function getTotalBookedAttribute(): int
    {
        return $this->bookings()
            ->whereIn('booking_status', ['paid', 'used', 'refund_requested'])
            ->with('passengers')
            ->get()
            ->sum(function ($booking) {
                return $booking->passengers->count();
            });
    }

    public function getVipBookedAttribute(): int
    {
        return $this->bookings()
            ->whereIn('booking_status', ['paid', 'used', 'refund_requested'])
            ->with('passengers')
            ->get()
            ->sum(function ($booking) {
                return $booking->passengers->where('ticket_class', 'vip')->count();
            });
    }

    public function getRegularBookedAttribute(): int
    {
        return $this->bookings()
            ->whereIn('booking_status', ['paid', 'used', 'refund_requested'])
            ->with('passengers')
            ->get()
            ->sum(function ($booking) {
                return $booking->passengers->where('ticket_class', 'regular')->count();
            });
    }

    public function getIsFullyBookedAttribute(): bool
    {
        $vipCapacity = $this->vessel?->vip_capacity ?? 0;
        $regularCapacity = $this->vessel?->regular_capacity ?? 0;

        return $this->vipBooked >= $vipCapacity && $this->regularBooked >= $regularCapacity;
    }

    public function getIsH6PassedAttribute(): bool
    {
        return $this->departure_time?->copy()->subHours(6)->isPast() ?? true;
    }

    public function getIsBoardingClosedAttribute(): bool
    {
        return $this->departure_time?->copy()->subMinutes(30)->isPast() ?? true;
    }
}
