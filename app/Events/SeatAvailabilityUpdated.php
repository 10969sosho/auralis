<?php

namespace App\Events;

use App\Models\Schedule;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeatAvailabilityUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $data;

    public function __construct(?Schedule $schedule)
    {
        // Deportation bookings have no schedule — nothing to broadcast
        if (! $schedule) {
            $this->data = ['schedule_id' => 0];

            return;
        }

        $this->data = ['schedule_id' => $schedule->id] + $schedule->seatAvailabilityData();
    }

    public function broadcastOn(): array
    {
        if (! isset($this->data['vip'])) {
            return [];
        }

        return [
            new Channel('schedule.'.$this->data['schedule_id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'seat.availability.updated';
    }
}
