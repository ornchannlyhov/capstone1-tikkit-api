@extends('layouts.app')

@section('content')
    <h1>Event Details</h1>
    <p><strong>Name:</strong> {{ $event->name }}</p>
    <p><strong>Description:</strong> {{ $event->description }}</p>
    <p><strong>Start Date:</strong> {{ $event->startDate }}</p>
    <p><strong>End Date:</strong> {{ $event->endDate }}</p>
    <p><strong>Status:</strong> {{ $event->status }}</p>

    <a href="{{ route('events.edit', $event->id) }}" class="btn btn-warning">Edit Event</a>
    <form action="{{ route('events.destroy', $event->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete Event</button>
    </form>
@endsection
