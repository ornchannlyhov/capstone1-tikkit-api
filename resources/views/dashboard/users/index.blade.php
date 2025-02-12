@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-4">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">User Management</h2>

        <!-- Search & Add Button -->
        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="{{ route('users.index') }}">
                <input type="text" name="search" value="{{ request('search') }}" class="border rounded-md px-4 py-2 w-full" placeholder="Search by user email">
            </form>
            <a href="{{ route('users.create') }}" class="ml-4 bg-green-500 text-white px-4 py-2 rounded-md flex items-center">
                <span class="mr-2">➕</span> Add User
            </a>
        </div>

        <!-- User Table -->
        <table class="w-full border-collapse bg-white shadow-md rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-2 px-4 border">Email</th>
                    <th class="py-2 px-4 border">Firstname</th>
                    <th class="py-2 px-4 border">Lastname</th>
                    <th class="py-2 px-4 border">Gender</th>
                    <th class="py-2 px-4 border">Role</th>
                    <th class="py-2 px-4 border">Phone</th>
                    <th class="py-2 px-4 border">Actions</th>
                </tr>
            </thead>
            <tbody>
         
                @foreach ($users as $user)
                <tr>
                    <td class="py-2 px-4 border">{{ $user->email }}</td>
                    <td class="py-2 px-4 border">{{ $user->firstname }}</td>
                    <td class="py-2 px-4 border">{{ $user->lastname }}</td>
                    <td class="py-2 px-4 border">{{ $user->gender }}</td>
                    <td class="py-2 px-4 border">{{ ucfirst($user->role) }}</td>
                    <td class="py-2 px-4 border">{{ $user->phone_number }}</td>
                    <td class="py-2 px-4 border">
                        <a href="{{ route('users.edit', $user->id) }}" class="bg-blue-500 text-white px-2 py-1 rounded">Edit</a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
