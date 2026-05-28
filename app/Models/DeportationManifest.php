<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeportationManifest extends Model
{
    protected $fillable = [
        'schedule_id', 'officer_id', 'manifest_code',
        'total_passengers', 'notes',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(DeportationPassenger::class, 'manifest_id');
    }

    public static function generateManifestCode(): string
    {
        return 'DEP-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -4));
    }

    public function updateTotalPassengers(): void
    {
        $this->update(['total_passengers' => $this->passengers()->count()]);
    }
}
