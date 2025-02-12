<aside class="w-64 bg-white h-screen shadow-md">
    <div class="p-4">
        <h2 class="text-lg font-bold">Admin V1.0.1</h2>
    </div>
    <nav class="mt-4">
        <ul>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="#" class="block">🏠 Home</a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="#" class="block">📦 Orders</a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="#" class="block">🛍️ Product</a>
            </li>
            <li class="py-2 px-4 bg-black text-white">
                <a href="{{ route('users.index') }}" class="block">👤 Users</a>
            </li>
            <li class="py-2 px-4 hover:bg-gray-200">
                <a href="#" class="block">📊 Analytics</a>
            </li>
        </ul>
    </nav>
    <div class="absolute bottom-4 left-4">
        <a href="#" class="px-4 py-2 bg-green-500 text-white rounded">Log out</a>
    </div>
</aside>
