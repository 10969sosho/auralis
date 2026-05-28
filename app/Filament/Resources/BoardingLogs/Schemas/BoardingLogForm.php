<?php

namespace App\Filament\Resources\BoardingLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BoardingLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ticket_id')
                    ->relationship('ticket', 'id')
                    ->required(),
                TextInput::make('validated_by')
                    ->required()
                    ->numeric(),
                TextInput::make('validation_result')
                    ->required(),
                TextInput::make('device_info'),
                TextInput::make('scan_method')
                    ->required()
                    ->default('qr'),
                DateTimePicker::make('validated_at')
                    ->required(),
            ]);
    }
}
