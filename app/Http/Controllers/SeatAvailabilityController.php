<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class SeatAvailabilityController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['vessel', 'route'])
            ->where('status', 'scheduled')
            ->where('is_active', true)
            ->where('departure_time', '>', now())
            ->orderBy('departure_time')
            ->get();

        return view('seat-availability', compact('schedules'));
    }

    public function show(Schedule $schedule)
    {
        $schedule->load('vessel');

        $vipCapacity = $schedule->vessel->vip_capacity;
        $regularCapacity = $schedule->vessel->regular_capacity;

        $vipBooked = $schedule->vip_booked;
        $regularBooked = $schedule->regular_booked;

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

        $vipRemaining = max(0, $vipCapacity - $vipBooked);
        $regularRemaining = max(0, $regularCapacity - $regularBooked);

        $totalCapacity = $vipCapacity + $regularCapacity;
        $totalBooked = $vipBooked + $regularBooked;
        $totalPaid = $vipPaid + $regularPaid;
        $totalRemaining = $totalCapacity - $totalBooked;
        $totalAvailable = $totalCapacity - $totalPaid;

        $occupancy = $totalCapacity > 0 ? round(($totalBooked / $totalCapacity) * 100, 2) : 0;

        return response()->json([
            'schedule_id' => $schedule->id,
            'vessel_name' => $schedule->vessel->name,
            'route' => $schedule->route->origin_port . ' → ' . $schedule->route->destination_port,
            'departure' => $schedule->departure_time->format('d M Y H:i'),
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
                'remaining' => $totalRemaining,
                'available' => $totalAvailable,
                'occupancy_percentage' => $occupancy,
            ],
        ]);
    }
}
