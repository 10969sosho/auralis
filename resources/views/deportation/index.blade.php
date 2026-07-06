@extends('layouts.app')
@section('title', 'Deportation Manifests')

@section('content')
<h1 class="text-2xl font-bold text-gray-900" data-translate-en="Deportation List" data-translate-id="Daftar Deportasi">Deportation Manifests</h1>
<div class="mt-4">
    <a href="{{ route('deportation.create') }}" class="btn btn-primary btn-sm" data-translate-en="Create Manifest" data-translate-id="Buat Manifest">Create New Manifest</a>
</div>
<div class="mt-6 space-y-4">
    @forelse($manifests as $manifest)
    <div class="card">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center justify-between">
            <div>
                <h3 class="font-semibold">{{ $manifest->manifest_code }}</h3>
                <p class="text-gray-600">{{ $manifest->schedule->vessel->name }} — {{ $manifest->schedule->route->origin_port }} → {{ $manifest->schedule->route->destination_port }}</p>
                <p class="text-sm text-gray-500"><span data-translate-en="Passengers:" data-translate-id="Penumpang:">Passengers:</span> {{ $manifest->total_passengers }} | <span data-translate-en="Officer:" data-translate-id="Petugas:">Officer:</span> {{ $manifest->officer->name }}</p>
                <p class="text-xs text-gray-400">{{ $manifest->created_at->format('d M Y H:i') }}</p>
            </div>
            <a href="{{ route('deportation.manifest.show', $manifest->manifest_code) }}" class="link" data-translate-en="View" data-translate-id="Lihat">View Manifest</a>
        </div>
    </div>
    @empty
    <div class="card text-center p-8">
        <p class="text-gray-500" data-translate-en="No manifests found" data-translate-id="Tidak ada manifest">No deportation manifests yet.</p>
    </div>
    @endforelse
    {{ $manifests->links() }}
</div>
@endsection
