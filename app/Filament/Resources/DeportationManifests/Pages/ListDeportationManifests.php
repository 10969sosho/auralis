<?php

namespace App\Filament\Resources\DeportationManifests\Pages;

use App\Filament\Resources\DeportationManifests\DeportationManifestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeportationManifests extends ListRecords
{
    protected static string $resource = DeportationManifestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
