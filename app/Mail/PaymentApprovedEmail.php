<?php

namespace App\Mail;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QROutputInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
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

    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->booking->passengers as $passenger) {
            $ticket = $passenger->ticket;
            if (!$ticket) continue;

            $ticket->loadMissing(['passenger', 'booking.schedule.vessel', 'booking.schedule.route']);

            // Generate QR code
            $qrData = json_encode([
                'ticket_id' => $ticket->id,
                'booking_code' => $ticket->booking->booking_code,
                'passenger_id' => $ticket->passenger->id,
                'token' => $ticket->qr_token,
                'schedule_id' => $ticket->booking->schedule_id,
            ]);

            $options = new QROptions;
            $options->outputType = QROutputInterface::GDIMAGE_PNG;
            $options->eccLevel = EccLevel::M;
            $options->scale = 8;
            $options->imageBase64 = true;
            $options->bgColor = [255, 255, 255];
            $options->imageTransparent = false;

            $qrcode = (new QRCode($options))->render($qrData);

            $pdf = Pdf::loadView('tickets.pdf', compact('ticket', 'qrcode'));

            $attachments[] = Attachment::fromData(
                fn () => $pdf->output(),
                "ticket-{$ticket->ticket_number}.pdf"
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
