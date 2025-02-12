@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-4">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">User Details</h2>

        <div class="mb-4"><strong>Email:</strong> {{ $user->email }}</div>
        <div class="mb-4"><strong>Firstname:</strong> {{ $user->firstname }}</div>
        <div class="mb-4"><strong>Lastname:</strong> {{ $user->lastname }}</div>
        <div class="mb-4"><strong>Gender:</strong> {{ $user->gender }}</div>
        <div class="mb-4"><strong>Role:</strong> {{ ucfirst($user->role) }}</div>
        <div class="mb-4"><strong>Phone Number:</strong> {{ $user->phone_number }}</div>
        <div class="mb-4"><strong>Created At:</strong> {{ $user->created_at->format('d-m-Y H:i A') }}</div>

        <a href="{{ route('users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Back</a>
    </div>
</div>
@endsection
