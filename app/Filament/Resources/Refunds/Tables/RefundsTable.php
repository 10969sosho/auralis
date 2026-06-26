<?php

namespace App\Filament\Resources\Refunds\Tables;

use App\Events\SeatAvailabilityUpdated;
use App\Models\Refund;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class RefundsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking.booking_code')
                    ->label('Booking Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('booking.user.name')
                    ->label('Customer')
                    ->searchable(),

                TextColumn::make('booking.schedule.route.origin_port')
                    ->label('Route')
                    ->formatStateUsing(fn ($record) => ($record->booking?->schedule?->route->origin_port ?? '?') . ' → ' . ($record->booking?->schedule?->route->destination_port ?? '?')),

                TextColumn::make('refund_amount')
                    ->label('Amount')
                    ->money('MYR')
                    ->sortable(),

                TextColumn::make('refund_reason')
                    ->label('Reason')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('refund_status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'requested',
                        'success' => ['approved', 'refunded'],
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                TextColumn::make('booking.user.phone')
                    ->label('Contact')
                    ->formatStateUsing(fn ($state) => $state
                        ? '<a href="https://wa.me/' . preg_replace('/[^0-9]/', '', $state) . '?text=Refund%20Booking%20' . urlencode($state) . '" target="_blank" class="text-green-600 underline text-sm">WhatsApp</a>'
                        : '—'
                    )
                    ->html(),

                TextColumn::make('admin_notes')
                    ->label('Admin Notes')
                    ->limit(30),

                TextColumn::make('created_at')
                    ->label('Requested At')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('processed_at')
                    ->label('Processed At')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->defaultSort('created_at', 'desc')
            ->recordAction(ViewAction::class)
            ->recordActions([
                ViewAction::make()
                    ->modalHeading('Refund Details')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->infolist(fn (Schema $schema): Schema => $schema
                        ->schema([
                            Section::make('Booking Information')
                                ->columns(2)
                                ->schema([
                                    TextEntry::make('booking.booking_code')
                                        ->label('Booking Code'),
                                    TextEntry::make('booking.user.name')
                                        ->label('Customer'),
                                    TextEntry::make('booking.schedule.route.origin_port')
                                        ->label('Route')
                                        ->formatStateUsing(fn ($record) => ($record->booking?->schedule?->route->origin_port ?? '?') . ' → ' . ($record->booking?->schedule?->route->destination_port ?? '?')),
                                    TextEntry::make('refund_amount')
                                        ->label('Refund Amount')
                                        ->money('MYR'),
                                ]),
                            Section::make('Refund Details')
                                ->columns(2)
                                ->schema([
                                    TextEntry::make('refund_status')
                                        ->label('Status')
                                        ->badge()
                                        ->formatStateUsing(fn ($state) => ucfirst($state)),
                                    TextEntry::make('created_at')
                                        ->label('Requested At')
                                        ->dateTime('d M Y H:i'),
                                    TextEntry::make('processed_by')
                                        ->label('Processed By')
                                        ->formatStateUsing(fn ($state, $record) => $record->processedBy?->name ?? '—'),
                                    TextEntry::make('processed_at')
                                        ->label('Processed At')
                                        ->dateTime('d M Y H:i'),
                                ]),
                            Section::make('Reason & Notes')
                                ->schema([
                                    TextEntry::make('refund_reason')
                                        ->label('Customer Reason')
                                        ->markdown(),
                                    TextEntry::make('admin_notes')
                                        ->label('Admin Notes')
                                        ->markdown()
                                        ->placeholder('No admin notes.'),
                                ]),
                        ])
                    ),

                Action::make('approve')
                    ->label('Approve Refund')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Refund')
                    ->modalDescription('Confirm this refund? The booking will be marked as refunded. The total refund amount (25%) will be processed manually by admin via WhatsApp.')
                    ->modalSubmitActionLabel('Yes, Approve')
                    ->form([
                        Textarea::make('admin_notes')
                            ->label('Admin Notes (optional)')
                            ->placeholder('Add notes about this refund...'),
                    ])
                    ->action(function (Refund $record, array $data) {
                        $booking = $record->booking;

                        DB::transaction(function () use ($record, $booking, $data) {
                            $record->update([
                                'refund_status' => 'refunded',
                                'admin_notes' => $data['admin_notes'] ?? $record->admin_notes,
                                'processed_by' => auth()->id(),
                                'processed_at' => now(),
                            ]);

                            $booking->update([
                                'booking_status' => 'refunded',
                            ]);

                            // Cancel all tickets
                            foreach ($booking->tickets as $ticket) {
                                $ticket->update(['ticket_status' => 'refunded']);
                            }

                            event(new SeatAvailabilityUpdated($booking->schedule));
                        });

                        Notification::make()
                            ->title('Refund approved — booking marked as refunded')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Refund $record): bool => $record->refund_status === 'requested'),

                Action::make('reject')
                    ->label('Reject Refund')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Refund')
                    ->modalDescription('Reject this refund request. The booking will go back to "Paid" status.')
                    ->modalSubmitActionLabel('Yes, Reject')
                    ->form([
                        Textarea::make('admin_notes')
                            ->label('Rejection Reason')
                            ->required()
                            ->placeholder('Explain why the refund was rejected...'),
                    ])
                    ->action(function (Refund $record, array $data) {
                        $booking = $record->booking;

                        DB::transaction(function () use ($record, $booking, $data) {
                            $record->update([
                                'refund_status' => 'rejected',
                                'admin_notes' => $data['admin_notes'],
                                'processed_by' => auth()->id(),
                                'processed_at' => now(),
                            ]);

                            $booking->update([
                                'booking_status' => 'paid',
                            ]);
                        });

                        Notification::make()
                            ->title('Refund rejected — booking returned to Paid')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn (Refund $record): bool => $record->refund_status === 'requested'),

                Action::make('contact_wa')
                    ->label('WhatsApp')
                    ->color('success')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->url(fn (Refund $record): string => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->booking?->user?->phone ?? '') . '?text=Hi%20' . urlencode($record->booking?->user?->name ?? '') . '%2C%20regarding%20your%20refund%20request%20for%20Booking%20%23' . ($record->booking?->booking_code ?? '') . '.')
                    ->openUrlInNewTab()
                    ->visible(fn (Refund $record): bool => !empty($record->booking?->user?->phone)),
            ]);
    }
}
