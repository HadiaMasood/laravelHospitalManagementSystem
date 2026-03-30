<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\CashierAuthController;
use App\Http\Controllers\Auth\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StockWebController;
use App\Http\Controllers\Admin\MedicineWebController;
use App\Http\Controllers\Admin\SupplierWebController;
use App\Http\Controllers\Admin\SaleWebController;
use App\Http\Controllers\Admin\ReportWebController;
use App\Http\Controllers\Admin\EmailAlertController; 
use App\Http\Controllers\SalesController;

/*
|--------------------------------------------------------------------------
| Home Route
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('auth.home');

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::get('/admin/register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
Route::post('/admin/register', [AdminAuthController::class, 'register'])->name('admin.register.post');

/*
|--------------------------------------------------------------------------
| Cashier Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/cashier/login', [CashierAuthController::class, 'showLoginForm'])->name('cashier.login');
Route::post('/cashier/login', [CashierAuthController::class, 'login'])->name('cashier.login.post');
Route::get('/cashier/register', [CashierAuthController::class, 'showRegisterForm'])->name('cashier.register');
Route::post('/cashier/register', [CashierAuthController::class, 'register'])->name('cashier.register.post');

/*
|--------------------------------------------------------------------------
| Old Authentication Routes (Kept for backward compatibility)
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
Route::post('/cashier/logout', [CashierAuthController::class, 'logout'])->name('cashier.logout');

Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

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

Route::get('/clear-route-cache', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        return "SUCCESS: Route, Config, and View cache cleared. Now login again.";
    } catch (\Exception $e) {
        return "ERROR: " . $e->getMessage();
    }
});

/*
|--------------------------------------------------------------------------
| API Routes (Barcode Scan) - Removed, use api.php instead
|--------------------------------------------------------------------------
*/