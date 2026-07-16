<x-emails::layout>
    <p>Hello <strong>{{ $booking->user?->name ?? 'Valued Customer' }}</strong>,</p>
    <p>Boarding has been completed successfully!</p>

    <div class="email-details">
        <table>
            <tr><td>Passenger</td><td><strong>{{ $passengerName }}</strong></td></tr>
            <tr><td>Booking Code</td><td>#{{ $booking->booking_code }}</td></tr>
            <tr><td>Route</td><td>{{ $booking->route_display }}</td></tr>
            <tr><td>Status</td><td><span class="email-badge email-badge-green">BOARDED</span></td></tr>
        </table>
    </div>

    <p>Thank you for travelling with Auralis8. We wish you a safe and pleasant journey!</p>
</x-emails::layout>
