@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white h-screen shadow-md fixed top-0 left-0">
        <div class="p-4">
            <h2 class="text-lg font-bold">Admin V1.0.1</h2>
        </div>

        <nav class="mt-4">
            <ul>
                <li class="py-2 px-4 hover:bg-gray-200">
                    <a href="#" class="block">🏠 Home</a>
                </li>
                <li class="py-2 px-4 hover:bg-gray-200">
                    <a href="{{ route('users.index', ['role' => 'admin']) }}" 
                       class="block {{ request()->is('admin/users*') ? 'bg-black text-white' : '' }}">
                        👤 Users
                    </a>
                </li>
                <li class="py-2 px-4 hover:bg-gray-200">
                    <a href="{{ route('events.index') }}" class="block">📅 Events</a>
                </li>
                <li class="py-2 px-4 hover:bg-gray-200">
                    <a href="{{ route('addresses.index') }}" class="block">📍 Addresses</a>
                </li>
                <li class="py-2 px-4 hover:bg-gray-200">
                    <a href="#" class="block">📊 Analytics</a>
                </li>
            </ul>
        </nav>

        <div class="absolute bottom-4 left-4">
            <a href="{{ route('logout') }}" class="px-4 py-2 bg-red-500 text-white rounded">Log Out</a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 ml-64 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">User Management</h1>
            <a href="{{ route('users.create', ['role' => $role]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                + Add User
            </a>
        </div>

        <!-- Filter Tabs -->
        <div class="flex space-x-4 mb-4">
            <a href="{{ route('users.index', ['role' => 'admin']) }}" class="px-4 py-2 rounded-lg text-white {{ $role === 'admin' ? 'bg-gray-800' : 'bg-gray-500 hover:bg-gray-600' }}">
                Admins
            </a>
            <a href="{{ route('users.index', ['role' => 'buyer']) }}" class="px-4 py-2 rounded-lg text-white {{ $role === 'buyer' ? 'bg-gray-800' : 'bg-gray-500 hover:bg-gray-600' }}">
                Buyers
            </a>
            <a href="{{ route('users.index', ['role' => 'vendor']) }}" class="px-4 py-2 rounded-lg text-white {{ $role === 'vendor' ? 'bg-gray-800' : 'bg-gray-500 hover:bg-gray-600' }}">
                Vendors
            </a>
        </div>

        <!-- Search Bar -->
        <div class="flex mb-4">
            <input type="text" id="search" placeholder="Search by user email" class="px-4 py-2 border rounded-l-lg w-full focus:outline-none focus:ring-2 focus:ring-gray-400">
            <button id="searchBtn" class="px-4 py-2 bg-gray-800 text-white rounded-r-lg hover:bg-gray-900">
                Filter
            </button>
        </div>

        <!-- User Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full border-collapse border">
                <thead class="bg-green-800 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Firstname</th>
                        <th class="px-4 py-2 text-left">Lastname</th>
                        <th class="px-4 py-2 text-left">Gender</th>
                        <th class="px-4 py-2 text-left">Role</th>
                        <th class="px-4 py-2 text-left">Phone</th>
                        <th class="px-4 py-2 text-left">Created At</th>
                        <th class="px-4 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr class="border-b hover:bg-green-100">
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">{{ $user->firstname }}</td>
                        <td class="px-4 py-2">{{ $user->lastname }}</td>
                        <td class="px-4 py-2">{{ $user->gender }}</td>
                        <td class="px-4 py-2">{{ ucfirst($user->role) }}</td>
                        <td class="px-4 py-2">{{ $user->phone_number }}</td>
                        <td class="px-4 py-2">{{ $user->created_at->format('d-m-Y h:ia') }}</td>
                        <td class="px-4 py-2 text-center flex space-x-2">
                            <!-- Edit Button -->
                            <a href="{{ route('users.edit', $user->id) }}" class="px-2 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-700">
                                ✏️
                            </a>
                            <!-- Delete Button -->
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded-md hover:bg-red-700">
                                    🗑
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($users->isEmpty())
            <div class="p-4 text-gray-600 text-center">
                No users found.
            </div>
            @endif
        </div>
    </div>
</div>

<!-- JavaScript for Search Functionality -->
<script>
    document.getElementById('searchBtn').addEventListener('click', function() {
        let query = document.getElementById('search').value;
        if(query.trim() !== '') {
            window.location.href = "{{ route('users.index', ['role' => $role]) }}" + "&query=" + encodeURIComponent(query);
        }
    });
</script>

@endsection
