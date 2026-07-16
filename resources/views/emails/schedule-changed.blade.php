<x-emails::layout>
    <p>Hello <strong>{{ $booking->user?->name ?? 'Valued Customer' }}</strong>,</p>
    <p>Your booking schedule has been updated. Please review the new details below.</p>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div style="background:#fef2f2;border-radius:12px;padding:16px;">
            <h3 style="margin:0 0 8px;font-size:14px;color:#dc2626;">Previous Schedule</h3>
            <p style="margin:0;font-size:13px;color:#4b5563;">
                {{ $oldSchedule->route?->origin_port ?? '—' }} → {{ $oldSchedule->route?->destination_port ?? '—' }}<br>
                {{ $oldSchedule->vessel?->name ?? '—' }}<br>
                {{ $oldSchedule->departure_time?->format('d M Y, H:i') ?? '—' }}
            </p>
        </div>
        <div style="background:#f0fdf4;border-radius:12px;padding:16px;">
            <h3 style="margin:0 0 8px;font-size:14px;color:#16a34a;">New Schedule</h3>
            <p style="margin:0;font-size:13px;color:#4b5563;">
                {{ $newSchedule->route?->origin_port ?? '—' }} → {{ $newSchedule->route?->destination_port ?? '—' }}<br>
                {{ $newSchedule->vessel?->name ?? '—' }}<br>
                {{ $newSchedule->departure_time?->format('d M Y, H:i') ?? '—' }}
            </p>
        </div>
    </div>

    <div class="email-divider"></div>

    <p>If you have any questions, please contact our support team.</p>
</x-emails::layout>
