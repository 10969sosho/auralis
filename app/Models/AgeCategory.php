<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgeCategory extends Model
{
    protected $fillable = [
        'name', 'min_age', 'max_age', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'min_age' => 'integer',
        'max_age' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function schedulePrices(): HasMany
    {
        return $this->hasMany(ScheduleAgePrice::class);
    }

    public static function detectCategory(int $age): ?self
    {
        return self::where('is_active', true)
            ->where('min_age', '<=', $age)
            ->where('max_age', '>=', $age)
            ->orderBy('sort_order')
            ->first();
    }
}
