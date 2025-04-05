@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar Section (You might already have this in your layout, but this is just to ensure proper spacing) -->
    <div class="w-64 bg-gray-800 text-white p-6">
        <!-- Sidebar content here (e.g., links, profile) -->
    </div>

    <!-- Main Content Section -->
    <div class="flex-1 p-6">
        <div class="max-w-7xl mx-auto px-6 py-6">
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Left Section: Order Info -->
                <div class="w-full lg:w-1/2 bg-white p-6 shadow-lg rounded-md">
                    <h2 class="text-2xl font-semibold mb-4">Order Details</h2>

                    <div class="mb-4">
                        <strong>Order ID:</strong> {{ $order->id }}
                    </div>
                    <div class="mb-4">
                        <strong>User Name:</strong> {{ $order->user->name }}
                    </div>
                    <div class="mb-4">
                        <strong>Email:</strong> {{ $order->user->email }}
                    </div>
                    <div class="mb-4">
                        <strong>Phone Number:</strong> {{ $order->user->phone_number }}
                    </div>
                    <div class="mb-4">
                        <strong>Total Price:</strong> ${{ number_format($order->total, 2) }}
                    </div>
                    <div class="mb-4">
                        <strong>Status:</strong> {{ ucfirst($order->status) }}
                    </div>
                </div>

                <!-- Right Section: Cart and Transaction Details -->
                <div class="w-full lg:w-1/2 bg-white p-6 shadow-lg rounded-md">
                    <h2 class="text-2xl font-semibold mb-4">Cart & Transaction Details</h2>

                    <h3 class="text-xl font-semibold mb-2">Cart Items:</h3>
                    <div class="space-y-4">
                        @foreach($order->carts as $cart)
                            <div class="border-b pb-2">
                                <strong>Product Name:</strong> 
                                {{ $cart->ticketOption->type }} <!-- Displaying the ticket type -->
                                <br>
                                <strong>Description:</strong> {{ $cart->ticketOption->description }} <!-- Description of the ticket -->
                                <br>
                                <strong>Quantity:</strong> {{ $cart->pivot->quantity }} <br>
                                <strong>Price per Item:</strong> ${{ number_format($cart->ticketOption->price, 2) }} <br>
                            </div>
                        @endforeach
                    </div>

                    <h3 class="text-xl font-semibold mt-4 mb-2">Payment Transaction:</h3>
                    @if($order->transaction)
                        <div class="border-t pt-2">
                            <strong>Transaction ID:</strong> {{ $order->transaction->id }} <br>
                            <strong>Amount:</strong> ${{ number_format($order->transaction->amount, 2) }} <br>
                            <strong>Status:</strong> {{ ucfirst($order->transaction->status) }} <br>
                            <strong>Transaction Date:</strong> {{ $order->transaction->created_at->format('Y-m-d H:i:s') }} <br>
                            <strong>Payment Method:</strong> {{ $order->transaction->paymentMethod->name ?? 'N/A' }}
                        </div>
                    @else
                        <div class="border-t pt-2">
                            <strong>No transaction found for this order.</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
