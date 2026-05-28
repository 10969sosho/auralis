<?php

namespace App\Filament\Resources\BoardingLogs\Pages;

use App\Filament\Resources\BoardingLogs\BoardingLogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBoardingLog extends EditRecord
{
    protected static string $resource = BoardingLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
