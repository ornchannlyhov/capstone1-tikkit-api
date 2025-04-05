@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <div class="flex-1 ml-64 p-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">Order Management</h1>
        </div>

        <!-- Search & Filter -->
        <div class="mb-4 w-full">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="flex items-center space-x-4">
                <!-- Search -->
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search orders..."
                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 w-full max-w-xs">

                <!-- Filter By Status -->
                <select name="status" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancel_request" {{ request('status') == 'cancel_request' ? 'selected' : '' }}>Cancel Request</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <!-- Sort By -->
                <select name="sort_by" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <option value="">Sort By</option>
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date</option>
                    <option value="total" {{ request('sort_by') == 'total' ? 'selected' : '' }}>Total</option>
                    <option value="id" {{ request('sort_by') == 'id' ? 'selected' : '' }}>Order ID</option>
                </select>

                <!-- Sort Order -->
                <select name="sort_order" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                </select>

                <!-- Submit Button -->
                <button type="submit" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 flex items-center space-x-2">
                    <i class="fas fa-search"></i>
                    <span>Apply</span>
                </button>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full border-collapse border table-auto">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-2 text-center">Order ID</th>
                        <th class="px-4 py-2 text-center">User Name</th>
                        <th class="px-4 py-2 text-center">Total</th>
                        <th class="px-4 py-2 text-center">Status</th>
                        <th class="px-4 py-2 text-center">Date/Time</th>
                        <th class="px-4 py-2 text-center">Payment Method</th>
                        <th class="px-4 py-2 text-center">Transaction Method</th>
                        <th class="px-4 py-2 text-center">Reference</th>
                        <th class="px-4 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="border-b">
                            <td class="px-4 py-2 text-center">{{ $order->id }}</td>
                            <td class="px-4 py-2 text-center">{{ $order->user->name }}</td>
                            <td class="px-4 py-2 text-center">${{ number_format($order->total, 2) }}</td>
                            <td class="px-4 py-2 text-center">{{ ucfirst($order->status) }}</td>
                            <td class="px-4 py-2 text-center">{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-4 py-2">
                                {{ optional($order->transaction?->paymentMethod)->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-2 text-center">{{ ucfirst($order->transaction->status) }}</td>
                            <td class="px-4 py-2 text-center">{{ ucfirst($order->transaction->reference) }} </td>
                            <td class="px-4 py-2 flex justify-center space-x-2">
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                    class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center justify-center">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
