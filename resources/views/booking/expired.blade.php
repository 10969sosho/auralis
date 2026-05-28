@extends('layouts.app')
@section('title', 'Booking Expired')

@section('content')
<div class="max-w-md mx-auto text-center">
    <div class="card card-lg alert-error">
        <h1 class="text-2xl font-bold" style="color:#991b1b">Booking Expired</h1>
        <p class="mt-2 text-gray-600">Your booking #{{ $booking->booking_code }} has expired because payment was not completed in time.</p>
        <div class="mt-6">
            <a href="{{ route('schedules') }}" class="btn btn-primary">Search Again</a>
        </div>
    </div>
</div>
@endsection
