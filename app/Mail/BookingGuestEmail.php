<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingGuestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $bookingLink = ''
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
            view: 'emails.booking-guest',
            with: [
                'booking' => $this->booking,
                'bookingLink' => $this->bookingLink,
                'heading' => 'Booking Confirmed!',
            ],
        );
    }
}
