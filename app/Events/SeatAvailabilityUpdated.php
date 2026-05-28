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

    public function __construct(Schedule $schedule)
    {
        $vipCapacity = $schedule->vessel->vip_capacity;
        $regularCapacity = $schedule->vessel->regular_capacity;
        $vipBooked = $schedule->vip_booked;
        $regularBooked = $schedule->regular_booked;
        $vipRemaining = max(0, $vipCapacity - $vipBooked);
        $regularRemaining = max(0, $regularCapacity - $regularBooked);
        $totalCapacity = $vipCapacity + $regularCapacity;
        $totalBooked = $vipBooked + $regularBooked;
        $occupancy = $totalCapacity > 0 ? round(($totalBooked / $totalCapacity) * 100, 2) : 0;

        $this->data = [
            'schedule_id' => $schedule->id,
            'vip' => [
                'capacity' => $vipCapacity,
                'booked' => $vipBooked,
                'remaining' => $vipRemaining,
                'status' => $vipRemaining > 0 ? 'available' : 'full',
            ],
            'regular' => [
                'capacity' => $regularCapacity,
                'booked' => $regularBooked,
                'remaining' => $regularRemaining,
                'status' => $regularRemaining > 0 ? 'available' : 'full',
            ],
            'total' => [
                'capacity' => $totalCapacity,
                'booked' => $totalBooked,
                'remaining' => $totalCapacity - $totalBooked,
                'occupancy_percentage' => $occupancy,
            ],
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('schedule.' . $this->data['schedule_id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'seat.availability.updated';
    }
}
