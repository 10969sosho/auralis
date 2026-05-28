<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class SeatAvailabilityController extends Controller
{
    public function show(Schedule $schedule)
    {
        $vipCapacity = $schedule->vessel->vip_capacity;
        $regularCapacity = $schedule->vessel->regular_capacity;

        $vipBooked = $schedule->vip_booked;
        $regularBooked = $schedule->regular_booked;

        $vipRemaining = max(0, $vipCapacity - $vipBooked);
        $regularRemaining = max(0, $regularCapacity - $regularBooked);

        $totalCapacity = $vipCapacity + $regularCapacity;
        $totalBooked = $vipBooked + $regularBooked;
        $totalRemaining = $totalCapacity - $totalBooked;

        $occupancy = $totalCapacity > 0 ? round(($totalBooked / $totalCapacity) * 100, 2) : 0;

        return response()->json([
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
                'remaining' => $totalRemaining,
                'occupancy_percentage' => $occupancy,
            ],
        ]);
    }
}
