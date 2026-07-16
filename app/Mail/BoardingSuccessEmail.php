<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BoardingSuccessEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $passengerName = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Boarding Successful – Have a Safe Trip! | Auralis8',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.boarding-success',
            with: [
                'booking' => $this->booking,
                'passengerName' => $this->passengerName,
                'heading' => 'Boarding Successful!',
            ],
        );
    }
}
