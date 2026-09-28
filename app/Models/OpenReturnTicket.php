<?php

namespace App\Models;

use App\Services\ShipCapacityService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class OpenReturnTicket extends Model
{
    protected $fillable = [
        'booking_id', 'return_date', 'status',
    ];

    protected $casts = [
        'return_date' => 'date',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Schedules have no id on this table — resolve the schedule the return leg sails on.
     */
    public function schedule(): ?Schedule
    {
        if (! $this->return_date) {
            return null;
        }

        return Schedule::whereDate('departure_time', $this->return_date->toDateString())
            ->where('status', 'scheduled')
            ->orderBy('departure_time')
            ->first();
    }

    /**
     * Bind this open return to the sailing on its return date, if one exists
     * and master ship capacity still allows it. Safe to call repeatedly.
     */
    public function assignIfPossible(): bool
    {
        if ($this->status !== 'open' || ! $this->return_date || ! $this->schedule()) {
            return false;
        }

        if (! app(ShipCapacityService::class)->canAllocate($this->return_date->toDateString(), 1)) {
            Log::warning('Open return ticket left unassigned: ship capacity exceeded', [
                'open_return_ticket_id' => $this->id,
                'return_date' => $this->return_date->toDateString(),
            ]);

            return false;
        }

        return $this->update(['status' => 'assigned']);
    }
}
