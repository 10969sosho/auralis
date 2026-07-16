<x-emails::layout>
    <p>Hello <strong>{{ $booking->user?->name ?? 'Valued Customer' }}</strong>,</p>
    <p>Your payment has been approved! Your tickets are now ready.</p>

    <div class="email-details">
        <table>
            <tr><td>Booking Code</td><td>#{{ $booking->booking_code }}</td></tr>
            <tr><td>Route</td><td>{{ $booking->route_display }}</td></tr>
            @if($booking->schedule)
            <tr><td>Vessel</td><td>{{ $booking->schedule?->vessel?->name ?? '—' }}</td></tr>
            <tr><td>Departure</td><td>{{ $booking->schedule?->departure_time?->format('d M Y, H:i') ?? '—' }}</td></tr>
            @endif
            <tr><td>Amount Paid</td><td><strong>RM {{ number_format($booking->total_amount, 2) }}</strong></td></tr>
            <tr><td>Status</td><td><span class="email-badge email-badge-green">PAID</span></td></tr>
        </table>
    </div>

    @if($ticketUrl)
    <div style="text-align:center;">
        <a href="{{ $ticketUrl }}" class="email-btn">View Booking &amp; Download Tickets</a>
    </div>
    <p style="font-size:0.8rem;color:#6b7280;text-align:center;margin-top:8px;">
        Your e-ticket PDFs are also attached to this email.
    </p>
    @endif

    <p>Thank you for choosing Auralis8. Have a safe journey!</p>
</x-emails::layout>
