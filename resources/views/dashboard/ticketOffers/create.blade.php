@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <div class="flex-1 ml-64 p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">Create Ticket Offer</h1>
            <a href="{{ route('ticketOffers.index') }}" 
                class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('ticketOffers.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Ticket Option Dropdown -->
        <div>
            <label for="ticket_option_id" class="block text-gray-700 font-medium mb-2">Select Ticket Option</label>
            <select name="ticket_option_id" id="ticket_option_id" class="w-full p-3 border rounded-lg focus:ring focus:ring-blue-300" required>
                <option value="">-- Select Ticket Option --</option>
                @foreach ($ticketOptions as $ticketOption)
                    <option value="{{ $ticketOption->id }}" {{ old('ticket_option_id') == $ticketOption->id ? 'selected' : '' }}>
                        {{ $ticketOption->type }}
                    </option>
                @endforeach
            </select>
            @error('ticket_option_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Offer Name -->
        <div>
            <label for="name" class="block text-gray-700 font-medium mb-2">Offer Name</label>
            <input type="text" name="name" id="name" class="w-full p-3 border rounded-lg focus:ring focus:ring-blue-300" value="{{ old('name') }}" required>
            @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Offer Discount -->
        <div>
            <label for="details_discount" class="block text-gray-700 font-medium mb-2">Discount</label>
            <input type="text" name="details[discount]" id="details_discount" class="w-full p-3 border rounded-lg focus:ring focus:ring-blue-300" value="{{ old('details.discount') }}" placeholder="e.g., 20%" required>
            @error('details.discount')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Offer Valid Until -->
        <div>
            <label for="details_valid_until" class="block text-gray-700 font-medium mb-2">Valid Until</label>
            <input type="date" name="details[valid_until]" id="details_valid_until" class="w-full p-3 border rounded-lg focus:ring focus:ring-blue-300" value="{{ old('details.valid_until') }}" required>
            @error('details.valid_until')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Quantity -->
        <div class="md:col-span-2">
            <label for="quantity" class="block text-gray-700 font-medium mb-2">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="w-full p-3 border rounded-lg focus:ring focus:ring-blue-300" min="1" value="{{ old('quantity') }}" required>
            @error('quantity')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- Submit Button -->
    <div class="mt-6 flex justify-end">
        <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300">
            Create Offer
        </button>
    </div>
</form>
    </div>
</div>
@endsection