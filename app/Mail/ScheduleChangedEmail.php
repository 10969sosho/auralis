<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScheduleChangedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public Schedule $oldSchedule,
        public Schedule $newSchedule,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Schedule Change Notification | Auralis8',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.schedule-changed',
            with: [
                'booking' => $this->booking,
                'oldSchedule' => $this->oldSchedule,
                'newSchedule' => $this->newSchedule,
                'heading' => 'Schedule Updated',
            ],
        );
    }
}
