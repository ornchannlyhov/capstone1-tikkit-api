<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- Load styles & JS -->
</head>
<body class="bg-gray-100">
    <div class="flex">
        @include('layouts.sidebar')

        <div class="w-full px-6 py-4">
            @yield('content') <!-- This ensures content is loaded properly -->
        </div>
    </div>
</body>

</html>
