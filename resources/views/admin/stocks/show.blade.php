@extends('layouts.app')

@section('title', 'Stock Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Stock Details</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.stocks.edit', $stock->id) }}" 
                   class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Edit Stock
                </a>
                <a href="{{ route('admin.stocks.index') }}" 
                   class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Back to List
                </a>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="mb-4">
            @if($stock->is_expired)
                <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                    Expired
                </span>
            @elseif($stock->is_expiring)
                <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                     Expiring Soon ({{ $stock->days_until_expiry }} days)
                </span>
            @else
                <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                     Active
                </span>
            @endif
        </div>

        <!-- Stock Information -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-indigo-600 text-white px-6 py-4">
                <h2 class="text-xl font-bold">Stock Information</h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Medicine Info -->
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Medicine Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Medicine Name</p>
                                <p class="font-medium">{{ $stock->medicine->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Generic Name</p>
                                <p class="font-medium">{{ $stock->medicine->generic_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Category</p>
                                <p class="font-medium">{{ $stock->medicine->category }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Barcode</p>
                                <p class="font-medium font-mono">{{ $stock->medicine->barcode }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Batch Info -->
                    <div>
                        <p class="text-sm text-gray-500">Batch Number</p>
                        <p class="font-medium text-lg">{{ $stock->batch_number }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Quantity Available</p>
                        <p class="font-medium text-lg {{ $stock->quantity < 10 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $stock->quantity }} units
                        </p>
                    </div>

                    <!-- Pricing -->
                    <div>
                        <p class="text-sm text-gray-500">Purchase Price</p>
                        <p class="font-medium text-lg">${{ number_format($stock->purchase_price, 2) }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Selling Price</p>
                        <p class="font-medium text-lg text-green-600">${{ number_format($stock->selling_price, 2) }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Profit Per Unit</p>
                        <p class="font-medium text-lg text-blue-600">
                            ${{ number_format($stock->selling_price - $stock->purchase_price, 2) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Total Stock Value</p>
                        <p class="font-medium text-lg text-indigo-600">
                            ${{ number_format($stock->stock_value, 2) }}
                        </p>
                    </div>

                    <!-- Dates -->
                    <div>
                        <p class="text-sm text-gray-500">Purchase Date</p>
                        <p class="font-medium">{{ $stock->purchase_date->format('M d, Y') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Expiry Date</p>
                        <p class="font-medium {{ $stock->is_expiring ? 'text-red-600' : '' }}">
                            {{ $stock->expiry_date->format('M d, Y') }}
                        </p>
                    </div>

                    <!-- Supplier Info -->
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Supplier Information</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Supplier Name</p>
                                <p class="font-medium">{{ $stock->supplier->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Contact Person</p>
                                <p class="font-medium">{{ $stock->supplier->contact_person }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="font-medium">{{ $stock->supplier->phone }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium">{{ $stock->supplier->email }}</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Sales History -->
        @if($stock->saleItems->count() > 0)
        <div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
            <div class="bg-gray-100 px-6 py-4">
                <h2 class="text-xl font-bold">Sales History</h2>
            </div>
            <div class="p-6">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Date</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Invoice</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Quantity</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stock->saleItems as $item)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $item->sale->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-2">{{ $item->sale->invoice_number }}</td>
                            <td class="px-4 py-2">{{ $item->quantity }}</td>
                            <td class="px-4 py-2">${{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection