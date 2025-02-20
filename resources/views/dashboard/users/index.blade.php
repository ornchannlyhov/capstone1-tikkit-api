@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen bg-gray-100">
        <div class="flex-1 ml-64 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-700">User Management</h1>
                <a href="{{ route('users.create', request('role', 'vendor')) }}"
                    class="px-4 py-2 bg-[#03624c] text-white rounded-lg hover:bg-[#048d7d] flex items-center space-x-2">
                    <i class="fas fa-user-plus"></i>
                    <span>Add User</span>
                </a>
            </div>

            <!-- Filter Tabs -->
            <div class="flex space-x-4 mb-4 w-full">
                <!-- For 'All' users, which means no role filter -->
                <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-lg text-white flex justify-center items-center space-x-2 w-full
                                {{ !request('role') ? 'bg-green-800' : 'bg-[#030f0f] hover:bg-gray-700' }}">
                    <i class="fas fa-users"></i>
                    <span>All</span>
                </a>
                @foreach (['buyer', 'admin', 'vendor'] as $role)
                    <a href="{{ route('users.index', ['role' => $role]) }}"
                        class="px-4 py-2 rounded-lg text-white flex justify-center items-center space-x-2 w-full
                                                {{ request('role') === $role ? 'bg-green-800' : 'bg-[#030f0f] hover:bg-gray-700' }}">
                        <i class="fas fa-users"></i>
                        <span>{{ ucfirst($role) }}</span>
                    </a>
                @endforeach
            </div>

            <!-- Search Bar -->
            <div class="flex mb-4 w-full">
                <input type="text" id="search" placeholder="Search by user email"
                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 w-full">
                <button id="searchBtn"
                    class="ml-2 px-6 py-2 bg-[#030f0f] text-white rounded-lg hover:bg-gray-700 flex items-center space-x-2">
                    <i class="fas fa-search"></i>
                    <span>Search</span>
                </button>
            </div>

            <!-- User Table -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border table-auto">
                        <thead class="bg-green-800 text-white">
                            <tr>
                                <th class="px-6 py-3 text-left">Name</th>
                                <th class="px-6 py-3 text-left">Email</th>
                                <th class="px-6 py-3 text-left">Phone Number</th>
                                <th class="px-6 py-3 text-left">Role</th>

                                @if (request('role') === 'buyer')
                                    <th class="px-6 py-3 text-left">Provider</th>
                                @endif

                                <th class="px-6 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr class="border-b hover:bg-gray-100 transition duration-300">
                                    <td class="px-6 py-4 font-semibold text-gray-700">{{ $user->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $user->phone_number ?? '-' }}</td>
                                    <td class="px-6 py-4 font-medium">{{ ucfirst($user->role) }}</td>

                                    @if (request('role') === 'buyer')
                                        <td class="px-6 py-4 text-gray-600">{{ $user->provider ?? '-' }}</td>
                                    @endif

                                    <td class="px-6 py-4 text-center flex justify-center space-x-4">
                                        @if (request('role') === 'buyer')
                                            <button id="ban-btn-{{ $user->id }}" onclick="toggleBan({{ $user->id }})"
                                                class="px-4 py-2 text-white rounded-md shadow-md transition flex items-center space-x-2
                                                                                                {{ $user->isBanned() ? 'bg-[#00df82] hover:bg-[#00b35f]' : 'bg-[#FD2942] hover:bg-[#e52835]' }}">
                                                <i class="{{ $user->isBanned() ? 'fas fa-check' : 'fas fa-ban' }}"></i>
                                                <span>{{ $user->isBanned() ? 'Unban' : 'Ban' }}</span>
                                            </button>
                                        @else
                                            <a href="{{ route('users.edit', $user->id) }}"
                                                class="px-3 py-2 bg-[#030f0f] text-white rounded-md shadow-md hover:bg-gray-700 transition flex items-center space-x-2">
                                                <i class="fas fa-edit"></i>
                                                <span>Edit</span>
                                            </a>
                                            <button onclick="deleteUser({{ $user->id }})"
                                                class="px-3 py-2 bg-[#FD2942] text-white rounded-md shadow-md hover:bg-[#e52835] transition flex items-center space-x-2">
                                                <i class="fas fa-trash"></i>
                                                <span>Delete</span>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center p-6 text-gray-600">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $users->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchBtn').addEventListener('click', function () {
            let query = document.getElementById('search').value;
            let role = "{{ request('role') }}";

            if (query.trim() !== '') {
                window.location.href = "{{ route('users.index') }}?role=" + encodeURIComponent(role) + "&search=" + encodeURIComponent(query);
            }
        });

        function toggleBan(userId) {
            fetch(`/dashboard/users/${userId}/toggle-ban`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let button = document.getElementById(`ban-btn-${userId}`);
                        let icon = button.querySelector("i");
                        let text = button.querySelector("span");

                        if (data.is_banned) {
                            button.classList.remove('bg-[#FD2942]');
                            button.classList.add('bg-[#00df82]');
                            icon.classList.replace('fa-ban', 'fa-check');
                            text.textContent = "Unban";
                        } else {
                            button.classList.remove('bg-[#00df82]');
                            button.classList.add('bg-[#FD2942]');
                            icon.classList.replace('fa-check', 'fa-ban');
                            text.textContent = "Ban";
                        }
                    }
                });
        }

        function deleteUser(userId) {
            fetch(`/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
        }
    </script>
@endsection