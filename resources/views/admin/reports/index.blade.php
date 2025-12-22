@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800"> Reports</h1>
        <p class="text-gray-600 mt-2">Daily sales, top medicines, stock reports</p>
    </div>

    <!-- Report Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Daily Sales Report -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold"> Daily Sales</h3>
                <i class="fas fa-chart-line text-3xl opacity-50"></i>
            </div>
            <p class="text-blue-100 mb-4">View daily sales performance and revenue</p>
            <a href="{{ route('admin.reports.daily-sales') }}" 
               class="inline-block bg-white text-blue-600 px-4 py-2 rounded font-semibold hover:bg-blue-50 transition">
                View Report →
            </a>
        </div>

        <!-- Top Medicines Report -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold"> Top Medicines</h3>
                <i class="fas fa-pills text-3xl opacity-50"></i>
            </div>
            <p class="text-green-100 mb-4">Best selling medicines and trends</p>
            <a href="{{ route('admin.reports.top-medicines') }}" 
               class="inline-block bg-white text-green-600 px-4 py-2 rounded font-semibold hover:bg-green-50 transition">
                View Report →
            </a>
        </div>

        <!-- Stock Value Report -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold"> Stock Value</h3>
                <i class="fas fa-box text-3xl opacity-50"></i>
            </div>
            <p class="text-purple-100 mb-4">Total inventory value and analysis</p>
            <a href="{{ route('admin.reports.stock-value') }}" 
               class="inline-block bg-white text-purple-600 px-4 py-2 rounded font-semibold hover:bg-purple-50 transition">
                View Report →
            </a>
        </div>

        <!-- Monthly Sales Report -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold"> Monthly Sales</h3>
                <i class="fas fa-calendar-alt text-3xl opacity-50"></i>
            </div>
            <p class="text-orange-100 mb-4">Monthly revenue and growth analysis</p>
            <a href="{{ route('admin.reports.monthly-sales') }}" 
               class="inline-block bg-white text-orange-600 px-4 py-2 rounded font-semibold hover:bg-orange-50 transition">
                View Report →
            </a>
        </div>

        <!-- Profit Analysis -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold"> Profit Analysis</h3>
                <i class="fas fa-dollar-sign text-3xl opacity-50"></i>
            </div>
            <p class="text-red-100 mb-4">Profit margins and cost analysis</p>
            <a href="{{ route('admin.reports.profit-analysis') }}" 
               class="inline-block bg-white text-red-600 px-4 py-2 rounded font-semibold hover:bg-red-50 transition">
                View Report →
            </a>
        </div>

        <!-- Expiry Alert Report -->
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold"> Expiry Alerts</h3>
                <i class="fas fa-exclamation-triangle text-3xl opacity-50"></i>
            </div>
            <p class="text-yellow-100 mb-4">Medicines expiring soon</p>
            <a href="{{ route('admin.stocks.expiring') }}" 
               class="inline-block bg-white text-yellow-600 px-4 py-2 rounded font-semibold hover:bg-yellow-50 transition">
                View Report →
            </a>
        </div>

    </div>

    <!-- Quick Stats Overview -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6"> Quick Overview</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Today's Sales -->
            <div class="text-center">
                <p class="text-gray-600 text-sm">Today's Sales</p>
                <p class="text-3xl font-bold text-blue-600">₨{{ number_format($todaySales, 2) }}</p>
            </div>

            <!-- This Month -->
            <div class="text-center">
                <p class="text-gray-600 text-sm">This Month</p>
                <p class="text-3xl font-bold text-green-600">₨{{ number_format($monthSales, 2) }}</p>
            </div>

            <!-- Total Stock Value -->
            <div class="text-center">
                <p class="text-gray-600 text-sm">Stock Value</p>
                <p class="text-3xl font-bold text-purple-600">₨{{ number_format($stockValue, 2) }}</p>
            </div>

            <!-- Low Stock Items -->
            <div class="text-center">
                <p class="text-gray-600 text-sm">Low Stock Items</p>
                <p class="text-3xl font-bold text-red-600">{{ $lowStockCount }}</p>
            </div>
        </div>
    </div>

</div>
@endsection