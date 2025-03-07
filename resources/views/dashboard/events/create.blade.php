@extends('layouts.app')

@section('content')
    <h1>Create New Event</h1>

    <form action="{{ route('events.store') }}" method="POST">
        @csrf
        <label>Event Name</label>
        <input type="text" name="name" required>

        <label>Description</label>
        <textarea name="description" required></textarea>

        <label>Start Date</label>
        <input type="datetime-local" name="startDate" required>

        <label>End Date</label>
        <input type="datetime-local" name="endDate" required>

        <label>Category</label>
        <select name="category_id">
            <option value="1">Music</option>
            <option value="2">Conference</option>
            <!-- Add more categories -->
        </select>

        <button type="submit">Create Event</button>
    </form>
@endsection
