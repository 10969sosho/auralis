<?php

namespace App\Filament\Resources\AgeCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AgeCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Infant, Child, Adult'),
                TextInput::make('min_age')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(150)
                    ->helperText('Minimum age (inclusive)'),
                TextInput::make('max_age')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(150)
                    ->helperText('Maximum age (inclusive)'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first'),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
