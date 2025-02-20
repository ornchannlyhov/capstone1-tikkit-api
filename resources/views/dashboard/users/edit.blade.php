@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-2xl bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Edit User</h2>

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <label class="block mt-4 mb-2 text-gray-700">Name</label>
            <input type="text" name="name" value="{{ $user->name }}" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-gray-400" required>

            <label class="block mt-4 mb-2 text-gray-700">Email</label>
            <input type="email" name="email" value="{{ $user->email }}" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-gray-400" required>

            <label class="block mt-4 mb-2 text-gray-700">Phone Number</label>
            <input type="text" name="phone_number" value="{{ $user->phone_number }}" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-gray-400" required>

            <label class="block mt-4 mb-2 text-gray-700">Role</label>
            <select name="role" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-gray-400">
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>Buyer</option>
                <option value="vendor" {{ $user->role === 'vendor' ? 'selected' : '' }}>Vendor</option>
            </select>

            <button type="submit" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 w-full">
                Update User
            </button>
        </form>
    </div>
</div>
@endsection
