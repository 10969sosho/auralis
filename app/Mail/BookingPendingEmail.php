<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingPendingEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $paymentUrl = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmed – Complete Your Payment | Auralis8',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-pending',
            with: [
                'booking' => $this->booking,
                'paymentUrl' => $this->paymentUrl,
                'heading' => 'Booking Confirmed!',
            ],
        );
    }
}
