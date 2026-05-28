<?php

namespace App\Filament\Resources\DeportationManifests\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DeportationManifestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('schedule_id')
                    ->relationship('schedule', 'id')
                    ->required(),
                Select::make('officer_id')
                    ->relationship('officer', 'name')
                    ->required(),
                TextInput::make('manifest_code')
                    ->required(),
                TextInput::make('total_passengers')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
