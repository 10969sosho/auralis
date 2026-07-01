<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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

    protected function buildExportQuery(Schedule $schedule, Request $request)
    {
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

        return $query->orderBy('bookings.created_at', 'desc');
    }

    public function exportToPdf(Schedule $schedule, Request $request)
    {
        $schedule->loadMissing('vessel', 'route');
        $passengers = $this->buildExportQuery($schedule, $request)->get();

        $pdf = Pdf::loadView('admin.exports.passengers-pdf', compact('schedule', 'passengers'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('schedule-' . $schedule->id . '-passengers-' . date('Ymd') . '.pdf');
    }

    public function exportToExcel(Schedule $schedule, Request $request)
    {
        $schedule->loadMissing('vessel', 'route');
        $passengers = $this->buildExportQuery($schedule, $request)->get();

        $fmt = function ($val, $fmtStr = 'd M Y') {
            if (!$val) return '-';
            if ($val instanceof \Illuminate\Support\Carbon) return $val->format($fmtStr);
            if (is_string($val) && strtotime($val)) return date($fmtStr, strtotime($val));
            return (string) $val;
        };

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head><meta charset="UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Passengers</x:Name></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
<style>td,th{border:1px solid #ccc;padding:4px 6px;font-size:11px;font-family:sans-serif}th{background:#1D4ED8;color:#fff;font-weight:600}tr:nth-child(even){background:#f9fafb}</style></head><body>
<h2>' . htmlspecialchars($schedule->vessel->name) . ' — ' . htmlspecialchars($schedule->route->origin_port . ' → ' . $schedule->route->destination_port) . ' · ' . $fmt($schedule->departure_time, 'd M Y, H:i') . '</h2>
<table><thead><tr>
<th>No</th><th>Full Name</th><th>Gender</th><th>Birth Date</th><th>Nationality</th>
<th>Passport Number</th><th>Phone Number</th><th>Passenger Type</th>
<th>Ticket Class</th><th>Booking Code</th><th>Booking Status</th>
<th>Payment Status</th><th>Ticket Number</th><th>Boarding Status</th><th>Boarded At</th>
</tr></thead><tbody>';

        foreach ($passengers as $i => $p) {
            $boardingStatus = match ($p->ticket_status) {
                'used' => 'Boarded', 'active' => 'Not Boarded', 'expired' => 'Expired',
                'cancelled' => 'Cancelled', 'refunded' => 'Refunded', default => 'N/A',
            };

            $html .= '<tr>
<td>' . ($i + 1) . '</td>
<td>' . htmlspecialchars($p->full_name) . '</td>
<td>' . htmlspecialchars($p->gender ?? '-') . '</td>
<td>' . $fmt($p->birth_date ?? null) . '</td>
<td>' . htmlspecialchars($p->nationality ?? '-') . '</td>
<td>' . htmlspecialchars($p->passport_number ?? '-') . '</td>
<td>' . htmlspecialchars($p->phone_number ?? '-') . '</td>
<td>' . htmlspecialchars(ucfirst($p->passenger_type ?? '-')) . '</td>
<td>' . htmlspecialchars(ucfirst($p->ticket_class ?? '-')) . '</td>
<td>' . htmlspecialchars($p->booking_code) . '</td>
<td>' . htmlspecialchars(ucfirst(str_replace('_', ' ', $p->booking_status))) . '</td>
<td>' . htmlspecialchars(ucfirst(str_replace('_', ' ', $p->payment_status ?? '-'))) . '</td>
<td>' . htmlspecialchars($p->ticket_number ?? '-') . '</td>
<td>' . $boardingStatus . '</td>
<td>' . $fmt($p->boarded_at ?? null, 'd M Y H:i') . '</td>
</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="schedule-' . $schedule->id . '-passengers-' . date('Ymd') . '.xls"',
        ]);
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