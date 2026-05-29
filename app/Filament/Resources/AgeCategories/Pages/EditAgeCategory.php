<?php

namespace App\Filament\Resources\AgeCategories\Pages;

use App\Filament\Resources\AgeCategories\AgeCategoryResource;
use App\Filament\Resources\AgeCategories\Schemas\AgeCategoryForm;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditAgeCategory extends EditRecord
{
    protected static string $resource = AgeCategoryResource::class;

    public function form(Schema $schema): Schema
    {
        return AgeCategoryForm::configure($schema);
    }
}
