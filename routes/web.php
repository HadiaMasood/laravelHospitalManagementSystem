<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StockWebController;
use App\Http\Controllers\Admin\MedicineWebController;
use App\Http\Controllers\Admin\SupplierWebController;
use App\Http\Controllers\Admin\SaleWebController;
use App\Http\Controllers\Admin\ReportWebController;
use App\Http\Controllers\Admin\EmailAlertController; 
use App\Http\Controllers\SalesController;
use App\Http\Controllers\MedicineController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (Protected)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('medicines', MedicineWebController::class);
    Route::resource('suppliers', SupplierWebController::class);

    // Stock alerts - MUST come before resource routes
    Route::get('stocks/expiring', [StockWebController::class, 'expiring'])->name('stocks.expiring');
    Route::get('stocks/low-stock', [StockWebController::class, 'lowStock'])->name('stocks.low-stock');
    Route::get('stocks/expired', [StockWebController::class, 'expired'])->name('stocks.expired');
    
    Route::resource('stocks', StockWebController::class);

    // Sales (admin + cashier)
    Route::resource('sales', SaleWebController::class)->only(['index', 'create', 'store', 'show']);

    // Email Alerts
    Route::get('email-alerts', [EmailAlertController::class, 'index'])->name('email-alerts.index');
    Route::put('email-alerts/{setting}', [EmailAlertController::class, 'update'])->name('email-alerts.update');
    Route::get('email-alerts/test/{type}', [EmailAlertController::class, 'sendTestEmail'])->name('email-alerts.test');

    // Reports
    Route::get('reports', [ReportWebController::class, 'index'])->name('reports.index');
    Route::get('reports/daily-sales', [ReportWebController::class, 'dailySales'])->name('reports.daily-sales');
    Route::get('reports/top-medicines', [ReportWebController::class, 'topMedicines'])->name('reports.top-medicines');
    Route::get('reports/stock-value', [ReportWebController::class, 'stockValue'])->name('reports.stock-value');
    Route::get('reports/monthly-sales', [ReportWebController::class, 'monthlySales'])->name('reports.monthly-sales');
    Route::get('reports/profit-analysis', [ReportWebController::class, 'profitAnalysis'])->name('reports.profit-analysis');
    Route::post('reports/generate-pdf', [ReportWebController::class, 'generatePDF'])->name('reports.generate-pdf');
});

/*
|--------------------------------------------------------------------------
| Sales (POS) Routes - Outside admin prefix
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // These routes are for cashier POS access
    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}', [SalesController::class, 'show'])->name('sales.show');
});

/*
|--------------------------------------------------------------------------
| API Routes (Barcode Scan)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/api/medicines', [MedicineController::class, 'index']);
    Route::get('/api/medicines/barcode/{barcode}', [MedicineController::class, 'findByBarcode']);
});