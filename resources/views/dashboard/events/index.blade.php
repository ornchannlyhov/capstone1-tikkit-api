@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <!-- Main Content -->
    <div class="flex-1 ml-64 p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">Events Management</h1>
            <a href="{{ route('events.create') }}"
                class="px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 flex items-center space-x-2">
                <i class="fas fa-calendar-plus"></i>
                <span>Create Event</span>
            </a>
        </div>

        <!-- Search & Filter -->
        <div class="mb-4 w-full">
            <form action="{{ route('events.index') }}" method="GET" class="flex w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by event name"
                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 w-full">
                <button type="submit"
                    class="ml-2 px-6 py-2 bg-[#030f0f] text-white rounded-lg hover:bg-gray-700 flex items-center space-x-2">
                    <i class="fas fa-search"></i>
                    <span>Search</span>
                </button>
            </form>
        </div>

        <!-- Event Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border table-auto">
                    <thead class="bg-[#030f0f] text-white">
                        <tr>
                            <th class="px-3 py-3 sticky left-0 bg-[#030f0f] z-30">Image</th>
                            <th class="px-3 py-3">Event Name</th>
                            <th class="px-3 py-3">Vendor</th>
                            <th class="px-3 py-3">Category</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Start Date</th>
                            <th class="px-3 py-3">End Date</th>
                            <th class="px-3 py-3 text-center sticky right-0 bg-[#030f0f] z-30">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr class="border-b hover:bg-gray-100 transition duration-300">
                            <td>
                                @if($event->image && file_exists(public_path($event->image)))
                                    <img src="{{ asset($event->image) }}" alt="event Image" style="width:80px; height:70px; border-radius:5px;">
                                @else
                                    <img src="{{ asset('images/default-image.jpg') }}" alt="Default Image" style="width:80px; height:70px; border-radius:5px;">
                                @endif
                            </td>

                            <td class="px-3 py-4 font-semibold text-gray-700">{{ $event->name }}</td>
                            <td class="px-3 py-4 text-gray-600">{{ $event->user ? $event->user->name : 'N/A' }}</td>
                            <td class="px-3 py-4 text-gray-600">{{ $event->category ? $event->category->name : 'No Category' }}</td>

                            <!-- Status Column -->
                            <td class="px-2 py-4">
                                @php
                                    $statusColor = '';
                                    $statusText = '';

                                    switch($event->status) {
                                        case 'active':
                                            $statusColor = 'bg-green-500 text-white'; // Active is green
                                            $statusText = 'Active';
                                            break;
                                        case 'upcoming':
                                            $statusColor = 'bg-yellow-500 text-white'; // Upcoming is yellow
                                            $statusText = 'Upcoming';
                                            break;
                                        case 'passed':
                                            $statusColor = 'bg-black text-white'; // Passed is black
                                            $statusText = 'Passed';
                                            break;
                                        case 'delay':
                                            $statusColor = 'bg-gray-500 text-white'; // Delay is gray
                                            $statusText = 'Delayed';
                                            break;
                                        default:
                                            $statusColor = 'bg-gray-500 text-white'; // Default status
                                            $statusText = 'Unknown';
                                    }
                                @endphp
                                <span class="px-4 py-2 rounded-full {{ $statusColor }}">
                                    {{ $statusText }}
                                </span>
                            </td>

                            <td class="px-3 py-4 text-gray-600">
                                {{ \Carbon\Carbon::parse($event->startDate)->format('g:i A, j-F-y') }}
                            </td>
                            <td class="px-3 py-4 text-gray-600">
                                {{ \Carbon\Carbon::parse($event->endDate)->format('g:i A, j-F-y') }}
                            </td>

                            <!-- Action Buttons -->
                            <td class="px-4 py-4 text-center flex justify-center space-x-2">
                                <a href="{{ route('events.show', $event->id) }}" 
                                    class="px-2 py-2 bg-[#030f0f] text-white rounded-md shadow-md hover:bg-gray-700 transition flex items-center space-x-2">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <button type="button" class="px-2 py-2 bg-[#FD2942] text-white rounded-md shadow-md hover:bg-[#e52835] transition flex items-center space-x-2"
                                    onclick="openModal('deleteModal-{{ $event->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Delete Confirmation Modal -->
                        <div id="deleteModal-{{ $event->id }}" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
                            <div class="bg-white rounded-lg shadow-lg w-96">
                                <div class="p-4 border-b">
                                    <h3 class="text-lg font-semibold text-gray-700">Are you sure you want to delete this event?</h3>
                                </div>
                                <div class="flex justify-end gap-4 p-4 border-t">
                                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600"
                                        onclick="closeModal('deleteModal-{{ $event->id }}')">Cancel</button>
                                    <form action="{{ route('events.destroy', $event->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="8" class="px-3 py-4 text-center text-gray-500">No events found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $events->links() }}
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
