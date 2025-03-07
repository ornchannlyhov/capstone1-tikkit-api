@props(['action', 'role' => 'vendor']) <!-- Set default role to 'vendor' -->


<!-- ✅ Flowbite Modal -->
<div id="userModal" tabindex="-1" aria-hidden="true"
    class="fixed inset-0 z-50 hidden overflow-y-auto bg-dark bg-opacity-50 flex items-center justify-center">
    <div class="relative w-full max-w-lg">
        <div class="bg-white rounded-lg shadow-lg">

            <!-- 🔹 Close Button -->
            <button type="button" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition"
                data-modal-hide="userModal">
                <i class="fas fa-times"></i>
            </button>

            <!-- 🔹 Modal Header -->
            <div class="px-6 py-4 border-b border-gray-300">
                <h3 class="text-lg font-semibold text-dark">Create New User</h3>
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

                <!-- ✅ User Form -->
                <form action="{{ $action }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-dark text-sm font-medium">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                            required>
                    </div>

                    <div>
                        <label class="block text-dark text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                            required>
                    </div>

                    <div>
                        <label class="block text-dark text-sm font-medium">Phone Number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                            required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-1">Role</label>
                        <select name="role"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                            <option value="admin" {{ old('role', $role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="vendor" {{ old('role', $role) == 'vendor' ? 'selected' : '' }}>Vendor</option>
                            <option value="buyer" {{ old('role', $role) == 'buyer' ? 'selected' : '' }}>Buyer</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-dark text-sm font-medium">Password</label>
                        <input type="password" name="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                            required>
                    </div>

                    <div>
                        <label class="block text-dark text-sm font-medium">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                            required>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary text-white py-2 rounded-md font-semibold text-md shadow-md hover:bg-green-700 transition duration-300">
                        Create User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>