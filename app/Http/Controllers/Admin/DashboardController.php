<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Medicine;
use App\Models\Sale;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Different dashboard data based on role
        if (auth()->user()->role === 'admin') {
            return $this->adminDashboard();
        } else {
            return $this->cashierDashboard();
        }
    }

    private function adminDashboard()
    {
        $stats = [
            'total_medicines' => Medicine::count(),
            'total_stock_value' => Stock::sum('quantity') * Stock::avg('selling_price'),
            'low_stock_count' => Medicine::all()->filter(function($medicine) {
                return $medicine->stocks()->sum('quantity') <= $medicine->reorder_level;
            })->count(),
            'expiring_soon' => Stock::where('expiry_date', '<=', now()->addDays(90))
                                    ->where('expiry_date', '>', now())
                                    ->count(),
        ];

        $recentSales = Sale::with('user')->latest()->take(10)->get();
        
        return view('admin.dashboard', compact('stats', 'recentSales'));
    }

    public function cashierDashboard()
    {
        // Get stock data for cashier (read-only) with pagination
        $stocks = Stock::with('medicine')
            ->where('quantity', '>', 0)
            ->where('expiry_date', '>', now())
            ->paginate(20); // ✅ Changed from get() to paginate()
            
        $expiringSoon = Stock::where('expiry_date', '<=', now()->addDays(90))
                            ->where('expiry_date', '>', now())
                            ->count();
                            
        $expired = Stock::where('expiry_date', '<=', now())->count();
        
        $lowStock = Medicine::all()->filter(function($medicine) {
            return $medicine->stocks()->sum('quantity') <= $medicine->reorder_level;
        })->count();
        
        return view('admin.dashboard', compact('stocks', 'expiringSoon', 'expired', 'lowStock'));
    }
}