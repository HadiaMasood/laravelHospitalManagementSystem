<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicineController extends Controller
{
    // 🔹 List medicines with search & filters
    public function index(Request $request)
    {
        $query = Medicine::with('supplier');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $medicines = $query->paginate($request->per_page ?? 15);

        return response()->json($medicines);
    }

    // 🔹 Store medicine
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'generic_name'  => 'nullable|string|max:255',
            'category'      => 'required|string|max:100',
            'description'   => 'nullable|string',
            'unit_price'    => 'required|numeric|min:0',
            'barcode'       => 'required|string|max:50|unique:medicines',
            'reorder_level' => 'integer|min:0',
            'supplier_id'   => 'required|exists:suppliers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $medicine = Medicine::create($request->all());

        return response()->json([
            'message'  => 'Medicine created successfully',
            'medicine' => $medicine->load('supplier')
        ], 201);
    }

    // 🔹 Show single medicine
    public function show($id)
    {
        $medicine = Medicine::with(['supplier', 'stocks'])->findOrFail($id);
        return response()->json($medicine);
    }

    // 🔹 Update medicine
    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'          => 'string|max:255',
            'generic_name'  => 'nullable|string|max:255',
            'category'      => 'string|max:100',
            'description'   => 'nullable|string',
            'unit_price'    => 'numeric|min:0',
            'barcode'       => 'string|max:50|unique:medicines,barcode,' . $id,
            'reorder_level' => 'integer|min:0',
            'supplier_id'   => 'exists:suppliers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $medicine->update($request->all());

        return response()->json([
            'message'  => 'Medicine updated successfully',
            'medicine' => $medicine->load('supplier')
        ]);
    }

    // 🔹 Delete medicine
    public function destroy($id)
    {
        Medicine::findOrFail($id)->delete();
        return response()->json(['message' => 'Medicine deleted successfully']);
    }

    // 🔹 Find by barcode (POS scan)
    public function findByBarcode($barcode)
    {
        $medicine = Medicine::with([
            'supplier',
            'stocks' => function ($query) {
                $query->where('quantity', '>', 0)
                      ->where('expiry_date', '>', now());
            }
        ])->where('barcode', $barcode)->first();

        if (!$medicine) {
            return response()->json(['error' => 'Medicine not found'], 404);
        }

        return response()->json($medicine);
    }
}
