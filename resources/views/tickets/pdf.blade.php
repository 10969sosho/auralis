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
        <h1>AURALIS8 - E-TICKET</h1>
        <h2>{{ $ticket->booking->schedule->vessel->name }}</h2>
        <p>{{ $ticket->booking->schedule->route->origin_port }} → {{ $ticket->booking->schedule->route->destination_port }}</p>
    </div>

    <div class="info">
        <table>
            <tr><td><strong data-translate-en="Passenger" data-translate-id="Penumpang">Passenger</strong></td><td>{{ $ticket->passenger->full_name }}</td></tr>
            <tr><td><strong data-translate-en="Category" data-translate-id="Kategori">Category</strong></td><td>{{ ucfirst($ticket->passenger->passenger_type) }}</td></tr>
            <tr><td><strong data-translate-en="Class" data-translate-id="Kelas">Class</strong></td><td>{{ ucfirst($ticket->ticket_class) }}</td></tr>
            <tr><td><strong data-translate-en="Booking Code" data-translate-id="Kode Pemesanan">Booking Code</strong></td><td>{{ $ticket->booking->booking_code }}</td></tr>
            <tr><td><strong data-translate-en="Passport/ID" data-translate-id="Paspor/ID">Passport/ID</strong></td><td>{{ $ticket->passenger->passport_number }}</td></tr>
            <tr><td><strong data-translate-en="Departure" data-translate-id="Keberangkatan">Departure</strong></td><td>{{ $ticket->booking->schedule->departure_time->format('d M Y, H:i') }}</td></tr>
            <tr><td><strong data-translate-en="Arrival" data-translate-id="Kedatangan">Arrival</strong></td><td>{{ $ticket->booking->schedule->arrival_time->format('d M Y, H:i') }}</td></tr>
            <tr><td><strong data-translate-en="Free Baggage" data-translate-id="Bagasi Gratis">Free Baggage</strong></td><td>{{ $ticket->booking->schedule->vessel->free_baggage }}kg</td></tr>
        </table>
    </div>

    <div class="qr">
        <img src="{{ $qrcode }}" alt="QR Code" style="width:180px;height:180px;">
        <p><strong>{{ $ticket->ticket_number }}</strong></p>
    </div>

    <div class="footer">
        <p data-translate-en="This is an electronic ticket. Present this QR code at boarding." data-translate-id="Ini adalah tiket elektronik. Tunjukkan kode QR ini saat naik kapal.">This is an electronic ticket. Present this QR code at boarding.</p>
        <p><span data-translate-en="Generated:" data-translate-id="Dibuat:">Generated:</span> {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>
</html>
