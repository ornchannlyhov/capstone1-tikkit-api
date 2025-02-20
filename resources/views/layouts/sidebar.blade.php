<aside class="w-64 bg-white h-screen shadow-md fixed top-0 left-0">
    <div class="p-4">
        <h1 class="text-lg font-bold">Tikit</h1>
    </div>

    <nav class="mt-4">
        <ul>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="#" class="block">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="{{ route('users.index') }}"
                    class="block {{ request()->is('admin/users*') ? 'bg-[#030f0f] text-white' : '' }}">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="#" class="block">
                    <i class="fas fa-cogs"></i> Vendor
                </a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="{{ route('events.index') }}"
                    class="block {{ request()->is('admin/events*') ? 'bg-[#030f0f] text-white' : '' }}">
                    <i class="fas fa-calendar-alt"></i> Events
                </a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="{{ route('addresses.index') }}"
                    class="block {{ request()->is('admin/addresses*') ? 'bg-[#030f0f] text-white' : '' }}">
                    <i class="fas fa-map-marker-alt"></i> Addresses
                </a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="#" class="block">
                    <i class="fas fa-box"></i> Orders
                </a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="#" class="block">
                    <i class="fas fa-cube"></i> Product
                </a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="#" class="block">
                    <i class="fas fa-chart-line"></i> Analytics
                </a>
            </li>
        </ul>
    </nav>

    <div class="absolute bottom-4 left-4">
        <a href="{{ route('logout') }}" class="px-4 py-2 bg-green-500 text-white rounded">Log Out</a>
    </div>
</aside>