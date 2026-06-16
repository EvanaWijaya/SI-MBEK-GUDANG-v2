<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display supplier list
     */
    public function index()
    {
        $suppliers = Supplier::latest()->get();

        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Show create supplier form
     */
    public function create()
    {
        return view('admin.suppliers.create');
    }

    /**
     * Store new supplier
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact'       => 'nullable|string|max:100',
            'address'       => 'nullable|string',
            'city'          => 'nullable|string|max:100',
            'province'      => 'nullable|string|max:100',
            'notes'         => 'nullable|string',
        ]);

        Supplier::create($validated);

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan');
    }

    /**
     * Show edit supplier form
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * Update supplier data
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact'       => 'nullable|string|max:100',
            'address'       => 'nullable|string',
            'city'          => 'nullable|string|max:100',
            'province'      => 'nullable|string|max:100',
            'notes'         => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui');
    }

    /**
     * Display supplier details
     */
    public function show(Supplier $supplier)
    {
        $supplier->load('purchaseOrders');

        return view('admin.suppliers.show', compact('supplier'));
    }

    /**
     * Delete supplier
     */
    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchaseOrders()->exists()) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Supplier tidak dapat dihapus karena masih memiliki purchase order'
                );
        }

        $supplier->delete();

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil dihapus');
    }
}