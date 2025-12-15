@extends('layouts.app')

@section('title', 'POS - Point of Sale')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Point of Sale System</h1>
        <div class="flex gap-2">
            <button type="button" onclick="openBarcodeScanner()" 
                    class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Camera Scan
            </button>
            <a href="{{ route('admin.sales.index') }}" 
               class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                View Sales
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- LEFT PANEL: Product Search & Scan -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('admin.sales.store') }}" method="POST" id="saleForm">
                    @csrf
                    
                    <!-- BARCODE SCANNING METHODS -->
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-2 border-indigo-300 rounded-lg p-6 mb-6">
                        <h3 class="text-xl font-bold text-indigo-800 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Product Scanning & Lookup
                        </h3>

                        <!-- Method 1: Barcode Scanner Input -->
                        <div class="mb-4">
                            <label class="block mb-2 font-semibold text-gray-700">
                                🔍 Method 1: Barcode Scanner Input
                            </label>
                            <div class="flex gap-2">
                                <input type="text" 
                                       id="barcodeInput" 
                                       placeholder="Scan barcode or type code (e.g., MED001)..." 
                                       class="flex-1 border-2 border-indigo-300 rounded-lg px-4 py-3 text-lg focus:ring-2 focus:ring-indigo-500"
                                       autofocus>
                                <button type="button" 
                                        onclick="searchByBarcode()" 
                                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold">
                                    Search
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">💡 Focus here and scan with barcode scanner, or press Enter</p>
                        </div>

                        <!-- Method 2: Camera Barcode Scanner -->
                        <div class="mb-4">
                            <label class="block mb-2 font-semibold text-gray-700">
                                📷 Method 2: Camera Barcode Scanner
                            </label>
                            <button type="button" 
                                    onclick="openBarcodeScanner()"
                                    class="w-full bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 font-semibold flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                </svg>
                                Open Camera Scanner
                            </button>
                            <p class="text-xs text-gray-500 mt-1">💡 Use your device camera to scan product barcodes</p>
                        </div>

                        <!-- Method 3: Product Name Search -->
                        <div class="mb-4">
                            <label class="block mb-2 font-semibold text-gray-700">
                                📝 Method 3: Search by Product Name
                            </label>
                            <div class="flex gap-2">
                                <input type="text" 
                                       id="productNameSearch" 
                                       placeholder="Type medicine name to search..." 
                                       class="flex-1 border-2 border-indigo-300 rounded-lg px-4 py-3"
                                       oninput="searchByName(this.value)">
                                <button type="button" 
                                        onclick="clearNameSearch()"
                                        class="bg-gray-400 text-white px-4 py-3 rounded-lg hover:bg-gray-500">
                                    Clear
                                </button>
                            </div>
                            <div id="nameSearchResults" class="mt-2 max-h-48 overflow-y-auto hidden"></div>
                        </div>

                        <!-- Method 4: Browse All Products -->
                        <div>
                            <label class="block mb-2 font-semibold text-gray-700">
                                📋 Method 4: Browse All Products
                            </label>
                            <select id="medicineSelect" 
                                    class="w-full border-2 border-indigo-300 rounded-lg px-4 py-3"
                                    onchange="selectFromDropdown()">
                                <option value="">-- Select Medicine --</option>
                                @foreach($medicines as $medicine)
                                    @foreach($medicine->stocks as $stock)
                                        @if($stock->quantity > 0 && $stock->expiry_date > now())
                                        <option value="{{ $stock->id }}" 
                                                data-name="{{ $medicine->name }}"
                                                data-generic="{{ $medicine->generic_name }}"
                                                data-price="{{ $stock->selling_price }}"
                                                data-available="{{ $stock->quantity }}"
                                                data-barcode="{{ $medicine->barcode }}"
                                                data-batch="{{ $stock->batch_number }}">
                                            {{ $medicine->name }} ({{ $medicine->generic_name }}) - 
                                            Batch: {{ $stock->batch_number }} | 
                                            Stock: {{ $stock->quantity }} | 
                                            ₨{{ number_format($stock->selling_price, 2) }}
                                        </option>
                                        @endif
                                    @endforeach
                                @endforeach
                            </select>
                        </div>

                        <!-- Quick Reference Codes -->
                        <div class="mt-4 bg-white border border-indigo-200 rounded p-3">
                            <p class="text-xs font-semibold text-indigo-800 mb-2">📋 Available Test Barcodes:</p>
                            <div class="flex flex-wrap gap-2 text-xs">
                                @foreach($medicines->take(5) as $medicine)
                                    <button type="button" 
                                            onclick="quickAddByBarcode('{{ $medicine->barcode }}')"
                                            class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded hover:bg-indigo-200 transition">
                                        {{ $medicine->barcode }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- SEARCH RESULT DISPLAY -->
                    <div id="searchResult" class="mb-6 hidden">
                        <div class="bg-green-50 border-2 border-green-300 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <h4 class="font-bold text-lg text-green-800">Product Found!</h4>
                                    </div>
                                    <div class="bg-white rounded p-3 mb-3">
                                        <p class="font-bold text-xl text-gray-900" id="resultName"></p>
                                        <p class="text-sm text-gray-600" id="resultGeneric"></p>
                                        <div class="grid grid-cols-2 gap-2 mt-2 text-sm">
                                            <div>
                                                <span class="text-gray-600">Barcode:</span>
                                                <span class="font-semibold" id="resultBarcode"></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">Batch:</span>
                                                <span class="font-semibold" id="resultBatch"></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">Available:</span>
                                                <span class="font-bold text-green-600 text-lg" id="resultQty"></span> units
                                            </div>
                                            <div>
                                                <span class="text-gray-600">Price:</span>
                                                <span class="font-bold text-blue-600 text-lg">₨<span id="resultPrice"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button" 
                                                onclick="addSearchedMedicine()"
                                                class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-bold">
                                            ✓ Add to Cart
                                        </button>
                                        <button type="button" 
                                                onclick="closeSearchResult()"
                                                class="bg-gray-300 text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-400">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CUSTOMER INFO -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block mb-2 font-medium">Customer Name</label>
                            <input type="text" name="customer_name" 
                                   class="w-full border rounded-lg px-4 py-2">
                        </div>
                        <div>
                            <label class="block mb-2 font-medium">Customer Phone</label>
                            <input type="text" name="customer_phone" 
                                   class="w-full border rounded-lg px-4 py-2">
                        </div>
                    </div>

                    <!-- HIDDEN CART FIELDS -->
                    <div id="hiddenCartFields"></div>

                    <!-- PAYMENT DETAILS -->
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block mb-2 font-medium">Discount (₨)</label>
                            <input type="number" name="discount" value="0" min="0" step="0.01" 
                                   id="discountInput" oninput="updateTotals()"
                                   class="w-full border rounded-lg px-4 py-2">
                        </div>
                        <div>
                            <label class="block mb-2 font-medium">Tax (₨)</label>
                            <input type="number" name="tax" value="0" min="0" step="0.01" 
                                   id="taxInput" oninput="updateTotals()"
                                   class="w-full border rounded-lg px-4 py-2">
                        </div>
                        <div>
                            <label class="block mb-2 font-medium">Payment Method *</label>
                            <select name="payment_method" required 
                                    class="w-full border rounded-lg px-4 py-2">
                                <option value="cash">💵 Cash</option>
                                <option value="card">💳 Card</option>
                                <option value="upi">📱 UPI</option>
                            </select>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="flex gap-4">
                        <button type="submit" 
                                class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-bold text-lg">
                            💰 Complete Sale (F9)
                        </button>
                        <button type="button" 
                                onclick="clearCart()"
                                class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 font-bold">
                            🗑️ Clear Cart
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT PANEL: Cart -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                <h3 class="font-bold text-xl mb-4 flex items-center justify-between">
                    <span>🛒 Cart (<span id="cartCount">0</span>)</span>
                    <button type="button" onclick="clearCart()" 
                            class="text-sm bg-red-100 text-red-600 px-3 py-1 rounded hover:bg-red-200">
                        Clear All
                    </button>
                </h3>

                <div id="cartContainer" class="mb-4 max-h-96 overflow-y-auto">
                    <div id="emptyCart" class="text-center py-12 text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p>Cart is empty</p>
                    </div>
                    <div id="cartItems"></div>
                </div>

                <div class="border-t-2 pt-4">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span id="subtotalDisplay" class="font-semibold">₨0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Discount:</span>
                            <span id="discountDisplay" class="font-semibold text-red-600">₨0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tax:</span>
                            <span id="taxDisplay" class="font-semibold">₨0.00</span>
                        </div>
                    </div>
                    <div class="border-t-2 mt-3 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold">Total:</span>
                            <span id="totalDisplay" class="text-2xl font-bold text-green-600">₨0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Camera Scanner Modal -->
<div id="scannerModal" class="fixed inset-0 bg-black bg-opacity-90 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl">
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-4 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-white">📷 Barcode Scanner</h3>
                <p class="text-sm text-purple-100">Point camera at barcode</p>
            </div>
            <button onclick="closeBarcodeScanner()" 
                    class="text-white hover:text-gray-200 text-3xl w-10 h-10 flex items-center justify-center">
                ✕
            </button>
        </div>
        
        <div class="p-6">
            <div id="reader" style="width: 100%; min-height: 400px; border-radius: 8px; overflow: hidden;"></div>
            
            <div id="scannerStatus" class="mt-4 p-3 bg-blue-100 text-blue-800 rounded-lg text-center font-semibold">
                Initializing camera...
            </div>
            
            <div class="mt-4 bg-gray-50 p-4 rounded-lg">
                <label class="block mb-2 text-sm font-semibold text-gray-700">Manual Entry (if camera fails):</label>
                <div class="flex gap-2">
                    <input type="text" 
                           id="manualBarcodeInput" 
                           placeholder="Enter barcode number manually"
                           class="flex-1 border-2 border-gray-300 rounded-lg px-4 py-2"
                           onkeypress="if(event.key === 'Enter') useManualBarcode()">
                    <button onclick="useManualBarcode()" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold">
                        Use Code
                    </button>
                </div>
            </div>
            
            <div class="mt-4 flex gap-2 justify-center">
                <button onclick="switchCamera()" 
                        class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                    🔄 Switch Camera
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="notificationToast" class="fixed top-4 right-4 z-50 hidden">
    <div id="toastContent" class="px-6 py-3 rounded-lg shadow-lg">
        <span id="toastMessage"></span>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
// ===================================
// GLOBAL VARIABLES
// ===================================
let cartItems = [];
let cartIndex = 0;
let searchedMedicine = null;
let allMedicines = [];
let html5QrCodeScanner = null;
let availableCameras = [];
let currentCameraIndex = 0;

// ===================================
// INITIALIZATION
// ===================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('POS System Loading...');
    
    // Load all medicines from dropdown
    const select = document.getElementById('medicineSelect');
    Array.from(select.options).forEach(option => {
        if (option.value) {
            allMedicines.push({
                stockId: option.value,
                name: option.dataset.name,
                generic: option.dataset.generic,
                price: parseFloat(option.dataset.price),
                available: parseInt(option.dataset.available),
                barcode: option.dataset.barcode,
                batch: option.dataset.batch
            });
        }
    });
    
    console.log('Loaded ' + allMedicines.length + ' medicines');
    
    // Setup barcode input enter key
    document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchByBarcode();
        }
    });
    
    // Focus on barcode input
    document.getElementById('barcodeInput').focus();
});

// ===================================
// CAMERA SCANNER - FIXED VERSION
// ===================================
function updateStatus(message, type) {
    const status = document.getElementById('scannerStatus');
    if (!status) return;
    
    const colors = {
        success: 'bg-green-100 text-green-800',
        error: 'bg-red-100 text-red-800',
        warning: 'bg-yellow-100 text-yellow-800',
        info: 'bg-blue-100 text-blue-800'
    };
    
    status.className = 'mt-4 p-3 rounded-lg text-center font-semibold ' + (colors[type] || colors.info);
    status.textContent = message;
    console.log('Scanner Status:', message);
}

async function openBarcodeScanner() {
    console.log('Opening barcode scanner...');
    
    const modal = document.getElementById('scannerModal');
    modal.classList.remove('hidden');
    
    updateStatus('Requesting camera access...', 'info');
    
    try {
        // Get available cameras
        availableCameras = await Html5Qrcode.getCameras();
        
        if (!availableCameras || availableCameras.length === 0) {
            throw new Error('No cameras found on this device');
        }
        
        console.log('Found ' + availableCameras.length + ' camera(s)');
        availableCameras.forEach((cam, idx) => {
            console.log('  Camera ' + idx + ': ' + (cam.label || cam.id));
        });
        
        // Select back camera if available
        currentCameraIndex = availableCameras.length - 1;
        
        await startScanning();
        
    } catch (err) {
        console.error('Camera error:', err);
        handleCameraError(err);
    }
}

async function startScanning() {
    try {
        const cameraId = availableCameras[currentCameraIndex].id;
        const cameraLabel = availableCameras[currentCameraIndex].label || 'Camera ' + (currentCameraIndex + 1);
        
        updateStatus('Starting ' + cameraLabel + '...', 'info');
        
        // Clean up previous scanner
        if (html5QrCodeScanner) {
            try {
                await html5QrCodeScanner.stop();
                html5QrCodeScanner.clear();
            } catch (e) {
                console.log('Cleanup warning:', e);
            }
        }
        
        // Create new scanner instance
        html5QrCodeScanner = new Html5Qrcode("reader");
        
        // CRITICAL: Simple config without format restrictions
        const config = {
            fps: 10,
            qrbox: { width: 300, height: 200 }
        };
        
        // Start scanning
        await html5QrCodeScanner.start(
            cameraId,
            config,
            onScanSuccess,
            onScanError
        );
        
        updateStatus('✅ Scanner ready! Point at barcode', 'success');
        showNotification('Camera active - scan your barcode!', 'success');
        
    } catch (err) {
        console.error('Start scanning error:', err);
        handleCameraError(err);
    }
}

function onScanSuccess(decodedText, decodedResult) {
    console.log('✅ BARCODE SCANNED:', decodedText);
    console.log('Format:', decodedResult.result.format);
    
    // Haptic feedback
    if (navigator.vibrate) {
        navigator.vibrate([100, 50, 100]);
    }
    
    updateStatus('✅ Barcode detected: ' + decodedText, 'success');
    
    // Stop scanner and process
    html5QrCodeScanner.stop().then(() => {
        document.getElementById('barcodeInput').value = decodedText.trim();
        closeBarcodeScanner();
        setTimeout(() => searchByBarcode(), 100);
    }).catch(err => {
        console.error('Stop error:', err);
        closeBarcodeScanner();
    });
}

function onScanError(errorMessage) {
    // Ignore frequent "not found" errors
    if (!errorMessage.includes('NotFoundException')) {
        console.warn('Scan error:', errorMessage);
    }
}

function handleCameraError(err) {
    console.error('Camera Error:', err);
    
    let message = 'Camera Error:\n\n';
    
    if (err.name === 'NotAllowedError') {
        message += '❌ Camera permission denied.\n\n';
        message += 'Please allow camera access in your browser settings and reload the page.';
    } else if (err.name === 'NotFoundError') {
        message += '❌ No camera found.\n\n';
        message += 'Please ensure your device has a camera.';
    } else if (err.name === 'NotReadableError') {
        message += '❌ Camera is in use by another application.\n\n';
        message += 'Please close other apps using the camera.';
    } else if (err.message && err.message.includes('secure')) {
        message += '❌ Camera requires HTTPS connection.\n\n';
        message += 'Please use HTTPS or localhost.';
    } else {
        message += err.message || 'Unknown error occurred';
    }
    
    alert(message);
    updateStatus('❌ ' + (err.message || 'Camera error'), 'error');
    closeBarcodeScanner();
}

async function switchCamera() {
    if (availableCameras.length <= 1) {
        showNotification('Only one camera available', 'warning');
        return;
    }
    
    currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;
    updateStatus('Switching camera...', 'info');
    
    try {
        await startScanning();
    } catch (err) {
        console.error('Switch camera error:', err);
        showNotification('Failed to switch camera', 'error');
    }
}

function closeBarcodeScanner() {
    const modal = document.getElementById('scannerModal');
    modal.classList.add('hidden');
    
    if (html5QrCodeScanner) {
        html5QrCodeScanner.stop()
            .then(() => {
                html5QrCodeScanner.clear();
                html5QrCodeScanner = null;
                console.log('Scanner stopped');
            })
            .catch(err => {
                console.error('Stop error:', err);
                html5QrCodeScanner = null;
            });
    }
    
    setTimeout(() => {
        document.getElementById('barcodeInput').focus();
    }, 100);
}

function useManualBarcode() {
    const input = document.getElementById('manualBarcodeInput');
    const barcode = input.value.trim();
    
    if (!barcode) {
        showNotification('Please enter a barcode', 'warning');
        return;
    }
    
    document.getElementById('barcodeInput').value = barcode;
    closeBarcodeScanner();
    searchByBarcode();
    input.value = '';
}

// ===================================
// BARCODE SEARCH
// ===================================
function searchByBarcode() {
    const barcode = document.getElementById('barcodeInput').value.trim();
    
    if (!barcode) {
        showNotification('⚠️ Please enter a barcode', 'warning');
        return;
    }
    
    console.log('Searching for barcode:', barcode);
    
    // Find medicine with exact or partial match
    const medicine = allMedicines.find(m => {
        if (!m.barcode) return false;
        
        // Exact match
        if (m.barcode === barcode) return true;
        
        // Case insensitive match
        if (m.barcode.toLowerCase() === barcode.toLowerCase()) return true;
        
        // Match without leading zeros
        const cleanBarcode = barcode.replace(/^0+/, '');
        const cleanMedicineBarcode = m.barcode.replace(/^0+/, '');
        if (cleanMedicineBarcode === cleanBarcode) return true;
        
        return false;
    });
    
    if (medicine) {
        console.log('✅ Found:', medicine.name);
        displaySearchResult(medicine);
    } else {
        console.log('❌ Not found:', barcode);
        showNotification('❌ Product not found: ' + barcode, 'error');
    }
    
    document.getElementById('barcodeInput').value = '';
    document.getElementById('barcodeInput').focus();
}

function quickAddByBarcode(barcode) {
    document.getElementById('barcodeInput').value = barcode;
    searchByBarcode();
}

// ===================================
// SEARCH BY NAME
// ===================================
function searchByName(query) {
    const resultsDiv = document.getElementById('nameSearchResults');
    
    if (!query || query.length < 2) {
        resultsDiv.classList.add('hidden');
        return;
    }
    
    const matches = allMedicines.filter(m => 
        m.name.toLowerCase().includes(query.toLowerCase()) ||
        m.generic.toLowerCase().includes(query.toLowerCase())
    ).slice(0, 5);
    
    if (matches.length > 0) {
        resultsDiv.innerHTML = matches.map(m => 
            '<div onclick="selectMedicineByIndex(' + allMedicines.indexOf(m) + ')" class="p-3 hover:bg-indigo-50 cursor-pointer border-b flex justify-between">' +
                '<div>' +
                    '<p class="font-semibold">' + m.name + '</p>' +
                    '<p class="text-xs text-gray-600">' + m.generic + '</p>' +
                '</div>' +
                '<span class="text-sm font-bold text-green-600">₨' + m.price.toFixed(2) + '</span>' +
            '</div>'
        ).join('');
        resultsDiv.classList.remove('hidden');
    } else {
        resultsDiv.innerHTML = '<p class="p-3 text-gray-500 text-center">No matches</p>';
        resultsDiv.classList.remove('hidden');
    }
}

function selectMedicineByIndex(index) {
    const medicine = allMedicines[index];
    if (medicine) {
        displaySearchResult(medicine);
        clearNameSearch();
    }
}

function clearNameSearch() {
    document.getElementById('productNameSearch').value = '';
    document.getElementById('nameSearchResults').classList.add('hidden');
}

// ===================================
// DROPDOWN SELECTION
// ===================================
function selectFromDropdown() {
    const select = document.getElementById('medicineSelect');
    if (select.value) {
        const option = select.options[select.selectedIndex];
        displaySearchResult({
            stockId: option.value,
            name: option.dataset.name,
            generic: option.dataset.generic,
            price: parseFloat(option.dataset.price),
            available: parseInt(option.dataset.available),
            barcode: option.dataset.barcode,
            batch: option.dataset.batch
        });
        select.value = '';
    }
}

// ===================================
// DISPLAY & CART
// ===================================
function displaySearchResult(medicine) {
    searchedMedicine = medicine;
    
    document.getElementById('resultName').textContent = medicine.name;
    document.getElementById('resultGeneric').textContent = medicine.generic;
    document.getElementById('resultBarcode').textContent = medicine.barcode;
    document.getElementById('resultBatch').textContent = medicine.batch;
    document.getElementById('resultQty').textContent = medicine.available;
    document.getElementById('resultPrice').textContent = medicine.price.toFixed(2);
    
    document.getElementById('searchResult').classList.remove('hidden');
    document.getElementById('searchResult').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function closeSearchResult() {
    document.getElementById('searchResult').classList.add('hidden');
    searchedMedicine = null;
    document.getElementById('barcodeInput').focus();
}

function addSearchedMedicine() {
    if (searchedMedicine) {
        addToCart(searchedMedicine);
        closeSearchResult();
    }
}

function addToCart(item) {
    const existing = cartItems.find(i => i.stockId === item.stockId);
    
    if (existing) {
        if (existing.quantity < existing.available) {
            existing.quantity++;
            renderCart();
            showNotification('✓ Quantity increased', 'success');
        } else {
            showNotification('⚠️ Maximum stock reached', 'warning');
        }
    } else {
        const index = cartIndex++;
        cartItems.push({...item, index, quantity: 1});
        renderCart();
        showNotification('✓ Added: ' + item.name, 'success');
    }
}

function removeFromCart(index) {
    cartItems = cartItems.filter(item => item.index !== index);
    renderCart();
    showNotification('✓ Removed', 'info');
}

function updateQuantity(index, quantity) {
    const item = cartItems.find(i => i.index === index);
    if (item) {
        item.quantity = Math.min(Math.max(1, parseInt(quantity)), item.available);
        renderCart();
    }
}

function incrementQuantity(index) {
    const item = cartItems.find(i => i.index === index);
    if (item && item.quantity < item.available) {
        item.quantity++;
        renderCart();
    } else {
        showNotification('⚠️ Maximum stock reached', 'warning');
    }
}

function decrementQuantity(index) {
    const item = cartItems.find(i => i.index === index);
    if (item && item.quantity > 1) {
        item.quantity--;
        renderCart();
    }
}

function clearCart() {
    if (cartItems.length > 0 && confirm('Clear all items from cart?')) {
        cartItems = [];
        renderCart();
        showNotification('Cart cleared', 'info');
    }
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const emptyCart = document.getElementById('emptyCart');
    const hiddenFields = document.getElementById('hiddenCartFields');
    
    if (cartItems.length === 0) {
        emptyCart.classList.remove('hidden');
        container.innerHTML = '';
        hiddenFields.innerHTML = '';
        document.getElementById('cartCount').textContent = '0';
        updateTotals();
        return;
    }
    
    emptyCart.classList.add('hidden');
    document.getElementById('cartCount').textContent = cartItems.length;
    
    // Render visible cart items
    container.innerHTML = cartItems.map(item => 
        '<div class="border-b pb-3 mb-3">' +
            '<div class="flex justify-between items-start mb-2">' +
                '<div class="flex-1">' +
                    '<p class="font-semibold text-sm">' + item.name + '</p>' +
                    '<p class="text-xs text-gray-500">' + item.barcode + ' | Batch: ' + item.batch + '</p>' +
                '</div>' +
                '<button type="button" onclick="removeFromCart(' + item.index + ')" class="text-red-500 hover:text-red-700 text-lg">✕</button>' +
            '</div>' +
            '<div class="flex justify-between items-center">' +
                '<div class="flex items-center gap-2">' +
                    '<button type="button" onclick="decrementQuantity(' + item.index + ')" class="bg-gray-200 px-2 py-1 rounded hover:bg-gray-300 text-sm font-bold">−</button>' +
                    '<input type="number" value="' + item.quantity + '" min="1" max="' + item.available + '" ' +
                           'onchange="updateQuantity(' + item.index + ', this.value)" ' +
                           'class="w-12 text-center border rounded py-1">' +
                    '<button type="button" onclick="incrementQuantity(' + item.index + ')" class="bg-gray-200 px-2 py-1 rounded hover:bg-gray-300 text-sm font-bold">+</button>' +
                '</div>' +
                '<div class="text-right">' +
                    '<p class="text-xs text-gray-500">₨' + item.price.toFixed(2) + ' each</p>' +
                    '<p class="font-bold text-blue-600">₨' + (item.price * item.quantity).toFixed(2) + '</p>' +
                '</div>' +
            '</div>' +
        '</div>'
    ).join('');
    
    // Render hidden form fields
    hiddenFields.innerHTML = cartItems.map(item => 
        '<input type="hidden" name="items[' + item.index + '][stock_id]" value="' + item.stockId + '">' +
        '<input type="hidden" name="items[' + item.index + '][quantity]" value="' + item.quantity + '">' +
        '<input type="hidden" name="items[' + item.index + '][price]" value="' + item.price + '">'
    ).join('');
    
    updateTotals();
}

// ===================================
// TOTALS CALCULATION
// ===================================
function updateTotals() {
    const subtotal = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const tax = parseFloat(document.getElementById('taxInput').value) || 0;
    const total = subtotal - discount + tax;
    
    document.getElementById('subtotalDisplay').textContent = '₨' + subtotal.toFixed(2);
    document.getElementById('discountDisplay').textContent = '₨' + discount.toFixed(2);
    document.getElementById('taxDisplay').textContent = '₨' + tax.toFixed(2);
    document.getElementById('totalDisplay').textContent = '₨' + total.toFixed(2);
}

// ===================================
// NOTIFICATIONS
// ===================================
function showNotification(message, type) {
    const toast = document.getElementById('notificationToast');
    const content = document.getElementById('toastContent');
    const messageEl = document.getElementById('toastMessage');
    
    if (!toast || !content || !messageEl) return;
    
    const colors = {
        success: 'bg-green-600 text-white',
        error: 'bg-red-600 text-white',
        warning: 'bg-yellow-600 text-white',
        info: 'bg-blue-600 text-white'
    };
    
    content.className = 'px-6 py-3 rounded-lg shadow-lg ' + (colors[type] || colors.info);
    messageEl.textContent = message;
    
    toast.classList.remove('hidden');
    
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
}

// ===================================
// FORM VALIDATION
// ===================================
document.getElementById('saleForm').addEventListener('submit', function(e) {
    if (cartItems.length === 0) {
        e.preventDefault();
        showNotification('⚠️ Cart is empty! Add items before completing sale.', 'warning');
        return false;
    }
    
    const total = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const tax = parseFloat(document.getElementById('taxInput').value) || 0;
    const finalTotal = total - discount + tax;
    
    if (finalTotal < 0) {
        e.preventDefault();
        showNotification('⚠️ Total cannot be negative!', 'warning');
        return false;
    }
    
    if (!confirm('Complete sale of ₨' + finalTotal.toFixed(2) + '?')) {
        e.preventDefault();
        return false;
    }
    
    showNotification('✅ Processing sale...', 'success');
});
</script>

@endsection