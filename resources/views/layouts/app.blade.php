<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Medical Store</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')
</head>

<body class="bg-gray-100">

<!-- ================= NAVBAR ================= -->
<nav class="bg-white shadow-lg">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">

            <div class="flex items-center space-x-6">
                <h1 class="text-2xl font-bold text-indigo-600">
                    <i class="fas fa-hospital"></i> Medical Store
                </h1>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button onclick="toggleMobileMenu()" class="text-gray-700 hover:text-indigo-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <!-- Desktop menu -->
                <div class="hidden md:flex space-x-3">
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
                        
                        <!-- Stocks Dropdown -->
                        <div class="relative dropdown-container">
                            <button onclick="toggleDropdown()" class="nav-link inline-flex items-center focus:outline-none" id="stocksDropdownBtn">
                                Stocks
                                <svg class="ml-1 w-4 h-4 transition-transform duration-200" id="dropdownArrow" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <div id="stocksDropdown" class="hidden absolute left-0 mt-2 w-56 bg-white rounded-md shadow-xl z-50 border border-gray-200 py-1">
                                <a href="{{ route('admin.stocks.index') }}" class="dropdown-item flex items-center px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                    <i class="fas fa-boxes mr-3 text-indigo-500"></i>
                                    <span>All Stocks</span>
                                </a>
                                <a href="{{ route('admin.stocks.low-stock') }}" class="dropdown-item flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                                    <i class="fas fa-exclamation-triangle mr-3 text-orange-500"></i>
                                    <span>Low Stock Alert</span>
                                </a>
                                <a href="{{ route('admin.stocks.expiring') }}" class="dropdown-item flex items-center px-4 py-3 text-gray-700 hover:bg-yellow-50 hover:text-yellow-600">
                                    <i class="fas fa-clock mr-3 text-yellow-500"></i>
                                    <span>Expiring Soon</span>
                                </a>
                                <a href="{{ route('admin.stocks.expired') }}" class="dropdown-item flex items-center px-4 py-3 text-gray-700 hover:bg-red-50 hover:text-red-600">
                                    <i class="fas fa-times-circle mr-3 text-red-500"></i>
                                    <span>Expired Items</span>
                                </a>
                            </div>
                        </div>
                        
                        <a href="{{ route('admin.medicines.index') }}" class="nav-link">Medicines</a>
                        <a href="{{ route('admin.suppliers.index') }}" class="nav-link">Suppliers</a>
                        <a href="{{ route('admin.sales.index') }}" class="nav-link">Sales</a>
                        <a href="{{ route('admin.reports.index') }}" class="nav-link">Reports</a>
                    @elseif(auth()->user()->role === 'cashier')
                        <a href="{{ route('cashier.dashboard') }}" class="nav-link">Dashboard</a>
                        <a href="{{ route('admin.sales.create') }}" class="nav-link">POS</a>
                        <a href="{{ route('admin.sales.index') }}" class="nav-link">My Sales</a>
                    @endif
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="font-medium text-gray-700">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                </div>

                <form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
        Logout
    </button>
</form>
            </div>

        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-200">
        <div class="px-4 py-2 space-y-1">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                
                <!-- Mobile Stocks Menu -->
                <div class="border-l-2 border-indigo-200 pl-4 ml-2">
                    <p class="text-sm font-semibold text-gray-600 mb-2">Stocks</p>
                    <a href="{{ route('admin.stocks.index') }}" class="nav-link text-sm">
                        <i class="fas fa-boxes mr-2 text-indigo-500"></i>All Stocks
                    </a>
                    <a href="{{ route('admin.stocks.low-stock') }}" class="nav-link text-sm">
                        <i class="fas fa-exclamation-triangle mr-2 text-orange-500"></i>Low Stock Alert
                    </a>
                    <a href="{{ route('admin.stocks.expiring') }}" class="nav-link text-sm">
                        <i class="fas fa-clock mr-2 text-yellow-500"></i>Expiring Soon
                    </a>
                    <a href="{{ route('admin.stocks.expired') }}" class="nav-link text-sm">
                        <i class="fas fa-times-circle mr-2 text-red-500"></i>Expired Items
                    </a>
                </div>
                
                <a href="{{ route('admin.medicines.index') }}" class="nav-link">
                    <i class="fas fa-pills mr-2"></i>Medicines
                </a>
                <a href="{{ route('admin.suppliers.index') }}" class="nav-link">
                    <i class="fas fa-truck mr-2"></i>Suppliers
                </a>
                <a href="{{ route('admin.sales.index') }}" class="nav-link">
                    <i class="fas fa-shopping-cart mr-2"></i>Sales
                </a>
                <a href="{{ route('admin.reports.index') }}" class="nav-link">
                    <i class="fas fa-chart-bar mr-2"></i>Reports
                </a>
            @elseif(auth()->user()->role === 'cashier')
                <a href="{{ route('cashier.dashboard') }}" class="nav-link">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="{{ route('admin.sales.create') }}" class="nav-link">
                    <i class="fas fa-cash-register mr-2"></i>POS
                </a>
                <a href="{{ route('admin.sales.index') }}" class="nav-link">
                    <i class="fas fa-receipt mr-2"></i>My Sales
                </a>
            @endif
        </div>
    </div>
</nav>

<!-- ================= FLASH MESSAGES ================= -->
@if(session('success'))
<div class="container mx-auto px-4 mt-4">
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
</div>
@endif

@if(session('error'))
<div class="container mx-auto px-4 mt-4">
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        {{ session('error') }}
    </div>
</div>
@endif

<!-- ================= MAIN CONTENT ================= -->
<main class="container mx-auto px-4 py-6">
    @yield('content')
</main>

@stack('scripts')

<script>
// Improved dropdown functionality
let dropdownOpen = false;
let mobileMenuOpen = false;

function toggleDropdown() {
    const dropdown = document.getElementById('stocksDropdown');
    const arrow = document.getElementById('dropdownArrow');
    
    dropdownOpen = !dropdownOpen;
    
    if (dropdownOpen) {
        dropdown.classList.remove('hidden');
        arrow.style.transform = 'rotate(180deg)';
    } else {
        dropdown.classList.add('hidden');
        arrow.style.transform = 'rotate(0deg)';
    }
}

function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    mobileMenuOpen = !mobileMenuOpen;
    
    if (mobileMenuOpen) {
        mobileMenu.classList.remove('hidden');
    } else {
        mobileMenu.classList.add('hidden');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdownContainer = document.querySelector('.dropdown-container');
    const dropdown = document.getElementById('stocksDropdown');
    
    if (dropdownContainer && !dropdownContainer.contains(event.target)) {
        dropdown.classList.add('hidden');
        document.getElementById('dropdownArrow').style.transform = 'rotate(0deg)';
        dropdownOpen = false;
    }
});

// Close menus on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        // Close dropdown
        const dropdown = document.getElementById('stocksDropdown');
        dropdown.classList.add('hidden');
        document.getElementById('dropdownArrow').style.transform = 'rotate(0deg)';
        dropdownOpen = false;
        
        // Close mobile menu
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenu.classList.add('hidden');
        mobileMenuOpen = false;
    }
});

// Close mobile menu when window is resized to desktop
window.addEventListener('resize', function() {
    if (window.innerWidth >= 768) {
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenu.classList.add('hidden');
        mobileMenuOpen = false;
    }
});
</script>

</body>
</html>
<style>
    .nav-link {
        color: #374151;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        transition: color 0.2s ease-in-out;
        text-decoration: none;
        display: inline-block;
    }
    
    .nav-link:hover {
        color: #4f46e5;
    }
    
    .dropdown-container {
        position: relative;
    }
    
    #stocksDropdown {
        animation: fadeIn 0.15s ease-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Mobile responsive navbar */
    @media (max-width: 768px) {
        .nav-link {
            display: block;
            width: 100%;
            text-align: left;
            padding: 0.5rem 1rem;
        }
    }
    
    /* Additional mobile menu styles */
    #mobileMenu {
        transition: all 0.3s ease-in-out;
    }
    
    /* Dropdown hover effects */
    .dropdown-item {
        transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
    }
</style>