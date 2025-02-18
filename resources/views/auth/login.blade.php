@extends('layouts.guest')

@section('content')
<div class="w-full max-w-4xl bg-white shadow-lg rounded-lg overflow-hidden flex">
    
    <!-- Left Side: Login Form -->
    <div class="w-1/2 p-12 flex flex-col justify-center">
        <h2 class="text-3xl font-bold text-center text-gray-800">Welcome</h2>
        <p class="text-center text-gray-500 mb-8">Efficiently manage events and tickets with ease.</p>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-gray-600 text-sm font-medium mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-black">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-gray-600 text-sm font-medium mb-1">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-black">
            </div>

            <!-- Submit Button -->
            <div class="mt-4">
                <button type="submit"
                    class="w-full py-2 bg-black text-white rounded-md shadow-md hover:bg-gray-800 transition">
                    Sign in
                </button>
            </div>
        </form>
    </div>

    <!-- Right Side: Styled Pattern -->
    <div class="w-1/2 bg-green-700 flex items-center justify-center">
        <div class="w-4/5 h-4/5 bg-green-600 shadow-lg rounded-lg" style="
            background: repeating-linear-gradient(
                45deg,
                rgba(255, 255, 255, 0.1),
                rgba(255, 255, 255, 0.1) 10px,
                transparent 10px,
                transparent 20px
            );">
        </div>
    </div>

</div>
@endsection
