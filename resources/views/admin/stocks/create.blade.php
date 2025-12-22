@extends('layouts.app')

@section('title', 'Add New Stock')

@section('content')
<div class="container mx-auto px-4 py-8">

    @if(Auth::user()->role !== 'admin')
        {{-- ACCESS DENIED FOR NON-ADMIN --}}
        <div class="bg-red-50 border-2 border-red-200 rounded-lg p-8 text-center">
            <div class="mb-4">
                <svg class="w-20 h-20 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-red-800 mb-3"> Access Denied</h2>
            <p class="text-red-600 text-lg mb-6">You do not have permission to add stock. Only administrators can modify inventory.</p>
            <a href="{{ route('admin.dashboard') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold">
                ← Return to Dashboard
            </a>
        </div>
    @else
        {{-- ADMIN: ADD NEW STOCK FORM --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Add New Stock</h1>
            <a href="{{ route('admin.stocks.index') }}" 
               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to List
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.stocks.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 font-medium">Medicine *</label>
                        <select name="medicine_id" required 
                                class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select Medicine</option>
                            @foreach($medicines as $medicine)
                                <option value="{{ $medicine->id }}" {{ old('medicine_id') == $medicine->id ? 'selected' : '' }}>
                                    {{ $medicine->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('medicine_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Supplier *</label>
                        <select name="supplier_id" required 
                                class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Batch Number *</label>
                        <input type="text" name="batch_number" value="{{ old('batch_number') }}" required 
                               class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        @error('batch_number')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Quantity *</label>
                        <input type="number" name="quantity" value="{{ old('quantity') }}" required min="1" 
                               class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        @error('quantity')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Purchase Price *</label>
                        <input type="number" name="purchase_price" value="{{ old('purchase_price') }}" required min="0" step="0.01" 
                               class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        @error('purchase_price')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Selling Price *</label>
                        <input type="number" name="selling_price" value="{{ old('selling_price') }}" required min="0" step="0.01" 
                               class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        @error('selling_price')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Purchase Date *</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required 
                               class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        @error('purchase_date')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Expiry Date *</label>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" required 
                               class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        @error('expiry_date')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex gap-4 mt-6">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-semibold">
                         Save Stock
                    </button>
                    <a href="{{ route('admin.stocks.index') }}" 
                       class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400 font-semibold">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection