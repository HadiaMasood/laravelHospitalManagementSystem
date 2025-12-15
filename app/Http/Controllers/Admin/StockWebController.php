<?php
// ============================================
// 1. FIXED: app/Http/Controllers/Admin/StockWebController.php
// ============================================

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Medicine;
use App\Models\Supplier;

class StockWebController extends Controller
{
    public function __construct()
    {
        // Only admin can access all stock management functions except index and show
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                abort(403, 'Unauthorized. Only administrators can access this section.');
            }
            return $next($request);
        })->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        // Only admin can view stock index
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only administrators can access this section.');
        }

        $query = Stock::with(['medicine', 'supplier']);
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhereHas('medicine', function($mq) use ($search) {
                      $mq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->has('status')) {
            switch ($request->status) {
                case 'expiring':
                    $query->expiring(90);
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'active':
                    $query->active();
                    break;
            }
        }
        
        $stocks = $query->latest()->paginate(15);
        
        $expiringCount = Stock::expiring(90)->count();
        $expiredCount = Stock::expired()->count();
        $lowStockCount = Medicine::all()->filter(function($medicine) {
            return $medicine->stocks->sum('quantity') <= $medicine->reorder_level;
        })->count();

        return view('admin.stocks.index', compact(
            'stocks',
            'expiringCount',
            'expiredCount',
            'lowStockCount'
        ));
    }

    public function create()
    {
        $medicines = Medicine::all();
        $suppliers = Supplier::all();
        return view('admin.stocks.create', compact('medicines', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'batch_number' => 'required|string|max:50|unique:stocks,batch_number',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gt:purchase_price',
            'expiry_date' => 'required|date|after:today',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date|before_or_equal:today',
        ]);

        Stock::create($validated);

        return redirect()->route('admin.stocks.index')
                        ->with('success', 'Stock added successfully');
    }

    public function show($id)
    {
        // Only admin can view stock details
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only administrators can access this section.');
        }

        $stock = Stock::with(['medicine', 'supplier', 'saleItems'])->findOrFail($id);
        return view('admin.stocks.show', compact('stock'));
    }

    public function edit($id)
    {
        $stock = Stock::findOrFail($id);
        $medicines = Medicine::all();
        $suppliers = Supplier::all();
        return view('admin.stocks.edit', compact('stock', 'medicines', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $stock = Stock::findOrFail($id);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gt:purchase_price',
            'expiry_date' => 'required|date',
            'is_active' => 'boolean',
        ]);

        $stock->update($validated);

        return redirect()->route('admin.stocks.index')
                        ->with('success', 'Stock updated successfully');
    }

    public function destroy($id)
    {
        $stock = Stock::findOrFail($id);

        if ($stock->saleItems()->count() > 0) {
            return back()->with('error', 'Cannot delete stock used in sales');
        }

        $stock->delete();

        return redirect()->route('admin.stocks.index')
                        ->with('success', 'Stock deleted successfully');
    }

    public function expiring()
    {
        $stocks = Stock::with(['medicine', 'supplier'])
            ->expiring(90)
            ->orderBy('expiry_date', 'asc')
            ->paginate(15);

        return view('admin.stocks.expiring', compact('stocks'));
    }

    public function expired()
    {
        $stocks = Stock::with(['medicine', 'supplier'])
            ->expired()
            ->orderBy('expiry_date', 'desc')
            ->paginate(15);

        return view('admin.stocks.expired', compact('stocks'));
    }

    public function lowStock()
    {
        $lowStocks = Medicine::with('stocks')
            ->get()
            ->filter(function($medicine) {
                return $medicine->stocks->sum('quantity') <= $medicine->reorder_level;
            })
            ->values();

        return view('admin.stocks.low-stock', compact('lowStocks'));
    }
}