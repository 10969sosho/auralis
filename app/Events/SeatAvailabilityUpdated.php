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

        $paidBookings = $schedule->bookings()
            ->whereIn('booking_status', ['paid', 'used'])
            ->with('passengers')
            ->get();

        $vipPaid = 0;
        $regularPaid = 0;
        foreach ($paidBookings as $booking) {
            foreach ($booking->passengers as $p) {
                if ($p->ticket_class === 'vip') {
                    $vipPaid++;
                } else {
                    $regularPaid++;
                }
            }
        }

        $totalPaid = $vipPaid + $regularPaid;

        $this->data = [
            'schedule_id' => $schedule->id,
            'vip' => [
                'capacity' => $vipCapacity,
                'booked' => $vipBooked,
                'paid' => $vipPaid,
                'remaining' => $vipRemaining,
                'available' => max(0, $vipCapacity - $vipPaid),
                'status' => $vipRemaining > 0 ? 'available' : 'full',
            ],
            'regular' => [
                'capacity' => $regularCapacity,
                'booked' => $regularBooked,
                'paid' => $regularPaid,
                'remaining' => $regularRemaining,
                'available' => max(0, $regularCapacity - $regularPaid),
                'status' => $regularRemaining > 0 ? 'available' : 'full',
            ],
            'total' => [
                'capacity' => $totalCapacity,
                'booked' => $totalBooked,
                'paid' => $totalPaid,
                'remaining' => $totalCapacity - $totalBooked,
                'available' => $totalCapacity - $totalPaid,
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
