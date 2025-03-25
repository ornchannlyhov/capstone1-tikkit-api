@php
    $statusClasses = [
        'active' => 'bg-green-500 text-white',
        'complete' => 'bg-black text-white',
        'upcoming' => 'bg-blue-500 text-white',
        // Add more status classes if needed
    ];

    // Default status class for undefined status
    $statusClass = $statusClasses[$status] ?? 'bg-gray-500 text-white';
@endphp

<span class="px-3 py-1 rounded-full {{ $statusClass }}">
    {{ ucfirst($status) }}
</span>
