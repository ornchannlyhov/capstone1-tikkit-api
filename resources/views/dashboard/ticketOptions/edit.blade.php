@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <!-- Main Content -->
    <div class="flex-1 ml-64 p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">Edit Ticket Option</h1>
        </div>
        <div class="flex gap-4 mt-3 mb-3">
            <a href="{{ route('ticketOptions.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 flex items-center space-x-2">
                <i class="fas fa-chevron-left"></i>
                <span>Back</span>
            </a>
        </div>
        <form action="{{ route('ticketOptions.update', $ticketOption->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Type -->
                <div>
                    <label class="block text-gray-700">Type</label>
                    <input type="text" name="type" value="{{ $ticketOption->type }}" class="w-full p-2 border rounded-lg" required>
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-gray-700">Price</label>
                    <input type="number" step="0.01" name="price" value="{{ $ticketOption->price }}" class="w-full p-2 border rounded-lg" required>
                </div>

                <!-- Event -->
                <div>
                    <label class="block text-gray-700">Event</label>
                    <select name="event_id" class="w-full p-2 border rounded-lg">
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" {{ $ticketOption->event_id == $event->id ? 'selected' : '' }}>
                                {{ $event->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block text-gray-700">Quantity</label>
                    <input type="number" name="quantity" value="{{ $ticketOption->quantity }}" class="w-full p-2 border rounded-lg" required>
                </div>

                <!-- Start Date -->
                <div>
                    <label class="block text-gray-700">Start Date</label>
                    <input type="datetime-local" name="startDate" value="{{ $ticketOption->startDate }}" class="w-full p-2 border rounded-lg" required>
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-gray-700">End Date</label>
                    <input type="datetime-local" name="endDate" value="{{ $ticketOption->endDate }}" class="w-full p-2 border rounded-lg" required>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-gray-700">Status</label>
                    <select name="is_active" class="w-full p-2 border rounded-lg">
                        <option value="1" {{ $ticketOption->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$ticketOption->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="col-span-2">
                    <label class="block text-gray-700">Description</label>
                    <textarea name="description" class="w-full p-2 border rounded-lg">{{ $ticketOption->description }}</textarea>
                </div>

                <!-- Refund Policy -->
                <div class="col-span-2">
                    <label class="block text-gray-700">Refund Policy</label>
                    <textarea name="refund_policy" class="w-full p-2 border rounded-lg">{{ $ticketOption->refund_policy }}</textarea>
                </div>

                <!-- Image Upload -->
                <div>
                    <label for="image" class="block text-gray-700 font-medium mb-2">Image</label>
                    <div
                        class="relative w-full h-64 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:bg-gray-50">
                        <input type="file" id="image" name="image"
                            class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewImage(event)">
                        <div id="imagePreview" class="flex items-center justify-center">
                            @if ($ticketOption->image)
                                <img id="imageDisplay" src="{{ asset('storage/' . $ticketOption->image) }}" alt="Image Preview"
                                    class="w-full h-full object-cover rounded-lg" style="height:300px">
                            @else
                                <span class="text-gray-400" id="placeholder">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-16 w-16 mx-auto text-gray-300" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V7M3 7L10 3M21 7L14 3M14 3V7M10 3V7M5 19H19" />
                                    </svg>
                                    Upload Image
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4 mt-6 items-center">
                <button type="submit" class="px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Update</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const imagePreview = document.getElementById('imagePreview');
        const imageDisplay = document.getElementById('imageDisplay');
        const placeholder = document.getElementById('placeholder');
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imageDisplay.src = e.target.result;
                imageDisplay.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection