@extends('layouts.app')

@section('title', 'Monthly Sales Report')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">📈 Monthly Sales Report</h1>
            <p class="text-gray-600 mt-2">{{ $summary['month'] }}</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" 
           class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Back to Reports
        </a>
    </div>

    <!-- Month/Year Filter -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('admin.reports.monthly-sales') }}" method="GET" class="flex items-center gap-4">
            <label class="font-medium text-gray-700">Month:</label>
            <select name="month" class="border border-gray-300 rounded px-4 py-2">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endfor
            </select>
            
            <label class="font-medium text-gray-700">Year:</label>
            <select name="year" class="border border-gray-300 rounded px-4 py-2">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                View Report
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Total Revenue</p>
            <p class="text-3xl font-bold text-blue-600">₨{{ number_format($summary['total_sales'], 2) }}</p>
            <p class="text-sm mt-2 {{ $summary['growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $summary['growth'] >= 0 ? '↑' : '↓' }} 
                {{ number_format(abs($summary['growth']), 1) }}% from last month
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Transactions</p>
            <p class="text-3xl font-bold text-green-600">{{ $summary['total_transactions'] }}</p>
            <p class="text-sm text-gray-500 mt-2">sales completed</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Average Sale</p>
            <p class="text-3xl font-bold text-purple-600">₨{{ number_format($summary['average_sale'], 2) }}</p>
            <p class="text-sm text-gray-500 mt-2">per transaction</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 text-sm">Items Sold</p>
            <p class="text-3xl font-bold text-orange-600">{{ number_format($summary['total_items_sold']) }}</p>
            <p class="text-sm text-gray-500 mt-2">units</p>
        </div>
    </div>

    <!-- Daily Breakdown -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Daily Breakdown</h2>
        </div>

        @if($dailyBreakdown->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transactions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($dailyBreakdown as $day)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::parse($day->date)->format('l') }}</td>
                        <td class="px-6 py-4 text-sm font-bold">{{ $day->transactions }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-green-600">₨{{ number_format($day->revenue, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-500">No sales data for this month.</p>
        </div>
        @endif
    </div>

</div>
@endsection