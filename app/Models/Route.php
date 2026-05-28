<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    protected $fillable = [
        'origin_port', 'destination_port', 'estimated_duration', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function promos(): HasMany
    {
        return $this->hasMany(Promo::class);
    }
}
