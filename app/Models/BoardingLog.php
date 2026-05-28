<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoardingLog extends Model
{
    protected $fillable = [
        'ticket_id', 'validated_by', 'validation_result',
        'device_info', 'scan_method', 'validated_at',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public static function log(Ticket $ticket, string $result, ?User $officer = null, ?string $deviceInfo = null, string $scanMethod = 'qr'): self
    {
        return self::create([
            'ticket_id' => $ticket->id,
            'validated_by' => $officer?->id,
            'validation_result' => $result,
            'device_info' => $deviceInfo,
            'scan_method' => $scanMethod,
            'validated_at' => now(),
        ]);
    }
}
