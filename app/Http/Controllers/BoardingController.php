<?php

namespace App\Http\Controllers;

use App\Events\SeatAvailabilityUpdated;
use App\Models\BoardingLog;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoardingController extends Controller
{
    public function scanner()
    {
        return view('boarding.scanner');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'qr_data' => ['required', 'string'],
        ]);

        $qrData = json_decode($request->qr_data, true);

        if (! $qrData || ! isset($qrData['ticket_id'])) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Invalid QR code format.',
            ]);
        }

        $ticket = Ticket::with(['passenger', 'booking.schedule.vessel', 'booking.schedule.route'])
            ->find($qrData['ticket_id']);

        if (! $ticket) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Ticket not found.',
            ]);
        }

        $result = $this->validateTicket($ticket);

        BoardingLog::log(
            $ticket,
            $result['status'],
            auth()->user(),
            request()->userAgent(),
            'qr',
        );

        return response()->json($result);
    }

    public function manualValidate(Request $request)
    {
        $request->validate([
            'booking_code' => ['required', 'string'],
        ]);

        $ticket = Ticket::whereHas('booking', function ($q) use ($request) {
            $q->where('booking_code', $request->booking_code);
        })->with(['passenger', 'booking.schedule.vessel', 'booking.schedule.route'])->first();

        if (! $ticket) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Ticket not found.',
            ]);
        }

        $result = $this->validateTicket($ticket);

        BoardingLog::log(
            $ticket,
            $result['status'],
            auth()->user(),
            request()->userAgent(),
            'manual',
        );

        return response()->json($result);
    }

    private function validateTicket(Ticket $ticket): array
    {
        $schedule = $ticket->booking->schedule;

        if ($ticket->ticket_status === 'used') {
            return [
                'status' => 'used',
                'message' => 'This ticket has already been used.',
                'type' => 'red_warning',
            ];
        }

        if ($ticket->ticket_status === 'expired' || ($ticket->expiry_date && $ticket->expiry_date->isPast())) {
            return [
                'status' => 'expired',
                'message' => 'This ticket has expired.',
                'type' => 'orange_warning',
            ];
        }

        if ($ticket->ticket_status === 'cancelled') {
            return [
                'status' => 'invalid',
                'message' => 'This booking has been cancelled.',
                'type' => 'red_rejection',
            ];
        }

        if ($ticket->ticket_status === 'refunded') {
            return [
                'status' => 'invalid',
                'message' => 'This ticket has been refunded.',
                'type' => 'red_rejection',
            ];
        }

        if ($schedule->isBoardingClosed) {
            return [
                'status' => 'expired',
                'message' => 'Boarding time has closed.',
                'type' => 'orange_warning',
            ];
        }

        if ($ticket->ticket_status !== 'active') {
            return [
                'status' => 'invalid',
                'message' => 'Ticket is not valid for boarding.',
                'type' => 'red_rejection',
            ];
        }

        DB::transaction(function () use ($ticket) {
            $ticket->update([
                'ticket_status' => 'used',
                'boarded_at' => now(),
            ]);

            $booking = $ticket->booking;
            if ($booking->schedule->isFullyBooked) {
                $booking->schedule->update(['status' => 'departed']);
            }

            $allUsed = $booking->tickets()->where('ticket_status', '!=', 'used')->doesntExist();
            if ($allUsed) {
                $booking->update(['booking_status' => 'used']);
            }

            event(new SeatAvailabilityUpdated($booking->schedule));
        });

        return [
            'status' => 'valid',
            'message' => 'Boarding successful!',
            'type' => 'green_success',
            'passenger' => [
                'name' => $ticket->passenger->full_name,
                'ticket_class' => $ticket->ticket_class,
                'passenger_type' => $ticket->passenger->passenger_type,
            ],
            'schedule' => [
                'vessel' => $schedule->vessel->name,
                'route' => $schedule->route->origin_port.' → '.$schedule->route->destination_port,
                'departure' => $schedule->departure_time->format('Y-m-d H:i'),
            ],
        ];
    }

    public function manifest(Schedule $schedule)
    {
        $bookings = $schedule->bookings()
            ->whereIn('booking_status', ['paid', 'used'])
            ->with(['passengers.ticket', 'user'])
            ->get();

        return view('boarding.manifest', compact('schedule', 'bookings'));
    }
}
