<?php

namespace App\Observers;

use App\Helpers\MailHelper;
use App\Models\OpenReturnTicket;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Vessel;
use Illuminate\Support\Facades\DB;

class ScheduleObserver
{
    public function created(Schedule $schedule): void
    {
        $this->assignOpenReturnTickets($schedule);
    }

    public function updated(Schedule $schedule): void
    {
        if ($schedule->isDirty('departure_time')) {
            $oldSchedule = new Schedule([
                'departure_time' => $schedule->getOriginal('departure_time'),
                'arrival_time' => $schedule->getOriginal('arrival_time'),
                'route_id' => $schedule->getOriginal('route_id'),
                'vessel_id' => $schedule->getOriginal('vessel_id'),
                'status' => $schedule->getOriginal('status'),
            ]);
            $oldSchedule->setRelation('route', Route::find($oldSchedule->route_id) ?? $schedule->route);
            $oldSchedule->setRelation('vessel', Vessel::find($oldSchedule->vessel_id) ?? $schedule->vessel);

            DB::transaction(function () use ($schedule, $oldSchedule) {
                foreach ($schedule->bookings()->with('user')->get() as $booking) {
                    // Update ticket expiry dates (valid through the whole departure day)
                    foreach ($booking->tickets as $ticket) {
                        $ticket->update(['expiry_date' => $schedule->departure_time->copy()->endOfDay()]);
                    }

                    MailHelper::sendScheduleChanged($booking, $oldSchedule, $schedule);
                }
            });
        }

        $this->assignOpenReturnTickets($schedule);
    }

    /**
     * Open return tickets whose return date lands on this sailing get assigned,
     * as long as master ship capacity still allows it.
     */
    private function assignOpenReturnTickets(Schedule $schedule): void
    {
        $date = $schedule->departure_time?->toDateString();

        if (! $date) {
            return;
        }

        OpenReturnTicket::whereDate('return_date', $date)
            ->where('status', 'open')
            ->orderBy('id')
            ->get()
            ->each(fn (OpenReturnTicket $ticket) => $ticket->assignIfPossible());
    }
}
