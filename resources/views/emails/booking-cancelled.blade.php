<x-emails::layout>
    <p>Hello <strong>{{ $booking->user?->name ?? 'Valued Customer' }}</strong>,</p>
    <p>Your booking has been cancelled.</p>

    <div class="email-details">
        <table>
            <tr><td>Booking Code</td><td>#{{ $booking->booking_code }}</td></tr>
            <tr><td>Route</td><td>{{ $booking->route_display }}</td></tr>
            <tr><td>Status</td><td><span class="email-badge email-badge-red">CANCELLED</span></td></tr>
        </table>
    </div>

    @if($reason)
    <p><strong>Reason:</strong> {{ $reason }}</p>
    @endif

    <p>If you have any questions, please contact our support team.</p>
</x-emails::layout>
