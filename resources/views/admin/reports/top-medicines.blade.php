@extends('layouts.app')

@section('title', 'Top Medicines Report')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"> Top Selling Medicines</h1>
            <p class="text-gray-600 mt-2">Best performers from {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.reports.index') }}" 
               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to Reports
            </a>
            <form action="{{ route('admin.reports.generate-pdf') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="type" value="top-medicines">
                <input type="hidden" name="from_date" value="{{ $from }}">
                <input type="hidden" name="to_date" value="{{ $to }}">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    🖨️ Download PDF
                </button>
            </form>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('admin.reports.top-medicines') }}" method="GET" class="flex items-center gap-4">
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

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($topMedicines->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-green-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Units Sold</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Left</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($topMedicines as $index => $medicine)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="text-2xl font-bold
                                {{ $index == 0 ? 'text-yellow-500' : '' }}
                                {{ $index == 1 ? 'text-gray-400' : '' }}
                                {{ $index == 2 ? 'text-orange-600' : '' }}">
                                #{{ $index + 1 }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $medicine->name }}</div>
                            <div class="text-sm text-gray-500">{{ $medicine->generic_name ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $medicine->category }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-blue-600">{{ $medicine->total_sold }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-green-600">₨{{ number_format($medicine->total_revenue, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 rounded {{ $medicine->total_stock < 50 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $medicine->total_stock }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="mt-2 text-gray-500">No sales data available for the selected period.</p>
        </div>
        @endif
    </div>

</div>
@endsection