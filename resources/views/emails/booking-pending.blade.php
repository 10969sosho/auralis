<x-emails::layout>
    <p>Hello <strong>{{ $booking->user?->name ?? 'Valued Customer' }}</strong>,</p>
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

    @if($paymentUrl)
    <div style="text-align:center;">
        <a href="{{ $paymentUrl }}" class="email-btn">Complete Payment</a>
    </div>
    @endif

    <p>Please complete payment within <strong>30 minutes</strong> or the booking will be automatically cancelled.</p>
</x-emails::layout>
