@extends('layouts.app')
@section('title', 'Home')

@section('content')
<div class="text-center">
    <h1 class="text-4xl font-bold text-gray-900">Ship Ticketing</h1>
    <p class="mt-4 text-lg text-gray-600">International Ferry Booking — Malaysia ↔ Philippines</p>
    <p class="text-gray-500 mt-2">Bongao, Tawi-Tawi ↔ Lahad Datu, Sabah</p>
    <div class="mt-8">
        <a href="{{ route('schedules') }}" class="btn btn-primary btn-lg">Search Schedule</a>
    </div>
    <div class="mt-12 grid lg:grid-cols-3 gap-6">
        <div class="card text-left">
            <h3 class="text-lg font-semibold">Easy Booking</h3>
            <p class="mt-2 text-gray-600">Book your ferry tickets in under 3 minutes with our mobile-friendly platform.</p>
        </div>
        <div class="card text-left">
            <h3 class="text-lg font-semibold">QR Boarding</h3>
            <p class="mt-2 text-gray-600">Quick boarding with dynamic QR validation. Anti-duplicate scan protection.</p>
        </div>
        <div class="card text-left">
            <h3 class="text-lg font-semibold">VIP & Regular</h3>
            <p class="mt-2 text-gray-600">Choose VIP for premium comfort or Regular for standard seating. Auralis 8 vessel.</p>
        </div>
    </div>
</div>
@endsection
