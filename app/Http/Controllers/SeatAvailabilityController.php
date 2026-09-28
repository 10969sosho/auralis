<?php

namespace App\Http\Controllers;

use App\Models\Schedule;

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
        $schedule->load('vessel', 'route');

        return response()->json([
            'schedule_id' => $schedule->id,
            'vessel_name' => $schedule->vessel->name,
            'route' => $schedule->route->origin_port.' → '.$schedule->route->destination_port,
            'departure' => $schedule->departure_time->format('d M Y H:i'),
        ] + $schedule->seatAvailabilityData());
    }
}
