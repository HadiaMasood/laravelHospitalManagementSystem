@extends('layouts.app')

@section('title', 'POS - Point of Sale')

@section('content')
<!-- Tailwind CDN for quick styling (remove in production if you build CSS) -->
<script src="https://cdn.tailwindcss.com"></script>

<style>
  /* Compact scanner styling */
  #reader { width: 100%; height: 250px; border-radius: 8px; background: #0b1220; }
  #notificationToast { transition: transform .12s ease, opacity .12s ease; transform-origin: top right; }
  #notificationToast.hidden { opacity: 0; transform: translateY(-6px) scale(.98); pointer-events: none; }
  #notificationToast.show { opacity: 1; transform: translateY(0) scale(1); }
</style>

<div class="container mx-auto px-4 py-8">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">🛒 Point of Sale System</h1>
    <div class="flex gap-2">
      <button type="button" onclick="openBarcodeScanner()" 
              class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
         Camera Scan
      </button>

      <a href="{{ route('admin.sales.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">View Sales</a>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- LEFT: Product Search & Scan -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
      <form action="{{ route('admin.sales.store') }}" method="POST" id="saleForm">
        @csrf

        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border rounded-lg p-6 mb-6">
          <h3 class="text-xl font-bold text-indigo-800 mb-4">Product Scanning & Lookup</h3>

          <!-- Method 1: Input -->
          <div class="mb-4">
            <label class="block mb-2 font-semibold text-gray-700"> Scan / Type Barcode</label>
            <div class="flex gap-2">
              <input id="barcodeInput" type="text" placeholder="Scan or type barcode (e.g., GTIN/EAN)..." class="flex-1 border rounded px-4 py-3" autocomplete="off" />
              <button type="button" onclick="onManualSearch()" class="bg-indigo-600 text-white px-5 py-3 rounded hover:bg-indigo-700">Search</button>
            </div>
            <p class="text-xs text-gray-500 mt-1">Focus here and scan with camera or hardware scanner. Press Enter to search.</p>
          </div>

          <!-- Method 2: Camera -->
          <div class="mb-4">
            <label class="block mb-2 font-semibold text-gray-700"> Camera Barcode Scanner</label>
            <button type="button" onclick="openBarcodeScanner()" class="w-full bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700">Open Camera Scanner</button>
            <p class="text-xs text-gray-500 mt-1">Allow camera permission (HTTPS / localhost required).</p>
          </div>

          <!-- Method 3: Name -->
          <div class="mb-4">
            <label class="block mb-2 font-semibold text-gray-700">Search by Product Name</label>
            <div class="flex gap-2">
              <input id="productNameSearch" type="text" placeholder="Type medicine name..." class="flex-1 border rounded px-4 py-3" oninput="searchByName(this.value)" />
              <button type="button" onclick="clearNameSearch()" class="bg-gray-400 text-white px-4 py-3 rounded hover:bg-gray-500">Clear</button>
            </div>
            <div id="nameSearchResults" class="mt-2 max-h-48 overflow-y-auto hidden bg-white rounded shadow-sm border"></div>
          </div>

          <!-- Method 4: Browse -->
          <div>
            <label class="block mb-2 font-semibold text-gray-700">Browse All Products</label>
            <select id="medicineSelect" class="w-full border rounded px-3 py-2" onchange="selectFromDropdown()">
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
                      {{ $medicine->name }} — Batch {{ $stock->batch_number }} • ₨{{ number_format($stock->selling_price, 2) }}
                    </option>
                  @endif
                @endforeach
              @endforeach
            </select>
          </div>

        </div>

        <!-- Search result -->
        <div id="searchResult" class="hidden mb-6 bg-green-50 border-2 border-green-200 rounded-lg p-4 shadow-sm">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <h4 id="resultName" class="font-bold text-lg text-gray-800"></h4>
              <div id="resultGeneric" class="text-sm text-gray-600 mt-1"></div>

              <div class="grid grid-cols-2 gap-3 mt-3 text-sm">
                <div><span class="text-gray-600">Barcode:</span> <span id="resultBarcode" class="font-semibold text-gray-800"></span></div>
                <div><span class="text-gray-600">Batch:</span> <span id="resultBatch" class="font-semibold text-gray-800"></span></div>
                <div><span class="text-gray-600">Available:</span> <span id="resultQty" class="font-bold text-green-600"></span></div>
                <div><span class="text-gray-600">Price:</span> <span id="resultPrice" class="font-bold text-blue-600"></span></div>
              </div>
            </div>

            <div class="flex flex-col gap-2 ml-4">
              <button type="button" onclick="addSearchedMedicine()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-semibold whitespace-nowrap">✓ Add to Cart</button>
              <button type="button" onclick="closeSearchResult()" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">Cancel</button>
            </div>
          </div>
        </div>

        <!-- Customer & Payment -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          <input type="text" name="customer_name" placeholder="Customer name (Optional)" class="px-4 py-2 border rounded" />
          <input type="text" name="customer_phone" placeholder="Customer phone (Optional)" class="px-4 py-2 border rounded" />
        </div>

        <div id="hiddenCartFields"></div>

       <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <div>
    <label class="block mb-2 text-sm font-semibold text-gray-700"> Discount (₨)</label>
    <input id="discountInput" name="discount" type="number" min="0" step="0.01" value="0" class="px-4 py-2 border rounded w-full" oninput="updateTotals()" placeholder="0.00" />
  </div>
  
  <div>
    <label class="block mb-2 text-sm font-semibold text-gray-700"> Tax (₨)</label>
    <input id="taxInput" name="tax" type="number" min="0" step="0.01" value="0" class="px-4 py-2 border rounded w-full" oninput="updateTotals()" placeholder="0.00" />
  </div>
  
  <div>
    <label class="block mb-2 text-sm font-semibold text-gray-700"> Payment Method</label>
    <select name="payment_method" required class="px-4 py-2 border rounded w-full">
      <option value="cash"> Cash</option>
      <option value="card"> Card</option>
      <option value="upi"> UPI</option>
    </select>
  </div>
</div>

        <div class="flex gap-4">
          <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700"> Complete Sale</button>
          <button type="button" onclick="clearCart()" class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600">🗑️ Clear Cart</button>
        </div>
      </form>
    </div>

    <!-- RIGHT: Cart -->
    <div class="bg-white rounded-lg shadow p-6 sticky top-4">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-xl">🛒 Cart (<span id="cartCount">0</span>)</h3>
        <button onclick="clearCart()" class="text-sm bg-red-50 text-red-600 px-3 py-1 rounded hover:bg-red-100">Clear</button>
      </div>

      <div id="cartContainer" class="max-h-96 overflow-auto mb-4">
        <div id="emptyCart" class="text-center text-gray-400 py-8">
          <svg class="w-16 h-16 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          <p>Cart is empty</p>
        </div>
        <div id="cartItems" class="space-y-3"></div>
      </div>

      <div class="border-t pt-4 text-sm space-y-2">
        <div class="flex justify-between"><span>Subtotal</span><strong id="subtotalDisplay">₨0.00</strong></div>
        <div class="flex justify-between"><span>Discount</span><strong id="discountDisplay" class="text-red-600">₨0.00</strong></div>
        <div class="flex justify-between"><span>Tax</span><strong id="taxDisplay">₨0.00</strong></div>
        <div class="mt-3 pt-3 border-t flex justify-between items-center">
          <span class="text-lg font-bold">Total</span>
          <span id="totalDisplay" class="text-2xl font-bold text-green-600">₨0.00</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- COMPACT Scanner Modal -->
<div id="scannerModal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 rounded-t-xl flex justify-between items-center text-white">
      <div>
        <h3 class="font-semibold text-lg">📷 Barcode Scanner</h3>
        <div class="text-xs opacity-90">Point camera at barcode</div>
      </div>
      <button onclick="closeBarcodeScanner()" class="text-white hover:text-gray-200 text-2xl leading-none">✕</button>
    </div>

    <div class="p-6">
      <!-- Compact camera preview -->
      <div id="reader" class="rounded-lg shadow-inner"></div>
      <div id="scannerStatus" class="mt-3 p-2 bg-blue-50 text-blue-700 rounded text-center text-sm">Initializing camera...</div>

      <!-- Manual Entry -->
      <div class="mt-4 bg-gray-50 p-4 rounded-lg">
        <label class="block mb-2 text-sm font-medium text-gray-700">Or enter barcode manually:</label>
        <div class="flex gap-2">
          <input id="manualBarcodeInput" class="flex-1 border rounded px-3 py-2" placeholder="Enter barcode..." onkeypress="if(event.key==='Enter') useManualBarcode()" />
          <button onclick="useManualBarcode()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Use</button>
        </div>
      </div>

      <!-- Switch Camera Button -->
      <div class="mt-3 text-center">
        <button onclick="switchCamera()" class="px-4 py-2 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 text-sm">🔄 Switch Camera</button>
      </div>
    </div>
  </div>
</div>

<!-- Toast Notification -->
<div id="notificationToast" class="fixed top-6 right-6 z-50 hidden">
  <div id="toastContent" class="px-5 py-3 rounded-lg shadow-lg bg-blue-600 text-white">
    <span id="toastMessage"></span>
  </div>
</div>

<!-- html5-qrcode library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
// ------------------------
// Robust POS with Compact Scanner
// ------------------------
let cartItems = [];
let cartIndex = 0;
let searchedMedicine = null;
let allMedicines = [];
let html5QrCodeScanner = null;
let availableCameras = [];
let currentCameraIndex = 0;

// Helpers
function normalizeDigits(s){ return s == null ? '' : String(s).replace(/[^0-9]/g,'').trim(); }
function showNotification(msg, type='info'){
  const toast = document.getElementById('notificationToast');
  const content = document.getElementById('toastContent');
  const messageEl = document.getElementById('toastMessage');
  if(!toast||!content||!messageEl) return;
  const colors = { 
    success:'bg-green-600 text-white', 
    error:'bg-red-600 text-white', 
    warning:'bg-yellow-500 text-black', 
    info:'bg-blue-600 text-white' 
  };
  content.className = 'px-5 py-3 rounded-lg shadow-lg ' + (colors[type]||colors.info);
  messageEl.textContent = msg;
  toast.classList.remove('hidden'); 
  toast.classList.add('show');
  setTimeout(()=>{ 
    toast.classList.add('hidden'); 
    toast.classList.remove('show'); 
  }, 3000);
}

// Build local medicines list from select
document.addEventListener('DOMContentLoaded', function(){
  const select = document.getElementById('medicineSelect');
  if (select) {
    Array.from(select.options).forEach(opt=>{
      if (opt.value) {
        allMedicines.push({
          stockId: opt.value,
          name: opt.dataset.name || '',
          generic: opt.dataset.generic || '',
          price: parseFloat(opt.dataset.price) || 0,
          available: parseInt(opt.dataset.available) || 0,
          barcode: opt.dataset.barcode || '',
          batch: opt.dataset.batch || ''
        });
      }
    });
  }
  console.log('Loaded medicines:', allMedicines.length);

  // Enter key on barcode input
  const bi = document.getElementById('barcodeInput');
  if (bi) bi.addEventListener('keypress', (e)=>{ 
    if(e.key==='Enter'){ 
      e.preventDefault(); 
      onManualSearch(); 
    }
  });
});

// GS1-aware parser to extract GTIN/batch/expiry and candidate codes
function parseGS1(payload){
  const out = { gtin:null, batch:null, expiry:null, candidates:[] };
  if(!payload) return out;

  // (01)GTIN / (17)expiry / (10)batch
  const p01 = payload.match(/\(01\)(\d{13,14})/);
  const p17 = payload.match(/\(17\)(\d{6})/);
  const p10 = payload.match(/\(10\)([^()]+)/);
  if(p01) out.gtin = normalizeDigits(p01[1]);
  if(p17) out.expiry = p17[1];
  if(p10) out.batch = p10[1].trim();

  // Try AI without parentheses
  if(!out.gtin){
    const ai01 = payload.match(/01(\d{13,14})/);
    if(ai01) out.gtin = normalizeDigits(ai01[1]);
  }

  // Fallback: any 8/12/13/14 digit sequence
  if(!out.gtin){
    const seq = payload.match(/(?:\D|^)(\d{8}|\d{12}|\d{13}|\d{14})(?:\D|$)/);
    if(seq) out.gtin = normalizeDigits(seq[1]);
  }

  if(out.gtin){
    out.candidates.push(out.gtin);
    out.candidates.push(out.gtin.replace(/^0+/, ''));
    if(out.gtin.length>=13) out.candidates.push(out.gtin.slice(-13));
    if(out.gtin.length>=12) out.candidates.push(out.gtin.slice(-12));
    if(out.gtin.length>=8) out.candidates.push(out.gtin.slice(-8));
  }

  // Any digit groups
  const allDigits = payload.match(/\d+/g) || [];
  allDigits.forEach(d=>{
    const nd = normalizeDigits(d);
    if(nd){ 
      out.candidates.push(nd); 
      out.candidates.push(nd.replace(/^0+/, '')); 
    }
  });

  out.candidates = Array.from(new Set(out.candidates)).filter(Boolean);
  return out;
}

// Camera scanner functions
async function openBarcodeScanner(){
  const modal = document.getElementById('scannerModal');
  if(!modal) { alert('Scanner element missing'); return; }
  modal.classList.remove('hidden');
  updateScannerStatus('Requesting camera...', 'info');

  try{
    availableCameras = await Html5Qrcode.getCameras();
    if(!availableCameras || availableCameras.length===0) throw new Error('No camera devices found');
    currentCameraIndex = availableCameras.length - 1; // Use back camera by default
    await startScanning();
  } catch(err){ handleCameraError(err); }
}

async function startScanning(){
  if(!availableCameras || availableCameras.length===0) return handleCameraError(new Error('No cameras available'));
  const camId = availableCameras[currentCameraIndex].id;
  if(html5QrCodeScanner){
    try{ await html5QrCodeScanner.stop(); html5QrCodeScanner.clear(); }catch(e){ console.warn(e); }
  }
  html5QrCodeScanner = new Html5Qrcode('reader');
  const config = { fps: 10, qrbox: { width: 280, height: 140 } }; // Compact size
  try{
    await html5QrCodeScanner.start(camId, config, onScanSuccess, onScanError);
    updateScannerStatus('Scanner ready — point at barcode', 'success');
    showNotification('Camera active', 'info');
  } catch(err){ handleCameraError(err); }
}

function onScanError(err){ /* ignore frequent minor errors */ }

function onScanSuccess(decodedText){
  try{
    navigator.vibrate?.(100); // Haptic feedback
    const parsed = parseGS1(decodedText);
    console.log('Decoded:', decodedText, parsed);
    const candidates = parsed.candidates.length ? parsed.candidates : [ normalizeDigits(decodedText) ];
    
    // Stop scanner
    if(html5QrCodeScanner){
      html5QrCodeScanner.stop()
        .then(()=>{ html5QrCodeScanner.clear(); html5QrCodeScanner = null; })
        .catch(()=>{ html5QrCodeScanner = null; });
    }
    closeBarcodeScanner();
    setTimeout(()=> searchByBarcodeCandidates(candidates), 120);
  } catch(e){ 
    console.error(e); 
    showNotification('Scan error', 'error'); 
  }
}

function switchCamera(){
  if(!availableCameras || availableCameras.length <= 1){ 
    showNotification('Only one camera available', 'warning'); 
    return; 
  }
  currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;
  showNotification('Switching camera...', 'info');
  startScanning();
}

function closeBarcodeScanner(){
  const modal = document.getElementById('scannerModal'); 
  if(modal) modal.classList.add('hidden');
  
  // Stop camera
  if(html5QrCodeScanner){
    html5QrCodeScanner.stop()
      .then(()=>{ html5QrCodeScanner.clear(); html5QrCodeScanner = null; })
      .catch(()=>{ html5QrCodeScanner = null; });
  }
  
  setTimeout(()=> document.getElementById('barcodeInput')?.focus(), 120);
}

function useManualBarcode(){
  const input = document.getElementById('manualBarcodeInput');
  const code = input?.value?.trim();
  if(!code) return showNotification('Please enter a barcode', 'warning');
  input.value = '';
  closeBarcodeScanner();
  searchByBarcodeCandidates([ normalizeDigits(code) ]);
}

function updateScannerStatus(msg, type='info'){
  const el = document.getElementById('scannerStatus'); 
  if(!el) return;
  el.textContent = msg;
  const classes = {
    success: 'bg-green-50 text-green-800',
    error: 'bg-red-50 text-red-800',
    info: 'bg-blue-50 text-blue-700'
  };
  el.className = 'mt-3 p-2 rounded text-center text-sm ' + (classes[type] || classes.info);
}

function handleCameraError(err){
  console.error(err);
  const msg = err?.message || String(err);
  showNotification('Camera error: ' + msg, 'error');
  updateScannerStatus('Camera error: ' + msg, 'error');
}

// Search using candidates
function searchByBarcodeCandidates(candidates){
  if(!Array.isArray(candidates)) candidates = [candidates];
  console.log('Trying candidates:', candidates);
  
  const found = allMedicines.find(m=>{
    const stored = normalizeDigits(m.barcode || '');
    if(!stored) return false;
    return candidates.some(c=>{
      const nc = normalizeDigits(c);
      if(!nc) return false;
      if(stored === nc) return true;
      if(stored.replace(/^0+/, '') === nc.replace(/^0+/, '')) return true;
      if(stored.endsWith(nc) || nc.endsWith(stored)) return true;
      return false;
    });
  });

  if(found) {
    displaySearchResult(found);
    showNotification('Product found!', 'success');
  } else {
    showNotification('Product not found. Check barcode or add to system.', 'error');
  }

  document.getElementById('barcodeInput').value = '';
  document.getElementById('barcodeInput').focus();
}

// Manual search triggered by button or Enter
function onManualSearch(){
  const raw = document.getElementById('barcodeInput').value.trim();
  if(!raw){ showNotification('Please enter barcode', 'warning'); return; }
  const parsed = parseGS1(raw);
  const candidates = parsed.candidates.length ? parsed.candidates : [ normalizeDigits(raw) ];
  searchByBarcodeCandidates(candidates);
}

// Name search
function searchByName(q){
  const results = document.getElementById('nameSearchResults');
  if(!results) return;
  if(!q || q.length < 2){ results.classList.add('hidden'); return; }
  const ql = q.toLowerCase();
  const matches = allMedicines.filter(m => 
    (m.name||'').toLowerCase().includes(ql) || 
    (m.generic||'').toLowerCase().includes(ql)
  ).slice(0,6);
  
  if(!matches.length){ 
    results.innerHTML = '<div class="p-3 text-gray-500 text-center">No matches found</div>'; 
    results.classList.remove('hidden'); 
    return; 
  }
  
  results.innerHTML = matches.map(m => `
    <div class="p-3 hover:bg-indigo-50 cursor-pointer flex justify-between border-b last:border-b-0" 
         onclick="selectMedicineByIndex(${allMedicines.indexOf(m)})">
      <div>
        <div class="font-medium">${m.name}</div>
        <div class="text-xs text-gray-500">${m.generic}</div>
      </div>
      <div class="text-green-600 font-semibold">₨${(m.price||0).toFixed(2)}</div>
    </div>
  `).join('');
  results.classList.remove('hidden');
}

function selectMedicineByIndex(idx){ 
  const m = allMedicines[idx]; 
  if(m) {
    displaySearchResult(m); 
    clearNameSearch(); 
  }
}

function clearNameSearch(){ 
  const e = document.getElementById('productNameSearch'); 
  if(e) e.value = ''; 
  document.getElementById('nameSearchResults').classList.add('hidden'); 
}

// Dropdown selection
function selectFromDropdown(){
  const sel = document.getElementById('medicineSelect'); 
  if(!sel || !sel.value) return;
  const opt = sel.options[sel.selectedIndex];
  displaySearchResult({
    stockId: opt.value,
    name: opt.dataset.name || '',
    generic: opt.dataset.generic || '',
    price: parseFloat(opt.dataset.price) || 0,
    available: parseInt(opt.dataset.available) || 0,
    barcode: opt.dataset.barcode || '',
    batch: opt.dataset.batch || ''
  });
  sel.value = '';
}

// Display & cart
function displaySearchResult(m){
  searchedMedicine = m;
  document.getElementById('resultName').textContent = m.name || '';
  document.getElementById('resultGeneric').textContent = m.generic || '';
  document.getElementById('resultBarcode').textContent = m.barcode || 'N/A';
  document.getElementById('resultBatch').textContent = m.batch || 'N/A';
  document.getElementById('resultQty').textContent = m.available || 0;
  document.getElementById('resultPrice').textContent = '₨' + (m.price||0).toFixed(2);
  document.getElementById('searchResult').classList.remove('hidden');
  document.getElementById('searchResult').scrollIntoView({ behavior:'smooth', block:'center' });
}

function closeSearchResult(){ 
  document.getElementById('searchResult').classList.add('hidden'); 
  searchedMedicine = null; 
  document.getElementById('barcodeInput')?.focus(); 
}

function addSearchedMedicine(){ 
  if(searchedMedicine) { 
    addToCart(searchedMedicine); 
    closeSearchResult(); 
  } 
}

function addToCart(item){
  const existing = cartItems.find(i=>i.stockId===item.stockId);
  if(existing){
    if(existing.quantity < existing.available){ 
      existing.quantity++; 
      renderCart(); 
      showNotification('Quantity increased','success'); 
    } else {
      showNotification('Maximum stock reached','warning');
    }
  } else {
    cartItems.push({...item, index: cartIndex++, quantity:1});
    renderCart();
    showNotification('Added to cart','success');
  }
}

function removeFromCart(idx){ 
  cartItems = cartItems.filter(i=>i.index!==idx); 
  renderCart(); 
  showNotification('Removed from cart','info'); 
}

function updateQuantity(idx, qty){
  const it = cartItems.find(i=>i.index===idx);
  if(!it) return;
  it.quantity = Math.min(Math.max(1, parseInt(qty)||1), it.available||9999);
  renderCart();
}

function incrementQuantity(idx){
  const it = cartItems.find(i=>i.index===idx);
  if(it && it.quantity < it.available){
    it.quantity++;
    renderCart();
  } else {
    showNotification('Maximum stock reached','warning');
  }
}

function decrementQuantity(idx){
  const it = cartItems.find(i=>i.index===idx);
  if(it && it.quantity>1){
    it.quantity--;
    renderCart();
  }
}

function clearCart(){
  if(cartItems.length>0 && confirm('Clear entire cart?')){
    cartItems = [];
    renderCart();
    showNotification('Cart cleared','info');
  }
}

function renderCart(){
  const container = document.getElementById('cartItems');
  const empty = document.getElementById('emptyCart');
  const hidden = document.getElementById('hiddenCartFields');
  if(!container||!empty||!hidden) return;
  
  if(cartItems.length===0){
    empty.classList.remove('hidden');
    container.innerHTML='';
    hidden.innerHTML='';
    document.getElementById('cartCount').textContent='0';
    updateTotals();
    return;
  }
  
  empty.classList.add('hidden');
  document.getElementById('cartCount').textContent = cartItems.length;
  
  container.innerHTML = cartItems.map(it => `
    <div class="border-b pb-3">
      <div class="flex justify-between items-start mb-2">
        <div class="flex-1">
          <div class="font-medium text-gray-800">${it.name}</div>
          <div class="text-xs text-gray-500">${it.barcode || 'N/A'} • Batch ${it.batch}</div>
        </div>
        <button onclick="removeFromCart(${it.index})" class="text-red-500 hover:text-red-700 ml-2">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        </button>
      </div>
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-2">
          <button onclick="decrementQuantity(${it.index})" class="px-2 py-1 bg-slate-100 rounded hover:bg-slate-200">−</button>
          <input type="number" value="${it.quantity}" min="1" max="${it.available||9999}" 
                 onchange="updateQuantity(${it.index}, this.value)" 
                 class="w-14 text-center border rounded py-1" />
          <button onclick="incrementQuantity(${it.index})" class="px-2 py-1 bg-slate-100 rounded hover:bg-slate-200">+</button>
        </div>
        <div class="text-right">
          <div class="text-xs text-gray-500">₨${(it.price||0).toFixed(2)} each</div>
          <div class="font-bold text-blue-600">₨${((it.price||0)*it.quantity).toFixed(2)}</div>
        </div>
      </div>
    </div>
  `).join('');
  
  hidden.innerHTML = cartItems.map(it=> `
    <input type="hidden" name="items[${it.index}][stock_id]" value="${it.stockId}">
    <input type="hidden" name="items[${it.index}][quantity]" value="${it.quantity}">
    <input type="hidden" name="items[${it.index}][price]" value="${it.price}">
  `).join('');
  
  updateTotals();
}

function updateTotals(){
  const subtotal = cartItems.reduce((s,i)=> s + ((i.price||0)*(i.quantity||0)), 0);
  const discount = parseFloat(document.getElementById('discountInput')?.value || 0) || 0;
  const tax = parseFloat(document.getElementById('taxInput')?.value || 0) || 0;
  const total = subtotal - discount + tax;
  
  document.getElementById('subtotalDisplay').textContent = '₨' + subtotal.toFixed(2);
  document.getElementById('discountDisplay').textContent = '₨' + discount.toFixed(2);
  document.getElementById('taxDisplay').textContent = '₨' + tax.toFixed(2);
  document.getElementById('totalDisplay').textContent = '₨' + total.toFixed(2);
}

// Form validation
document.addEventListener('DOMContentLoaded', function(){
  const saleForm = document.getElementById('saleForm');
  if(saleForm){
    saleForm.addEventListener('submit', function(e){
      if(cartItems.length === 0){
        e.preventDefault();
        showNotification('Cart is empty! Please add items.', 'warning');
        return false;
      }
      
      const subtotal = cartItems.reduce((s,i)=> s + ((i.price||0)*(i.quantity||0)), 0);
      const discount = parseFloat(document.getElementById('discountInput')?.value || 0) || 0;
      const tax = parseFloat(document.getElementById('taxInput')?.value || 0) || 0;
      const finalTotal = subtotal - discount + tax;
      
      if(finalTotal < 0){ 
        e.preventDefault(); 
        showNotification('Total cannot be negative', 'warning'); 
        return false; 
      }
      
      if(!confirm('Complete sale of ₨' + finalTotal.toFixed(2) + '?')){ 
        e.preventDefault(); 
        return false; 
      }
      
      showNotification('Processing sale...', 'success');
    });
  }
});
   
</script>

@endsection