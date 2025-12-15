@extends('layouts.app')

@section('title', 'Supplier Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Supplier Details</h1>
        <div class="space-x-2">
            <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" 
               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Edit
            </a>
            <a href="{{ route('admin.suppliers.index') }}" 
               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Supplier Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-indigo-600 text-white px-6 py-4">
                    <h2 class="text-xl font-bold">{{ $supplier->name }}</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Contact Person</p>
                        <p class="font-medium">{{ $supplier->contact_person }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Phone</p>
                        <p class="font-medium">{{ $supplier->phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium">{{ $supplier->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Address</p>
                        <p class="font-medium">{{ $supplier->address }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Member Since</p>
                        <p class="font-medium">{{ $supplier->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medicines from this Supplier -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-xl font-bold">Medicines Supplied</h2>
                </div>
                <div class="p-6">
                    @if($supplier->medicines->count() > 0)
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Medicine Name</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Generic Name</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Category</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Price</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($supplier->medicines as $medicine)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.medicines.show', $medicine->id) }}" 
                                       class="text-indigo-600 hover:underline font-medium">
                                        {{ $medicine->name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $medicine->generic_name }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $medicine->category }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">₨{{ number_format($medicine->unit_price, 2) }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $stock = $medicine->stocks->sum('quantity');
                                        $isLow = $stock < $medicine->reorder_level;
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $isLow ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $stock }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-center text-gray-500 py-8">No medicines from this supplier yet.</p>
                    @endif
                </div>
            </div>

            <!-- Recent Stock Purchases -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mt-6">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-xl font-bold">Recent Stock Purchases</h2>
                </div>
                <div class="p-6">
                    @if($supplier->stocks->count() > 0)
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Medicine</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Batch</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Quantity</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Purchase Price</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Purchase Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($supplier->stocks->take(10) as $stock)
                            <tr class="border-b">
                                <td class="px-4 py-3">{{ $stock->medicine->name }}</td>
                                <td class="px-4 py-3 font-mono text-sm">{{ $stock->batch_number }}</td>
                                <td class="px-4 py-3">{{ $stock->quantity }}</td>
                                <td class="px-4 py-3">₨{{ number_format($stock->purchase_price, 2) }}</td>
                                <td class="px-4 py-3 text-sm">{{ $stock->purchase_date->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-center text-gray-500 py-8">No stock purchases from this supplier yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection