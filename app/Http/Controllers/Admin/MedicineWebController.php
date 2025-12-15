<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Supplier;
use Illuminate\Http\Request;

class MedicineWebController extends Controller
{
    public function __construct()
    {
        // Only admin can access medicine management
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                abort(403, 'Unauthorized. Only administrators can access this section.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = Medicine::with('supplier');
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
        }
        
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        $medicines = $query->paginate(15);
        $categories = Medicine::distinct()->pluck('category');
        
        return view('admin.medicines.index', compact('medicines', 'categories'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('admin.medicines.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'barcode' => 'required|string|max:50|unique:medicines',
            'reorder_level' => 'integer|min:0',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        Medicine::create($validated);

        return redirect()->route('admin.medicines.index')
                        ->with('success', 'Medicine added successfully');
    }

    public function show($id)
    {
        $medicine = Medicine::with(['supplier', 'stocks'])->findOrFail($id);
        return view('admin.medicines.show', compact('medicine'));
    }

    public function edit($id)
    {
        $medicine = Medicine::findOrFail($id);
        $suppliers = Supplier::all();
        return view('admin.medicines.edit', compact('medicine', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'barcode' => 'required|string|max:50|unique:medicines,barcode,' . $id,
            'reorder_level' => 'integer|min:0',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $medicine->update($validated);

        return redirect()->route('admin.medicines.index')
                        ->with('success', 'Medicine updated successfully');
    }

    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);
        $medicine->delete();

        return redirect()->route('admin.medicines.index')
                        ->with('success', 'Medicine deleted successfully');
    }
}