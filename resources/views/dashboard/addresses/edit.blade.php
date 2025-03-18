      
<!-- ✅ Edit Address Modal -->
<div id="editAddressModal" tabindex="-1" aria-hidden="true"
    class="fixed inset-0 z-50 hidden overflow-y-auto bg-dark bg-opacity-50 flex items-center justify-center">
    <div class="relative w-full max-w-lg">
        <div class="bg-white rounded-lg shadow-lg p-6">

            <!-- 🔹 Close Button -->
            <button type="button" id="closeEditModalBtn"
                class="absolute top-3 right-3 text-gray-400 hover:text-danger transition">
                <i class="fas fa-times"></i>
            </button>

            <!-- 🔹 Modal Header -->
            <h3 class="text-lg font-semibold text-dark text-center">Edit Address</h3>

            <!-- 🔹 Form Validation Errors -->
            @if ($errors->any())
                <div class="p-4 mb-4 text-danger border border-danger rounded-lg bg-red-100">
                    <strong>Whoops!</strong> There were some problems with your input.
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 🔹 Edit Form -->
            <form id="editAddressForm" method="POST" action="">
                @csrf
                @method('PUT')

                <input type="hidden" id="editAddressId" name="address_id">

                <!-- 🔹 Street -->
                <div>
                    <label for="editStreet" class="block text-dark text-sm font-medium">Street</label>
                    <input type="text" id="editStreet" name="street"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required>
                </div>

                <!-- 🔹 City -->
                <div>
                    <label for="editCity" class="block text-dark text-sm font-medium">City</label>
                    <input type="text" id="editCity" name="city"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required>
                </div>

                <!-- 🔹 Country -->
                <div>
                    <label for="editCountry" class="block text-dark text-sm font-medium">Country</label>
                    <input type="text" id="editCountry" name="country"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required>
                </div>

                <!-- 🔹 Venue Name -->
                <div>
                    <label for="editVenueName" class="block text-dark text-sm font-medium">Venue Name</label>
                    <input type="text" id="editVenueName" name="venue_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                </div>

                <!-- 🔹 Extra Info -->
                <div>
                    <label for="editExtraInfo" class="block text-dark text-sm font-medium">Extra Info</label>
                    <textarea id="editExtraInfo" name="extra_info" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"></textarea>
                </div>

                <!-- 🔹 Event Selection -->
                <div>
                    <label for="editEventId" class="block text-dark text-sm font-medium">Event (Optional)</label>
                    <select id="editEventId" name="event_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                        <option value="">Select an event</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 🔹 Buttons -->
                <div class="flex justify-between mt-4">
                    <button type="button" id="closeEditModalBtn2"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-primary text-white rounded-md hover:bg-green-800 transition">Update Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ✅ JavaScript for Handling Modal and Setting Action -->
<script>


</script>

    