@props(['action', 'user'])
{{-- {{ dd('Edit blade file is loaded') }} --}}

<!-- ✅ Flowbite Edit User Modal -->
<div id="editUserModal" tabindex="-1" aria-hidden="true"
    class="fixed inset-0 z-50 hidden overflow-y-auto bg-dark bg-opacity-50 flex items-center justify-center">
    <div class="relative w-full max-w-lg">
        <div class="bg-white rounded-lg shadow-lg">

            <!-- 🔹 Close Button -->
            <button type="button" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition"
                data-modal-hide="editUserModal">
                <i class="fas fa-times"></i>
            </button>

            <!-- 🔹 Modal Header -->
            <div class="px-6 py-4 border-b border-gray-300">
                <h3 class="text-lg font-semibold text-dark">Edit User</h3>
            </div>

            <!-- 🔹 Modal Body (Form) -->
            <div class="p-6">
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

                <!-- ✅ User Edit Form -->
                <form action="{{ $action }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-dark text-sm font-medium">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                            required>
                    </div>

                    <div>
                        <label class="block text-dark text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                            required>
                    </div>

                    <div>
                        <label class="block text-dark text-sm font-medium">Phone Number (Optional)</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-1">Role</label>
                        <select name="role"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin
                            </option>
                            <option value="vendor" {{ old('role', $user->role) == 'vendor' ? 'selected' : '' }}>Vendor
                            </option>
                            <option value="buyer" {{ old('role', $user->role) == 'buyer' ? 'selected' : '' }}>Buyer
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-dark text-sm font-medium">New Password (Leave empty to keep current
                            password)</label>
                        <input type="password" name="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                            placeholder="Enter at least 8 characters" minlength="8">
                    </div>

                    <div>
                        <label class="block text-dark text-sm font-medium">Confirm New Password</label>
                        <input type="password" name="password_confirmation"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                            placeholder="Re-enter new password" minlength="8">
                    </div>

                    <button type="submit"
                        class="w-full bg-primary text-white py-2 rounded-md font-semibold text-md shadow-md hover:bg-green-700 transition duration-300">
                        Update User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>