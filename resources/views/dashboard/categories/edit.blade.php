@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <div class="flex-1 ml-64 p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">Edit Category</h1>
        </div>

        <div class="flex gap-4 mt-3 mb-3">
            <a href="{{ route('categories.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 flex items-center space-x-2">
                <i class="fas fa-chevron-left"></i>
                <span>Back</span>
            </a>
        </div>

        <form action="{{ route('categories.update', $category->id) }}" method="POST" class="box-shadow rounded-lg p-6 bg-white">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Category Name -->
                <div>
                    <label for="name" class="block text-gray-700">Category Name</label>
                    <input type="text" id="name" name="name" class="w-full p-2 border rounded-lg" value="{{ old('name', $category->name) }}" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="col-span-2">
                    <button type="submit" class="w-full bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-800 flex items-center justify-center space-x-2">
                        <i class="fas fa-save"></i>
                        <span>Save</span>
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

@endsection
