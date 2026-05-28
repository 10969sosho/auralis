<?php

namespace App\Filament\Resources\Vessels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VesselForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('capacity')
                    ->required()
                    ->numeric(),
                TextInput::make('vip_capacity')
                    ->required()
                    ->numeric(),
                TextInput::make('regular_capacity')
                    ->required()
                    ->numeric(),
                TextInput::make('free_baggage')
                    ->required()
                    ->numeric()
                    ->default(10),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
            ]);
    }
}
