<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'guest_email', 'guest_token', 'schedule_id', 'booking_code', 'total_passengers',
        'total_amount', 'discount_amount', 'promo_id', 'booking_status',
        'payment_status', 'locked_at', 'expires_at', 'paid_at',
        'is_deportation', 'shelter_point', 'shelter_fee',
        'route_text', 'vessel_text', 'route_vip_price', 'route_regular_price',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shelter_fee' => 'decimal:2',
        'route_vip_price' => 'decimal:2',
        'route_regular_price' => 'decimal:2',
        'locked_at' => 'datetime',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'is_deportation' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(BookingPassenger::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }

    /**
     * Get display route text, falling back to schedule route if available.
     */
    public function getRouteDisplayAttribute(): string
    {
        if ($this->route_text) {
            return $this->route_text;
        }

        if ($this->schedule && $this->schedule->route) {
            return $this->schedule->route->origin_port . ' → ' . $this->schedule->route->destination_port;
        }

        return '—';
    }

    /**
     * Get display vessel name, falling back to schedule vessel if available.
     */
    public function getVesselDisplayAttribute(): string
    {
        if ($this->vessel_text) {
            return $this->vessel_text;
        }

        return $this->schedule?->vessel?->name ?? '—';
    }
}
