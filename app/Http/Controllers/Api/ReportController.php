<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Medicine;
use App\Models\Stock;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Get daily sales report
     */
    public function dailySales(Request $request)
    {
        $date = $request->date ?? Carbon::today();
        
        $sales = Sale::with('items.medicine')
            ->whereDate('created_at', $date)
            ->get();

        $summary = [
            'date' => $date,
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('total'),
            'total_discount' => $sales->sum('discount'),
            'total_tax' => $sales->sum('tax'),
            'sales' => $sales
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Get top selling medicines
     */
    public function topMedicines(Request $request)
    {
        $limit = $request->limit ?? 10;
        $from = $request->from_date ?? Carbon::now()->subMonth();
        $to = $request->to_date ?? Carbon::now();

        $topMedicines = DB::table('sale_items')
            ->join('medicines', 'sale_items.medicine_id', '=', 'medicines.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$from, $to])
            ->select(
                'medicines.id',
                'medicines.name',
                'medicines.category',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->groupBy('medicines.id', 'medicines.name', 'medicines.category')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $topMedicines
        ]);
    }

    /**
     * Get stock value report
     */
    public function stockValue()
    {
        $stockValue = Stock::with('medicine')
            ->where('quantity', '>', 0)
            ->where('expiry_date', '>', Carbon::now())
            ->get()
            ->map(function($stock) {
                return [
                    'medicine' => $stock->medicine->name,
                    'batch' => $stock->batch_number,
                    'quantity' => $stock->quantity,
                    'purchase_price' => $stock->purchase_price,
                    'selling_price' => $stock->selling_price,
                    'purchase_value' => $stock->quantity * $stock->purchase_price,
                    'selling_value' => $stock->quantity * $stock->selling_price,
                ];
            });

        $summary = [
            'total_items' => $stockValue->count(),
            'total_purchase_value' => $stockValue->sum('purchase_value'),
            'total_selling_value' => $stockValue->sum('selling_value'),
            'potential_profit' => $stockValue->sum('selling_value') - $stockValue->sum('purchase_value'),
            'items' => $stockValue
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Generate sales summary PDF
     */
    public function salesSummaryPDF(Request $request)
    {
        $from = $request->from_date ?? Carbon::now()->subMonth();
        $to = $request->to_date ?? Carbon::now();

        $sales = Sale::with(['items.medicine', 'user'])
            ->whereBetween('created_at', [$from, $to])
            ->get();

        $summary = [
            'from_date' => $from,
            'to_date' => $to,
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('total'),
            'total_discount' => $sales->sum('discount'),
            'total_tax' => $sales->sum('tax'),
            'sales' => $sales
        ];

        $pdf = Pdf::loadView('reports.sales-summary', compact('summary'));
        
        return $pdf->download('sales-summary-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Get inventory report
     */
    public function inventoryReport()
    {
        $medicines = Medicine::with(['stocks' => function($query) {
            $query->where('quantity', '>', 0)
                  ->where('expiry_date', '>', Carbon::now());
        }])->get()->map(function($medicine) {
            $totalStock = $medicine->stocks->sum('quantity');
            $stockValue = $medicine->stocks->sum(function($stock) {
                return $stock->quantity * $stock->selling_price;
            });

            return [
                'medicine_id' => $medicine->id,
                'medicine_name' => $medicine->name,
                'category' => $medicine->category,
                'total_stock' => $totalStock,
                'reorder_level' => $medicine->reorder_level,
                'stock_status' => $totalStock <= $medicine->reorder_level ? 'Low Stock' : 'Adequate',
                'stock_value' => $stockValue,
                'batches' => $medicine->stocks
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $medicines
        ]);
    }

    /**
     * Get expiry report
     */
    public function expiryReport()
    {
        $expiring = Stock::with(['medicine', 'supplier'])
            ->where('expiry_date', '<=', Carbon::now()->addDays(30))
            ->where('expiry_date', '>', Carbon::now())
            ->where('quantity', '>', 0)
            ->get();

        $expired = Stock::with(['medicine', 'supplier'])
            ->where('expiry_date', '<', Carbon::now())
            ->where('quantity', '>', 0)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'expiring_soon' => [
                    'count' => $expiring->count(),
                    'items' => $expiring
                ],
                'expired' => [
                    'count' => $expired->count(),
                    'items' => $expired
                ]
            ]
        ]);
    }
}