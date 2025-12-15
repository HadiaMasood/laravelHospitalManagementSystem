<?php

namespace App\Http\Controllers\Api;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    /* =======================
       WEB METHODS
    ======================== */

    public function index()
    {
        $sales = Sale::latest()->paginate(20);
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $medicines = Medicine::with(['stocks' => function($query) {
            $query->where('quantity', '>', 0)
                  ->where('expiry_date', '>', now());
        }])->get();
        
        return view('sales.create', compact('medicines'));
    }

    /* =======================
       API / POS SALE STORE
    ======================== */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.stock_id' => 'required|exists:stocks,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|in:cash,card,upi'
        ]);

        if ($validator->fails()) {
            // Return HTML redirect for web requests
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $subtotal = 0;

            // Validate stock availability
            foreach ($request->items as $item) {
                $stock = Stock::with('medicine')->findOrFail($item['stock_id']);

                if ($stock->quantity < $item['quantity']) {
                    throw new \Exception('Insufficient stock for ' . $stock->medicine->name . '. Available: ' . $stock->quantity);
                }

                $subtotal += $stock->selling_price * $item['quantity'];
            }

            $discount = $request->discount ?? 0;
            $tax = $request->tax ?? 0;
            $total = ($subtotal - $discount) + $tax;

            // Generate sequential invoice number
            $lastSale = Sale::whereDate('created_at', today())->latest()->first();
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(
                ($lastSale ? ((int)substr($lastSale->invoice_number, -4) + 1) : 1), 
                4, '0', STR_PAD_LEFT
            );

            // Create sale with customer info
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $request->payment_method,
            ]);

            // Create sale items and update stock
            foreach ($request->items as $item) {
                $stock = Stock::findOrFail($item['stock_id']);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'stock_id' => $stock->id,
                    'medicine_id' => $stock->medicine_id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $stock->selling_price,
                    'subtotal' => $stock->selling_price * $item['quantity'],
                ]);

                // Decrement stock
                $stock->decrement('quantity', $item['quantity']);
            }

            DB::commit();

            // Return appropriate response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sale completed successfully',
                    'invoice_number' => $sale->invoice_number,
                    'sale_id' => $sale->id,
                    'total' => $total
                ], 201);
            }

            // Web response - redirect to sales page
            return redirect()->route('admin.sales.index')
                ->with('success', 'Sale completed successfully! Invoice: ' . $sale->invoice_number);

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Sale failed: ' . $e->getMessage());
        }
    }

    /* =======================
       SHOW SALE (API & WEB)
    ======================== */

    public function show($id)
    {
        $sale = Sale::with(['items.medicine', 'items.stock', 'user'])->findOrFail($id);
        
        // Return JSON for API requests
        if (request()->expectsJson()) {
            return response()->json($sale);
        }
        
        // Return view for web requests
        return view('sales.show', compact('sale'));
    }

    /* =======================
       INVOICE PDF
    ======================== */

    public function generateInvoice($id)
    {
        $sale = Sale::with(['items.medicine', 'items.stock', 'user'])->findOrFail($id);
        $pdf = Pdf::loadView('invoices.sale', compact('sale'));
        return $pdf->download('invoice-' . $sale->invoice_number . '.pdf');
    }

    /* =======================
       DELETE SALE
    ======================== */

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $sale = Sale::with('items')->findOrFail($id);
            
            // Restore stock quantities
            foreach ($sale->items as $item) {
                $stock = Stock::find($item->stock_id);
                if ($stock) {
                    $stock->increment('quantity', $item->quantity);
                }
            }
            
            // Delete sale items
            $sale->items()->delete();
            
            // Delete sale
            $sale->delete();
            
            DB::commit();
            
            return redirect()->route('admin.sales.index')
                ->with('success', 'Sale deleted and stock restored successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete sale: ' . $e->getMessage());
        }
    }
}