<?php

namespace App\Http\Controllers;

use App\Events\SeatAvailabilityUpdated;
use App\Helpers\MailHelper;
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
                'success' => false,
                'status' => 'invalid',
                'message' => 'Invalid QR code format.',
            ]);
        }

        $ticket = Ticket::with(['passenger', 'booking.schedule.vessel', 'booking.schedule.route'])
            ->find($qrData['ticket_id']);

        if (! $ticket) {
            return response()->json([
                'success' => false,
                'status' => 'invalid',
                'message' => 'Ticket not found.',
            ]);
        }

        if (! hash_equals((string) ($ticket->qr_token ?? ''), (string) ($qrData['token'] ?? ''))) {
            return response()->json([
                'success' => false,
                'status' => 'invalid',
                'message' => 'QR code signature does not match this ticket.',
            ]);
        }

        if (filled($request->schedule_id) && ! $this->matchesSchedule($ticket, (int) $request->schedule_id)) {
            return response()->json([
                'success' => false,
                'status' => 'invalid',
                'message' => 'This ticket does not belong to the selected schedule.',
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

    /**
     * An open return ticket has no booking.schedule_id — it belongs to the sailing
     * its return date was assigned to.
     */
    private function matchesSchedule(Ticket $ticket, int $scheduleId): bool
    {
        if ($ticket->booking->schedule_id !== null) {
            return (int) $ticket->booking->schedule_id === $scheduleId;
        }

        return (int) $ticket->booking->openReturnTicket?->schedule()?->id === $scheduleId;
    }

    public function manualValidate(Request $request)
    {
        $request->validate([
            'booking_code' => ['required', 'string'],
            'ticket_id' => ['nullable', 'integer'],
        ]);

        $ticket = Ticket::whereHas('booking', function ($q) use ($request) {
            $q->where('booking_code', $request->booking_code);
        })->when($request->ticket_id, function ($query) use ($request) {
            $query->where('id', $request->ticket_id);
        })->with(['passenger', 'booking.schedule.vessel', 'booking.schedule.route'])->first();

        if (! $ticket) {
            return response()->json([
                'success' => false,
                'status' => 'invalid',
                'message' => 'Ticket not found.',
            ]);
        }

        if (filled($request->schedule_id) && ! $this->matchesSchedule($ticket, (int) $request->schedule_id)) {
            return response()->json([
                'success' => false,
                'status' => 'invalid',
                'message' => 'This ticket does not belong to the selected schedule.',
            ]);
        }

        if (! $request->ticket_id && $ticket->booking->tickets()->count() > 1) {
            return response()->json([
                'success' => false,
                'status' => 'select',
                'message' => 'Multiple passengers on this booking. Select the passenger to board.',
                'booking_code' => $ticket->booking->booking_code,
                'tickets' => $ticket->booking->tickets()->with('passenger')->get()->map(fn (Ticket $t) => [
                    'ticket_id' => $t->id,
                    'ticket_number' => $t->ticket_number,
                    'passenger_name' => $t->passenger->full_name,
                    'ticket_status' => $t->ticket_status,
                ])->values()->all(),
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
        $openReturn = $ticket->booking->openReturnTicket;
        $schedule = $ticket->booking->schedule ?? $openReturn?->schedule();

        if ($openReturn && ! in_array($openReturn->status, ['assigned', 'used'], true)) {
            return [
                'success' => false,
                'status' => 'invalid',
                'message' => 'Return leg is not assigned to a sailing yet.',
                'type' => 'red_rejection',
            ];
        }

        if (! $schedule) {
            return [
                'success' => false,
                'status' => 'invalid',
                'message' => 'This ticket is not assigned to any sailing.',
                'type' => 'red_rejection',
            ];
        }

        if ($ticket->ticket_status === 'used') {
            return [
                'success' => false,
                'status' => 'used',
                'message' => 'This ticket has already been used.',
                'type' => 'red_warning',
            ];
        }

        if ($ticket->ticket_status === 'expired' || ($ticket->expiry_date && $ticket->expiry_date->copy()->endOfDay()->isPast())) {
            return [
                'success' => false,
                'status' => 'expired',
                'message' => 'This ticket has expired.',
                'type' => 'orange_warning',
            ];
        }

        if ($ticket->ticket_status === 'cancelled') {
            return [
                'success' => false,
                'status' => 'invalid',
                'message' => 'This booking has been cancelled.',
                'type' => 'red_rejection',
            ];
        }

        if ($ticket->ticket_status === 'refunded') {
            return [
                'success' => false,
                'status' => 'invalid',
                'message' => 'This ticket has been refunded.',
                'type' => 'red_rejection',
            ];
        }

        if ($schedule->isBoardingClosed) {
            return [
                'success' => false,
                'status' => 'expired',
                'message' => 'Boarding time has closed.',
                'type' => 'orange_warning',
            ];
        }

        if ($ticket->ticket_status !== 'active') {
            return [
                'success' => false,
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

            $allUsed = $booking->tickets()->where('ticket_status', '!=', 'used')->doesntExist();
            if ($allUsed) {
                $booking->update(['booking_status' => 'used']);
                // Mark payment as completed when all passengers have boarded
                if ($booking->payment) {
                    $booking->payment->update(['payment_status' => 'completed']);
                }
                $booking->openReturnTicket?->update(['status' => 'used']);
            }

            event(new SeatAvailabilityUpdated($booking->schedule));

            MailHelper::sendBoardingSuccess($booking, $ticket->passenger->full_name);
        });

        return [
            'success' => true,
            'status' => 'valid',
            'message' => 'Boarding successful!',
            'type' => 'green_success',
            'passenger_name' => $ticket->passenger->full_name,
            'ticket_number' => $ticket->ticket_number,
            'ticket_class' => ucfirst($ticket->ticket_class),
            'passenger_type' => $ticket->passenger->passenger_type,
            'route' => $ticket->booking->route_display,
            'vessel' => $ticket->booking->vessel_display,
            'departure' => $schedule?->departure_time?->format('d M Y, H:i'),
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
