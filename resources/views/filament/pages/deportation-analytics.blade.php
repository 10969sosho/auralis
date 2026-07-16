<x-filament-panels::page>
<div class="deportation-analytics-page">

    {{-- Stats Cards --}}
    <div class="da-stats-grid">
        <div class="da-stat-card da-stat-blue">
            <div class="da-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <p class="da-stat-value">{{ number_format($this->getTotalUsers()) }}</p>
                <p class="da-stat-label">Registered Users</p>
            </div>
        </div>
        <div class="da-stat-card da-stat-indigo">
            <div class="da-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div>
                <p class="da-stat-value">{{ number_format($this->getTotalBookings()) }}</p>
                <p class="da-stat-label">Total Bookings</p>
            </div>
        </div>
        <div class="da-stat-card da-stat-green">
            <div class="da-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <p class="da-stat-value">{{ number_format($this->getTotalPaid()) }}</p>
                <p class="da-stat-label">Paid</p>
            </div>
        </div>
        <div class="da-stat-card da-stat-amber">
            <div class="da-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"/><polygon points="12 15 17 21 7 21 12 15"/></svg>
            </div>
            <div>
                <p class="da-stat-value">{{ number_format($this->getTotalBoarded()) }}</p>
                <p class="da-stat-label">Boarded</p>
            </div>
        </div>
    </div>

    <div class="da-grid-2col">

        {{-- Registered Users --}}
        <div class="da-card">
            <div class="da-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <h3>Registered Users</h3>
                <span class="da-badge">{{ number_format($this->getTotalUsers()) }}</span>
            </div>
            <div class="da-table-wrap">
                <table class="da-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Shelter</th>
                            <th>Bookings</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->getRegisteredUsers() as $u)
                        <tr>
                            <td class="da-cell-name">{{ $u['name'] }}</td>
                            <td>{{ $u['phone'] ?? '—' }}</td>
                            <td>{{ \App\Models\User::SHELTER_POINTS[$u['shelter_point']]['name'] ?? '—' }}</td>
                            <td>{{ $u['bookings_count'] }}</td>
                            <td class="da-cell-date">{{ \Carbon\Carbon::parse($u['created_at'])->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="da-empty">No users registered</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Payment Status --}}
        <div class="da-card">
            <div class="da-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                <h3>Payment Status</h3>
                <span class="da-badge">{{ number_format($this->getTotalBookings()) }}</span>
            </div>
            <div class="da-table-wrap">
                <table class="da-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->getBookingPayments() as $b)
                        <tr>
                            <td class="da-cell-code">{{ $b['booking_code'] }}</td>
                            <td class="da-cell-name">{{ $b['user']['name'] ?? '—' }}</td>
                            <td>RM{{ number_format($b['total_amount'], 2) }}</td>
                            <td>
                                <span class="da-status-badge da-status-{{ $b['payment_status'] }}">
                                    {{ str_replace('_', ' ', $b['payment_status']) }}
                                </span>
                            </td>
                            <td class="da-cell-date">{{ \Carbon\Carbon::parse($b['created_at'])->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="da-empty">No bookings yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Boarded Passengers --}}
    <div class="da-card">
        <div class="da-card-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"/><polygon points="12 15 17 21 7 21 12 15"/></svg>
            <h3>Boarded Passengers</h3>
            <span class="da-badge">{{ number_format($this->getTotalBoarded()) }}</span>
        </div>
        <div class="da-table-wrap">
            <table class="da-table">
                <thead>
                    <tr>
                        <th>Passenger</th>
                        <th>Ticket #</th>
                        <th>Booking</th>
                        <th>User</th>
                        <th>Boarded At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->getBoardedPassengers() as $t)
                    <tr>
                        <td class="da-cell-name">{{ $t['passenger']['full_name'] ?? '—' }}</td>
                        <td class="da-cell-code">{{ $t['ticket_number'] }}</td>
                        <td class="da-cell-code">{{ $t['booking']['booking_code'] ?? '—' }}</td>
                        <td>{{ $t['booking']['user']['name'] ?? '—' }}</td>
                        <td class="da-cell-date">{{ $t['boarded_at'] ? \Carbon\Carbon::parse($t['boarded_at'])->format('d M Y H:i') : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="da-empty">No passengers boarded yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
.deportation-analytics-page {
    padding: 8px 0;
}

/* Stats grid */
.da-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media (max-width: 768px) {
    .da-stats-grid { grid-template-columns: repeat(2, 1fr); }
}
.da-stat-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    border-radius: 14px;
    background: #fff;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.da-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.da-stat-icon svg {
    width: 24px;
    height: 24px;
}
.da-stat-blue .da-stat-icon { background: #eff6ff; color: #2563eb; }
.da-stat-indigo .da-stat-icon { background: #eef2ff; color: #4f46e5; }
.da-stat-green .da-stat-icon { background: #f0fdf4; color: #16a34a; }
.da-stat-amber .da-stat-icon { background: #fffbeb; color: #d97706; }
.da-stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #111827;
    margin: 0;
    line-height: 1.2;
}
.da-stat-label {
    font-size: 0.8rem;
    color: #6b7280;
    margin: 2px 0 0;
}

/* Two column grid */
.da-grid-2col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}
@media (max-width: 768px) {
    .da-grid-2col { grid-template-columns: 1fr; }
}

/* Cards */
.da-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    overflow: hidden;
}
.da-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
}
.da-card-header svg {
    width: 20px;
    height: 20px;
    color: #2563eb;
    flex-shrink: 0;
}
.da-card-header h3 {
    font-size: 0.95rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
    flex: 1;
}
.da-badge {
    font-size: 0.75rem;
    font-weight: 700;
    color: #6b7280;
    background: #f3f4f6;
    padding: 3px 10px;
    border-radius: 20px;
}

/* Tables */
.da-table-wrap {
    overflow-x: auto;
}
.da-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
}
.da-table th {
    background: #f8fafc;
    padding: 10px 16px;
    text-align: left;
    font-size: 0.72rem;
    text-transform: uppercase;
    color: #64748b;
    white-space: nowrap;
}
.da-table td {
    padding: 10px 16px;
    border-bottom: 1px solid #f1f5f9;
    white-space: nowrap;
}
.da-table tr:last-child td {
    border-bottom: none;
}
.da-cell-name {
    font-weight: 600;
    color: #111827;
}
.da-cell-code {
    font-family: monospace;
    font-size: 0.8rem;
}
.da-cell-date {
    color: #6b7280;
    font-size: 0.8rem;
}
.da-empty {
    text-align: center;
    color: #9ca3af;
    padding: 24px !important;
    font-size: 0.85rem;
}

/* Status badges */
.da-status-badge {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: capitalize;
}
.da-status-pending { background: #fff7ed; color: #c2410c; }
.da-status-awaiting_approval { background: #eff6ff; color: #1d4ed8; }
.da-status-paid { background: #f0fdf4; color: #16a34a; }
.da-status-approved { background: #f0fdf4; color: #16a34a; }
.da-status-rejected { background: #fef2f2; color: #dc2626; }
.da-status-expired { background: #f3f4f6; color: #6b7280; }
.da-status-cancelled { background: #f3f4f6; color: #6b7280; }
.da-status-failed { background: #fef2f2; color: #dc2626; }
</style>
</div>
</x-filament-panels::page>
