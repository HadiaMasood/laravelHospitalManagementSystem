@extends('layouts.app')

@section('title', 'Edit Stock')

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
            <h2 class="text-3xl font-bold text-red-800 mb-3">⛔ Access Denied</h2>
            <p class="text-red-600 text-lg mb-6">You do not have permission to edit stock. Only administrators can modify inventory.</p>
            <a href="{{ route('admin.dashboard') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold">
                ← Return to Dashboard
            </a>
        </div>
    @else
        {{-- ADMIN: EDIT STOCK FORM --}}
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Edit Stock</h1>
                <a href="{{ route('admin.stocks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Back to List
                </a>
            </div>

            <!-- Edit Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="{{ route('admin.stocks.update', $stock->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Medicine (Read-only) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Medicine</label>
                            <input type="text" value="{{ $stock->medicine->name }}" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100" readonly>
                        </div>

                        <!-- Batch Number (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                            <input type="text" value="{{ $stock->batch_number }}" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100" readonly>
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                            <input type="number" name="quantity" value="{{ old('quantity', $stock->quantity) }}" 
                                   min="0" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                            @error('quantity')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Purchase Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Price *</label>
                            <input type="number" step="0.01" name="purchase_price" 
                                   value="{{ old('purchase_price', $stock->purchase_price) }}" 
                                   min="0" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                            @error('purchase_price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Selling Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price *</label>
                            <input type="number" step="0.01" name="selling_price" 
                                   value="{{ old('selling_price', $stock->selling_price) }}" 
                                   min="0" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                            @error('selling_price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Expiry Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date *</label>
                            <input type="date" name="expiry_date" 
                                   value="{{ old('expiry_date', $stock->expiry_date->format('Y-m-d')) }}" 
                                   required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                            @error('expiry_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="is_active" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                                <option value="1" {{ old('is_active', $stock->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $stock->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-4 mt-6">
                        <a href="{{ route('admin.stocks.index') }}" 
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">
                            ✅ Update Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection