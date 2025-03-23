@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <div class="flex-1 ml-64 p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">Edit Event</h1>
        </div>

        <div class="flex gap-4 mt-3 mb-3">
            <a href="{{ route('events.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 flex items-center space-x-2">
                <i class="fas fa-chevron-left"></i>
                <span>Back</span>
            </a>
        </div>

        <form action="{{ route('events.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="box-shadow rounded-lg p-6 bg-white">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              
                <div>
                    <label class="block text-gray-700">Event Name</label>
                    <input type="text" name="name" class="w-full p-2 border rounded-lg" value="{{ $event->name }}" required>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-gray-700">Category</label>
                    <select name="category_id" class="w-full p-2 border rounded-lg">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $event->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700">Start Date</label>
                    <input type="datetime-local" name="startDate" class="w-full p-2 border rounded-lg" value="{{ \Carbon\Carbon::parse($event->startDate)->format('Y-m-d\TH:i') }}" required>
                </div>
       
                <div>
                    <label class="block text-gray-700">End Date</label>
                    <input type="datetime-local" name="endDate" class="w-full p-2 border rounded-lg" value="{{ \Carbon\Carbon::parse($event->endDate)->format('Y-m-d\TH:i') }}" required>
                </div>

                <div class="col-span-2">
                    <label class="block text-gray-700">Description</label>
                    <textarea name="description" class="w-full p-2 border rounded-lg">{{ $event->description }}</textarea>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-gray-700">Status</label>
                    <select name="status" class="w-full p-2 border rounded-lg">
                        <option value="upcoming" {{ $event->status == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="active" {{ $event->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="passed" {{ $event->status == 'passed' ? 'selected' : '' }}>Passed</option>
                        <option value="delay" {{ $event->status == 'delay' ? 'selected' : '' }}>Delay</option>
                    </select>
                </div>

                <!-- Image Upload -->
                <div>
                    <label for="image" class="block text-gray-700 font-medium mb-2">Event Image</label>
                    <div class="relative w-full h-64 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:bg-gray-50">
                        <input type="file" id="image" name="image" class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewImage(event)">
                        <div id="imagePreview" class="flex items-center justify-center">
                            <img id="imageDisplay" src="{{ asset($event->image) }}?{{ time() }}" alt="Current Image" class="w-full h-full object-cover rounded-lg" style="height: 300px; width:100%;">
                            <span class="text-gray-400" id="placeholder" style="display: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V7M3 7L10 3M21 7L14 3M14 3V7M10 3V7M5 19H19" />
                                </svg>
                                Upload Image
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex gap-4 mt-6 px-5">
                <button type="submit" class="px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Save</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const imageDisplay = document.getElementById('imageDisplay');
        const placeholder = document.getElementById('placeholder');
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imageDisplay.src = e.target.result; // Update image 
                imageDisplay.classList.remove('hidden');
                placeholder.style.display = 'none'; // Hide placeholder
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection