@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-xl">
        <h2 class="text-2xl font-semibold text-center text-gray-800 mb-4">Create New User</h2>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Full Name</label>
                <input type="text" name="name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                    placeholder="Full Name" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Email</label>
                <input type="email" name="email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                    placeholder="Email address" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Phone Number</label>
                <input type="text" name="phone_number"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                    placeholder="Phone Number" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Role</label>
                <select name="role"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" required>
                    <option value="admin">Admin</option>
                    <option value="vendor">Vendor</option>
                    <option value="buyer">Buyer</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Password</label>
                <input type="password" name="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                    placeholder="Enter password" required>
            </div>

            <button type="submit"
                class="w-full bg-green-600 text-white py-2 rounded-md font-semibold text-md shadow-md hover:bg-green-700 transition duration-300">
                Create User
            </button>
        </form>
    </div>
</div>
@endsection
