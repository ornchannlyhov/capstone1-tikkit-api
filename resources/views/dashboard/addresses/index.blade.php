@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <div class="flex-1 ml-64 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">Address Management</h1>

            <!-- Button to Open Create Modal -->
            <button id="openCreateModalBtn"
                class="px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 flex items-center space-x-2">
                <i class="fas fa-map-marker-alt"></i>
                <span>Add Address</span>
            </button>
        </div>

        <!-- Address Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border table-auto">
                    <thead class="bg-[#030f0f] text-white">
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
                                <!-- Edit Button -->
                                <a href="#" onclick="openEditAddressModal({{ $address->id }}, '{{ $address->street }}', '{{ $address->city }}', '{{ $address->country }}', '{{ $address->venue_name }}', '{{ $address->extra_info }}', '{{ $address->event_id }}')"
                                    class="px-3 py-2 bg-green-600 text-white rounded-md shadow-md hover:bg-blue-700 transition flex items-center space-x-2">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                
                                <!-- Select Event Button -->
                                <select data-id="{{ $address->id }}" class="select-event-dropdown w-44 px-3 py-2 border border-gray -300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                    <option value="">Select an event</option>
                                    @foreach ($events as $event)
                                        <option value="{{ $event->id }}">{{ $event->name }}</option>
                                    @endforeach
                                </select>
                                
                                <!-- Delete Button -->
                                <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="inline">
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
                        @empty
                        <tr>
                            <td colspan="5" class="text-center p-6 text-gray-600">No addresses found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Create and Edit Modals -->z
@include('dashboard.addresses.create')
@include('dashboard.addresses.edit')

<!-- ✅ JavaScript for Modals and Edit Logic -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log("JavaScript Loaded ✅");

    // ✅ Open Create Modal
    document.getElementById('openCreateModalBtn').addEventListener('click', function () {
        document.getElementById('createAddressModal').classList.remove('hidden');
    });

    // ✅ Close Create Modal
    document.getElementById('closeModalBtn').addEventListener('click', function () {
        document.getElementById('createAddressModal').classList.add('hidden');
    });
    document.getElementById('closeModalBtn2').addEventListener('click', function () {
        document.getElementById('createAddressModal').classList.add('hidden');
    });

    // ✅ Open Edit Modal and Populate Data
    window.openEditAddressModal = function (id, street, city, country, venue_name, extra_info, event_id) {
        console.log("Editing Address:", { id, street, city, country, venue_name, extra_info, event_id });

        // Fill form inputs
        document.getElementById('editAddressId').value = id;
        document.getElementById('editStreet').value = street;
        document.getElementById('editCity').value = city;
        document.getElementById('editCountry').value = country;
        document.getElementById('editVenueName').value = venue_name ?? '';
        document.getElementById('editExtraInfo').value = extra_info ?? '';
        document.getElementById('editEventId').value = event_id ?? '';

        // ✅ Set form action dynamically
        let form = document.getElementById('editAddressForm');
        form.action = `{{ route('addresses.update', '') }}/${ll}`; // Corrected form action

        // Show modal
        document.getElementById('editAddressModal').classList.remove('hidden');
    };

    // ✅ Close Edit Modal
    document.getElementById('closeEditModalBtn').addEventListener('click', function () {
        document.getElementById('editAddressModal').classList.add('hidden');
    });
    document.getElementById('closeEditModalBtn2').addEventListener('click', function () {
        document.getElementById('editAddressModal').classList.add('hidden');
    });
});
</script>

@endsection
