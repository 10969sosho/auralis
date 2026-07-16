<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Events\SeatAvailabilityUpdated;
use App\Models\Payment;
use App\Models\Ticket;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\View\View;
use App\Helpers\StatusHelper;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking.booking_code')
                    ->label('Kode Booking')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pembeli')
                    ->label('Pembeli')
                    ->searchable(false)
                    ->getStateUsing(function ($record) {
                        if ($record->booking && is_null($record->booking->user_id)) {
                            return 'COUNTER';
                        }
                        return $record->booking?->user?->name ?? '—';
                    }),

                TextColumn::make('booking.schedule.route.origin_port')
                    ->label('Rute')
                    ->formatStateUsing(function (TextColumn $column) {
                        $record = $column->getRecord();
                        $booking = $record?->booking;
                        if (!$booking) return '—';
                        if ($booking->is_deportation) {
                            return $booking->route_display ?? 'Deportation';
                        }
                        return ($booking->schedule?->route?->origin_port ?? '?') . ' → ' . ($booking->schedule?->route?->destination_port ?? '?');
                    }),

                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('MYR')
                    ->sortable(),

                IconColumn::make('proof_of_transfer')
                    ->label('Bukti')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-minus-circle')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->tooltip(fn ($state) => $state ? 'Ada bukti transfer' : 'Belum upload'),

                TextColumn::make('payment_status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'awaiting_approval',
                        'success' => ['approved', 'paid'],
                        'danger' => ['rejected', 'expired'],
                        'gray' => 'pending',
                        'info' => 'completed',
                    ])
                    ->formatStateUsing(fn ($state) => StatusHelper::paymentStatuses()[$state] ?? ucfirst(str_replace('_', ' ', $state))),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('Status')
                    ->options(StatusHelper::paymentStatuses())
                    ->placeholder('All Status'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('view_proof')
                    ->label('Lihat Bukti')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading('Bukti Transfer')
                    ->modalContent(fn (Payment $record): View => view('filament.components.payment-proof-modal', [
                        'payment' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->visible(fn (Payment $record): bool => $record->proof_of_transfer !== null),

                Action::make('approve')
                    ->label('ACC')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran')
                    ->modalDescription('Pembayaran akan disetujui dan tiket akan otomatis diterbitkan untuk customer.')
                    ->modalSubmitActionLabel('Ya, Setujui')
                    ->action(function (Payment $record) {
                        $booking = $record->booking;

                        DB::transaction(function () use ($record, $booking) {
                            $record->update([
                                'payment_status' => 'approved',
                                'approved_by' => auth()->id(),
                                'approved_at' => now(),
                            ]);

                            $booking->update([
                                'booking_status' => 'paid',
                                'payment_status' => 'paid',
                                'paid_at' => now(),
                            ]);

                            $isDeportation = $booking->is_deportation ?? false;

                            foreach ($booking->passengers as $passenger) {
                                if (!$passenger->ticket) {
                                    $ticketData = [
                                        'booking_id' => $booking->id,
                                        'ticket_class' => $passenger->ticket_class,
                                        'qr_token' => Ticket::generateQrToken(),
                                        'ticket_number' => Ticket::generateTicketNumber(),
                                        'ticket_status' => 'active',
                                    ];

                                    if ($isDeportation) {
                                        // Open ticket — no expiry date
                                        $ticketData['expiry_date'] = null;
                                        $ticketData['is_deportation'] = true;
                                    } else {
                                        $ticketData['expiry_date'] = $booking->schedule?->departure_time?->startOfDay();
                                    }

                                    $passenger->ticket()->create($ticketData);
                                }
                            }

                            if (!$isDeportation && $booking->schedule) {
                                event(new SeatAvailabilityUpdated($booking->schedule));
                            }
                        });
                    })
                    ->visible(fn (Payment $record): bool => $record->payment_status === 'awaiting_approval'),

                Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->modalHeading('Tolak Pembayaran')
                    ->modalDescription('Berikan alasan penolakan agar buyer tahu.')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Jelaskan alasan kenapa pembayaran ditolak...'),
                    ])
                    ->modalSubmitActionLabel('Ya, Tolak')
                    ->action(function (Payment $record, array $data) {
                        $booking = $record->booking;

                        DB::transaction(function () use ($record, $booking, $data) {
                            $record->update([
                                'payment_status' => 'rejected',
                                'rejection_reason' => $data['rejection_reason'],
                                'approved_by' => auth()->id(),
                                'approved_at' => now(),
                            ]);

                            $booking->update([
                                'booking_status' => 'pending_payment',
                                'payment_status' => 'rejected',
                            ]);
                        });
                    })
                    ->visible(fn (Payment $record): bool => $record->payment_status === 'awaiting_approval'),
            ]);
    }
}

