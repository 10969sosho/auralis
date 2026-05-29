<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BookingPassenger extends Model
{
    protected $fillable = [
        'booking_id', 'full_name', 'gender', 'birth_date', 'nationality',
        'passport_number', 'phone_number', 'passenger_type', 'ticket_class',
        'age_category_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function ageCategory(): BelongsTo
    {
        return $this->belongsTo(AgeCategory::class);
    }

    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
