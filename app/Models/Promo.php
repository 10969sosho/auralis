<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promo extends Model
{
    protected $fillable = [
        'name', 'code', 'type', 'value', 'start_date', 'end_date',
        'usage_quota', 'used_count', 'route_id', 'ticket_class',
        'is_active', 'auto_apply', 'min_passengers', 'max_passengers',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'auto_apply' => 'boolean',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function isApplicableToSchedule(Schedule $schedule, int $passengerCount, string $ticketClass): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if (now()->lt($this->start_date) || now()->gt($this->end_date)) {
            return false;
        }

        if ($this->used_count >= $this->usage_quota) {
            return false;
        }

        if ($this->route_id && $this->route_id !== $schedule->route_id) {
            return false;
        }

        if ($this->ticket_class && $this->ticket_class !== 'all' && $this->ticket_class !== $ticketClass) {
            return false;
        }

        if ($this->min_passengers && $passengerCount < $this->min_passengers) {
            return false;
        }

        if ($this->max_passengers && $passengerCount > $this->max_passengers) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'percentage') {
            return round($amount * ($this->value / 100), 2);
        }

        return min((float) $this->value, $amount);
    }
}
