@extends('layouts.app')

@section('title', 'Expired Stocks')

@section('content')
<div class="container mx-auto px-4 py-8">

    @if(Auth::user()->role === 'admin')
        {{-- ADMIN: EXPIRED STOCKS --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Expired Stocks</h1>
                <p class="text-gray-600 mt-2">Items that have passed their expiry date</p>
            </div>
            <a href="{{ route('admin.stocks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to All Stocks
            </a>
        </div>

        <!-- Alert Box -->
        <div class="bg-red-100 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-bold text-red-800">Immediate Action Required</p>
                    <p class="text-red-700 text-sm">{{ $stocks->total() }} items have expired. These must be removed from inventory immediately.</p>
                </div>
            </div>
        </div>

        <!-- Expired Stocks Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if($stocks->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-red-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days Expired</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loss Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stocks as $stock)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $stock->medicine->name }}</div>
                            <div class="text-sm text-gray-500">{{ $stock->supplier->name }}</div>
                        </td>
                        <td class="px-6 py-4 font-mono text-sm">{{ $stock->batch_number }}</td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-red-600">{{ $stock->quantity }}</span>
                        </td>
                        <td class="px-6 py-4 text-red-600 font-medium">
                            {{ $stock->expiry_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800">
                                {{ abs($stock->days_until_expiry) }} days ago
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium text-red-600">
                            ₨{{ number_format($stock->quantity * $stock->purchase_price, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <form action="{{ route('admin.stocks.destroy', $stock) }}" 
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Are you sure you want to remove this expired stock?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">
                                    🗑️ Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-red-50">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right font-bold text-gray-700">Total Loss Value:</td>
                        <td class="px-6 py-4 font-bold text-red-600">
                            ₨{{ number_format($stocks->sum(function($stock) { 
                                return $stock->quantity * $stock->purchase_price; 
                            }), 2) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-50">
                {{ $stocks->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Expired Stock</h3>
                <p class="mt-1 text-sm text-gray-500">Great! No expired items in your inventory.</p>
            </div>
            @endif
        </div>
    @endif

</div>
@endsection