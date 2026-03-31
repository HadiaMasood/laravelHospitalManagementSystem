@extends('layouts.app')

@section('title', 'New Sale - POS')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">🛒 Point of Sale (POS)</h1>
            <p class="text-gray-600 mt-1">Create a new sale</p>
        </div>
        <a href="{{ route('admin.sales.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 font-semibold">
            ← Back to Sales
        </a>
    </div>

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
    @endif

    <form action="{{ route('admin.sales.store') }}" method="POST" id="salesForm">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left: Medicine Selection -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Select Medicines</h2>
                    
                    <!-- Medicine Search -->
                    <div class="mb-4">
                        <input type="text" id="medicineSearch" placeholder="Search medicines by name or barcode..." 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Medicine List -->
                    <div class="max-h-96 overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="medicineList">
                            @foreach($medicines as $medicine)
                            @if($medicine->stocks->count() > 0)
                            <div class="border rounded-lg p-4 hover:border-blue-500 cursor-pointer medicine-item" 
                                 data-name="{{ strtolower($medicine->name) }}"
                                 data-barcode="{{ strtolower($medicine->barcode ?? '') }}">
                                <h3 class="font-bold text-gray-800">{{ $medicine->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $medicine->generic_name }}</p>
                                <p class="text-sm text-gray-600 mt-2">Stock: {{ $medicine->stocks->sum('quantity') }}</p>
                                <p class="text-lg font-bold text-green-600 mt-1">₨{{ number_format($medicine->unit_price, 2) }}</p>
                                
                                @foreach($medicine->stocks as $stock)
                                <button type="button" 
                                        class="mt-2 bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 w-full add-to-cart"
                                        data-stock-id="{{ $stock->id }}"
                                        data-medicine-name="{{ $medicine->name }}"
                                        data-price="{{ $stock->selling_price }}"
                                        data-max-qty="{{ $stock->quantity }}"
                                        data-batch="{{ $stock->batch_number }}">
                                    Add (Batch: {{ $stock->batch_number }})
                                </button>
                                @endforeach
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Cart & Customer Info -->
            <div>
                <!-- Customer Info -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Customer Info</h2>
                    <div class="space-y-3">
                        <input type="text" name="customer_name" placeholder="Customer Name (Optional)" 
                               class="w-full border border-gray-300 rounded px-4 py-2">
                        <input type="text" name="customer_phone" placeholder="Phone (Optional)" 
                               class="w-full border border-gray-300 rounded px-4 py-2">
                    </div>
                </div>

                <!-- Cart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Cart (<span id="cartCount">0</span> items)</h2>
                    
                    <div id="cartItems" class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                        <p class="text-gray-500 text-center py-4">No items in cart</p>
                    </div>

                    <!-- Totals -->
                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span class="font-bold" id="subtotalDisplay">₨0.00</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Discount:</span>
                            <input type="number" name="discount" value="0" min="0" step="0.01" 
                                   class="border rounded px-2 py-1 w-24 text-right" id="discountInput">
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Tax:</span>
                            <input type="number" name="tax" value="0" min="0" step="0.01" 
                                   class="border rounded px-2 py-1 w-24 text-right" id="taxInput">
                        </div>
                        <div class="flex justify-between text-xl font-bold border-t pt-2">
                            <span>Total:</span>
                            <span class="text-green-600" id="totalDisplay">₨0.00</span>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mt-4">
                        <label class="block font-medium mb-2">Payment Method:</label>
                        <select name="payment_method" required class="w-full border border-gray-300 rounded px-4 py-2">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="upi">UPI</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 mt-4">
                        Complete Sale 
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let cart = [];

// Search medicines
document.getElementById('medicineSearch').addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    document.querySelectorAll('.medicine-item').forEach(item => {
        const name = item.dataset.name;
        const barcode = item.dataset.barcode;
        if (name.includes(search) || barcode.includes(search)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

// Add to cart
document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', function() {
        const stockId = this.dataset.stockId;
        const medicineName = this.dataset.medicineName;
        const price = parseFloat(this.dataset.price);
        const maxQty = parseInt(this.dataset.maxQty);
        const batch = this.dataset.batch;

        // Check if already in cart
        const existingItem = cart.find(item => item.stockId === stockId);
        if (existingItem) {
            if (existingItem.quantity < maxQty) {
                existingItem.quantity++;
            } else {
                alert('Maximum quantity reached for this batch!');
                return;
            }
        } else {
            cart.push({
                stockId: stockId,
                medicineName: medicineName,
                price: price,
                quantity: 1,
                maxQty: maxQty,
                batch: batch
            });
        }

        updateCart();
    });
});

function updateCart() {
    const cartItemsDiv = document.getElementById('cartItems');
    const cartCountSpan = document.getElementById('cartCount');
    
    if (cart.length === 0) {
        cartItemsDiv.innerHTML = '<p class="text-gray-500 text-center py-4">No items in cart</p>';
        cartCountSpan.textContent = '0';
        updateTotals();
        return;
    }

    cartCountSpan.textContent = cart.length;

    let html = '';
    cart.forEach((item, index) => {
        html += `
            <div class="border rounded p-3">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-medium text-sm">${item.medicineName}</p>
                        <p class="text-xs text-gray-500">Batch: ${item.batch}</p>
                    </div>
                    <button type="button" onclick="removeFromCart(${index})" class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="updateQuantity(${index}, -1)" class="bg-gray-200 px-2 py-1 rounded">-</button>
                        <span class="font-bold">${item.quantity}</span>
                        <button type="button" onclick="updateQuantity(${index}, 1)" class="bg-gray-200 px-2 py-1 rounded">+</button>
                    </div>
                    <span class="font-bold text-green-600">₨${(item.price * item.quantity).toFixed(2)}</span>
                </div>
                <input type="hidden" name="items[${index}][stock_id]" value="${item.stockId}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
            </div>
        `;
    });

    cartItemsDiv.innerHTML = html;
    updateTotals();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}

function updateQuantity(index, change) {
    const item = cart[index];
    const newQty = item.quantity + change;
    
    if (newQty < 1) {
        removeFromCart(index);
        return;
    }
    
    if (newQty > item.maxQty) {
        alert('Maximum quantity reached!');
        return;
    }
    
    item.quantity = newQty;
    updateCart();
}

function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const tax = parseFloat(document.getElementById('taxInput').value) || 0;
    const total = subtotal - discount + tax;

    document.getElementById('subtotalDisplay').textContent = `₨${subtotal.toFixed(2)}`;
    document.getElementById('totalDisplay').textContent = `₨${total.toFixed(2)}`;
}

// Update totals when discount/tax changes
document.getElementById('discountInput').addEventListener('input', updateTotals);
document.getElementById('taxInput').addEventListener('input', updateTotals);

// Form validation
document.getElementById('salesForm').addEventListener('submit', function(e) {
    if (cart.length === 0) {
        e.preventDefault();
        alert('Please add at least one item to the cart!');
    }
});
</script>
@endsection