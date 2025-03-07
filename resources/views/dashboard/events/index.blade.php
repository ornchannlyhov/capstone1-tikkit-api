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
        <div class="flex mb-4 w-full">
            <input type="text" id="search" placeholder="Search by event name"
                class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 w-full">
            <button id="searchBtn"
                class="ml-2 px-6 py-2 bg-[#030f0f] text-white rounded-lg hover:bg-gray-700 flex items-center space-x-2">
                <i class="fas fa-search"></i>
                <span>Search</span>
            </button>
        </div>

        <!-- Event Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border table-auto">
                    <thead class="bg-[#030f0f] text-white">
                        <tr>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Vendor</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Start Date</th>
                            <th class="px-6 py-3 text-left">End Date</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                        <tr class="border-b hover:bg-gray-100 transition duration-300">
                            <td class="px-6 py-4 font-semibold text-gray-700">{{ $event->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $event->vendor }}</td>
                            <td class="px-6 py-4">
                                @include('dashboard.events.components.status-labels', ['status' => $event->status])
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $event->start_date }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $event->end_date }}</td>
                            <td class="px-6 py-4 text-center flex justify-center space-x-4">
                                <a href="{{ route('events.edit', $event->id) }}"
                                    class="px-3 py-2 bg-[#030f0f] text-white rounded-md shadow-md hover:bg-gray-700 transition flex items-center space-x-2">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-2 bg-[#FD2942] text-white rounded-md shadow-md hover:bg-[#e52835] transition flex items-center space-x-2">
                                        <i class="fas fa-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination (Fix for Collection::links() error) -->
        @if ($events instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">
                {{ $events->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>

<script>
    // ✅ Search Functionality
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("searchBtn").addEventListener("click", function () {
            let query = document.getElementById("search").value.trim();
            if (query.length > 0) {
                let url = new URL(window.location.href);
                url.searchParams.set("search", query);
                window.location.href = url.toString();
            }
        });

        // Enable "Enter" keypress for searching
        document.getElementById("search").addEventListener("keypress", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                document.getElementById("searchBtn").click();
            }
        });
    });
</script>
@endsection
