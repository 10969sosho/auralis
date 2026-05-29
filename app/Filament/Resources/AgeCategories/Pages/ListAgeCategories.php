<?php

namespace App\Filament\Resources\AgeCategories\Pages;

use App\Filament\Resources\AgeCategories\AgeCategoryResource;
use App\Filament\Resources\AgeCategories\Tables\AgeCategoriesTable;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListAgeCategories extends ListRecords
{
    protected static string $resource = AgeCategoryResource::class;

    public function table(Table $table): Table
    {
        return AgeCategoriesTable::configure($table);
    }
}
