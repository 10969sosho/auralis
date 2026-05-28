<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('schedule_id')
                    ->relationship('schedule', 'id')
                    ->required(),
                TextInput::make('booking_code')
                    ->required(),
                TextInput::make('total_passengers')
                    ->required()
                    ->numeric(),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('promo_id')
                    ->relationship('promo', 'name'),
                TextInput::make('booking_status')
                    ->required()
                    ->default('pending_payment'),
                TextInput::make('payment_status')
                    ->required()
                    ->default('pending'),
                DateTimePicker::make('locked_at'),
                DateTimePicker::make('expires_at'),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
