<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use App\Http\Controllers\NotificationController;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    protected ?Carbon $oldDepartureTime = null;
    protected ?Carbon $oldArrivalTime = null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->oldDepartureTime = $this->record->departure_time;
        $this->oldArrivalTime = $this->record->arrival_time;
    }

    protected function afterSave(): void
    {
        $schedule = $this->record;

        $newDeparture = $schedule->departure_time;
        $newArrival = $schedule->arrival_time;

        $departureChanged = $this->oldDepartureTime && $newDeparture && !$this->oldDepartureTime->eq($newDeparture);
        $arrivalChanged = $this->oldArrivalTime && $newArrival && !$this->oldArrivalTime->eq($newArrival);

        if (!$departureChanged && !$arrivalChanged) {
            return;
        }

        // Get all users who have active bookings for this schedule
        $userIds = $schedule->bookings()
            ->whereIn('booking_status', ['pending_payment', 'paid', 'used'])
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique();

        if ($userIds->isEmpty()) {
            return;
        }

        $changes = [];
        if ($departureChanged) {
            $changes[] = 'departure from ' . $this->oldDepartureTime->format('d M Y, H:i') . ' to ' . $newDeparture->format('d M Y, H:i');
        }
        if ($arrivalChanged) {
            $changes[] = 'arrival from ' . $this->oldArrivalTime->format('d M Y, H:i') . ' to ' . $newArrival->format('d M Y, H:i');
        }

        $routeLabel = $schedule->route->origin_port . ' → ' . $schedule->route->destination_port;

        $title = 'Schedule Time Changed';
        $body = 'Your booking for ' . $routeLabel . ' has been updated: ' . implode(' and ', $changes) . '. Please check your booking details.';

        foreach ($userIds as $userId) {
            NotificationController::createForUser(
                $userId,
                'schedule_update',
                $title,
                $body
            );
        }
    }
}
