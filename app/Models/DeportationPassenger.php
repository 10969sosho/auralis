<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeportationPassenger extends Model
{
    protected $fillable = [
        'manifest_id', 'full_name', 'gender', 'nationality',
        'passport_number', 'qr_token', 'boarding_status', 'boarded_at',
    ];

    protected $casts = [
        'boarded_at' => 'datetime',
    ];

    public function manifest(): BelongsTo
    {
        return $this->belongsTo(DeportationManifest::class, 'manifest_id');
    }

    public static function generateQrToken(): string
    {
        return hash('sha256', uniqid('deport_', true).random_bytes(16));
    }
}
