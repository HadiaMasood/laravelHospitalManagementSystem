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

                <div class="hidden md:flex space-x-3">
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
                        
                        <!-- Stocks Dropdown -->
                        <div class="relative group">
                            <button class="nav-link inline-flex items-center">
                                Stocks
                                <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <div class="hidden group-hover:block absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                <a href="{{ route('admin.stocks.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">All Stocks</a>
                                <a href="{{ route('admin.stocks.low-stock') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Low Stock Alert</a>
                                <a href="{{ route('admin.stocks.expiring') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Expiring Soon</a>
                                <a href="{{ route('admin.stocks.expired') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Expired</a>
                            </div>
                        </div>
                        
                        <a href="{{ route('admin.medicines.index') }}" class="nav-link">Medicines</a>
                        <a href="{{ route('admin.suppliers.index') }}" class="nav-link">Suppliers</a>
                        <a href="{{ route('admin.sales.index') }}" class="nav-link">Sales</a>
                        <a href="{{ route('admin.reports.index') }}" class="nav-link">Reports</a>
                    @elseif(auth()->user()->role === 'cashier')
                        <a href="{{ route('sales.create') }}" class="nav-link">POS</a>
                        <a href="{{ route('sales.index') }}" class="nav-link">My Sales</a>
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

</body>
</html>
<style>
    .nav-link {
        @apply text-gray-700 hover:text-indigo-600 px-3 py-2 rounded;
    }
</style>