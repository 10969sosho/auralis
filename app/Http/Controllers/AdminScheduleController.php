<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AdminScheduleController extends Controller
{
    public function passengers(Schedule $schedule, Request $request)
    {
        $schedule->loadMissing('vessel', 'route');

        $query = $schedule->bookings()
            ->with(['passengers.ticket', 'user', 'payment'])
            ->join('booking_passengers', 'bookings.id', '=', 'booking_passengers.booking_id')
            ->leftJoin('tickets', 'booking_passengers.id', '=', 'tickets.booking_passenger_id')
            ->select(
                'booking_passengers.*',
                'bookings.booking_code',
                'bookings.booking_status',
                'bookings.payment_status',
                'tickets.id as ticket_id',
                'tickets.ticket_number',
                'tickets.ticket_status',
                'tickets.boarded_at',
            );

        // Filter by boarding status
        if ($request->filled('boarding_status')) {
            switch ($request->boarding_status) {
                case 'boarded':
                    $query->where('tickets.ticket_status', 'used');
                    break;
                case 'not_boarded':
                    $query->where(function ($q) {
                        $q->where('tickets.ticket_status', '!=', 'used')
                          ->orWhereNull('tickets.id');
                    });
                    break;
                case 'active':
                    $query->where('tickets.ticket_status', 'active');
                    break;
                case 'expired':
                    $query->where('tickets.ticket_status', 'expired');
                    break;
            }
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('bookings.payment_status', $request->payment_status);
        }

        // Filter by booking status
        if ($request->filled('booking_status')) {
            $query->where('bookings.booking_status', $request->booking_status);
        }

        // Filter by ticket class
        if ($request->filled('ticket_class')) {
            $query->where('booking_passengers.ticket_class', $request->ticket_class);
        }

        // Filter by passenger type
        if ($request->filled('passenger_type')) {
            $query->where('booking_passengers.passenger_type', $request->passenger_type);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_passengers.full_name', 'like', "%{$search}%")
                  ->orWhere('booking_passengers.passport_number', 'like', "%{$search}%")
                  ->orWhere('booking_passengers.phone_number', 'like', "%{$search}%")
                  ->orWhere('bookings.booking_code', 'like', "%{$search}%")
                  ->orWhere('tickets.ticket_number', 'like', "%{$search}%");
            });
        }

        $passengers = $query->orderBy('bookings.created_at', 'desc')
            ->paginate(50)
            ->withQueryString();

        // Stats summary for this schedule
        $stats = $this->getScheduleStats($schedule);

        return view('admin.schedule-show', compact('schedule', 'passengers', 'stats'));
    }

    public function exportPassengers(Schedule $schedule, Request $request)
    {
        $schedule->loadMissing('vessel', 'route');

        $query = $schedule->bookings()
            ->with(['passengers.ticket', 'user', 'payment'])
            ->join('booking_passengers', 'bookings.id', '=', 'booking_passengers.booking_id')
            ->leftJoin('tickets', 'booking_passengers.id', '=', 'tickets.booking_passenger_id')
            ->select(
                'booking_passengers.*',
                'bookings.booking_code',
                'bookings.booking_status',
                'bookings.payment_status',
                'tickets.id as ticket_id',
                'tickets.ticket_number',
                'tickets.ticket_status',
                'tickets.boarded_at',
            );

        // Apply same filters
        if ($request->filled('boarding_status')) {
            switch ($request->boarding_status) {
                case 'boarded':
                    $query->where('tickets.ticket_status', 'used');
                    break;
                case 'not_boarded':
                    $query->where(function ($q) {
                        $q->where('tickets.ticket_status', '!=', 'used')
                          ->orWhereNull('tickets.id');
                    });
                    break;
                case 'active':
                    $query->where('tickets.ticket_status', 'active');
                    break;
                case 'expired':
                    $query->where('tickets.ticket_status', 'expired');
                    break;
            }
        }

        if ($request->filled('payment_status')) {
            $query->where('bookings.payment_status', $request->payment_status);
        }

        if ($request->filled('booking_status')) {
            $query->where('bookings.booking_status', $request->booking_status);
        }

        if ($request->filled('ticket_class')) {
            $query->where('booking_passengers.ticket_class', $request->ticket_class);
        }

        if ($request->filled('passenger_type')) {
            $query->where('booking_passengers.passenger_type', $request->passenger_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_passengers.full_name', 'like', "%{$search}%")
                  ->orWhere('booking_passengers.passport_number', 'like', "%{$search}%")
                  ->orWhere('booking_passengers.phone_number', 'like', "%{$search}%")
                  ->orWhere('bookings.booking_code', 'like', "%{$search}%")
                  ->orWhere('tickets.ticket_number', 'like', "%{$search}%");
            });
        }

        $passengers = $query->orderBy('bookings.created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="schedule-' . $schedule->id . '-passengers-' . date('Ymd') . '.csv"',
        ];

        $callback = function () use ($passengers, $schedule) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'No', 'Full Name', 'Gender', 'Birth Date', 'Nationality',
                'Passport Number', 'Phone Number', 'Passenger Type',
                'Ticket Class', 'Booking Code', 'Booking Status',
                'Payment Status', 'Ticket Number', 'Boarding Status', 'Boarded At'
            ]);

            foreach ($passengers as $i => $p) {
                $boardingStatus = match ($p->ticket_status) {
                    'used' => 'Boarded',
                    'active' => 'Not Boarded',
                    'expired' => 'Expired',
                    'cancelled' => 'Cancelled',
                    'refunded' => 'Refunded',
                    default => 'N/A',
                };

                fputcsv($file, [
                    $i + 1,
                    $p->full_name,
                    $p->gender ?? '-',
                    $p->birth_date ? $p->birth_date->format('d M Y') : '-',
                    $p->nationality ?? '-',
                    $p->passport_number ?? '-',
                    $p->phone_number ?? '-',
                    ucfirst($p->passenger_type ?? '-'),
                    ucfirst($p->ticket_class ?? '-'),
                    $p->booking_code,
                    ucfirst(str_replace('_', ' ', $p->booking_status)),
                    ucfirst(str_replace('_', ' ', $p->payment_status ?? '-')),
                    $p->ticket_number ?? '-',
                    $boardingStatus,
                    $p->boarded_at ? $p->boarded_at->format('d M Y H:i') : '-',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function getScheduleStats(Schedule $schedule)
    {
        $totalBookings = $schedule->bookings()->count();
        $totalPassengers = $schedule->bookings()->join('booking_passengers', 'bookings.id', '=', 'booking_passengers.booking_id')->count();
        $totalPaid = $schedule->bookings()->where('bookings.booking_status', 'paid')->join('booking_passengers', 'bookings.id', '=', 'booking_passengers.booking_id')->count();
        $totalPending = $schedule->bookings()->where('bookings.booking_status', 'pending_payment')->join('booking_passengers', 'bookings.id', '=', 'booking_passengers.booking_id')->count();
        $totalCancelled = $schedule->bookings()->whereIn('bookings.booking_status', ['cancelled', 'expired'])->join('booking_passengers', 'bookings.id', '=', 'booking_passengers.booking_id')->count();
        $totalBoarded = $schedule->bookings()
            ->join('booking_passengers', 'bookings.id', '=', 'booking_passengers.booking_id')
            ->join('tickets', 'booking_passengers.id', '=', 'tickets.booking_passenger_id')
            ->where('tickets.ticket_status', 'used')
            ->count();

        $totalRevenue = $schedule->bookings()
            ->whereIn('bookings.booking_status', ['paid', 'used'])
            ->sum('bookings.total_amount');

        $vipCapacity = $schedule->vessel->vip_capacity ?? 0;
        $regularCapacity = $schedule->vessel->regular_capacity ?? 0;
        $totalCapacity = $vipCapacity + $regularCapacity;
        $occupancy = $totalCapacity > 0 ? round(($totalPaid / $totalCapacity) * 100, 1) : 0;

        return compact(
            'totalBookings', 'totalPassengers', 'totalPaid', 'totalPending',
            'totalCancelled', 'totalBoarded', 'totalRevenue', 'totalCapacity',
            'occupancy', 'vipCapacity', 'regularCapacity'
        );
    }
}