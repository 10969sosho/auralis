<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket {{ $ticket->ticket_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .info { margin-top: 20px; }
        .info table { width: 100%; border-collapse: collapse; }
        .info td { padding: 6px; border-bottom: 1px solid #eee; }
        .qr { text-align: center; margin-top: 25px; padding-top: 20px; border-top: 2px dashed #ccc; }
        .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SHIP TICKETING - E-TICKET</h1>
        <h2>{{ $ticket->booking->schedule->vessel->name }}</h2>
        <p>{{ $ticket->booking->schedule->route->origin_port }} → {{ $ticket->booking->schedule->route->destination_port }}</p>
    </div>

    <div class="info">
        <table>
            <tr><td><strong>Passenger</strong></td><td>{{ $ticket->passenger->full_name }}</td></tr>
            <tr><td><strong>Category</strong></td><td>{{ ucfirst($ticket->passenger->passenger_type) }}</td></tr>
            <tr><td><strong>Class</strong></td><td>{{ ucfirst($ticket->ticket_class) }}</td></tr>
            <tr><td><strong>Booking Code</strong></td><td>{{ $ticket->booking->booking_code }}</td></tr>
            <tr><td><strong>Passport/ID</strong></td><td>{{ $ticket->passenger->passport_number }}</td></tr>
            <tr><td><strong>Departure</strong></td><td>{{ $ticket->booking->schedule->departure_time->format('d M Y, H:i') }}</td></tr>
            <tr><td><strong>Arrival</strong></td><td>{{ $ticket->booking->schedule->arrival_time->format('d M Y, H:i') }}</td></tr>
            <tr><td><strong>Free Baggage</strong></td><td>{{ $ticket->booking->schedule->vessel->free_baggage }}kg</td></tr>
        </table>
    </div>

    <div class="qr">
        <img src="{{ $qrcode }}" alt="QR Code" style="width:180px;height:180px;">
        <p><strong>{{ $ticket->ticket_number }}</strong></p>
    </div>

    <div class="footer">
        <p>This is an electronic ticket. Present this QR code at boarding.</p>
        <p>Generated: {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>
</html>
