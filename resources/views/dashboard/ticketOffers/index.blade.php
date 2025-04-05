@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <div class="flex-1 ml-64 p-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">Ticket Offers Management</h1>
            <a href="{{ route('ticketOffers.create') }}"
                class="px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Create Ticket Offer</span>
            </a>
        </div>

        <!-- Search & Filter -->
        <div class="mb-4 w-full">
            <form action="{{ route('ticketOffers.index') }}" method="GET" class="flex items-center space-x-4">
                <!-- Ticket Option Filter -->
                <select name="ticket_option_id" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <option value="">All Ticket Options</option>
                    @foreach ($ticketOptions as $ticketOption)
                        <option value="{{ $ticketOption->id }}" {{ request('ticket_option_id') == $ticketOption->id ? 'selected' : '' }}>
                            {{ $ticketOption->type }}
                        </option>
                    @endforeach
                </select>

                <!-- Sort By Dropdown -->
                <select name="sort_by" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <option value="id" {{ request('sort_by') == 'id' ? 'selected' : '' }}>Default</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="quantity" {{ request('sort_by') == 'quantity' ? 'selected' : '' }}>Quantity</option>
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Created At</option>
                    <option value="updated_at" {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>Updated At</option>
                </select>

                <!-- Sort Order Dropdown -->
                <select name="sort_order" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                </select>

                <!-- Search Input -->
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name"
                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 w-full">

                <!-- Submit Button -->
                <button type="submit"
                    class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 flex items-center space-x-2">
                    <i class="fas fa-search"></i>
                    <span>Apply</span>
                </button>
            </form>
        </div>

        <!-- Ticket Offers Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full border-collapse border table-auto">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Discount</th>
                        <th class="px-4 py-2">Valid Until</th>
                        <th class="px-4 py-2">Quantity</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ticketOffers as $ticketOffer)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $ticketOffer->name }}</td>
                            <td class="px-4 py-2">
                                @php
                                    $details = json_decode($ticketOffer->details, true);
                                @endphp
                                {{ $details['discount'] ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $details = json_decode($ticketOffer->details, true);
                                @endphp
                                {{ $details['valid_until'] ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-2">{{ $ticketOffer->quantity }}</td>
                            <td class="px-4 py-2 flex space-x-2">
                                <a href="{{ route('ticketOffers.edit', $ticketOffer->id) }}" class="px-2 py-1 bg-blue-500 text-white rounded">Edit</a>
                                <form action="{{ route('ticketOffers.destroy', $ticketOffer->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No ticket offers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $ticketOffers->links() }}
        </div>
    </div>
</div>
@endsection