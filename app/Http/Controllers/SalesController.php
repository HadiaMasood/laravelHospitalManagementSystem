<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Medicine;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'items.medicine']);

        // Cashiers only see their own sales
        if (auth()->user()->role === 'cashier') {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled(['from_date', 'to_date'])) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }

        $sales = $query->latest()->paginate(15);
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $medicines = Medicine::with(['stocks' => function ($query) {
            $query->where('quantity', '>', 0)
                  ->where('expiry_date', '>', now())
                  ->orderBy('expiry_date');
        }])->get();

        return view('sales.create', compact('medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'discount' => 'numeric|min:0',
            'tax' => 'numeric|min:0',
            'payment_method' => 'required|in:cash,card,upi',
            'items' => 'required|array|min:1',
            'items.*.stock_id' => 'required|exists:stocks,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $stock = Stock::findOrFail($item['stock_id']);

                if ($stock->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$stock->medicine->name}");
                }

                $itemSubtotal = $stock->selling_price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'stock' => $stock,
                    'quantity' => $item['quantity'],
                    'unit_price' => $stock->selling_price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $discount = $request->discount ?? 0;
            $tax = $request->tax ?? 0;
            $total = ($subtotal - $discount) + $tax;

            $sale = Sale::create([
    'invoice_number' => 'INV-' . now()->format('YmdHis'),
    'customer_name' => $request->customer_name,
    'customer_phone' => $request->customer_phone,
    'total_amount' => $subtotal,      // Changed from 'subtotal'
    'discount' => $discount,
    'tax' => $tax,
    'final_amount' => $total,         // Changed from 'total'
    'payment_method' => $request->payment_method,
    'user_id' => auth()->id(),
]);
            foreach ($itemsData as $item) {
                $sale->items()->create([
                    'stock_id' => $item['stock']->id,
                    'medicine_id' => $item['stock']->medicine_id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $item['stock']->decrement('quantity', $item['quantity']);
            }

            DB::commit();

            return redirect()
                ->route('sales.show', $sale->id)
                ->with('success', 'Sale completed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.medicine', 'items.stock', 'user']);

        // Cashiers can only view their own sales
        if (auth()->user()->role === 'cashier' && $sale->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        return view('sales.show', compact('sale'));
    }
}