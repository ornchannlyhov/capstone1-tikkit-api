<!-- Create Address Modal -->
<div id="createAddressModal" tabindex="-1" aria-hidden="true"
    class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="relative w-full max-w-lg">
        <div class="bg-white rounded-lg shadow-lg p-6">  

            <!-- 🔹 Close Button -->
            <button type="button" id="closeModalBtn"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times"></i>
            </button>

            <!-- 🔹 Modal Header -->
            <h3 class="text-lg font-semibold text-gray-700 text-center">Add New Address</h3>

            <!-- 🔹 Form Validation Errors -->
            @if ($errors->any())
                <div class="p-4 mb-4 text-red-700 border border-red-400 rounded-lg bg-red-100">
                    <strong>Whoops!</strong> There were some problems with your input.
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 🔹 Address Form -->
            <form action="{{ route('addresses.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Street -->
                <div>
                    <label for="street" class="block text-gray-700 text-sm font-medium">Street</label>
                    <input type="text" name="street" id="street" placeholder="Street"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" required>
                </div>

                <!-- City -->
                <div>
                    <label for="city" class="block text-gray-700 text-sm font-medium">City</label>
                    <input type="text" name="city" id="city" placeholder="City"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" required>
                </div>

                <!-- Country -->
                <div>
                    <label for="country" class="block text-gray-700 text-sm font-medium">Country</label>
                    <input type="text" name="country" id="country" placeholder="Country"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" required>
                </div>

                <!-- Venue Name -->
                <div>
                    <label for="venue_name" class="block text-gray-700 text-sm font-medium">Venue Name</label>
                    <input type="text" name="venue_name" id="venue_name" placeholder="Venue Name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                </div>

                <!-- Extra Info -->
                <div>
                    <label for="extra_info" class="block text-gray-700 text-sm font-medium">Extra Info</label>
                    <textarea name="extra_info" id="extra_info" rows="3" placeholder="Additional details..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"></textarea>
                </div>

                <!-- Event Selection -->
                <div>
                    <label for="event_id" class="block text-gray-700 text-sm font-medium">Event (Optional)</label>
                    <select name="event_id" id="event_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        <option value="">Select an event</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex justify-between">
                    <button type="button" id="closeModalBtn2"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('openModalBtn').addEventListener('click', function () {
        document.getElementById('createAddressModal').classList.remove('hidden');
    });

    document.getElementById('closeModalBtn').addEventListener('click', function () {
        document.getElementById('createAddressModal').classList.add('hidden');
    });

    document.getElementById('closeModalBtn2').addEventListener('click', function () {
        document.getElementById('createAddressModal').classList.add('hidden');
    });
</script>
