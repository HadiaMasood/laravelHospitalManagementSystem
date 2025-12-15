@extends('layouts.app')

@section('title', 'Medicine Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Medicine Details</h1>
        <div class="space-x-2">
            <a href="{{ route('admin.medicines.edit', $medicine->id) }}" 
               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Edit
            </a>
            <a href="{{ route('admin.medicines.index') }}" 
               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to List
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Medicine Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Medicine Name</label>
                    <p class="text-lg font-semibold">{{ $medicine->name }}</p>
                </div>

                <!-- Generic Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Generic Name</label>
                    <p class="text-lg">{{ $medicine->generic_name }}</p>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                    <p class="text-lg">{{ $medicine->category }}</p>
                </div>

                <!-- Unit Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Unit Price</label>
                    <p class="text-lg font-semibold text-green-600">₨{{ number_format($medicine->unit_price, 2) }}</p>
                </div>

                <!-- Barcode -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Barcode</label>
                    <p class="text-lg font-mono">{{ $medicine->barcode }}</p>
                </div>

                <!-- Reorder Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Reorder Level</label>
                    <p class="text-lg">{{ $medicine->reorder_level }} units</p>
                </div>

                <!-- Supplier -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Supplier</label>
                    <p class="text-lg">{{ $medicine->supplier->name ?? 'N/A' }}</p>
                </div>

                <!-- Current Stock -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Current Stock</label>
                    <p class="text-lg font-semibold {{ $medicine->stocks->sum('quantity') < $medicine->reorder_level ? 'text-red-600' : 'text-green-600' }}">
                        {{ $medicine->stocks->sum('quantity') }} units
                    </p>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                    <p class="text-lg">{{ $medicine->description ?? 'No description available' }}</p>
                </div>

                <!-- Created At -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                    <p class="text-lg">{{ $medicine->created_at->format('M d, Y H:i') }}</p>
                </div>

                <!-- Updated At -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                    <p class="text-lg">{{ $medicine->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock History -->
    @if($medicine->stocks->count() > 0)
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mt-6">
        <div class="px-6 py-4 border-b">
            <h2 class="text-xl font-bold">Stock History</h2>
        </div>
        <div class="p-6">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Batch Number</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Quantity</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Purchase Price</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Selling Price</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Expiry Date</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Purchase Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medicine->stocks as $stock)
                    <tr class="border-b">
                        <td class="px-4 py-3 font-mono">{{ $stock->batch_number }}</td>
                        <td class="px-4 py-3">{{ $stock->quantity }}</td>
                        <td class="px-4 py-3">₨{{ number_format($stock->purchase_price, 2) }}</td>
                        <td class="px-4 py-3">₨{{ number_format($stock->selling_price, 2) }}</td>
                        <td class="px-4 py-3 {{ $stock->expiry_date->isPast() ? 'text-red-600 font-semibold' : '' }}">
                            {{ $stock->expiry_date->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3">{{ $stock->purchase_date->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection