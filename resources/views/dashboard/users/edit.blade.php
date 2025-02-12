@extends('dashboard.layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Edit User</h1>
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block text-gray-700">Name</label>
            <input type="text" name="name" value="{{ $user->name }}" class="w-full px-4 py-2 border rounded">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" value="{{ $user->email }}" class="w-full px-4 py-2 border rounded">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Password (leave blank to keep current)</label>
            <input type="password" name="password" class="w-full px-4 py-2 border rounded">
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
    </form>
</div>
@endsection