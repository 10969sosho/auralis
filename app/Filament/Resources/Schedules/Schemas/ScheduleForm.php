<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('vessel_id')
                    ->relationship('vessel', 'name')
                    ->required(),
                Select::make('route_id')
                    ->relationship('route', 'id')
                    ->required(),
                DateTimePicker::make('departure_time')
                    ->required(),
                DateTimePicker::make('arrival_time')
                    ->required(),
                TextInput::make('vip_price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('regular_price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('vip_remaining')
                    ->numeric(),
                TextInput::make('regular_remaining')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('scheduled'),
            ]);
    }
}
