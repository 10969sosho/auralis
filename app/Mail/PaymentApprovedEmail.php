<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentApprovedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $ticketUrl = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Approved – Tickets Ready! | Auralis8',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-approved',
            with: [
                'booking' => $this->booking,
                'ticketUrl' => $this->ticketUrl,
                'heading' => 'Payment Approved!',
            ],
        );
    }
}
