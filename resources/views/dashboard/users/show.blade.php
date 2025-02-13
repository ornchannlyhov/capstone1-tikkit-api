@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-lg bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">User Details</h2>

        <p class="mb-2"><strong>Email:</strong> {{ $user->email }}</p>
        <p class="mb-2"><strong>First Name:</strong> {{ $user->firstname }}</p>
        <p class="mb-2"><strong>Last Name:</strong> {{ $user->lastname }}</p>
        <p class="mb-2"><strong>Gender:</strong> {{ $user->gender }}</p>
        <p class="mb-2"><strong>Phone:</strong> {{ $user->phone_number }}</p>
        <p class="mb-2"><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
        <p class="mb-2"><strong>Created At:</strong> {{ $user->created_at->format('d-m-Y h:ia') }}</p>

        <a href="{{ route('users.edit', $user->id) }}" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 block text-center">
            Edit User
        </a>
    </div>
</div>
@endsection
