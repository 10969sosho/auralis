<?php

namespace App\Filament\Resources\BoardingLogs\Pages;

use App\Filament\Resources\BoardingLogs\BoardingLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBoardingLogs extends ListRecords
{
    protected static string $resource = BoardingLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
