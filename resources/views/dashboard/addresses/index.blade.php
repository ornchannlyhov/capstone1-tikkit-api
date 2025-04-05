@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <div class="flex-1 ml-64 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-dark">Address Management</h1>

            <!-- ✅ Button to Open Create Modal -->
            <button id="openCreateModalBtn"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-green-800 flex items-center space-x-2">
                <i class="fas fa-map-marker-alt"></i>
                <span>Add Address</span>
            </button>
        </div>

        <!-- ✅ Address Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border table-auto">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="px-6 py-3 text-left">Street</th>
                            <th class="px-6 py-3 text-left">City</th>
                            <th class="px-6 py-3 text-left">Country</th>
                            <th class="px-6 py-3 text-left">Event</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($addresses as $address)
                        <tr id="address-row-{{ $address->id }}" class="border-b hover:bg-gray-100 transition duration-300">
                            <td class="px-6 py-4 font-semibold text-gray-700">{{ $address->street }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $address->city }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $address->country }}</td>
                            <td class="px-6 py-4 font-medium">{{ $address->event->name ?? 'Unassigned' }}</td>
                            <td class="px-6 py-4 text-center flex justify-center space-x-4">
                                <!-- ✅ Edit Button -->
                                <a href="#" onclick="openEditAddressModal({{ $address->id }}, '{{ $address->street }}', '{{ $address->city }}', '{{ $address->country }}', '{{ $address->venue_name }}', '{{ $address->extra_info }}', '{{ $address->event_id }}')"
                                    class="px-3 py-2 bg-[#19b921] text-white rounded-md shadow-md hover:bg-green-800 transition flex items-center space-x-2">
                                    <i class="fas fa-edit"></i>
                                  
                                </a>
                                
                                <!-- ✅ Select Event Dropdown -->
                                <select data-id="{{ $address->id }}" class="select-event-dropdown w-44 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-info focus:border-info transition duration-200">
                                    <option value="">Select an event</option>
                                    @foreach ($events as $event)
                                        <option value="{{ $event->id }}">{{ $event->name }}</option>
                                    @endforeach
                                </select>
                                
                                <!-- ✅ Delete Button -->
                                <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-2 bg-danger text-white rounded-md shadow-md hover:bg-red-700 transition flex items-center space-x-2">
                                        <i class="fas fa-trash"></i>
                                       
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center p-6 text-gray-600">No addresses found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="mt-4">
            {{ $addresses->links() }}
        </div>
    </div>
</div>

<!-- Include Create and Edit Modals -->
@include('dashboard.addresses.create')
@include('dashboard.addresses.edit')

    
@endsection
