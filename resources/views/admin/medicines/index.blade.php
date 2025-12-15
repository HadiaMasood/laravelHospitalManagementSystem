
@extends('layouts.app')

@section('title', 'Medicines')

@section('content')
<div class="container mx-auto px-4 py-8">

    @if(Auth::user()->role === 'admin')
        {{-- ADMIN: MEDICINES LIST --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Medicines</h1>
            <a href="{{ route('admin.medicines.create') }}" 
               class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-medium">
                + Add New Medicine
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('admin.medicines.index') }}" class="flex gap-4">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search medicines..." 
                       class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                    Search
                </button>
                @if(request('search'))
                <a href="{{ route('admin.medicines.index') }}" 
                   class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                    Clear
                </a>
                @endif
            </form>
        </div>

        <!-- Medicines Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Generic</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($medicines as $medicine)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $medicine->name }}</div>
                            <div class="text-sm text-gray-500">{{ $medicine->barcode }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $medicine->generic_name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $medicine->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                            ₨{{ number_format($medicine->unit_price, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $totalStock = $medicine->stocks->sum('quantity');
                                $isLow = $totalStock < $medicine->reorder_level;
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $isLow ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $totalStock }} units
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $medicine->supplier->name }}
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.medicines.show', $medicine) }}" 
                               class="text-indigo-600 hover:text-indigo-900">View</a>
                            <a href="{{ route('admin.medicines.edit', $medicine) }}" 
                               class="text-green-600 hover:text-green-900">Edit</a>
                            <form action="{{ route('admin.medicines.destroy', $medicine) }}" 
                                  method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Delete this medicine?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            No medicines found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $medicines->links() }}
        </div>
    @endif

</div>
@endsection
