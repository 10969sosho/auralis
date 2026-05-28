<?php

namespace App\Filament\Resources\DeportationManifests\Pages;

use App\Filament\Resources\DeportationManifests\DeportationManifestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeportationManifest extends EditRecord
{
    protected static string $resource = DeportationManifestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
