@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen bg-gray-100">
        <div class="flex-1 ml-64 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-700">User Management</h1>

                <!-- ✅ Button to Open Modal -->
                <button data-modal-target="userModal" data-modal-toggle="userModal"
                    class="px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 flex items-center space-x-2">
                    <i class="fas fa-user-plus"></i>
                    <span>Add User</span>
                </button>
            </div>
            @include('dashboard.users.create', ['action' => route('users.store', ['role' => request('role', 'vendor')])])
            @include('dashboard.users.edit', ['action' => '', 'user' => new \App\Models\User()])

            <!-- Filter Tabs -->
            <div class="flex space-x-4 mb-4 w-full">
                <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-lg text-white flex justify-center items-center space-x-2 w-full
                        {{ !request('role') ? 'bg-green-800' : 'bg-[#030f0f] hover:bg-gray-700' }}">
                    <i class="fas fa-users"></i>
                    <span>All</span>
                </a>
                @foreach (['buyer', 'admin', 'vendor'] as $role)
                    <a href="{{ route('users.index', ['role' => $role]) }}" class="px-4 py-2 rounded-lg text-white flex justify-center items-center space-x-2 w-full
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

            <!-- ✅ User Table -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border table-auto">
                        <thead class="bg-[#030f0f] text-white">
                            <tr>
                                <th class="px-6 py-3 text-left">Name</th>
                                <th class="px-6 py-3 text-left">Email</th>
                                <th class="px-6 py-3 text-left">Phone Number</th>
                                <th class="px-6 py-3 text-left">Role</th>
                                <th class="px-6 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr id="user-row-{{ $user->id }}" class="border-b hover:bg-gray-100 transition duration-300">
                                    <td class="px-6 py-4 font-semibold text-gray-700">{{ $user->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $user->phone_number ?? '-' }}</td>
                                    <td class="px-6 py-4 font-medium">{{ ucfirst($user->role) }}</td>
                                    <td class="px-6 py-4 text-center flex justify-center space-x-4">
                                        @if (request('role') === 'buyer')
                                            <button id="ban-btn-{{ $user->id }}" onclick="toggleBan({{ $user->id }})"
                                                class="px-4 py-2 text-white rounded-md shadow-md transition flex items-center space-x-2
                                                        {{ $user->isBanned() ? 'bg-[#00df82] hover:bg-[#00b35f]' : 'bg-[#FD2942] hover:bg-[#e52835]' }}">
                                                <i class="{{ $user->isBanned() ? 'fas fa-check' : 'fas fa-ban' }}"></i>
                                                <span>{{ $user->isBanned() ? 'Unban' : 'Ban' }}</span>
                                            </button>
                                        @else
                                            <a href="#"
                                                onclick="openEditUserModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->phone_number }}', '{{ $user->role }}')"
                                                class="px-3 py-2 bg-[#030f0f] text-white rounded-md shadow-md hover:bg-gray-700 transition flex items-center space-x-2">
                                                <i class="fas fa-edit"></i>
                                                <span>Edit</span>
                                            </a>

                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-2 bg-[#FD2942] text-white rounded-md shadow-md hover:bg-[#e52835] transition flex items-center space-x-2">
                                                    <i class="fas fa-trash"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </form>
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

                        let message = "";

                        if (data.is_banned) {
                            button.classList.replace('bg-[#FD2942]', 'bg-[#00df82]');
                            button.classList.replace('hover:bg-[#e52835]', 'hover:bg-[#00b35f]');
                            icon.classList.replace('fa-ban', 'fa-check');
                            text.textContent = "Unban";
                            message = "User has been banned successfully.";
                        } else {
                            button.classList.replace('bg-[#00df82]', 'bg-[#FD2942]');
                            button.classList.replace('hover:bg-[#00b35f]', 'hover:bg-[#e52835]');
                            icon.classList.replace('fa-check', 'fa-ban');
                            text.textContent = "Ban";
                            message = "User has been unbanned successfully.";
                        }

                        // Show success message
                        showSuccessAlert(message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // ✅ Function to Show Success Alert
        function showSuccessAlert(message) {
            let alertBox = document.createElement('div');
            alertBox.id = "success-alert";
            alertBox.className = "fixed top-4 left-1/2 transform -translate-x-1/2 z-50 flex items-center p-4 text-green-800 border border-green-300 rounded-lg bg-green-100 dark:bg-green-200 dark:text-green-900 shadow-lg opacity-100 transition-all duration-500 ease-in-out";

            alertBox.innerHTML = `
            <svg class="w-5 h-5 text-green-700 dark:text-green-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-3a1 1 0 1 0-2 0v4a1 1 0 0 0 2 0V7Zm-1 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
            </svg>
            <span class="ml-3 text-sm font-medium">${message}</span>
        `;

            document.body.appendChild(alertBox);

            // ✅ Hide and remove after 3 seconds
            setTimeout(() => {
                alertBox.style.transition = "opacity 0.5s, transform 0.5s ease-in-out";
                alertBox.style.opacity = "0";
                alertBox.style.transform = "translate(-50%, -20px)";
                setTimeout(() => alertBox.remove(), 500);
            }, 3000);
        }



        document.getElementById("searchBtn").addEventListener("click", function () {
            let query = document.getElementById("search").value.trim();
            if (query.length > 0) {
                let url = new URL(window.location.href);
                url.searchParams.set("search", query);
                window.location.href = url.toString();
            }
        });


        document.addEventListener('DOMContentLoaded', function () {
            // ✅ Open Edit Modal and Populate Data
            window.openEditUserModal = function (id, name, email, phone, role) {
                document.getElementById('editUserModal').classList.remove('hidden');

                // Populate form fields with user data
                document.querySelector('#editUserModal input[name="name"]').value = name;
                document.querySelector('#editUserModal input[name="email"]').value = email;
                document.querySelector('#editUserModal input[name="phone_number"]').value = phone || '';
                document.querySelector('#editUserModal select[name="role"]').value = role;

                // Update form action with correct user ID
                let form = document.querySelector('#editUserModal form');
                form.action = `/dashboard/users/${id}`; // Update this to match your route
            };

            // ✅ Close Modal on Button Click
            document.querySelectorAll('[data-modal-hide="editUserModal"]').forEach(button => {
                button.addEventListener('click', function () {
                    document.getElementById('editUserModal').classList.add('hidden');
                });
            });

            // ✅ Close Modal When Clicking Outside
            document.getElementById('editUserModal').addEventListener('click', function (event) {
                if (event.target === this) {
                    this.classList.add('hidden');
                }
            });
        });


    </script>

@endsection