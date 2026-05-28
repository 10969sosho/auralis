<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vessel extends Model
{
    protected $fillable = [
        'name', 'capacity', 'vip_capacity', 'regular_capacity',
        'free_baggage', 'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'vip_capacity' => 'integer',
        'regular_capacity' => 'integer',
        'free_baggage' => 'integer',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
