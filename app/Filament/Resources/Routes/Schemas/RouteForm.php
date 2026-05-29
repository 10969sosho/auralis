<?php

namespace App\Filament\Resources\Routes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RouteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('origin_port')
                    ->required(),
                TextInput::make('destination_port')
                    ->required(),
                Toggle::make('active')
                    ->required(),
            ]);
    }
}
