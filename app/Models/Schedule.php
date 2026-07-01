<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $fillable = [
        'vessel_id', 'route_id', 'departure_time', 'arrival_time',
        'vip_price', 'regular_price', 'status', 'is_active',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'vip_price' => 'decimal:2',
        'regular_price' => 'decimal:2',
        'is_active' => 'boolean',
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

    public function agePrices(): HasMany
    {
        return $this->hasMany(ScheduleAgePrice::class);
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

    public function getAgeCategoryPrice(int $ageCategoryId): ?float
    {
        $price = $this->agePrices()->where('age_category_id', $ageCategoryId)->first();

        return $price ? (float) $price->price : null;
    }

    public function getPassengerPrice(int $age, string $ticketClass): float
    {
        $category = AgeCategory::detectCategory($age);

        if ($category) {
            $agePrice = $this->getAgeCategoryPrice($category->id);
            if ($agePrice !== null) {
                return $agePrice;
            }
        }

        return $ticketClass === 'vip' ? (float) $this->vip_price : (float) $this->regular_price;
    }

    public function getAgeCategoryName(int $age): string
    {
        $category = AgeCategory::detectCategory($age);

        return $category ? $category->name : ($age <= 2 ? 'Infant' : ($age <= 12 ? 'Child' : 'Adult'));
    }
}
