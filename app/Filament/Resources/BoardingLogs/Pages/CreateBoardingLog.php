<?php

namespace App\Filament\Resources\BoardingLogs\Pages;

use App\Filament\Resources\BoardingLogs\BoardingLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBoardingLog extends CreateRecord
{
    protected static string $resource = BoardingLogResource::class;
}
