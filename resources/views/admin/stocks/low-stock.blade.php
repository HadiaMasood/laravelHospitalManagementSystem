@extends('layouts.app')

@section('title', 'Low Stock Alert')

@section('content')
<div class="container mx-auto px-4 py-8">

    @if(Auth::user()->role === 'admin')
        {{-- ADMIN: LOW STOCK ALERT --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800"> Low Stock Alert</h1>
                <p class="text-gray-600 mt-2">Items that need to be reordered</p>
            </div>
            <a href="{{ route('admin.stocks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to All Stocks
            </a>
        </div>

        <!-- Alert Box -->
        <div class="bg-orange-100 border-l-4 border-orange-500 p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-orange-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="font-bold text-orange-800">Reorder Required</p>
                    <p class="text-orange-700 text-sm">{{ $lowStocks->count() }} medicines are below reorder level. Please place orders.</p>
                </div>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="grid gap-6">
            @forelse($lowStocks as $medicine)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900">{{ $medicine->name }}</h3>
                            <p class="text-gray-600 text-sm">{{ $medicine->generic_name }} - {{ $medicine->category }}</p>
                            <p class="text-gray-500 text-sm mt-1">Supplier: {{ $medicine->supplier->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Current Stock</p>
                            <p class="text-3xl font-bold text-red-600">{{ $medicine->total_stock }}</p>
                            <p class="text-sm text-gray-500 mt-1">Reorder Level: {{ $medicine->reorder_level }}</p>
                        </div>
                    </div>

                    <!-- Batches -->
                    @if($medicine->stocks->count() > 0)
                    <div class="mt-4 border-t pt-4">
                        <h4 class="font-medium text-gray-700 mb-3">Available Batches:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($medicine->stocks as $stock)
                            <div class="border rounded-lg p-3 {{ $stock->is_expiring ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200' }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-mono text-sm font-medium">{{ $stock->batch_number }}</p>
                                        <p class="text-sm text-gray-600">Qty: {{ $stock->quantity }}</p>
                                        <p class="text-xs text-gray-500">Expires: {{ $stock->expiry_date->format('M d, Y') }}</p>
                                    </div>
                                    @if($stock->is_expiring)
                                    <span class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded">Expiring</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('admin.stocks.create', ['medicine_id' => $medicine->id]) }}" 
                           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm font-semibold">
                            📦 Add New Stock
                        </a>
                        <a href="{{ route('admin.medicines.show', $medicine) }}" 
                           class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm font-semibold">
                            View Medicine Details
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Low Stock Items</h3>
                <p class="mt-1 text-sm text-gray-500">All medicines are adequately stocked.</p>
            </div>
            @endforelse
        </div>
    @endif

</div>
@endsection