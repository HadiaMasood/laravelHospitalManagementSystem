@extends('layouts.app')

@section('title', 'Edit Medicine')

@section('content')
<div class="container mx-auto px-4 py-8">

    @if(Auth::user()->role === 'admin')
        {{-- ADMIN: EDIT MEDICINE FORM --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Edit Medicine</h1>
            <a href="{{ route('admin.medicines.index') }}" 
               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to List
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.medicines.update', $medicine->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 font-medium">Medicine Name *</label>
                        <input type="text" name="name" value="{{ old('name', $medicine->name) }}" required 
                               class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Generic Name *</label>
                        <input type="text" name="generic_name" value="{{ old('generic_name', $medicine->generic_name) }}" required 
                               class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        @error('generic_name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Category *</label>
                        <select name="category" required 
                                class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                            <option value="Tablet" {{ $medicine->category == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                            <option value="Syrup" {{ $medicine->category == 'Syrup' ? 'selected' : '' }}>Syrup</option>
                            <option value="Injection" {{ $medicine->category == 'Injection' ? 'selected' : '' }}>Injection</option>
                            <option value="Capsule" {{ $medicine->category == 'Capsule' ? 'selected' : '' }}>Capsule</option>
                            <option value="Cream" {{ $medicine->category == 'Cream' ? 'selected' : '' }}>Cream</option>
                            <option value="Drops" {{ $medicine->category == 'Drops' ? 'selected' : '' }}>Drops</option>
                        </select>
                        @error('category')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Unit Price *</label>
                        <input type="number" name="unit_price" value="{{ old('unit_price', $medicine->unit_price) }}" required min="0" step="0.01" 
                               class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        @error('unit_price')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Supplier *</label>
                        <select name="supplier_id" required 
                                class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ $medicine->supplier_id == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Reorder Level *</label>
                        <input type="number" name="reorder_level" value="{{ old('reorder_level', $medicine->reorder_level) }}" required min="1" 
                               class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        @error('reorder_level')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block mb-2 font-medium">Description</label>
                        <textarea name="description" rows="3" 
                                  class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">{{ old('description', $medicine->description) }}</textarea>
                        @error('description')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex gap-4 mt-6">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-semibold">
                        ✅ Update Medicine
                    </button>
                    <a href="{{ route('admin.medicines.index') }}" 
                       class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400 font-semibold">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection
