<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->inline(false)
                    ->live(),
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
                    ->prefix('MYR'),
                TextInput::make('regular_price')
                    ->required()
                    ->numeric()
                    ->prefix('MYR'),
                TextInput::make('status')
                    ->required()
                    ->default('scheduled'),
                Repeater::make('agePrices')
                    ->relationship('agePrices')
                    ->label('Age Category Pricing')
                    ->schema([
                        Select::make('age_category_id')
                            ->relationship('ageCategory', 'name')
                            ->required()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->label('Age Category'),
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('MYR')
                            ->label('Price'),
                    ])
                    ->addActionLabel('Add Age Price')
                    ->columns(2)
                    ->collapsible()
                    ->helperText('Set specific pricing for age categories. If not set, the default VIP/Regular price applies.'),
            ]);
    }
}
