@extends('layouts.app')

@section('title', 'My Sales')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"> My Sales</h1>
            <p class="text-gray-600 mt-1">View your sales transactions</p>
        </div>
        <a href="{{ route('admin.sales.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold shadow-md">
             New Sale
        </a>
    </div>

    <!-- Filter Form -->
    <form method="GET" class="bg-white p-4 rounded-lg shadow-md mb-6">
        <div class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" 
                       class="border border-gray-300 rounded px-4 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" 
                       class="border border-gray-300 rounded px-4 py-2">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                 Filter
            </button>
            @if(request('from_date') || request('to_date'))
            <a href="{{ route('admin.sales.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                Clear
            </a>
            @endif
        </div>
    </form>

    <!-- Sales Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($sales as $sale)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono text-sm">{{ $sale->invoice_number }}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium">{{ $sale->customer_name ?? 'Walk-in Customer' }}</div>
                        @if($sale->customer_phone)
                        <div class="text-sm text-gray-500"> {{ $sale->customer_phone }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-bold text-green-600">₨{{ number_format($sale->final_amount, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $sale->payment_method == 'cash' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $sale->payment_method == 'card' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $sale->payment_method == 'upi' ? 'bg-purple-100 text-purple-800' : '' }}">
                            {{ strtoupper($sale->payment_method) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.sales.show', $sale) }}" 
                           class="text-blue-600 hover:text-blue-900 font-medium">View Details →</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-gray-400 mb-2">
                            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium">No sales found</p>
                        <a href="{{ route('admin.sales.create') }}" class="text-blue-600 hover:underline mt-2 inline-block">
                            Create your first sale →
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($sales->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $sales->links() }}
        </div>
        @endif
    </div>

    <!-- Summary Stats -->
    @if($sales->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">My Total Sales</p>
            <p class="text-2xl font-bold text-blue-600">{{ $sales->total() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Total Revenue</p>
            <p class="text-2xl font-bold text-green-600">₨{{ number_format($sales->sum('final_amount'), 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Average Sale</p>
            <p class="text-2xl font-bold text-purple-600">
                ₨{{ number_format($sales->avg('final_amount'), 2) }}
            </p>
        </div>
    </div>
    @endif
</div>
@endsection