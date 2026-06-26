<?php

namespace App\Filament\Resources\BoardingLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BoardingLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket.ticket_number')
                    ->label('Ticket')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ticket.passenger.full_name')
                    ->label('Passenger')
                    ->searchable(),

                TextColumn::make('ticket.booking.booking_code')
                    ->label('Booking')
                    ->searchable(),

                TextColumn::make('ticket.booking.schedule.route.origin_port')
                    ->label('Route')
                    ->formatStateUsing(function (TextColumn $column) {
                        $record = $column->getRecord();
                        $booking = $record?->ticket?->booking;
                        return $booking?->schedule?->route?->origin_port . ' → ' . $booking?->schedule?->route?->destination_port;
                    }),

                TextColumn::make('validation_result')
                    ->label('Result')
                    ->badge()
                    ->colors([
                        'success' => 'valid',
                        'danger' => 'invalid',
                        'warning' => 'used',
                        'orange' => 'expired',
                        'gray' => 'cancelled',
                        'info' => 'refunded',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'valid' => 'Valid',
                        'invalid' => 'Invalid',
                        'used' => 'Already Used',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    }),

                TextColumn::make('validatedBy.name')
                    ->label('Officer')
                    ->searchable(),

                TextColumn::make('scan_method')
                    ->label('Method')
                    ->badge()
                    ->color(fn ($state) => $state === 'qr' ? 'info' : 'gray')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'qr' => 'QR Scan',
                        'manual' => 'Manual',
                        default => ucfirst($state),
                    }),

                TextColumn::make('validated_at')
                    ->label('Time')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->defaultSort('validated_at', 'desc');
    }
}
