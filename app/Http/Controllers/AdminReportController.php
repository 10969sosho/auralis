<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $schedules = Schedule::with('vessel', 'route')
            ->when($request->schedule_id, fn ($q, $id) => $q->where('id', $id))
            ->orderBy('departure_time', 'desc')
            ->paginate(15);

        $allSchedules = Schedule::with('vessel', 'route')
            ->orderBy('departure_time', 'desc')
            ->get();

        return view('admin.reports', compact('schedules', 'allSchedules'));
    }

    public function getMetrics(Schedule $schedule)
    {
        $vipCapacity = $schedule->vessel->vip_capacity;
        $regularCapacity = $schedule->vessel->regular_capacity;
        $totalCapacity = $vipCapacity + $regularCapacity;

        $bookings = $schedule->bookings()->get();
        $totalPassengers = 0;
        $paymentSuccess = 0;
        $boarded = 0;
        $departed = $schedule->status === 'departed' ? 1 : 0;
        $refundCount = 0;
        $cancelCount = 0;

        foreach ($bookings as $booking) {
            $pCount = $booking->passengers()->count();
            $totalPassengers += $pCount;

            if ($booking->booking_status === 'paid' || $booking->booking_status === 'used') {
                $paymentSuccess += $pCount;
            }

            $bookedTickets = $booking->tickets()->where('ticket_status', 'used')->count();
            $boarded += $bookedTickets;

            if ($booking->booking_status === 'refund_requested' || $booking->booking_status === 'refunded') {
                $refundCount += $pCount;
            }

            if ($booking->booking_status === 'cancelled' || $booking->booking_status === 'expired') {
                $cancelCount += $pCount;
            }
        }

        $remaining = max(0, $totalCapacity - $boarded);
        $occupancy = $totalCapacity > 0 ? round(($totalPassengers / $totalCapacity) * 100, 2) : 0;

        return [
            'schedule' => [
                'id' => $schedule->id,
                'vessel' => $schedule->vessel->name,
                'route' => $schedule->route->origin_port . ' → ' . $schedule->route->destination_port,
                'departure' => $schedule->departure_time->format('d M Y H:i'),
            ],
            'total_registered_passengers' => $totalPassengers,
            'total_payment_success' => $paymentSuccess,
            'total_boarded' => $boarded,
            'total_departed' => $departed,
            'total_refund' => $refundCount,
            'total_cancel' => $cancelCount,
            'remaining_passengers' => $remaining,
            'occupancy_percentage' => $occupancy,
        ];
    }

    public function detail(Schedule $schedule)
    {
        $metrics = $this->getMetrics($schedule);
        return view('admin.report-detail', compact('metrics', 'schedule'));
    }

    public function exportCsv(Request $request)
    {
        $scheduleId = $request->schedule_id;
        $schedules = Schedule::with('vessel', 'route')
            ->when($scheduleId, fn ($q, $id) => $q->where('id', $id))
            ->orderBy('departure_time', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="schedule-report.csv"',
        ];

        $callback = function () use ($schedules) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Schedule ID', 'Vessel', 'Route', 'Departure Time',
                'Total Registered', 'Payment Success', 'Boarded', 'Departed',
                'Refund', 'Cancel', 'Remaining', 'Occupancy %'
            ]);

            foreach ($schedules as $schedule) {
                $metrics = $this->getMetrics($schedule);
                fputcsv($file, [
                    $metrics['schedule']['id'],
                    $metrics['schedule']['vessel'],
                    $metrics['schedule']['route'],
                    $metrics['schedule']['departure'],
                    $metrics['total_registered_passengers'],
                    $metrics['total_payment_success'],
                    $metrics['total_boarded'],
                    $metrics['total_departed'],
                    $metrics['total_refund'],
                    $metrics['total_cancel'],
                    $metrics['remaining_passengers'],
                    $metrics['occupancy_percentage'] . '%',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
