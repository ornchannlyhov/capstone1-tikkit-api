@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <!-- Main Content -->
    <div class="flex-1 ml-64 p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">Payment Management</h1>

        </div>

      

        <!-- transaction Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border table-auto">
                    <thead class="bg-[#030f0f] text-white">
                        <tr>
                            <th class="px-3 py-3 sticky left-0 bg-[#030f0f] z-30">OrderID</th>
                            <th class="px-3 py-3">By User</th>
                            <th class="px-3 py-3">Payment Method</th>
                            <th class="px-3 py-3">Amount</th>
                            <th class="px-3 py-3">Reference</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Date</th>
                            <th class="px-3 py-3">Currency</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr class="border-b hover:bg-gray-100 transition duration-300">
                        <td class="px-3 py-4 font-semibold text-gray-700">{{ $transaction->order->id ?? 'N/A' }}</td>
                        <td class="px-3 py-4 font-semibold text-gray-700">{{ $transaction->user->name }}</td>
                            <td class="px-3 py-4 text-gray-600">{{ $transaction->paymentMethod->name }}</td>
                            <td class="px-3 py-4 text-gray-600">{{ $transaction->amount }}</td>
                            <td class="px-3 py-4 text-gray-600">{{ $transaction->reference }}</td>
                            <td class="px-3 py-4 font-semibold text-gray-700">{{ $transaction->order->status ?? 'N/A' }}</td>
                            <td class="px-3 py-4 text-gray-600">{{ $transaction->date}}</td>
                            <td class="px-3 py-4 text-gray-600">{{ $transaction->currency}}</td>
                        </tr>

                

                        @empty
                        <tr>
                            <td colspan="8" class="px-3 py-4 text-center text-gray-500">No transactions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>



    </div>
</div>



@endsection
