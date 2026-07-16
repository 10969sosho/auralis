<x-emails::layout>
    <p>Hello <strong>{{ $booking->guest_email }}</strong>,</p>
    <p>Your booking has been confirmed! Please complete your payment to secure your tickets.</p>

    <div class="email-details">
        <table>
            <tr><td>Booking Code</td><td>#{{ $booking->booking_code }}</td></tr>
            <tr><td>Route</td><td>{{ $booking->route_display }}</td></tr>
            @if($booking->schedule)
            <tr><td>Vessel</td><td>{{ $booking->schedule?->vessel?->name ?? '—' }}</td></tr>
            <tr><td>Departure</td><td>{{ $booking->schedule?->departure_time?->format('d M Y, H:i') ?? '—' }}</td></tr>
            @endif
            <tr><td>Passengers</td><td>{{ $booking->total_passengers }} pax</td></tr>
            <tr><td>Total</td><td><strong>RM {{ number_format($booking->total_amount, 2) }}</strong></td></tr>
        </table>
    </div>

    @if($bookingLink)
    <div style="text-align:center;">
        <a href="{{ $bookingLink }}" class="email-btn">View Booking &amp; Make Payment</a>
    </div>
    @endif

    <p>Please complete payment within <strong>30 minutes</strong> or the booking will be automatically cancelled.</p>
    <p style="font-size:0.85rem;color:#6b7280;">You received this email because you made a guest booking on Auralis8.</p>
</x-emails::layout>
