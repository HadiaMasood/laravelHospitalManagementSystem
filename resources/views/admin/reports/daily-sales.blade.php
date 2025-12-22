@extends('layouts.app')

@section('title', 'Daily Sales Report')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"> Daily Sales Report</h1>
            <p class="text-gray-600 mt-2">{{ \Carbon\Carbon::parse($summary['date'])->format('F d, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.reports.index') }}" 
               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to Reports
            </a>
            <form action="{{ route('admin.reports.generate-pdf') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="type" value="daily-sales">
                <input type="hidden" name="date" value="{{ $summary['date'] }}">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                     Download PDF
                </button>
            </form>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('admin.reports.daily-sales') }}" method="GET" class="flex items-center gap-4">
            <label class="font-medium text-gray-700">Select Date:</label>
            <input type="date" 
                   name="date" 
                   value="{{ \Carbon\Carbon::parse($summary['date'])->format('Y-m-d') }}" 
                   class="border border-gray-300 rounded px-4 py-2">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                View Report
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Total Sales</p>
            <p class="text-3xl font-bold text-blue-600">₨{{ number_format($summary['total_sales'], 2) }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ $summary['total_transactions'] }} transactions</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Items Sold</p>
            <p class="text-3xl font-bold text-green-600">{{ $summary['total_items_sold'] }}</p>
            <p class="text-sm text-gray-500 mt-1">units</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Average Sale</p>
            <p class="text-3xl font-bold text-purple-600">₨{{ number_format($summary['average_sale'], 2) }}</p>
            <p class="text-sm text-gray-500 mt-1">per transaction</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Peak Hour</p>
            <p class="text-3xl font-bold text-orange-600">{{ $summary['peak_hour'] }}</p>
            <p class="text-sm text-gray-500 mt-1">busiest time</p>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Transactions</h2>
        </div>

        @if($sales->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cashier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono text-sm">{{ $sale->invoice_number }}</td>
                        <td class="px-6 py-4 text-sm">{{ $sale->created_at->format('h:i A') }}</td>
                        <td class="px-6 py-4 text-sm">{{ $sale->customer_name ?? 'Walk-in' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $sale->items->count() }} items</td>
                        <td class="px-6 py-4 text-sm font-bold">₨{{ number_format($sale->total, 2) }}</td>
                        <td class="px-6 py-4 text-sm">{{ $sale->user->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-gray-50">
            {{ $sales->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="mt-2 text-gray-500">No sales recorded for this date.</p>
        </div>
        @endif
    </div>

</div>
@endsection