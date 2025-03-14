<aside class="w-64 bg-gray-100 h-screen shadow-md fixed top-0 left-0 flex flex-col justify-between">
    <div class="p-4">
        <img src="{{ asset('images/tikit_dark.png') }}" alt="Tikit Logo" class="h-10 mx-auto">
    </div>

    <nav class="flex-1 flex flex-col mt-4">
        <ul class="space-y-2">
            <li class="{{ request()->is('dashboard/home*') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="#" class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>
            <li class="{{ request()->is('dashboard/users*') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="{{ route('users.index') }}" class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            
            <li class="{{ request()->is('dashboard/events*') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="{{ route('events.index') }}" class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-calendar-alt"></i> Events
                </a>
            </li>
            <li class="{{ request()->is('dashboard/addresses*') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="{{ route('addresses.index') }}" class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-map-marker-alt"></i> Addresses
                </a>
            </li>
            <li class="{{ request()->is('dashboard/orders*') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="#" class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-box"></i> Orders
                </a>
            </li>
            <li class="{{ request()->is('dashboard/products*') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="#" class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-cube"></i> Product
                </a>
            </li>
            <li class="{{ request()->is('dashboard/analytics*') ? 'bg-black text-white' : '' }} rounded-md w-3/4 mx-auto">
                <a href="#" class="flex items-center gap-2 text-lg py-2 px-3 hover:bg-gray-300 rounded-md">
                    <i class="fas fa-chart-line"></i> Analytics
                </a>
            </li>
        </ul>
    </nav>

    <div class="p-4">
        <form action="{{ route('admin.logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded w-full text-center hover:bg-green-700">
                Log Out
            </button>
        </form>
    </div>
</aside>
