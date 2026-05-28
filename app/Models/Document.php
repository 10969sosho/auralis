<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'booking_passenger_id', 'type', 'file_path',
        'file_name', 'mime_type', 'file_size', 'uploaded_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'uploaded_at' => 'datetime',
    ];

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(BookingPassenger::class, 'booking_passenger_id');
    }
}
