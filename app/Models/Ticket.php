<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'booking_id', 'booking_passenger_id', 'ticket_class',
        'qr_token', 'ticket_number', 'ticket_status', 'boarded_at', 'expiry_date',
        'is_deportation',
    ];

    protected $casts = [
        'boarded_at' => 'datetime',
        'expiry_date' => 'datetime',
        'is_deportation' => 'boolean',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(BookingPassenger::class, 'booking_passenger_id');
    }

    public function boardingLogs(): HasMany
    {
        return $this->hasMany(BoardingLog::class);
    }

    public static function generateTicketNumber(): string
    {
        return 'TKT-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -6));
    }

    public static function generateQrToken(): string
    {
        return hash('sha256', uniqid('ticket_', true).random_bytes(16));
    }
}
