<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Booking Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('booking.booking_code')
                            ->label('Booking Code')
                            ->disabled(),
                        TextInput::make('booking.user.name')
                            ->label('Buyer')
                            ->disabled(),
                        TextInput::make('amount')
                            ->label('Amount')
                            ->disabled()
                            ->prefix('MYR'),
                        TextInput::make('payment_status')
                            ->label('Status')
                            ->disabled(),
                        TextInput::make('payment_method')
                            ->label('Payment Method')
                            ->disabled(),
                        TextInput::make('transaction_id')
                            ->label('Transaction ID')
                            ->disabled(),
                    ]),

                Section::make('Proof of Transfer')
                    ->schema([
                        ViewField::make('proof_of_transfer')
                            ->view('filament.components.payment-proof-view'),
                    ]),

                Section::make('Approval')
                    ->columns(2)
                    ->schema([
                        Select::make('payment_status')
                            ->label('Payment Status')
                            ->options([
                                'pending' => 'Pending',
                                'awaiting_approval' => 'Awaiting Approval',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'paid' => 'Paid',
                                'expired' => 'Expired',
                            ]),
                        TextInput::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->disabled(),
                        DateTimePicker::make('approved_at')
                            ->label('Approved At')
                            ->disabled(),
                        TextInput::make('approver.name')
                            ->label('Approved By')
                            ->disabled(),
                    ]),
            ]);
    }
}
