<?php

namespace App\Observers;

use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class ScheduleObserver
{
    public function updated(Schedule $schedule): void
    {
        if ($schedule->isDirty('departure_time')) {
            // update all related tickets expiry_date to match new departure date (start of day)
            foreach ($schedule->bookings()->with('tickets')->get() as $booking) {
                foreach ($booking->tickets as $ticket) {
                    $ticket->update(['expiry_date' => $schedule->departure_time->copy()->startOfDay()]);
                }
            }
        }
    }
}
