<?php

namespace App\Filament\Resources\Refunds\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RefundForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('booking_id')
                    ->relationship('booking', 'id')
                    ->required(),
                TextInput::make('refund_amount')
                    ->required()
                    ->numeric(),
                Textarea::make('refund_reason')
                    ->columnSpanFull(),
                Textarea::make('admin_notes')
                    ->columnSpanFull(),
                TextInput::make('refund_status')
                    ->required()
                    ->default('requested'),
                TextInput::make('processed_by')
                    ->numeric(),
                DateTimePicker::make('processed_at'),
            ]);
    }
}
