<aside class="w-64 bg-white h-screen shadow-md fixed top-0 left-0">
    <div class="p-4">
        <h2 class="text-lg font-bold">Admin V1.0.1</h2>
    </div>

    <nav class="mt-4">
        <ul>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="#" class="block">🏠 Home</a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="{{ route('users.index') }}" class="block {{ request()->is('admin/users*') ? 'bg-black text-white' : '' }}">
                    👥 Users
                </a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="{{ route('events.index') }}" class="block {{ request()->is('admin/events*') ? 'bg-black text-white' : '' }}">
                    📅 Events
                </a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="{{ route('addresses.index') }}" class="block {{ request()->is('admin/addresses*') ? 'bg-black text-white' : '' }}">
                    📍 Addresses
                </a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="#" class="block">📦 Orders</a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="#" class="block">🛍️ Product</a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="#" class="block">📊 Analytics</a>
            </li>
        </ul>
    </nav>

    <div class="absolute bottom-4 left-4">
        <a href="{{ route('logout') }}" class="px-4 py-2 bg-red-500 text-white rounded">Log Out</a>
    </div>
</aside>
