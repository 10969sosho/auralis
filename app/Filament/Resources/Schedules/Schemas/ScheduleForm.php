<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Status')
                    ->compact()
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false)
                            ->live(),
                        TextInput::make('status')
                            ->required()
                            ->default('scheduled'),
                    ]),

                Section::make('Route & Vessel')
                    ->compact()
                    ->columns(2)
                    ->schema([
                        Select::make('vessel_id')
                            ->relationship('vessel', 'name')
                            ->required()
                            ->searchable(),
                        Select::make('route_id')
                            ->relationship('route', 'origin_port')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->origin_port . ' → ' . $record->destination_port)
                            ->required()
                            ->searchable(['origin_port', 'destination_port']),
                    ]),

                Section::make('Schedule Times')
                    ->compact()
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('departure_time')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i'),
                        DateTimePicker::make('arrival_time')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i'),
                    ]),

                Section::make('Pricing')
                    ->compact()
                    ->columns(2)
                    ->schema([
                        TextInput::make('vip_price')
                            ->required()
                            ->numeric()
                            ->prefix('MYR'),
                        TextInput::make('regular_price')
                            ->required()
                            ->numeric()
                            ->prefix('MYR'),
                    ]),

                Section::make('Age Category Pricing')
                    ->compact()
                    ->schema([
                        Repeater::make('agePrices')
                            ->relationship('agePrices')
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
                    ]),
            ]);
    }
}
