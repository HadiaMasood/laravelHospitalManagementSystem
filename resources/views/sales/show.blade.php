@extends('layouts.app')

@section('title', 'Invoice - ' . $sale->invoice_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        
        <!-- Header Actions (Hidden on Print) -->
        <div class="flex justify-between items-center mb-6 no-print">
            <a href="{{ route('admin.sales.index') }}" class="flex items-center text-blue-600 hover:text-blue-800 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to My Sales
            </a>
            <div class="flex gap-2">
                <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2 shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Invoice
                </button>
            </div>
        </div>

        <!-- Invoice Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden" id="invoice">
            
            <!-- Invoice Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold mb-2"> Medical Store</h1>
                        <p class="text-blue-100">123 Healthcare Street, City</p>
                        <p class="text-blue-100">Phone: +92-XXX-XXXXXXX</p>
                        <p class="text-blue-100">Email: info@medicalstore.com</p>
                    </div>
                    <div class="text-right">
                        <div class="bg-white text-blue-700 px-4 py-2 rounded-lg shadow-md inline-block">
                            <p class="text-sm font-medium">INVOICE</p>
                            <p class="text-xl font-bold">{{ $sale->invoice_number }}</p>
                        </div>
                        <p class="text-blue-100 mt-3 text-sm">{{ $sale->created_at->format('M d, Y') }}</p>
                        <p class="text-blue-100 text-sm">{{ $sale->created_at->format('h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Customer & Payment Info -->
            <div class="grid grid-cols-2 gap-6 p-8 bg-gray-50 border-b">
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase mb-3">Bill To</h3>
                    <div class="bg-white p-4 rounded-lg border">
                        <p class="font-bold text-lg text-gray-800">{{ $sale->customer_name ?? 'Walk-in Customer' }}</p>
                        @if($sale->customer_phone)
                        <p class="text-gray-600 mt-1"> {{ $sale->customer_phone }}</p>
                        @endif
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase mb-3">Payment Details</h3>
                    <div class="bg-white p-4 rounded-lg border">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600">Method:</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $sale->payment_method == 'cash' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $sale->payment_method == 'card' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $sale->payment_method == 'upi' ? 'bg-purple-100 text-purple-800' : '' }}">
                                {{ strtoupper($sale->payment_method) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Cashier:</span>
                            <span class="font-medium text-gray-800">{{ $sale->user->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Items</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-gray-300">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">#</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Medicine</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Batch</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Quantity</th>
                                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Unit Price</th>
                                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $index => $item)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-4 text-gray-600">{{ $index + 1 }}</td>
                                <td class="px-4 py-4">
                                    <div class="font-medium text-gray-900">{{ $item->medicine->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->medicine->generic_name }}</div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                                        {{ $item->stock->batch_number }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center font-semibold text-gray-800">{{ $item->quantity }}</td>
                                <td class="px-4 py-4 text-right text-gray-700">₨{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-4 text-right font-semibold text-gray-900">₨{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totals Section -->
            <div class="px-8 pb-8">
                <div class="flex justify-end">
                    <div class="w-80 bg-gray-50 rounded-lg p-6 border-2 border-gray-200">
                        <div class="space-y-3">
                            <div class="flex justify-between text-gray-700">
                                <span>Subtotal:</span>
                                <span class="font-medium">₨{{ number_format($sale->total_amount, 2) }}</span>
                            </div>
                            
                            @if($sale->discount > 0)
                            <div class="flex justify-between text-red-600">
                                <span>Discount:</span>
                                <span class="font-medium">-₨{{ number_format($sale->discount, 2) }}</span>
                            </div>
                            @endif
                            
                            @if($sale->tax > 0)
                            <div class="flex justify-between text-gray-700">
                                <span>Tax:</span>
                                <span class="font-medium">₨{{ number_format($sale->tax, 2) }}</span>
                            </div>
                            @endif
                            
                            <div class="border-t-2 border-gray-300 pt-3">
                                <div class="flex justify-between text-xl font-bold">
                                    <span class="text-gray-800">Grand Total:</span>
                                    <span class="text-green-600">₨{{ number_format($sale->final_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-100 px-8 py-6 border-t-2 border-gray-300">
                <div class="text-center">
                    <p class="text-gray-700 font-medium mb-2">Thank you for your business!</p>
                    <p class="text-sm text-gray-600">For any queries, please contact us.</p>
                    <p class="text-xs text-gray-500 mt-3">This is a computer-generated invoice.</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons (Hidden on Print) -->
        <div class="mt-6 flex justify-center gap-4 no-print">
            <a href="{{ route('admin.sales.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 font-medium">
                Close
            </a>
            <button onclick="window.print()" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 font-medium">
                Print & Save
            </button>
        </div>
    </div>
</div>

<style>
@media print {
    /* Hide everything */
    body * {
        visibility: hidden;
    }
    
    /* Show only invoice */
    #invoice, #invoice * {
        visibility: visible;
    }
    
    /* Position invoice at top */
    #invoice {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
    }
    
    /* Hide elements with no-print class */
    .no-print {
        display: none !important;
    }
    
    /* Remove rounded corners and shadows for print */
    #invoice {
        border-radius: 0 !important;
    }
    
    /* Ensure proper page breaks */
    .page-break {
        page-break-after: always;
    }
    
    /* Adjust print margins */
    @page {
        margin: 1cm;
    }
}
</style>

@endsection