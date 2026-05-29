<?php

namespace App\Filament\Resources\AgeCategories;

use App\Models\AgeCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class AgeCategoryResource extends Resource
{
    protected static ?string $model = AgeCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|null $navigationLabel = 'Age Categories';

    public static function getNavigationGroup(): ?string
    {
        return 'Booking Settings';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgeCategories::route('/'),
            'create' => Pages\CreateAgeCategory::route('/create'),
            'edit' => Pages\EditAgeCategory::route('/{record}/edit'),
        ];
    }
}
