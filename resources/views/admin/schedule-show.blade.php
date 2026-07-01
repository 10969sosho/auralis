@extends('layouts.app')
@section('title', 'Schedule #' . $schedule->id . ' - Passenger List')

@section('content')
<div class="schedule-show-page">
    {{-- Header --}}
    <div class="schedule-show-header">
        <div>
            <a href="{{ route('admin.schedule.passengers', $schedule) }}" class="schedule-show-back">&larr; Refresh</a>
            <h1 class="schedule-show-title">{{ $schedule->vessel->name }}</h1>
        </div>
        <div class="schedule-show-header-actions">
            <span class="status-badge status-{{ $schedule->status }}">{{ ucfirst($schedule->status) }}</span>
            <div class="ss-export-dropdown" style="position:relative;display:inline-block;">
                <button class="btn btn-primary btn-sm" onclick="toggleExportMenu()" style="cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div id="exportMenu" class="ss-export-menu" style="display:none;position:absolute;top:100%;right:0;margin-top:4px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.1);z-index:50;min-width:140px;overflow:hidden;">
                    <a href="{{ route('admin.schedule.passengers.export.pdf', $schedule) }}?{{ http_build_query(request()->query()) }}" class="ss-export-item" style="display:flex;align-items:center;gap:8px;padding:10px 14px;text-decoration:none;color:#374151;font-size:0.85rem;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;color:#DC2626;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        To PDF
                    </a>
                    <a href="{{ route('admin.schedule.passengers.export.excel', $schedule) }}?{{ http_build_query(request()->query()) }}" class="ss-export-item" style="display:flex;align-items:center;gap:8px;padding:10px 14px;text-decoration:none;color:#374151;font-size:0.85rem;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;color:#059669;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/></svg>
                        To Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Route & Schedule Highlight Card --}}
    <div class="ss-route-card">
        <div class="ss-route-row">
            <div class="ss-route-points">
                <div class="ss-route-origin">
                    <span class="ss-route-port">{{ $schedule->route->origin_port }}</span>
                    <span class="ss-route-detail">Departure</span>
                </div>
                <div class="ss-route-arrow">
                    <div class="ss-route-line"></div>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </div>
                <div class="ss-route-destination">
                    <span class="ss-route-port">{{ $schedule->route->destination_port }}</span>
                    <span class="ss-route-detail">Arrival</span>
                </div>
            </div>
            <div class="ss-route-times">
                <div class="ss-route-time-item">
                    <span class="ss-route-time-label">Depart</span>
                    <span class="ss-route-time-value">{{ $schedule->departure_time->format('d M Y, H:i') }}</span>
                </div>
                @if($schedule->arrival_time)
                <div class="ss-route-time-sep">→</div>
                <div class="ss-route-time-item">
                    <span class="ss-route-time-label">Arrive</span>
                    <span class="ss-route-time-value">{{ $schedule->arrival_time->format('d M Y, H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="ss-stats">
        <div class="ss-stat-card">
            <span class="ss-stat-num">{{ $stats['totalPassengers'] }}</span>
            <span class="ss-stat-label">Total Passengers</span>
        </div>
        <div class="ss-stat-card ss-stat-card-paid">
            <span class="ss-stat-num">{{ $stats['totalPaid'] }}</span>
            <span class="ss-stat-label">Paid / Confirmed</span>
        </div>
        <div class="ss-stat-card ss-stat-card-pending">
            <span class="ss-stat-num">{{ $stats['totalPending'] }}</span>
            <span class="ss-stat-label">Pending Payment</span>
        </div>
        <div class="ss-stat-card ss-stat-card-boarded">
            <span class="ss-stat-num">{{ $stats['totalBoarded'] }}</span>
            <span class="ss-stat-label">Boarded</span>
        </div>
        <div class="ss-stat-card ss-stat-card-cancel">
            <span class="ss-stat-num">{{ $stats['totalCancelled'] }}</span>
            <span class="ss-stat-label">Cancelled / Expired</span>
        </div>
        <div class="ss-stat-card ss-stat-card-revenue">
            <span class="ss-stat-num">RM {{ number_format($stats['totalRevenue'], 0) }}</span>
            <span class="ss-stat-label">Total Revenue</span>
        </div>
        <div class="ss-stat-card">
            <span class="ss-stat-num">{{ $stats['totalBookings'] }}</span>
            <span class="ss-stat-label">Total Bookings</span>
        </div>
        <div class="ss-stat-card">
            <span class="ss-stat-num">{{ $stats['occupancy'] }}%</span>
            <span class="ss-stat-label">Occupancy</span>
            <span class="ss-stat-sub">({{ $stats['totalCapacity'] }} seats)</span>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="ss-filter-panel card" style="padding:16px;margin-bottom:20px;">
        <form action="{{ route('admin.schedule.passengers', $schedule) }}" method="GET" class="ss-filter-form">
            <div class="ss-filter-grid">
                <div class="form-group">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-input" placeholder="Name, booking code, passport, ticket no..."
                           value="{{ request('search') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Boarding Status</label>
                    <select name="boarding_status" class="form-input">
                        <option value="">All Status</option>
                        <option value="boarded" {{ request('boarding_status') === 'boarded' ? 'selected' : '' }}>Boarded</option>
                        <option value="not_boarded" {{ request('boarding_status') === 'not_boarded' ? 'selected' : '' }}>Not Boarded</option>
                        <option value="active" {{ request('boarding_status') === 'active' ? 'selected' : '' }}>Active (Unused)</option>
                        <option value="expired" {{ request('boarding_status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Status</label>
                    <select name="payment_status" class="form-input">
                        <option value="">All Payments</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="awaiting_approval" {{ request('payment_status') === 'awaiting_approval' ? 'selected' : '' }}>Awaiting Approval</option>
                        <option value="rejected" {{ request('payment_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Booking Status</label>
                    <select name="booking_status" class="form-input">
                        <option value="">All Status</option>
                        <option value="pending_payment" {{ request('booking_status') === 'pending_payment' ? 'selected' : '' }}>Pending Payment</option>
                        <option value="paid" {{ request('booking_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="used" {{ request('booking_status') === 'used' ? 'selected' : '' }}>Used (Boarded)</option>
                        <option value="cancelled" {{ request('booking_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="expired" {{ request('booking_status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="refund_requested" {{ request('booking_status') === 'refund_requested' ? 'selected' : '' }}>Refund Requested</option>
                        <option value="refunded" {{ request('booking_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Ticket Class</label>
                    <select name="ticket_class" class="form-input">
                        <option value="">All Classes</option>
                        <option value="vip" {{ request('ticket_class') === 'vip' ? 'selected' : '' }}>VIP</option>
                        <option value="regular" {{ request('ticket_class') === 'regular' ? 'selected' : '' }}>Regular</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Passenger Type</label>
                    <select name="passenger_type" class="form-input">
                        <option value="">All Types</option>
                        <option value="adult" {{ request('passenger_type') === 'adult' ? 'selected' : '' }}>Adult</option>
                        <option value="child" {{ request('passenger_type') === 'child' ? 'selected' : '' }}>Child</option>
                        <option value="infant" {{ request('passenger_type') === 'infant' ? 'selected' : '' }}>Infant</option>
                        <option value="senior" {{ request('passenger_type') === 'senior' ? 'selected' : '' }}>Senior</option>
                    </select>
                </div>
            </div>
            <div class="ss-filter-actions">
                <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                <a href="{{ route('admin.schedule.passengers', $schedule) }}" class="btn btn-outline btn-sm">Reset</a>
            </div>
        </form>
    </div>

    {{-- Active Filters Tags --}}
    @if(request()->anyFilled(['search', 'boarding_status', 'payment_status', 'booking_status', 'ticket_class', 'passenger_type']))
    <div class="ss-active-filters">
        <span class="ss-filter-tag-label">Active Filters:</span>
        @foreach(['search' => 'Search', 'boarding_status' => 'Boarding', 'payment_status' => 'Payment', 'booking_status' => 'Booking', 'ticket_class' => 'Class', 'passenger_type' => 'Type'] as $key => $label)
            @if(request()->filled($key))
            <span class="ss-filter-tag">
                {{ $label }}: {{ ucfirst(str_replace('_', ' ', request($key))) }}
                <a href="{{ route('admin.schedule.passengers', $schedule) . '?' . http_build_query(request()->except($key)) }}" class="ss-filter-tag-remove">&times;</a>
            </span>
            @endif
        @endforeach
        <span class="ss-filter-count">{{ $passengers->total() }} result(s)</span>
    </div>
    @endif

    {{-- Passengers Table --}}
    <div class="ss-table-wrap">
        <table class="ss-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Passenger Name</th>
                    <th>Booking Code</th>
                    <th>Class</th>
                    <th>Type</th>
                    <th>Passport</th>
                    <th>Payment Status</th>
                    <th>Booking Status</th>
                    <th>Ticket No</th>
                    <th>Boarding Status</th>
                    <th>Boarded At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($passengers as $p)
                    <tr>
                        <td>{{ $loop->iteration + ($passengers->currentPage() - 1) * $passengers->perPage() }}</td>
                        <td>
                            <strong>{{ $p->full_name }}</strong>
                            @if($p->phone_number)
                                <br><small class="text-muted">{{ $p->phone_number }}</small>
                            @endif
                        </td>
                        <td>
                            <code>{{ $p->booking_code }}</code>
                        </td>
                        <td>
                            <span class="ss-class-badge ss-class-{{ $p->ticket_class ?? 'regular' }}">
                                {{ ucfirst($p->ticket_class ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="capitalize">{{ $p->passenger_type ?? '-' }}</td>
                        <td>{{ $p->passport_number ?? '-' }}</td>
                        <td>
                            @php
                                $payStatus = $p->payment_status ?? 'unknown';
                                $payBadge = match($payStatus) {
                                    'paid' => 'ss-badge-success',
                                    'pending' => 'ss-badge-warning',
                                    'awaiting_approval' => 'ss-badge-info',
                                    'rejected' => 'ss-badge-danger',
                                    'refunded' => 'ss-badge-secondary',
                                    default => 'ss-badge-secondary',
                                };
                                $payLabel = match($payStatus) {
                                    'paid' => 'Paid',
                                    'pending' => 'Pending',
                                    'awaiting_approval' => 'Awaiting',
                                    'rejected' => 'Rejected',
                                    'refunded' => 'Refunded',
                                    default => ucfirst($payStatus),
                                };
                            @endphp
                            <span class="ss-badge {{ $payBadge }}">{{ $payLabel }}</span>
                        </td>
                        <td>
                            @php
                                $bookStatus = $p->booking_status ?? 'unknown';
                                $bookBadge = match($bookStatus) {
                                    'paid' => 'ss-badge-success',
                                    'used' => 'ss-badge-info',
                                    'pending_payment' => 'ss-badge-warning',
                                    'cancelled' => 'ss-badge-danger',
                                    'expired' => 'ss-badge-secondary',
                                    'refund_requested', 'refunded' => 'ss-badge-danger',
                                    default => 'ss-badge-secondary',
                                };
                            @endphp
                            <span class="ss-badge {{ $bookBadge }}">{{ ucfirst(str_replace('_', ' ', $bookStatus)) }}</span>
                        </td>
                        <td>
                            @if($p->ticket_number)
                                <code class="ss-ticket-no">{{ $p->ticket_number }}</code>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $boardStatus = $p->ticket_status ?? 'no_ticket';
                                $boardBadge = match($boardStatus) {
                                    'used' => 'ss-badge-success',
                                    'active' => 'ss-badge-warning',
                                    'expired' => 'ss-badge-secondary',
                                    'cancelled', 'refunded' => 'ss-badge-danger',
                                    default => 'ss-badge-secondary',
                                };
                                $boardLabel = match($boardStatus) {
                                    'used' => 'Boarded',
                                    'active' => 'Not Boarded',
                                    'expired' => 'Expired',
                                    'cancelled' => 'Cancelled',
                                    'refunded' => 'Refunded',
                                    default => 'N/A',
                                };
                            @endphp
                            <span class="ss-badge {{ $boardBadge }}">{{ $boardLabel }}</span>
                        </td>
                        <td>
                            @if($p->boarded_at)
                                {{ $p->boarded_at->format('d M H:i') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="ss-empty">
                            <div class="ss-empty-content">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;color:#9ca3af;">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                </svg>
                                <p>No passengers found matching your filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="ss-pagination">
        {{ $passengers->links() }}
    </div>
</div>

<style>
.schedule-show-page { padding: 24px 0; }

/* Header */
.schedule-show-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.schedule-show-header-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.schedule-show-back { display: inline-block; font-size: 0.85rem; color: #2563eb; margin-bottom: 4px; text-decoration: none; }
.schedule-show-title { font-size: 24px; font-weight: 700; color: #111827; }
.schedule-show-sub { color: #6b7280; margin-top: 4px; font-size: 0.9rem; }

/* Route Highlight Card */
.ss-route-card { background: linear-gradient(135deg, #1E3A5F 0%, #1D4ED8 100%); border-radius: 14px; padding: 20px 28px; margin-bottom: 24px; color: #fff; }
.ss-route-row { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.ss-route-points { display: flex; align-items: center; gap: 16px; }
.ss-route-origin, .ss-route-destination { display: flex; flex-direction: column; }
.ss-route-port { font-size: 1.15rem; font-weight: 700; }
.ss-route-detail { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.75; margin-top: 2px; }
.ss-route-arrow { display: flex; flex-direction: column; align-items: center; gap: 2px; color: #93C5FD; }
.ss-route-arrow svg { width: 20px; height: 20px; }
.ss-route-line { width: 40px; height: 2px; background: #60A5FA; border-radius: 1px; }
.ss-route-times { display: flex; align-items: center; gap: 12px; }
.ss-route-time-item { display: flex; flex-direction: column; }
.ss-route-time-label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.7; }
.ss-route-time-value { font-size: 0.9rem; font-weight: 600; margin-top: 2px; white-space: nowrap; }
.ss-route-time-sep { color: #93C5FD; font-weight: 700; font-size: 0.9rem; }

/* Stats */
.ss-stats { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; margin-bottom: 20px; }
.ss-stat-card { background: #fff; border-radius: 10px; padding: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); text-align: center; }
.ss-stat-num { display: block; font-size: 1.3rem; font-weight: 700; color: #374151; }
.ss-stat-label { display: block; font-size: 0.65rem; color: #6b7280; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
.ss-stat-sub { display: block; font-size: 0.6rem; color: #9ca3af; }
.ss-stat-card-paid .ss-stat-num { color: #059669; }
.ss-stat-card-pending .ss-stat-num { color: #D97706; }
.ss-stat-card-boarded .ss-stat-num { color: #2563EB; }
.ss-stat-card-cancel .ss-stat-num { color: #6B7280; }
.ss-stat-card-revenue .ss-stat-num { color: #059669; font-size: 1.1rem; }

/* Filter */
.ss-filter-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; }
.ss-filter-actions { margin-top: 14px; display: flex; gap: 8px; }

/* Active filter tags */
.ss-active-filters { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin-bottom: 16px; font-size: 0.8rem; }
.ss-filter-tag-label { color: #6b7280; font-weight: 500; }
.ss-filter-tag { display: inline-flex; align-items: center; gap: 4px; background: #e5e7eb; padding: 2px 8px; border-radius: 12px; color: #374151; }
.ss-filter-tag-remove { color: #6b7280; text-decoration: none; font-weight: 700; font-size: 1rem; line-height: 1; }
.ss-filter-tag-remove:hover { color: #dc2626; }
.ss-filter-count { color: #6b7280; margin-left: 8px; font-size: 0.8rem; }

/* Export dropdown */
.ss-export-item:hover { background: #f3f4f6; }

/* Table */
.ss-table-wrap { overflow-x: auto; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
.ss-table { width: 100%; border-collapse: collapse; min-width: 1200px; }
.ss-table th { background: #f9fafb; padding: 10px 12px; font-size: 0.7rem; font-weight: 600; color: #374151; text-align: left; border-bottom: 2px solid #e5e7eb; white-space: nowrap; text-transform: uppercase; letter-spacing: 0.5px; }
.ss-table td { padding: 10px 12px; font-size: 0.82rem; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
.ss-table tbody tr:hover { background: #f9fafb; }

/* Badges */
.ss-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 600; white-space: nowrap; }
.ss-badge-success { background: #D1FAE5; color: #065F46; }
.ss-badge-warning { background: #FEF3C7; color: #92400E; }
.ss-badge-danger { background: #FEE2E2; color: #991B1B; }
.ss-badge-info { background: #DBEAFE; color: #1E40AF; }
.ss-badge-secondary { background: #F3F4F6; color: #6B7280; }

.ss-class-badge { display: inline-block; padding: 1px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
.ss-class-vip { background: #FEF3C7; color: #92400E; }
.ss-class-regular { background: #E0E7FF; color: #3730A3; }

.ss-ticket-no { font-size: 0.75rem; background: #f3f4f6; padding: 1px 4px; border-radius: 3px; }

/* Empty state */
.ss-empty { text-align: center; padding: 48px 16px; }
.ss-empty-content { display: flex; flex-direction: column; align-items: center; gap: 12px; color: #9ca3af; }
.ss-empty-content p { margin: 0; font-size: 0.9rem; }

/* Pagination */
.ss-pagination { margin-top: 20px; }

.status-badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; }
.status-scheduled { background: #DBEAFE; color: #1E40AF; }
.status-departed { background: #D1FAE5; color: #065F46; }
.status-cancelled { background: #FEE2E2; color: #991B1B; }
.status-completed { background: #E0E7FF; color: #3730A3; }

.text-muted { color: #9ca3af; }
.capitalize { text-transform: capitalize; }
</style>
@endsection

@push('scripts')
<script>
function toggleExportMenu() {
    const menu = document.getElementById('exportMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    const dd = document.querySelector('.ss-export-dropdown');
    if (dd && !dd.contains(e.target)) {
        const menu = document.getElementById('exportMenu');
        if (menu) menu.style.display = 'none';
    }
});
</script>
@endpush