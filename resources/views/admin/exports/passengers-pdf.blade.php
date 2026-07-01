<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Passenger List - {{ $schedule->vessel->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        h2 { font-size: 12px; font-weight: 400; color: #555; margin: 0 0 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #2563EB; color: #fff; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; }
        td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        tr:nth-child(even) td { background: #f9fafb; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 8px; font-weight: 600; }
        .bg-green { background: #D1FAE5; color: #065F46; }
        .bg-yellow { background: #FEF3C7; color: #92400E; }
        .bg-red { background: #FEE2E2; color: #991B1B; }
        .bg-blue { background: #DBEAFE; color: #1E40AF; }
        .bg-gray { background: #F3F4F6; color: #6B7280; }
        .footer { margin-top: 16px; font-size: 8px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <h1>{{ $schedule->vessel->name }}</h1>
    <h2>{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }} · {{ $schedule->departure_time->format('d M Y, H:i') }} · Arr: {{ $schedule->arrival_time ? $schedule->arrival_time->format('d M Y, H:i') : 'N/A' }} · {{ $passengers->count() }} passenger(s)</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Passport</th>
                <th>Type</th>
                <th>Class</th>
                <th>Nationality</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Booking</th>
                <th>Booking Status</th>
                <th>Payment</th>
                <th>Ticket No</th>
                <th>Boarding</th>
            </tr>
        </thead>
        <tbody>
            @forelse($passengers as $i => $p)
                @php
                    $boardStatus = $p->ticket_status ?? 'no_ticket';
                    $boardLabel = match($boardStatus) { 'used' => 'Boarded', 'active' => 'Not Boarded', 'expired' => 'Expired', 'cancelled' => 'Cancelled', 'refunded' => 'Refunded', default => 'N/A' };
                    $boardClass = match($boardStatus) { 'used' => 'bg-green', 'active' => 'bg-yellow', default => 'bg-gray' };
                    $payStatus = $p->payment_status ?? 'unknown';
                    $payLabel = match($payStatus) { 'paid' => 'Paid', 'pending' => 'Pending', 'awaiting_approval' => 'Awaiting', 'rejected' => 'Rejected', 'refunded' => 'Refunded', default => ucfirst($payStatus) };
                    $payClass = match($payStatus) { 'paid' => 'bg-green', 'pending' => 'bg-yellow', 'awaiting_approval' => 'bg-blue', 'rejected', 'refunded' => 'bg-red', default => 'bg-gray' };
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $p->full_name }}</strong></td>
                    <td>{{ $p->passport_number ?? '-' }}</td>
                    <td>{{ ucfirst($p->passenger_type ?? '-') }}</td>
                    <td>{{ ucfirst($p->ticket_class ?? '-') }}</td>
                    <td>{{ $p->nationality ?? '-' }}</td>
                    <td>{{ $p->gender ?? '-' }}</td>
                    <td>{{ $p->phone_number ?? '-' }}</td>
                    <td>{{ $p->booking_code }}</td>
                    <td><span class="badge {{ $boardClass }}">{{ ucfirst(str_replace('_', ' ', $p->booking_status)) }}</span></td>
                    <td><span class="badge {{ $payClass }}">{{ $payLabel }}</span></td>
                    <td>{{ $p->ticket_number ?? '-' }}</td>
                    <td><span class="badge {{ $boardClass }}">{{ $boardLabel }}</span></td>
                </tr>
            @empty
                <tr><td colspan="13" style="text-align:center;padding:24px;">No passengers found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('d M Y H:i') }} · Auralis Ferry Ticketing System
    </div>
</body>
</html>