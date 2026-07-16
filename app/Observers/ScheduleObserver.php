<?php

namespace App\Observers;

use App\Helpers\MailHelper;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class ScheduleObserver
{
    public function updated(Schedule $schedule): void
    {
        if ($schedule->isDirty('departure_time')) {
            $oldDeparture = $schedule->getOriginal('departure_time');
            $oldRouteId = $schedule->getOriginal('route_id');
            $oldVesselId = $schedule->getOriginal('vessel_id');

            // Build old schedule stub for email
            $oldSchedule = (object) [
                'departure_time' => $oldDeparture,
                'route' => $oldRouteId ? $schedule->route()->withTrashed()->first() : null,
                'vessel' => $oldVesselId ? $schedule->vessel()->withTrashed()->first() : null,
            ];

            // Notify all affected bookings
            foreach ($schedule->bookings()->with('user')->get() as $booking) {
                // Update ticket expiry dates
                foreach ($booking->tickets as $ticket) {
                    $ticket->update(['expiry_date' => $schedule->departure_time->copy()->startOfDay()]);
                }

                // Send email notification
                MailHelper::sendScheduleChanged($booking, $oldSchedule, $schedule);
            }
        }
    }
}
