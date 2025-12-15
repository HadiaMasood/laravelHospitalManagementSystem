<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Medicine;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportWebController extends Controller
{
    public function __construct()
    {
        // Only admin can access reports
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                abort(403, 'Unauthorized. Only administrators can access reports.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        // Quick stats for overview
        $todaySales = Sale::whereDate('created_at', today())->sum('total');
        $monthSales = Sale::whereMonth('created_at', now()->month)
                         ->whereYear('created_at', now()->year)
                         ->sum('total');
        
        // Use selling_price (this is what exists in your table)
        $stockValue = Stock::where('quantity', '>', 0)
                          ->where('expiry_date', '>', now())
                          ->sum(DB::raw('quantity * selling_price'));
        
        $lowStockCount = Medicine::whereRaw('total_stock < reorder_level')->count();

        return view('admin.reports.index', compact(
            'todaySales',
            'monthSales',
            'stockValue',
            'lowStockCount'
        ));
    }

    public function dailySales(Request $request)
    {
        $date = $request->date ?? Carbon::today();
        
        $sales = Sale::with(['user', 'items.medicine'])
            ->whereDate('created_at', $date)
            ->latest()
            ->paginate(20);

        $totalSales = Sale::whereDate('created_at', $date)->sum('total');
        $totalTransactions = Sale::whereDate('created_at', $date)->count();
        $totalItemsSold = SaleItem::whereHas('sale', function($q) use ($date) {
            $q->whereDate('created_at', $date);
        })->sum('quantity');

        $averageSale = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Peak hour calculation
        $peakHourData = Sale::whereDate('created_at', $date)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->first();

        $peakHour = $peakHourData ? Carbon::createFromTime($peakHourData->hour)->format('h A') : 'N/A';

        $summary = [
            'date' => $date,
            'total_sales' => $totalSales,
            'total_transactions' => $totalTransactions,
            'total_items_sold' => $totalItemsSold,
            'average_sale' => $averageSale,
            'peak_hour' => $peakHour,
        ];

        return view('admin.reports.daily-sales', compact('sales', 'summary'));
    }

    public function topMedicines(Request $request)
    {
        $from = $request->from_date ?? Carbon::now()->subMonth();
        $to = $request->to_date ?? Carbon::now();

        $topMedicines = DB::table('sale_items')
            ->join('medicines', 'sale_items.medicine_id', '=', 'medicines.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$from, $to])
            ->select(
                'medicines.id',
                'medicines.name',
                'medicines.generic_name',
                'medicines.category',
                'medicines.total_stock',
                DB::raw('SUM(sale_items.quantity) as total_sold'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->groupBy('medicines.id', 'medicines.name', 'medicines.generic_name', 'medicines.category', 'medicines.total_stock')
            ->orderByDesc('total_sold')
            ->limit(20)
            ->get();

        return view('admin.reports.top-medicines', compact('topMedicines', 'from', 'to'));
    }

    public function stockValue()
    {
        $stocks = Stock::with('medicine')
            ->where('quantity', '>', 0)
            ->where('expiry_date', '>', Carbon::now())
            ->select('*', DB::raw('quantity * selling_price as stock_value'))
            ->orderByDesc('stock_value')
            ->paginate(50);

        // Calculate totals using selling_price
        $totalValue = Stock::where('quantity', '>', 0)
                          ->where('expiry_date', '>', Carbon::now())
                          ->sum(DB::raw('quantity * selling_price'));
        
        $totalItems = Stock::where('quantity', '>', 0)
                          ->where('expiry_date', '>', Carbon::now())
                          ->sum('quantity');
        
        $uniqueMedicines = Medicine::count();

        // Stock value by category
        $categoryValues = Medicine::join('stocks', 'medicines.id', '=', 'stocks.medicine_id')
            ->where('stocks.quantity', '>', 0)
            ->where('stocks.expiry_date', '>', Carbon::now())
            ->select(
                'medicines.category',
                DB::raw('SUM(stocks.quantity) as total_quantity'),
                DB::raw('SUM(stocks.quantity * stocks.selling_price) as total_value')
            )
            ->groupBy('medicines.category')
            ->get();

        $summary = [
            'total_items' => $totalItems,
            'total_value' => $totalValue,
            'unique_medicines' => $uniqueMedicines,
        ];

        return view('admin.reports.stock-value', compact('stocks', 'summary', 'categoryValues'));
    }

    public function generatePDF(Request $request)
    {
        $type = $request->type;
        
        switch($type) {
            case 'daily-sales':
                return $this->dailySalesPDF($request);
            case 'top-medicines':
                return $this->topMedicinesPDF($request);
            case 'stock-value':
                return $this->stockValuePDF();
            default:
                return back()->with('error', 'Invalid report type');
        }
    }

    private function dailySalesPDF($request)
    {
        $date = $request->date ?? Carbon::today();
        $sales = Sale::with('items.medicine')->whereDate('created_at', $date)->get();
        
        $summary = [
            'total_sales' => $sales->sum('total'),
            'total_transactions' => $sales->count(),
        ];
        
        $pdf = Pdf::loadView('admin.reports.pdf.daily-sales', compact('sales', 'date', 'summary'));
        return $pdf->download('daily-sales-' . Carbon::parse($date)->format('Y-m-d') . '.pdf');
    }

    private function topMedicinesPDF($request)
    {
        $from = $request->from_date ?? Carbon::now()->subMonth();
        $to = $request->to_date ?? Carbon::now();
        
        $topMedicines = DB::table('sale_items')
            ->join('medicines', 'sale_items.medicine_id', '=', 'medicines.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$from, $to])
            ->select(
                'medicines.name',
                'medicines.category',
                DB::raw('SUM(sale_items.quantity) as total_sold'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->groupBy('medicines.id', 'medicines.name', 'medicines.category')
            ->orderByDesc('total_sold')
            ->limit(20)
            ->get();
        
        $pdf = Pdf::loadView('admin.reports.pdf.top-medicines', compact('topMedicines', 'from', 'to'));
        return $pdf->download('top-medicines-' . now()->format('Y-m-d') . '.pdf');
    }

    private function stockValuePDF()
    {
        $stocks = Stock::with('medicine')
            ->where('quantity', '>', 0)
            ->where('expiry_date', '>', Carbon::now())
            ->select('*', DB::raw('quantity * selling_price as stock_value'))
            ->get();

        $summary = [
            'total_items' => $stocks->sum('quantity'),
            'total_value' => $stocks->sum('stock_value'),
        ];
        
        $pdf = Pdf::loadView('admin.reports.pdf.stock-value', compact('stocks', 'summary'));
        return $pdf->download('stock-value-' . now()->format('Y-m-d') . '.pdf');
    }
    public function monthlySales(Request $request)
{
    $year = $request->year ?? now()->year;
    $month = $request->month ?? now()->month;
    
    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
    $endDate = Carbon::create($year, $month, 1)->endOfMonth();
    
    // Monthly summary
    $monthlySales = Sale::whereBetween('created_at', [$startDate, $endDate])->get();
    
    $summary = [
        'month' => $startDate->format('F Y'),
        'total_sales' => $monthlySales->sum('total'),
        'total_transactions' => $monthlySales->count(),
        'average_sale' => $monthlySales->count() > 0 ? $monthlySales->sum('total') / $monthlySales->count() : 0,
        'total_items_sold' => SaleItem::whereHas('sale', function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })->sum('quantity'),
    ];
    
    // Daily breakdown
    $dailyBreakdown = Sale::whereBetween('created_at', [$startDate, $endDate])
        ->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as transactions'),
            DB::raw('SUM(total) as revenue')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    
    // Previous month comparison
    $prevMonthStart = $startDate->copy()->subMonth()->startOfMonth();
    $prevMonthEnd = $startDate->copy()->subMonth()->endOfMonth();
    $prevMonthSales = Sale::whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])->sum('total');
    
    $growth = $prevMonthSales > 0 
        ? (($summary['total_sales'] - $prevMonthSales) / $prevMonthSales) * 100 
        : 0;
    
    $summary['growth'] = $growth;
    $summary['prev_month_sales'] = $prevMonthSales;
    
    return view('admin.reports.monthly-sales', compact('summary', 'dailyBreakdown', 'year', 'month'));
}

public function profitAnalysis(Request $request)
{
    $from = $request->from_date ?? Carbon::now()->startOfMonth();
    $to = $request->to_date ?? Carbon::now()->endOfMonth();
    
    // Get all sales in the period
    $sales = Sale::with('items.stock')
        ->whereBetween('created_at', [$from, $to])
        ->get();
    
    $totalRevenue = $sales->sum('total');
    $totalCost = 0;
    
    // Calculate total cost
    foreach ($sales as $sale) {
        foreach ($sale->items as $item) {
            if ($item->stock) {
                $totalCost += $item->quantity * $item->stock->purchase_price;
            }
        }
    }
    
    $totalProfit = $totalRevenue - $totalCost;
    $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;
    
    // Top profitable medicines
    $profitableMedicines = DB::table('sale_items')
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->join('medicines', 'sale_items.medicine_id', '=', 'medicines.id')
        ->join('stocks', 'sale_items.stock_id', '=', 'stocks.id')
        ->whereBetween('sales.created_at', [$from, $to])
        ->select(
            'medicines.name',
            'medicines.category',
            DB::raw('SUM(sale_items.quantity) as total_sold'),
            DB::raw('SUM(sale_items.subtotal) as revenue'),
            DB::raw('SUM(sale_items.quantity * stocks.purchase_price) as cost'),
            DB::raw('SUM(sale_items.subtotal - (sale_items.quantity * stocks.purchase_price)) as profit')
        )
        ->groupBy('medicines.id', 'medicines.name', 'medicines.category')
        ->orderByDesc('profit')
        ->limit(20)
        ->get();
    
    // Profit by category
    $categoryProfit = DB::table('sale_items')
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->join('medicines', 'sale_items.medicine_id', '=', 'medicines.id')
        ->join('stocks', 'sale_items.stock_id', '=', 'stocks.id')
        ->whereBetween('sales.created_at', [$from, $to])
        ->select(
            'medicines.category',
            DB::raw('SUM(sale_items.subtotal) as revenue'),
            DB::raw('SUM(sale_items.quantity * stocks.purchase_price) as cost'),
            DB::raw('SUM(sale_items.subtotal - (sale_items.quantity * stocks.purchase_price)) as profit')
        )
        ->groupBy('medicines.category')
        ->get();
    
    $summary = [
        'total_revenue' => $totalRevenue,
        'total_cost' => $totalCost,
        'total_profit' => $totalProfit,
        'profit_margin' => $profitMargin,
        'transactions' => $sales->count(),
    ];
    
    return view('admin.reports.profit-analysis', compact(
        'summary',
        'profitableMedicines',
        'categoryProfit',
        'from',
        'to'
    ));
}
}