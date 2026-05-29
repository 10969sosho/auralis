@extends('layouts.app')
@section('title', 'Search Bookings - Counter')

@section('content')
<div class="counter-search-page">
    <div class="mb-6">
        <a href="{{ route('counter.dashboard') }}" class="link">← Back to Counter</a>
    </div>

    <form action="{{ route('counter.search') }}" method="GET" class="mb-6">
        <div class="flex gap-3">
            <input type="text" name="query" value="{{ request('query') }}" placeholder="Search booking code, passenger name, passport..." class="form-input flex-1" required>
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <h2 class="text-lg font-semibold mb-4">
        @if(request('query'))
            Results for: <span class="text-blue-600">"{{ request('query') }}"</span>
        @endif
    </h2>

    @forelse($bookings as $booking)
        <div class="search-result-card">
            <div class="flex justify-between items-start flex-wrap gap-2">
                <div>
                    <span class="search-result-code">{{ $booking->booking_code }}</span>
                    <span class="status-badge status-{{ $booking->booking_status }}">{{ ucfirst($booking->booking_status) }}</span>
                </div>
                <span class="text-sm text-gray-500">{{ $booking->created_at->format('d M Y H:i') }}</span>
            </div>

            <div class="search-result-body">
                <div class="search-result-route">
                    <strong>{{ $booking->schedule->vessel->name }}</strong>
                    <span>{{ $booking->schedule->route->origin_port }} → {{ $booking->schedule->route->destination_port }}</span>
                    <span class="text-sm text-gray-500">{{ $booking->schedule->departure_time->format('d M Y, H:i') }}</span>
                </div>

                <div class="search-result-passengers">
                    @foreach($booking->passengers as $p)
                        <div class="flex justify-between items-center text-sm py-1">
                            <span>{{ $p->full_name }} <span class="text-gray-400">({{ $p->passenger_type }} · {{ ucfirst($p->ticket_class) }})</span></span>
                            <span>{{ $p->passport_number }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center pt-2 border-t">
                    <div>
                        <span class="text-sm text-gray-500">Total: </span>
                        <span class="font-semibold">MYR {{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                    <div class="flex gap-2">
                        @if($booking->payment)
                            <span class="text-xs px-2 py-1 rounded" style="background:#ECFDF5;color:#065F46;">{{ ucfirst($booking->payment->payment_method) }}</span>
                        @endif
                        @if($booking->booking_status === 'paid')
                            <a href="{{ route('counter.dashboard') }}" class="btn btn-outline btn-xs">Already Paid</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <h3>No results found</h3>
            <p>Try a different search term.</p>
        </div>
    @endforelse
</div>

<style>
.counter-search-page { padding: 24px 0; max-width: 700px; }
.search-result-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 20px; margin-bottom: 14px; }
.search-result-code { font-weight: 700; font-size: 1.05rem; margin-right: 8px; }
.search-result-body { margin-top: 12px; }
.search-result-route { display: flex; flex-direction: column; gap: 2px; margin-bottom: 10px; }
.search-result-passengers { margin-bottom: 10px; padding: 8px 0; }
.status-badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; }
.status-paid { background: #D1FAE5; color: #065F46; }
.status-pending_payment { background: #FEF3C7; color: #92400E; }
.status-cancelled, .status-expired { background: #FEE2E2; color: #991B1B; }
.status-refunded, .status-refund_requested { background: #F3E8FF; color: #5B21B6; }
.status-used { background: #DBEAFE; color: #1E40AF; }
</style>
@endsection
