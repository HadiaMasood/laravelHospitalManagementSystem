@extends('layouts.app')

@section('title', 'Barcode Test')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">📱 Barcode Scanner Test</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Test Barcodes Available in System</h2>
        <p class="text-gray-600 mb-4">You can use these barcodes to test the scanner. Print them or display on another device:</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($medicines as $medicine)
                <div class="border rounded-lg p-4 text-center">
                    <h3 class="font-semibold text-lg mb-2">{{ $medicine->name }}</h3>
                    <div class="bg-gray-100 p-3 rounded mb-2">
                        <div class="font-mono text-2xl font-bold">{{ $medicine->barcode }}</div>
                    </div>
                    <p class="text-sm text-gray-600">{{ $medicine->category }}</p>
                    <p class="text-sm font-semibold text-green-600">₨{{ $medicine->unit_price }}</p>
                    
                    <!-- Generate barcode image using online service -->
                    <div class="mt-3">
                        <img src="https://barcode.tec-it.com/barcode.ashx?data={{ $medicine->barcode }}&code=EAN13&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&qunit=Mm&quiet=0" 
                             alt="Barcode for {{ $medicine->name }}" 
                             class="mx-auto"
                             style="max-width: 200px;">
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">🔍 How to Test:</h3>
        <ol class="list-decimal list-inside space-y-2 text-blue-700">
            <li>Go to <a href="{{ route('admin.sales.create') }}" class="underline font-semibold">Create New Sale</a></li>
            <li>Click "Start Scanner" to activate the camera</li>
            <li>Point your camera at one of the barcodes above</li>
            <li>The medicine should automatically be added to your cart</li>
            <li>You can also manually enter the barcode numbers in the input field</li>
        </ol>
        
        <div class="mt-4 p-3 bg-yellow-100 border border-yellow-300 rounded">
            <p class="text-yellow-800 text-sm">
                <strong>Note:</strong> Make sure to allow camera access when prompted. The scanner works best with good lighting and clear barcode images.
            </p>
        </div>
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('admin.sales.create') }}" 
           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 inline-block">
            🛒 Go to POS System
        </a>
    </div>
</div>
@endsection