@extends('layouts.app')

@section('title', 'Stock Management')

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
            <p class="text-red-600 text-lg mb-6">You do not have permission to access stock management. Only administrators can modify inventory.</p>
            <a href="{{ route('admin.dashboard') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold">
                ← Return to Dashboard
            </a>
        </div>
    @else
        {{-- ADMIN STOCK MANAGEMENT --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Stock Management</h1>
            <a href="{{ route('admin.stocks.create') }}" 
               class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                + Add New Stock
            </a>
        </div>

        <!-- Alert Cards -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                <p class="text-sm text-yellow-800">Expiring Soon (90 days)</p>
                <p class="text-2xl font-bold">{{ $expiringCount }}</p>
            </div>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                <p class="text-sm text-red-800">Expired Items</p>
                <p class="text-2xl font-bold">{{ $expiredCount }}</p>
            </div>
            <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded">
                <p class="text-sm text-orange-800">Low Stock</p>
                <p class="text-2xl font-bold">{{ $lowStockCount }}</p>
            </div>
        </div>

        <!-- Search Form -->
        <form method="GET" class="bg-white p-4 rounded-lg shadow mb-6">
            <div class="flex gap-4">
                <input type="text" name="search" placeholder="Search..." 
                       value="{{ request('search') }}"
                       class="flex-1 border rounded px-4 py-2">
                <select name="status" class="border rounded px-4 py-2">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="expiring">Expiring</option>
                    <option value="expired">Expired</option>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Search
                </button>
            </div>
        </form>

        <!-- Stocks Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left">Medicine</th>
                        <th class="px-6 py-3 text-left">Batch</th>
                        <th class="px-6 py-3 text-left">Quantity</th>
                        <th class="px-6 py-3 text-left">Expiry</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $stock->medicine->name }}</td>
                        <td class="px-6 py-4">{{ $stock->batch_number }}</td>
                        <td class="px-6 py-4">{{ $stock->quantity }}</td>
                        <td class="px-6 py-4">{{ $stock->expiry_date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4">
                            @if($stock->is_expired)
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">Expired</span>
                            @elseif($stock->is_expiring)
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-semibold">Expiring</span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">Active</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.stocks.edit', $stock->id) }}" 
                               class="text-blue-600 hover:underline font-medium">Edit</a>
                            <form action="{{ route('admin.stocks.destroy', $stock->id) }}" 
                                  method="POST" class="inline ml-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-medium"
                                        onclick="return confirm('Are you sure you want to delete this stock?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No stocks found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $stocks->links() }}
        </div>
    @endif

</div>
@endsection