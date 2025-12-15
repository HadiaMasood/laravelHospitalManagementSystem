<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockController extends Controller
{
    /**
     * Display a listing of stocks
     */
    public function index(Request $request)
    {
        $query = Stock::with(['medicine', 'supplier']);
        
        if ($request->has('medicine_id')) {
            $query->where('medicine_id', $request->medicine_id);
        }
        
        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        if ($request->has('batch_number')) {
            $query->where('batch_number', 'like', '%' . $request->batch_number . '%');
        }
        
        if ($request->has('status')) {
            switch ($request->status) {
                case 'expiring':
                    $query->where('expiry_date', '<=', Carbon::now()->addDays(90))
                          ->where('expiry_date', '>', Carbon::now())
                          ->where('quantity', '>', 0);
                    break;
                case 'expired':
                    $query->where('expiry_date', '<', Carbon::now());
                    break;
                case 'active':
                    $query->where('is_active', true)
                          ->where('quantity', '>', 0)
                          ->where('expiry_date', '>', Carbon::now());
                    break;
                case 'low':
                    $query->whereHas('medicine', function($q) {
                        $q->whereRaw('(SELECT SUM(quantity) FROM stocks WHERE medicine_id = medicines.id) <= medicines.reorder_level');
                    });
                    break;
            }
        }
        
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('purchase_date', [
                $request->from_date, 
                $request->to_date
            ]);
        }
        
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $perPage = $request->get('per_page', 15);
        $stocks = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $stocks,
            'message' => 'Stocks retrieved successfully'
        ]);
    }

    /**
     * Store a newly created stock
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'medicine_id' => 'required|exists:medicines,id',
            'batch_number' => 'required|string|max:50|unique:stocks,batch_number',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gt:purchase_price',
            'expiry_date' => 'required|date|after:today',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date|before_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $stock = Stock::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $stock->load(['medicine', 'supplier']),
            'message' => 'Stock added successfully'
        ], 201);
    }

    /**
     * Display the specified stock
     */
    public function show($id)
    {
        $stock = Stock::with(['medicine', 'supplier', 'saleItems'])->find($id);

        if (!$stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stock not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $stock
        ]);
    }

    /**
     * Update the specified stock
     */
    public function update(Request $request, $id)
    {
        $stock = Stock::find($id);

        if (!$stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stock not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'integer|min:0',
            'purchase_price' => 'numeric|min:0',
            'selling_price' => 'numeric|min:0',
            'expiry_date' => 'date',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('purchase_price') && $request->has('selling_price')) {
            if ($request->selling_price <= $request->purchase_price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selling price must be greater than purchase price'
                ], 400);
            }
        }

        $stock->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $stock->load(['medicine', 'supplier']),
            'message' => 'Stock updated successfully'
        ]);
    }

    /**
     * Remove the specified stock
     */
    public function destroy($id)
    {
        $stock = Stock::find($id);

        if (!$stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stock not found'
            ], 404);
        }

        if ($stock->saleItems()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete stock that has been used in sales'
            ], 400);
        }

        $stock->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stock deleted successfully'
        ]);
    }

    /**
     * Get expiring stocks (within specified days)
     */
    public function expiring(Request $request)
    {
        $days = $request->get('days', 90);
        
        $stocks = Stock::with(['medicine', 'supplier'])
            ->where('expiry_date', '<=', Carbon::now()->addDays($days))
            ->where('expiry_date', '>', Carbon::now())
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $stocks,
            'message' => "Stocks expiring within {$days} days"
        ]);
    }

    /**
     * Get expired stocks
     */
    public function expired(Request $request)
    {
        $stocks = Stock::with(['medicine', 'supplier'])
            ->where('expiry_date', '<', Carbon::now())
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $stocks,
            'message' => 'Expired stocks retrieved successfully'
        ]);
    }

    /**
     * Get low stock items
     */
    public function lowStock(Request $request)
    {
        $lowStocks = Medicine::with(['stocks' => function($query) {
            $query->where('quantity', '>', 0)
                  ->where('expiry_date', '>', Carbon::now())
                  ->orderBy('expiry_date', 'asc');
        }])
        ->get()
        ->filter(function($medicine) {
            $totalStock = $medicine->stocks->sum('quantity');
            return $totalStock <= $medicine->reorder_level;
        })
        ->map(function($medicine) {
            $totalStock = $medicine->stocks->sum('quantity');
            return [
                'medicine_id' => $medicine->id,
                'medicine_name' => $medicine->name,
                'current_stock' => $totalStock,
                'reorder_level' => $medicine->reorder_level,
                'shortage' => $medicine->reorder_level - $totalStock,
                'batches' => $medicine->stocks
            ];
        })
        ->values();

        return response()->json([
            'success' => true,
            'data' => $lowStocks,
            'count' => $lowStocks->count(),
            'message' => 'Low stock items retrieved'
        ]);
    }

    /**
     * Get stock statistics
     */
    public function statistics()
    {
        $stats = [
            'total_items' => Stock::sum('quantity'),
            'total_batches' => Stock::count(),
            'active_batches' => Stock::where('quantity', '>', 0)
                                     ->where('expiry_date', '>', Carbon::now())
                                     ->count(),
            'expiring_soon' => Stock::where('expiry_date', '<=', Carbon::now()->addDays(90))
                                    ->where('expiry_date', '>', Carbon::now())
                                    ->where('quantity', '>', 0)
                                    ->count(),
            'expired' => Stock::where('expiry_date', '<', Carbon::now())
                              ->where('quantity', '>', 0)
                              ->count(),
            'low_stock_items' => Medicine::all()->filter(function($medicine) {
                $totalStock = $medicine->stocks->where('quantity', '>', 0)
                                               ->where('expiry_date', '>', Carbon::now())
                                               ->sum('quantity');
                return $totalStock <= $medicine->reorder_level;
            })->count(),
            'total_stock_value' => Stock::where('quantity', '>', 0)
                                        ->where('expiry_date', '>', Carbon::now())
                                        ->get()
                                        ->sum(function($stock) {
                                            return $stock->quantity * $stock->selling_price;
                                        }),
            'total_investment' => Stock::where('quantity', '>', 0)
                                       ->where('expiry_date', '>', Carbon::now())
                                       ->get()
                                       ->sum(function($stock) {
                                           return $stock->quantity * $stock->purchase_price;
                                       }),
        ];

        // Top medicines by stock quantity
        $topStockMedicines = Medicine::with('stocks')
            ->get()
            ->map(function($medicine) {
                $totalQuantity = $medicine->stocks->where('quantity', '>', 0)
                                                  ->where('expiry_date', '>', Carbon::now())
                                                  ->sum('quantity');
                $stockValue = $medicine->stocks->where('quantity', '>', 0)
                                               ->where('expiry_date', '>', Carbon::now())
                                               ->sum(function($stock) {
                                                   return $stock->quantity * $stock->selling_price;
                                               });
                return [
                    'medicine_name' => $medicine->name,
                    'total_quantity' => $totalQuantity,
                    'stock_value' => $stockValue,
                ];
            })
            ->sortByDesc('total_quantity')
            ->take(10)
            ->values();

        $stats['top_stock_medicines'] = $topStockMedicines;

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Adjust stock quantity
     */
    public function adjustQuantity(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'adjustment_type' => 'required|in:increase,decrease',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $stock = Stock::find($id);

        if (!$stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stock not found'
            ], 404);
        }

        $oldQuantity = $stock->quantity;

        if ($request->adjustment_type === 'decrease') {
            if ($stock->quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot decrease more than available quantity'
                ], 400);
            }
            $stock->quantity -= $request->quantity;
        } else {
            $stock->quantity += $request->quantity;
        }

        $stock->save();

        return response()->json([
            'success' => true,
            'data' => $stock->load(['medicine', 'supplier']),
            'message' => 'Stock quantity adjusted successfully',
            'adjustment' => [
                'old_quantity' => $oldQuantity,
                'new_quantity' => $stock->quantity,
                'type' => $request->adjustment_type,
                'amount' => $request->quantity,
                'reason' => $request->reason
            ]
        ]);
    }

    /**
     * Transfer stock between batches
     */
    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_stock_id' => 'required|exists:stocks,id',
            'to_stock_id' => 'required|exists:stocks,id|different:from_stock_id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $fromStock = Stock::find($request->from_stock_id);
        $toStock = Stock::find($request->to_stock_id);

        // Validate medicine is same
        if ($fromStock->medicine_id !== $toStock->medicine_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot transfer between different medicines'
            ], 400);
        }

        // Check available quantity
        if ($fromStock->quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient quantity in source stock. Available: ' . $fromStock->quantity
            ], 400);
        }

        // Check if source stock is expired
        if ($fromStock->expiry_date < Carbon::now()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot transfer from expired stock'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Decrease from source
            $fromStock->decrement('quantity', $request->quantity);
            
            // Increase in destination
            $toStock->increment('quantity', $request->quantity);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock transferred successfully',
                'data' => [
                    'from_stock' => $fromStock->fresh()->load('medicine'),
                    'to_stock' => $toStock->fresh()->load('medicine'),
                ],
                'transfer_details' => [
                    'quantity_transferred' => $request->quantity,
                    'reason' => $request->reason ?? 'Not specified',
                    'timestamp' => Carbon::now()->toDateTimeString()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Transfer failed: ' . $e->getMessage()
            ], 500);
        }
    }
}