<?php

namespace App\Filament\Resources\DeportationManifests\Pages;

use App\Filament\Resources\DeportationManifests\DeportationManifestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDeportationManifest extends CreateRecord
{
    protected static string $resource = DeportationManifestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
