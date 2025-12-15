@extends('layouts.app')

@section('title', 'Expiring Stocks')

@section('content')
<div class="container mx-auto px-4 py-8">

    @if(Auth::user()->role === 'admin')
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Expiring Stocks</h1>
                <p class="text-gray-600 mt-2">Items expiring within the next 30 days</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.stocks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Back to All Stocks
                </a>
                <a href="{{ route('admin.stocks.expiring') }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                    Refresh
                </a>
            </div>
        </div>

        <!-- Alert -->
        <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="font-bold text-yellow-800">Action Required</p>
                    <p class="text-yellow-700 text-sm">{{ $stocks->total() }} items are expiring soon. Please review and take necessary action.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if($stocks->count() > 0)
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-yellow-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days Left</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stocks as $stock)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ optional($stock->medicine)->name ?? '—' }}</div>
                            <div class="text-sm text-gray-500">{{ optional($stock->supplier)->name ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 font-mono text-sm">{{ $stock->batch_number ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="font-bold {{ $stock->quantity < 10 ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $stock->quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-red-600 font-medium">
                                {{ \Carbon\Carbon::parse($stock->expiry_date)->format('M d, Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-bold rounded-full
                                {{ $stock->days_until_expiry <= 7 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $stock->days_until_expiry }} days
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            ₨{{ number_format($stock->stock_value, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.stocks.show', $stock) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                            <a href="{{ route('admin.stocks.edit', $stock) }}" class="text-green-600 hover:text-green-900">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 flex items-center justify-between">
                <div class="text-sm text-gray-600">Showing {{ $stocks->firstItem() }} - {{ $stocks->lastItem() }} of {{ $stocks->total() }}</div>
                <div>
                    {{ $stocks->links() }}
                </div>
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Expiring Stock</h3>
                <p class="mt-1 text-sm text-gray-500">All stock items are within safe expiry dates.</p>
            </div>
            @endif
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-gray-600">You don't have permission to view this page.</p>
        </div>
    @endif

</div>
@endsection