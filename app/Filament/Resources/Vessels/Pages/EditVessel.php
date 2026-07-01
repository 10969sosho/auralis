<?php

namespace App\Filament\Resources\Vessels\Pages;

use App\Filament\Resources\Vessels\VesselResource;
use App\Models\Schedule;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditVessel extends EditRecord
{
    protected static string $resource = VesselResource::class;

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
        $vesselId = $this->record->id;

        $hasActiveSchedules = Schedule::where('vessel_id', $vesselId)
            ->where('is_active', true)
            ->exists();

        if (!$hasActiveSchedules) {
            return;
        }

        // Check if any of the capacity fields changed
        $changes = [];
        foreach (['capacity', 'vip_capacity', 'regular_capacity'] as $field) {
            if (isset($this->data[$field]) && $this->data[$field] != $this->record->$field) {
                $changes[] = $field;
            }
        }

        if (!empty($changes)) {
            Notification::make()
                ->title('Cannot change capacity')
                ->body('This vessel has active schedules. Deactivate all schedules using this vessel first to edit capacity fields.')
                ->warning()
                ->send();

            $this->halt();
        }
    }
}
