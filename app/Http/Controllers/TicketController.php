<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QROutputInterface;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected function authorizeTicketAccess(Ticket $ticket, ?Request $request = null): void
    {
        // Allow if the ticket belongs to the authenticated user
        if ($ticket->booking->user_id === auth()->id()) {
            return;
        }

        // Allow guest access if the booking's guest_token matches
        if ($request && $request->query('token') && $ticket->booking->guest_token === $request->query('token')) {
            return;
        }

        // Allow if the authenticated user is a counter officer or admin
        if (auth()->check() && auth()->user()->hasRole(['ticket_counter_officer', 'admin'])) {
            return;
        }

        abort(403, 'Unauthorized access to this ticket.');
    }

    protected function generateQrCode(Ticket $ticket): string
    {
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

        return (new QRCode($options))->render($qrData);
    }

    public function download(Ticket $ticket, Request $request)
    {
        $this->authorizeTicketAccess($ticket, $request);

        $ticket->load(['passenger', 'booking.schedule.vessel', 'booking.schedule.route']);

        $qrcode = $this->generateQrCode($ticket);

        $pdf = Pdf::loadView('tickets.pdf', compact('ticket', 'qrcode'));

        return $pdf->download('ticket-'.$ticket->ticket_number.'.pdf');
    }

    public function show(Ticket $ticket, Request $request)
    {
        $this->authorizeTicketAccess($ticket, $request);

        $ticket->load(['passenger', 'booking.schedule.vessel', 'booking.schedule.route']);

        $qrcode = $this->generateQrCode($ticket);

        $qrData = json_encode([
            'ticket_id' => $ticket->id,
            'booking_code' => $ticket->booking->booking_code,
            'passenger_id' => $ticket->passenger->id,
            'token' => $ticket->qr_token,
            'schedule_id' => $ticket->booking->schedule_id,
        ]);

        return view('tickets.show', compact('ticket', 'qrcode', 'qrData'));
    }
}
