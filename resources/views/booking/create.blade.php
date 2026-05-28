@extends('layouts.app')
@section('title', 'Book Ticket')

@section('content')
<h1 class="text-2xl font-bold text-gray-900">Book Your Ticket</h1>

<div class="mt-4 alert alert-info">
    <p class="font-semibold">{{ $schedule->vessel->name }}</p>
    <p class="text-gray-600">{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</p>
    <p class="text-sm text-gray-500">Departure: {{ $schedule->departure_time->format('d M Y, H:i') }}</p>
</div>

<form action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-6">
    @csrf
    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
    <input type="hidden" id="passenger-count-input" name="passenger_count" value="{{ request('passenger_count', 1) }}">

    <div class="form-group">
        <label for="promo_code" class="form-label">Promo Code (optional)</label>
        <input type="text" name="promo_code" id="promo_code" class="form-input max-w-xs">
        @if($autoPromos->isNotEmpty())
            <p class="mt-1 text-sm text-green-600">Available promos: {{ $autoPromos->pluck('name')->join(', ') }}</p>
        @endif
    </div>

    <div id="passengers-container">
        @for($i = 0; $i < (int)(request('passenger_count', 1)); $i++)
        <div class="card mb-6" id="passenger-{{ $i }}">
            <h3 class="text-lg font-semibold border-b pb-2 mb-4">Passenger {{ $i + 1 }}</h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="passengers[{{ $i }}][full_name]" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Gender *</label>
                    <select name="passengers[{{ $i }}][gender]" required class="form-select">
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" name="passengers[{{ $i }}][birth_date]" required max="{{ date('Y-m-d') }}" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Nationality *</label>
                    <input type="text" name="passengers[{{ $i }}][nationality]" required placeholder="e.g. Malaysian" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Passport/ID Number *</label>
                    <input type="text" name="passengers[{{ $i }}][passport_number]" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="passengers[{{ $i }}][phone_number]" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Ticket Class *</label>
                    <select name="passengers[{{ $i }}][ticket_class]" required class="form-select">
                        <option value="regular">Regular — MYR {{ number_format($schedule->regular_price, 2) }}</option>
                        <option value="vip">VIP — MYR {{ number_format($schedule->vip_price, 2) }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Passport/ID Upload *</label>
                    <input type="file" name="passengers[{{ $i }}][passport_file]" required accept=".pdf,.jpg,.jpeg,.png" class="form-input">
                    <p class="form-hint">PDF, JPG, PNG (max 5MB)</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Travel Permit (optional)</label>
                    <input type="file" name="passengers[{{ $i }}][travel_permit]" accept=".pdf,.jpg,.jpeg,.png" class="form-input">
                </div>
            </div>
        </div>
        @endfor
    </div>

    <div class="card">
        <h3 class="text-lg font-semibold">Booking Summary</h3>
        <p class="mt-2 text-sm text-gray-600">Free baggage: {{ $schedule->vessel->free_baggage }}kg per passenger</p>
        <p class="mt-2 text-sm text-gray-500">Booking will be held for 30 minutes after submission.</p>
        <button type="submit" class="btn btn-primary btn-lg mt-4 sm:w-auto btn-block">Continue to Payment</button>
    </div>
</form>
@endsection
