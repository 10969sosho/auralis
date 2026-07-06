@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
<h1 class="text-2xl font-bold text-gray-900" data-translate-en="Notifications" data-translate-id="Notifikasi">Notifications</h1>

<form action="{{ route('notifications.markAllRead') }}" method="POST" class="mt-4">
    @csrf
    <button type="submit" class="btn-link" data-translate-en="Mark all as read" data-translate-id="Tandai semua sudah dibaca">Mark all as read</button>
</form>

<div class="mt-6 space-y-3">
    @forelse($notifications as $notification)
    <div class="card card-sm {{ $notification->is_read ? 'opacity-60' : 'border-l-4 border-l-blue-500' }}">
        <div class="flex justify-between">
            <div>
                <h3 class="font-semibold">{{ $notification->title }}</h3>
                <p class="text-sm text-gray-600">{{ $notification->body }}</p>
                <p class="mt-1 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }} via {{ $notification->channel }}</p>
            </div>
            @if(!$notification->is_read)
            <form action="{{ route('notifications.read', $notification) }}" method="POST">
                @csrf
                <button class="btn-link" data-translate-en="Mark read" data-translate-id="Tandai dibaca">Mark read</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="card text-center p-8">
        <p class="text-gray-500" data-translate-en="No notifications." data-translate-id="Tidak ada notifikasi">No notifications.</p>
    </div>
    @endforelse
    {{ $notifications->links() }}
</div>
@endsection
