<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    
    // Auth routes
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // Medicine routes
    Route::get('/medicines/barcode/{barcode}', [MedicineController::class, 'findByBarcode']);
    Route::apiResource('medicines', MedicineController::class);

    // Supplier routes
    Route::apiResource('suppliers', SupplierController::class);

    // Stock routes
    Route::get('/stocks/alerts/expiring', [StockController::class, 'expiring']);
    Route::get('/stocks/alerts/expired', [StockController::class, 'expired']);
    Route::get('/stocks/alerts/low', [StockController::class, 'lowStock']);
    Route::get('/stocks/statistics', [StockController::class, 'statistics']);
    Route::post('/stocks/{id}/adjust', [StockController::class, 'adjustQuantity']);
    Route::post('/stocks/transfer', [StockController::class, 'transfer']);
    Route::apiResource('stocks', StockController::class);

    // Sale routes
    Route::get('/sales/{id}/invoice', [SaleController::class, 'generateInvoice']);
    Route::apiResource('sales', SaleController::class)->except(['update', 'destroy']);

    // Report routes
    Route::get('/reports/daily-sales', [ReportController::class, 'dailySales']);
    Route::get('/reports/top-medicines', [ReportController::class, 'topMedicines']);
    Route::get('/reports/stock-value', [ReportController::class, 'stockValue']);
    Route::get('/reports/sales-summary', [ReportController::class, 'salesSummaryPDF']);
    Route::get('/reports/inventory', [ReportController::class, 'inventoryReport']);
    Route::get('/reports/expiry', [ReportController::class, 'expiryReport']);
});