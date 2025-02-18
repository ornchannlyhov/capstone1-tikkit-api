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
                    <a href="#" class="block"> 👤 Vendor</a>
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
            <a href="{{ route('logout') }}" class="px-4 py-2 bg-green-500 text-white rounded">Log Out</a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 ml-64 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">User Management</h1>
            <a href="{{ route('users.create', ['role' => $role]) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                + Add User
            </a>
        </div>

        <!-- Filter Tabs -->
        <div class="flex space-x-4 mb-4">
            <a href="{{ route('users.index', ['role' => 'admin']) }}" class="px-4 py-2 rounded-lg text-white {{ $role === 'admin' ? 'bg-green-800' : 'bg-green-500 hover:bg-green-600' }}">
                Admins
            </a>
            <a href="{{ route('users.index', ['role' => 'buyer']) }}" class="px-4 py-2 rounded-lg text-white {{ $role === 'buyer' ? 'bg-green-800' : 'bg-green-500 hover:bg-green-600' }}">
                Buyers
            </a>
        </div>

        <!-- Search Bar -->
        <div class="flex mb-4">
            <input type="text" id="search" placeholder="Search by user email" class="px-5 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400">
            <button id="searchBtn" class="ml-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                🔍 Search
            </button>
        </div>

        <!-- User Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full border-collapse border">
                <thead class="bg-green-800 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>

                        @if ($role === 'buyer')  
                            <th class="px-6 py-3 text-left">Phone Number</th>
                        @endif

                        <th class="px-6 py-3 text-left">Role</th>

                        @if ($role === 'admin')  
                            <th class="px-6 py-5 text-left">Provider</th>
                            <th class="px-6 py-5 text-left">Provider ID</th>
                            <th class="px-6 py-5 text-left">Phone Number</th>
                        @endif

                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="user-table-body">
                    @foreach ($users as $user)
                    <tr class="border-b hover:bg-gray-100 transition duration-300">
                        <td class="px-6 py-4 font-semibold text-gray-700">{{ $user->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>

                        @if ($role === 'buyer')
                            <td class="px-6 py-4 text-gray-600">{{ $user->phone_number ?? '-' }}</td>
                        @endif

                        <td class="px-6 py-4 font-medium">{{ ucfirst($user->role) }}</td>

                        @if ($role === 'admin')
                            <td class="px-6 py-4 text-gray-600">{{ $user->provider ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $user->provider_id ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $user->phone_number ?? '-' }}</td>
                        @endif

                        <td class="px-6 py-4 text-center flex justify-center space-x-4">
                            @if ($role === 'admin')
                                <a href="{{ route('users.edit', $user->id) }}" class="px-3 py-2 bg-blue-500 text-white rounded-md shadow-md hover:bg-blue-700 transition">
                                    ✏️ Edit
                                </a>
                                <button onclick="deleteUser({{ $user->id }})" 
                                        class="px-3 py-2 bg-red-500 text-white rounded-md shadow-md hover:bg-red-700 transition">
                                    🗑 Delete
                                </button>
                            @else
                                <button onclick="toggleBan({{ $user->id }})" 
                                    class="px-4 py-2 text-white rounded-md shadow-md transition 
                                    {{ $user->is_banned ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}">
                                    {{ $user->is_banned ? '✅ Unban' : '🚫 Ban' }}
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($users->isEmpty())
            <div class="p-6 text-gray-600 text-center">
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
        let role = "{{ $role }}"; // ✅ Get the role dynamically

        if (query.trim() !== '') {
            window.location.href = "{{ route('users.index') }}?role=" + encodeURIComponent(role) + "&query=" + encodeURIComponent(query);
        }
    });

    function toggleBan(userId) {
        fetch(`/users/${userId}/ban-toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Reload to update the status
            } else {
                alert("Failed to update ban status");
            }
        });
    }
</script>

@endsection
