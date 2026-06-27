<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Events\SeatAvailabilityUpdated;
use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Ticket;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve Payment')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading('Approve Payment')
                ->modalDescription('Approve this payment? Tickets will be generated automatically.')
                ->modalSubmitActionLabel('Yes, Approve')
                ->action(fn () => $this->approvePayment())
                ->visible(fn () => $this->record->payment_status === 'awaiting_approval'),

            Action::make('reject')
                ->label('Reject Payment')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading('Reject Payment')
                ->modalDescription('Reject this payment. Provide a reason for the buyer.')
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Rejection Reason')
                        ->required()
                        ->placeholder('Explain why the payment was rejected...'),
                ])
                ->modalSubmitActionLabel('Yes, Reject')
                ->action(fn (array $data) => $this->rejectPayment($data))
                ->visible(fn () => $this->record->payment_status === 'awaiting_approval'),
        ];
    }

    protected function approvePayment(): void
    {
        $payment = $this->record;
        $booking = $payment->booking;

        DB::transaction(function () use ($payment, $booking) {
            $payment->update([
                'payment_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $booking->update([
                'booking_status' => 'paid',
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            foreach ($booking->passengers as $passenger) {
                if (!$passenger->ticket) {
                    $passenger->ticket()->create([
                        'booking_id' => $booking->id,
                        'ticket_class' => $passenger->ticket_class,
                        'qr_token' => Ticket::generateQrToken(),
                        'ticket_number' => Ticket::generateTicketNumber(),
                        'ticket_status' => 'active',
                        'expiry_date' => $booking->schedule->departure_time->startOfDay(),
                    ]);
                }
            }

            event(new SeatAvailabilityUpdated($booking->schedule));
        });

        Notification::make()
            ->title('Payment approved & tickets generated successfully')
            ->success()
            ->send();
    }

    protected function rejectPayment(array $data): void
    {
        $payment = $this->record;
        $booking = $payment->booking;

        DB::transaction(function () use ($payment, $booking, $data) {
            $payment->update([
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

        Notification::make()
            ->title('Payment rejected')
            ->warning()
            ->send();
    }
}
