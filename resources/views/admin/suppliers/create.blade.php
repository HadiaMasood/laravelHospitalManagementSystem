@extends('layouts.app')

@section('title', 'Add New Supplier')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Add New Supplier</h1>
        <a href="{{ route('admin.suppliers.index') }}" 
           class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.suppliers.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 font-medium">Supplier Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                           class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block mb-2 font-medium">Contact Person *</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}" required 
                           class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    @error('contact_person')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block mb-2 font-medium">Phone *</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required 
                           class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    @error('phone')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block mb-2 font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block mb-2 font-medium">License Number</label>
                    <input type="text" name="license_number" value="{{ old('license_number') }}" 
                           class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    @error('license_number')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label class="block mb-2 font-medium">Address</label>
                    <textarea name="address" rows="3" 
                              class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-indigo-500">{{ old('address') }}</textarea>
                    @error('address')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-semibold">
                    ✅ Add Supplier
                </button>
                <a href="{{ route('admin.suppliers.index') }}" 
                   class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400 font-semibold">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection