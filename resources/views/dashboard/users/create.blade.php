@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-4">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Add New User</h2>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <input type="text" name="firstname" class="border rounded-md px-4 py-2 w-full mb-2" placeholder="Firstname" required>
            <input type="text" name="lastname" class="border rounded-md px-4 py-2 w-full mb-2" placeholder="Lastname" required>
            <input type="email" name="email" class="border rounded-md px-4 py-2 w-full mb-2" placeholder="Email" required>
            <input type="text" name="phone_number" class="border rounded-md px-4 py-2 w-full mb-2" placeholder="Phone Number" required>
            <select name="role" class="border rounded-md px-4 py-2 w-full mb-2">
                <option value="admin">Admin</option>
                <option value="vendor">Vendor</option>
                <option value="user">User</option>
            </select>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md">Create User</button>
        </form>
    </div>
</div>
@endsection
