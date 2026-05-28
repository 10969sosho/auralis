<?php

namespace App\Filament\Resources\Promos\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PromoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code'),
                TextInput::make('type')
                    ->required(),
                TextInput::make('value')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('start_date')
                    ->required(),
                DateTimePicker::make('end_date')
                    ->required(),
                TextInput::make('usage_quota')
                    ->required()
                    ->numeric(),
                TextInput::make('used_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('route_id')
                    ->relationship('route', 'id'),
                TextInput::make('ticket_class')
                    ->default('all'),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('auto_apply')
                    ->required(),
                TextInput::make('min_passengers')
                    ->numeric(),
                TextInput::make('max_passengers')
                    ->numeric(),
            ]);
    }
}
