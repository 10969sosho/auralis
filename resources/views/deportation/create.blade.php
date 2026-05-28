@extends('layouts.app')
@section('title', 'Create Deportation Manifest')

@section('content')
<h1 class="text-2xl font-bold text-gray-900">Create Deportation Manifest</h1>

<form action="{{ route('deportation.manifests.store') }}" method="POST" class="mt-6 card space-y-4">
    @csrf
    <div class="form-group">
        <label for="schedule_id" class="form-label">Select Schedule</label>
        <select name="schedule_id" id="schedule_id" required class="form-select">
            <option value="">Select schedule...</option>
            @foreach($schedules as $s)
                <option value="{{ $s->id }}">{{ $s->vessel->name }} — {{ $s->route->origin_port }} → {{ $s->route->destination_port }} ({{ $s->departure_time->format('d M H:i') }})</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="notes" class="form-label">Notes (optional)</label>
        <textarea name="notes" id="notes" rows="3" class="form-textarea"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Create Manifest</button>
</form>
@endsection
