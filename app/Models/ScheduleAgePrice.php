<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleAgePrice extends Model
{
    protected $fillable = [
        'schedule_id', 'age_category_id', 'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function ageCategory(): BelongsTo
    {
        return $this->belongsTo(AgeCategory::class);
    }
}
