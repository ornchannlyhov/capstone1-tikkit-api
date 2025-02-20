<div class="bg-white text-white flex justify-between items-center px-6 py-3">
    <div class="text-lg font-semibold">
        Dashboard | <span class="text-white-300">{{ ucfirst(request()->segment(2) ?? 'Home') }}</span>
    </div>

    <div class="flex items-center space-x-4">

        <!-- Profile & Logout -->
        @if (Auth::check())
            <span class="text-gray-300">Welcome, {{ Auth::user()->firstname }}</span>
        @else

        @endif

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            {{-- <button type="submit" class="px-3 py-2 bg-green-600 rounded hover:bg-red-500">Logout</button> --}}
        </form>
    </div>
</div>