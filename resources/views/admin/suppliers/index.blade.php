@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="container mx-auto px-4 py-8">

    @if(Auth::user()->role === 'admin')
        {{-- ADMIN: SUPPLIERS LIST --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Suppliers</h1>
            <a href="{{ route('admin.suppliers.create') }}" 
               class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-medium">
                + Add New Supplier
            </a>
        </div>

        <!-- Search Form -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('admin.suppliers.index') }}" class="flex gap-4">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search suppliers..." 
                       class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                    Search
                </button>
                @if(request('search'))
                <a href="{{ route('admin.suppliers.index') }}" 
                   class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                    Clear
                </a>
                @endif
            </form>
        </div>

        <!-- Suppliers Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact Person</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($suppliers as $supplier)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $supplier->name }}</div>
                            <div class="text-sm text-gray-500">{{ $supplier->address }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $supplier->contact_person }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $supplier->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $supplier->phone }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $supplier->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.suppliers.show', $supplier) }}" 
                               class="text-indigo-600 hover:text-indigo-900">View</a>
                            <a href="{{ route('admin.suppliers.edit', $supplier) }}" 
                               class="text-green-600 hover:text-green-900">Edit</a>
                            <form action="{{ route('admin.suppliers.destroy', $supplier) }}" 
                                  method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Delete this supplier?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No suppliers found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $suppliers->links() }}
        </div>
    @endif

</div>
@endsection

 