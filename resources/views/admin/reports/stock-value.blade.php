@extends('layouts.app')

@section('title', 'Stock Value Report')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">💰 Stock Value Report</h1>
            <p class="text-gray-600 mt-2">Total inventory valuation</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.reports.index') }}" 
               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to Reports
            </a>
            <form action="{{ route('admin.reports.generate-pdf') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="type" value="stock-value">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    🖨️ Download PDF
                </button>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Total Stock Value</p>
            <p class="text-3xl font-bold text-purple-600">₨{{ number_format($summary['total_value'], 2) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Total Items</p>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($summary['total_items']) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Unique Medicines</p>
            <p class="text-3xl font-bold text-green-600">{{ $summary['unique_medicines'] }}</p>
        </div>
    </div>

    <!-- Stock by Category -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Stock Value by Category</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($categoryValues as $category)
            <div class="border rounded-lg p-4 hover:shadow-md transition">
                <p class="text-gray-600 text-sm font-medium">{{ $category->category }}</p>
                <p class="text-2xl font-bold text-indigo-600">₨{{ number_format($category->total_value, 2) }}</p>
                <p class="text-sm text-gray-500">{{ number_format($category->total_quantity) }} units</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Detailed Stock List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Detailed Stock Valuation</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-purple-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stocks as $stock)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $stock->medicine->name }}</div>
                            <div class="text-sm text-gray-500">{{ $stock->medicine->generic_name }}</div>
                        </td>
                        <td class="px-6 py-4 font-mono text-sm">{{ $stock->batch_number }}</td>
                        <td class="px-6 py-4 text-sm">{{ $stock->medicine->category }}</td>
                        <td class="px-6 py-4 text-sm font-bold">{{ $stock->quantity }}</td>
                        <td class="px-6 py-4 text-sm">₨{{ number_format($stock->unit_price, 2) }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-purple-600">
                            ₨{{ number_format($stock->stock_value, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ \Carbon\Carbon::parse($stock->expiry_date)->format('M d, Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-gray-50">
            {{ $stocks->links() }}
        </div>
    </div>

</div>
@endsection