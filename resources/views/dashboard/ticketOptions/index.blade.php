@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <!-- Main Content -->
    <div class="flex-1 ml-64 p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">Ticket Options Management</h1>
            <a href="{{ route('ticketOptions.create') }}"
                class="px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Create Ticket Option</span>
            </a>
        </div>

        <!-- Search & Filter -->
        <div class="mb-4 w-full">
            <form action="{{ route('ticketOptions.index') }}" method="GET" class="flex items-center space-x-4">
                <!-- Event Filter -->
                <select name="event_id" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <option value="">All Events</option>
                    @foreach ($events as $event)
                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                        {{ $event->name }}
                    </option>
                    @endforeach
                </select>

                <!-- Sort By Dropdown -->
                <select name="sort_by" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <option value="id" {{ request('sort_by') == 'id' ? 'selected' : '' }}>Default</option>
                    <option value="type" {{ request('sort_by') == 'type' ? 'selected' : '' }}>Type</option>
                    <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
                    <option value="quantity" {{ request('sort_by') == 'quantity' ? 'selected' : '' }}>Quantity</option>
                    <option value="startDate" {{ request('sort_by') == 'startDate' ? 'selected' : '' }}>Start Date</option>
                    <option value="endDate" {{ request('sort_by') == 'endDate' ? 'selected' : '' }}>End Date</option>
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Created At</option>
                    <option value="updated_at" {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>Updated At</option>
                </select>

                <!-- Sort Order Dropdown -->
                <select name="sort_order" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Earliest</option>
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Latest</option>
                </select>

                <!-- Search Input -->
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by type or description"
                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 w-full">

                <!-- Submit Button -->
                <button type="submit"
                    class="px-6 py-2 bg-[#030f0f] text-white rounded-lg hover:bg-gray-700 flex items-center space-x-2">
                    <i class="fas fa-search"></i>
                    <span>Apply</span>
                </button>
            </form>
        </div>

        <!-- Ticket Options Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border table-auto">
                    <thead class="bg-[#030f0f] text-white">
                        <tr>
                            <th>Image</th>
                            <th class="px-3 py-3">Event</th>
                            <th class="px-3 py-3">Type</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Description</th>
                            <th class="px-3 py-3">Price</th>
                            <th class="px-3 py-3">Quantity</th>
                            <th class="px-3 py-3">Start Date</th>
                            <th class="px-3 py-3">End Date</th>
                            <th class="px-3 py-3 text-center sticky right-0 bg-[#030f0f] z-30">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ticketOptions as $ticketOption)
                        <tr class="border-b">
                            <td>
                                <img src="{{ asset('storage/' . $ticketOption->image) }}" alt="Image" style="width:80px; height:70px; border-radius:5px; padding:10px;">
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded">
                                    {{ $ticketOption->event->name }}
                                </span>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">{{ $ticketOption->type }}</td>
                            <td class="px-3 py-3">
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded {{ $ticketOption->is_active ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                    {{ $ticketOption->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-3 py-3">{{ $ticketOption->description }}</td>
                            <td class="px-3 py-3">${{ number_format($ticketOption->price, 2) }}</td>
                            <td class="px-3 py-3">{{ $ticketOption->quantity }}</td>
                            <td class="px-3 py-3 whitespace-nowrap">{{ \Carbon\Carbon::parse($ticketOption->startDate)->format('M d, Y') }}</td>
                            <td class="px-3 py-3 whitespace-nowrap">{{ \Carbon\Carbon::parse($ticketOption->endDate)->format('M d, Y') }}</td>
                            <td class="px-4 py-4 text-center flex justify-center space-x-2 sticky right-0 bg-white z-10">
                                <!-- Edit Button -->
                                <a href="{{ route('ticketOptions.edit', $ticketOption->id) }}"
                                    class="px-2 py-2 bg-[#19b921] text-white rounded-md shadow-md hover:bg-gray-700 transition flex items-center space-x-2">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Delete Button -->
                                <button type="button" class="px-2 py-2 bg-[#FD2942] text-white rounded-md shadow-md hover:bg-[#e52835] transition flex items-center space-x-2"
                                    onclick="openModal('deleteModal-{{ $ticketOption->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Delete Confirmation Modal -->
                        <div id="deleteModal-{{ $ticketOption->id }}" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
                            <div class="bg-white rounded-lg shadow-lg p-6 w-1/3">
                                <h2 class="text-lg font-semibold text-gray-700 mb-4">Delete Confirmation</h2>
                                <p class="text-gray-600 mb-6">Are you sure you want to delete this ticket option?</p>
                                <div class="flex justify-end space-x-4">
                                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                                        onclick="closeModal('deleteModal-{{ $ticketOption->id }}')">Cancel</button>
                                    <form action="{{ route('ticketOptions.destroy', $ticketOption->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 bg-[#FD2942] text-white rounded-md hover:bg-[#e52835]">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-3">No Ticket Options Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination -->
        <div class="mt-4">
            {{ $ticketOptions->links() }}
        </div>
    </div>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>

@endsection
