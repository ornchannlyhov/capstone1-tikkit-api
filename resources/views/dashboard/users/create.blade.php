@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-2xl bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Add New User</h2>

        <form action="{{ route('users.store', ['role' => $role]) }}" method="POST">
            @csrf
            
            <!-- First Name -->
            <label class="block mb-2 text-gray-700">First Name</label>
            <input type="text" name="firstname" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-gray-400" required>

            <!-- Last Name -->
            <label class="block mt-4 mb-2 text-gray-700">Last Name</label>
            <input type="text" name="lastname" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-gray-400" required>

            <!-- Email -->
            <label class="block mt-4 mb-2 text-gray-700">Email</label>
            <input type="email" name="email" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-gray-400" required>

            <!-- Phone Number -->
            <label class="block mt-4 mb-2 text-gray-700">Phone Number</label>
            <input type="text" name="phone_number" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-gray-400" required>

            <!-- Gender -->
            <label class="block mt-4 mb-2 text-gray-700">Gender</label>
            <select name="gender" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-gray-400">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <!-- Password -->
            <label class="block mt-4 mb-2 text-gray-700">Password</label>
            <input type="password" name="password" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-gray-400" required>

            <!-- Role -->
            <input type="hidden" name="role" value="{{ $role }}">

            <!-- Submit Button -->
            <button type="submit" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 w-full">
                Create User
            </button>
        </form>
    </div>
</div>
@endsection
