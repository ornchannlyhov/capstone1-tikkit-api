@extends('layouts.app')

@section('content')
    <h1>Edit Event</h1>

    <form action="{{ route('events.update', $event->id) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Event Name</label>
        <input type="text" name="name" value="{{ $event->name }}" required>

        <label>Description</label>
        <textarea name="description" required>{{ $event->description }}</textarea>

        <label>Start Date</label>
        <input type="datetime-local" name="startDate" value="{{ $event->startDate }}" required>

        <label>End Date</label>
        <input type="datetime-local" name="endDate" value="{{ $event->endDate }}" required>

        <button type="submit">Update Event</button>
    </form>
@endsection
