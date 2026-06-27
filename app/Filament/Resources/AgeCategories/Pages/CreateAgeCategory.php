<?php

namespace App\Filament\Resources\AgeCategories\Pages;

use App\Filament\Resources\AgeCategories\AgeCategoryResource;
use App\Filament\Resources\AgeCategories\Schemas\AgeCategoryForm;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateAgeCategory extends CreateRecord
{
    protected static string $resource = AgeCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function form(Schema $schema): Schema
    {
        return AgeCategoryForm::configure($schema);
    }
}
