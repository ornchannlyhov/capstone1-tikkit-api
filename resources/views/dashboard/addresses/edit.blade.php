<!-- ✅ Edit Address Modal -->
<div id="editAddressModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div id="editModalContainer" class="relative w-full max-w-lg">
        <div class="modal-content bg-white rounded-lg shadow-lg p-6">

            <!-- 🔹 Modal Header -->
            <h3 class="text-lg font-semibold text-dark text-center">Edit Address</h3>

            <!-- 🔹 Edit Form -->
            <form id="editAddressForm" method="POST">
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
                <div class="flex justify-end mt-4">
                    <button type="submit"
                        class="px-4 py-2 bg-primary text-white rounded-md hover:bg-green-800 transition">Update Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ✅ JavaScript for Edit Address Modal -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const editModal = document.getElementById("editAddressModal");
    const modalContainer = document.getElementById("editModalContainer");

    // ✅ Open Edit Modal and Populate Data
    window.openEditAddressModal = function (id, street, city, country, venue_name, extra_info, event_id) {
        console.log("Editing Address:", { id, street, city, country, venue_name, extra_info, event_id });

        // Fill form inputs
        document.getElementById("editAddressId").value = id;
        document.getElementById("editStreet").value = street;
        document.getElementById("editCity").value = city;
        document.getElementById("editCountry").value = country;
        document.getElementById("editVenueName").value = venue_name ?? "";
        document.getElementById("editExtraInfo").value = extra_info ?? "";
        document.getElementById("editEventId").value = event_id ?? "";

        // ✅ Set form action dynamically
        let form = document.getElementById("editAddressForm");
        form.action = `{{ route('addresses.update', '') }}/${id}`;

        // Show modal
        editModal.classList.remove("hidden");
    };

    // ✅ Close Modal When Clicking Outside the Form
    editModal.addEventListener("click", function (event) {
        if (!modalContainer.contains(event.target)) {
            editModal.classList.add("hidden");
        }
    });
});
</script>
