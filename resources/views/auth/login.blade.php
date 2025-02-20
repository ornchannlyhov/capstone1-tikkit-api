@extends('layouts.guest')

@section('content')
    <div class="min-h-screen w-full bg-cover bg-center flex items-center justify-center"
        style="background-image: url('{{ asset('images/tikit1.png') }}'); background-size: 110%;">
        <div class="w-full max-w-sm bg-white shadow-lg rounded-xl overflow-hidden p-6"> <!-- Centered card -->
            <h2 class="text-2xl font-semibold text-gray-800 text-center">Welcome</h2>
            <p class="mt-2 text-sm text-gray-500 text-center">Efficiently manage events and tickets with ease.</p>

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mt-4">
                    <p class="font-bold">Error:</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="mt-6">
                @csrf

                <div>
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-gray-200"
                        placeholder="mengthong@gmail.com">
                    @error('email')
                        <span class="text-red-500 text-xs italic">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mt-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-gray-200"
                        placeholder="*********">
                    @error('password')
                        <span class="text-red-500 text-xs italic">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mt-8">
                    <button type="submit"
                        class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                        Sign In
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection