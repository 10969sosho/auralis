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
    public function getSchedulesData(?string $scheduleId = null, ?string $status = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $query = Schedule::with('vessel', 'route')
            ->when($scheduleId, fn ($q, $id) => $q->where('id', $id))
            ->when($status, fn ($q, $s) => $q->where('status', $s))
            ->when($dateFrom, fn ($q, $d) => $q->whereDate('departure_time', '>=', $d))
            ->when($dateTo, fn ($q, $d) => $q->whereDate('departure_time', '<=', $d))
            ->orderBy('departure_time', 'desc');

        $schedules = $query->paginate(20);

        return [
            'schedules' => $schedules,
            'summaryMetrics' => $this->getSummaryMetrics($schedules),
        ];
    }

    public function index(Request $request)
    {
        $data = $this->getSchedulesData(
            $request->query('schedule_id'),
            $request->query('status'),
            $request->query('date_from'),
            $request->query('date_to')
        );

        $allSchedules = Schedule::with('vessel', 'route')
            ->orderBy('departure_time', 'desc')
            ->get();

        return view('admin.reports', array_merge($data, compact('allSchedules')));
    }

    public function getSummaryMetrics($schedules)
    {
        $totalBookings = 0;
        $totalRevenue = 0;
        $totalPassengers = 0;
        $totalPaid = 0;
        $totalBoardings = 0;
        $totalRefunds = 0;
        $totalCancelled = 0;
        $totalPending = 0;

        foreach ($schedules as $schedule) {
            $metrics = $this->getMetrics($schedule);
            $totalBookings += $schedule->bookings()->count();
            $totalRevenue += $metrics['total_revenue'];
            $totalPassengers += $metrics['total_registered_passengers'];
            $totalPaid += $metrics['total_payment_success'];
            $totalBoardings += $metrics['total_boarded'];
            $totalRefunds += $metrics['total_refund'];
            $totalCancelled += $metrics['total_cancel'];
            $totalPending += $metrics['total_pending'];
        }

        return [
            'total_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
            'total_passengers' => $totalPassengers,
            'total_paid' => $totalPaid,
            'total_boardings' => $totalBoardings,
            'total_refunds' => $totalRefunds,
            'total_cancelled' => $totalCancelled,
            'total_pending' => $totalPending,
        ];
    }

    public function getMetrics(Schedule $schedule)
    {
        $schedule->loadMissing('vessel', 'route');

        $vipCapacity = $schedule->vessel->vip_capacity;
        $regularCapacity = $schedule->vessel->regular_capacity;
        $totalCapacity = $vipCapacity + $regularCapacity;

        $bookings = $schedule->bookings()->get();
        $totalPassengers = 0;
        $paymentSuccess = 0;
        $boarded = 0;
        $refundCount = 0;
        $cancelCount = 0;
        $pendingCount = 0;
        $totalRevenue = 0;

        foreach ($bookings as $booking) {
            $pCount = $booking->passengers()->count();
            $totalPassengers += $pCount;

            if ($booking->booking_status === 'paid' || $booking->booking_status === 'used') {
                $paymentSuccess += $pCount;
                $totalRevenue += (float) $booking->total_amount;
            }

            if ($booking->booking_status === 'pending_payment') {
                $pendingCount += $pCount;
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
        $departed = $schedule->status === 'departed' ? 1 : 0;

        return [
            'schedule' => [
                'id' => $schedule->id,
                'vessel' => $schedule->vessel->name,
                'route' => $schedule->route->origin_port . ' → ' . $schedule->route->destination_port,
                'departure' => $schedule->departure_time->format('d M Y H:i'),
                'status' => $schedule->status,
            ],
            'total_registered_passengers' => $totalPassengers,
            'total_payment_success' => $paymentSuccess,
            'total_boarded' => $boarded,
            'total_departed' => $departed,
            'total_refund' => $refundCount,
            'total_cancel' => $cancelCount,
            'total_pending' => $pendingCount,
            'total_revenue' => $totalRevenue,
            'remaining_passengers' => $remaining,
            'occupancy_percentage' => $occupancy,
        ];
    }

    public function detail(Schedule $schedule)
    {
        $metrics = $this->getMetrics($schedule);
        return view('admin.report-detail', compact('metrics', 'schedule'));
    }

    public function exportExcel(Request $request)
    {
        $scheduleId = $request->schedule_id;
        $schedules = Schedule::with('vessel', 'route')
            ->when($scheduleId, fn ($q, $id) => $q->where('id', $id))
            ->when($request->date_from, fn ($q, $d) => $q->whereDate('departure_time', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->whereDate('departure_time', '<=', $d))
            ->orderBy('departure_time', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="schedule-report-' . date('Ymd') . '.csv"',
        ];

        $callback = function () use ($schedules) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Schedule ID', 'Vessel', 'Route', 'Departure', 'Status',
                'Total Registered', 'Payment Success', 'Boarded', 'Pending',
                'Refund', 'Cancel', 'Revenue (MYR)', 'Remaining', 'Occupancy %'
            ]);

            foreach ($schedules as $schedule) {
                $metrics = $this->getMetrics($schedule);
                fputcsv($file, [
                    $metrics['schedule']['id'],
                    $metrics['schedule']['vessel'],
                    $metrics['schedule']['route'],
                    $metrics['schedule']['departure'],
                    $metrics['schedule']['status'],
                    $metrics['total_registered_passengers'],
                    $metrics['total_payment_success'],
                    $metrics['total_boarded'],
                    $metrics['total_pending'],
                    $metrics['total_refund'],
                    $metrics['total_cancel'],
                    number_format($metrics['total_revenue'], 2),
                    $metrics['remaining_passengers'],
                    $metrics['occupancy_percentage'] . '%',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportCsv(Request $request)
    {
        return $this->exportExcel($request);
    }
}
