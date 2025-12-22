@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    @if(Auth::user()->role === 'admin')
        {{-- ==================== ADMIN DASHBOARD ==================== --}}
        <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Medicines</p>
                        <p class="text-2xl font-bold">{{ $stats['total_medicines'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Stock Value</p>
                        <p class="text-2xl font-bold">₨{{ number_format($stats['total_stock_value'], 2) }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Low Stock Items</p>
                        <p class="text-2xl font-bold text-orange-600">{{ $stats['low_stock_count'] }}</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Expiring Soon</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['expiring_soon'] }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Alerts Section (Admin Only) -->
        <div class="mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Email Alerts</h3>
                    <svg class="w-10 h-10 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-blue-100 mb-4">Automated expiry notifications and stock alerts</p>
                <a href="{{ route('admin.email-alerts.index') }}" 
                   class="inline-block bg-white text-blue-600 px-4 py-2 rounded font-semibold hover:bg-blue-50 transition">
                    Configure Alerts →
                </a>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
                <h2 class="text-xl font-bold">Recent Sales</h2>
                <a href="{{ route('admin.sales.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View All →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentSales as $sale)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $sale->invoice_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $sale->customer_name ?? 'Walk-in' }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">₨{{ number_format($sale->total, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $sale->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('admin.sales.show', $sale->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p>No sales recorded yet</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif(Auth::user()->role === 'cashier')
        {{-- ==================== CASHIER DASHBOARD ==================== --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Cashier - Point of Sale</h1>
            <p class="text-gray-600">Process sales and view available medicines</p>
        </div>

      
        <!-- Alert Cards (Read-Only for Cashier) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <p class="text-yellow-800 font-medium mb-1">Expiring Soon ({{ config('pharmacy.expiry_warning_days', 90) }} days)</p>
                <p class="text-4xl font-bold text-yellow-900">{{ $expiringSoon }}</p>
            </div>
            <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <p class="text-red-800 font-medium mb-1">Expired Items</p>
                <p class="text-4xl font-bold text-red-900">{{ $expired }}</p>
            </div>
            <div class="bg-orange-50 border-l-4 border-orange-500 p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <p class="text-orange-800 font-medium mb-1">Low Stock</p>
                <p class="text-4xl font-bold text-orange-900">{{ $lowStock }}</p>
            </div>
        </div>

        <!-- Available Medicines Table with Search -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Available Medicines</h3>
                    <span class="text-sm text-gray-500">
{{ $stocks->count() }} items</span>
                </div>
                
                <!-- Search Bar -->
                <div class="relative">
                    <input type="text" 
                           id="medicineSearch" 
                           class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                           placeholder="Search by medicine name, batch number...">
                    <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full" id="medicinesTable">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicine</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($stocks as $stock)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $stock->medicine->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $stock->batch_number }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($stock->quantity <= ($stock->medicine->min_stock_level ?? 10))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $stock->quantity }}
                                    </span>
                                @else
                                    <span class="text-gray-900 font-medium">{{ $stock->quantity }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $daysUntilExpiry = now()->diffInDays($stock->expiry_date, false);
                                    $isExpired = $daysUntilExpiry < 0;
                                    $isExpiringSoon = $daysUntilExpiry >= 0 && $daysUntilExpiry <= 90;
                                @endphp
                                
                                @if($isExpired)
                                    <span class="text-red-600 font-medium">{{ $stock->expiry_date->format('Y-m-d') }}</span>
                                @elseif($isExpiringSoon)
                                    <span class="text-yellow-600 font-medium">{{ $stock->expiry_date->format('Y-m-d') }}</span>
                                @else
                                    <span class="text-gray-500">{{ $stock->expiry_date->format('Y-m-d') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">₨{{ number_format($stock->selling_price, 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($isExpired)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Expired
                                    </span>
                                @elseif($isExpiringSoon)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Expiring Soon
                                    </span>
                                @elseif($stock->quantity <= ($stock->medicine->min_stock_level ?? 10))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        Low Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Available
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p>No medicines available in stock</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($stocks->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t">
                {{ $stocks->links() }}
            </div>
            @endif
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('sales.create') }}" 
               class="inline-flex items-center px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold text-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Start New Sale
            </a>
        </div>

    @else
        {{-- ==================== UNKNOWN ROLE ==================== --}}
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <p class="text-red-800">Invalid user role. Please contact an administrator.</p>
        </div>
    @endif

</div>

@push('scripts')
<script>
// Search functionality for cashier medicine table
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('medicineSearch');
    const table = document.getElementById('medicinesTable');
    
    if (searchInput && table) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let row of rows) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            }
        });
    }
});
</script>
@endpush
@endsection