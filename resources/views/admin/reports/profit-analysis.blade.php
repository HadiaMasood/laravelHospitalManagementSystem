@extends('layouts.app')

@section('title', 'Profit Analysis Report')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">💵 Profit Analysis</h1>
            <p class="text-gray-600 mt-2">Profit margins and cost analysis</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" 
           class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Back to Reports
        </a>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('admin.reports.profit-analysis') }}" method="GET" class="flex items-center gap-4">
            <label class="font-medium text-gray-700">From:</label>
            <input type="date" 
                   name="from_date" 
                   value="{{ \Carbon\Carbon::parse($from)->format('Y-m-d') }}" 
                   class="border border-gray-300 rounded px-4 py-2">
            
            <label class="font-medium text-gray-700">To:</label>
            <input type="date" 
                   name="to_date" 
                   value="{{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}" 
                   class="border border-gray-300 rounded px-4 py-2">
            
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                View Report
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Total Revenue</p>
            <p class="text-3xl font-bold text-blue-600">₨{{ number_format($summary['total_revenue'], 2) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Total Cost</p>
            <p class="text-3xl font-bold text-orange-600">₨{{ number_format($summary['total_cost'], 2) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Net Profit</p>
            <p class="text-3xl font-bold text-green-600">₨{{ number_format($summary['total_profit'], 2) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Profit Margin</p>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($summary['profit_margin'], 1) }}%</p>
        </div>
    </div>

    <!-- Profit by Category -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Profit by Category</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($categoryProfit as $cat)
            <div class="border rounded-lg p-4">
                <p class="font-medium text-gray-700">{{ $cat->category }}</p>
                <p class="text-2xl font-bold text-green-600">₨{{ number_format($cat->profit, 2) }}</p>
                <div class="text-sm text-gray-500 mt-2">
                    <p>Revenue: ₨{{ number_format($cat->revenue, 2) }}</p>
                    <p>Cost: ₨{{ number_format($cat->cost, 2) }}</p>
                    <p>Margin: {{ $cat->revenue > 0 ? number_format(($cat->profit / $cat->revenue) * 100, 1) : 0 }}%</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Top Profitable Medicines -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Most Profitable Medicines</h2>
        </div>

        @if($profitableMedicines->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-green-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Units Sold</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Margin</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($profitableMedicines as $medicine)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium">{{ $medicine->name }}</td>
                        <td class="px-6 py-4 text-sm">{{ $medicine->category }}</td>
                        <td class="px-6 py-4 text-sm">{{ $medicine->total_sold }}</td>
                        <td class="px-6 py-4 text-sm">₨{{ number_format($medicine->revenue, 2) }}</td>
                        <td class="px-6 py-4 text-sm">₨{{ number_format($medicine->cost, 2) }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-green-600">₨{{ number_format($medicine->profit, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            {{ $medicine->revenue > 0 ? number_format(($medicine->profit / $medicine->revenue) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-500">No profit data available.</p>
        </div>
        @endif
    </div>

</div>
@endsection