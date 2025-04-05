<aside class="w-64 bg-gray-100 h-screen shadow-md fixed top-0 left-0 flex flex-col justify-between">
    <div class="p-4">
        <img src="{{ asset('images/tikit_dark.png') }}" alt="Tikit Logo" class="h-10 mx-auto">
    </div>

    <nav class="flex-1 flex flex-col mt-4">
        <ul class="space-y-2">
            <li class="{{ request()->is('dashboard/home*') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="{{ route('admin.reports.event_stats') }}"
                    class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>

            <li class="{{ request()->is('dashboard/users*') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="{{ route('users.index') }}"
                    class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            <li
                class="{{ request()->is('dashboard/categories*') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="{{ route('categories.index') }}"
                    class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-tags"></i> Categories
                </a>
            </li>
            <li class="{{ request()->is('dashboard/events*') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="{{ route('events.index') }}"
                    class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-calendar-alt"></i> Events
                </a>
            </li>
            <li
                class="{{ request()->is('dashboard/addresses*') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="{{ route('addresses.index') }}"
                    class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-map-marker-alt"></i> Addresses
                </a>
            </li>
            <li class="{{ request()->is('dashboard/payment*') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="{{ route('admin.payments.index') }}"
                    class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-money-bill"></i> Payments
                </a>
            </li>
            <li class="{{ request()->is('dashboard/orders*') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="#" class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-box"></i> Orders
                </a>
            </li>
            <li
                class="{{ request()->is('dashboard/products*') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="#" class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-cube"></i> Product
                </a>
            </li>


            <!-- New Activity & Transaction Logs Menu Items -->
            <li class="{{ request()->is('transaction-logs') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="{{ route('admin.transaction_logs') }}"
                    class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-file-alt"></i> Transaction
                </a>
            </li>
            <li class="{{ request()->is('activity-logs') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="{{ route('admin.activity_logs') }}"
                    class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-list"></i> Activity Logs
                </a>
            </li>

        </ul>
    </nav>

    <div class="p-4">

        <button type="button" onclick="openLogoutModal()"
            class="px-4 py-2 bg-green-600 text-white rounded w-full text-center hover:bg-green-700">
            Log Out
        </button>

        <!-- Hidden Logout Form -->
        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <!-- Logout Modal -->
        <div id="logoutModal"
            class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-[9999] hidden">
            <div class="bg-white rounded-lg shadow-lg p-6 w-80 z-[10000]">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Are you sure you want to log out?</h2>
                <div class="flex justify-end gap-4">
                    <button type="button" onclick="closeLogoutModal()"
                        class="px-4 py-2 text-gray-600 border border-gray-400 rounded hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="button" onclick="confirmLogout()"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Yes
                    </button>
                </div>
            </div>
        </div>


        <!-- JS (place just before </body> or at bottom of file) -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.openLogoutModal = function () {
                    document.getElementById('logoutModal').classList.remove('hidden');
                }

                window.closeLogoutModal = function () {
                    document.getElementById('logoutModal').classList.add('hidden');
                }

                window.confirmLogout = function () {
                    document.getElementById('logout-form').submit();
                }
            });
        </script>


    </div>
</aside>