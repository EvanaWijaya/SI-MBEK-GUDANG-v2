<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Display material list.
     */
    public function index()
    {
        $materials = Material::latest()->get();

        return view('owner.material.index', compact('materials'));
    }

    /**
     * Show create material form.
     */
    public function create()
    {
        return view('owner.material.create');
    }

    /**
     * Store a new material.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:materials,name',
            'category' => 'required|in:pakan,obat',
            'unit' => 'required|string|max:50',
            'average_usage' => 'nullable|numeric|min:0',
            'lead_time' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        Material::create([
            'name' => $request->name,
            'category' => $request->category,
            'unit' => $request->unit,
            'stock' => 0,
            'average_usage' => $request->average_usage ?? 0,
            'lead_time' => $request->lead_time ?? 0,
            'safety_stock' => $request->safety_stock ?? 5,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('owner.materials.index')
            ->with('success', 'Material berhasil ditambahkan');
    }

    /**
     * Display material details.
     */
    public function show(Material $material)
    {
        return view('owner.material.show', compact('material'));
    }

    /**
     * Update material data.
     */
    public function update(Request $request, Material $material)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:materials,name,' . $material->id,
            'category' => 'required|in:pakan,obat',
            'unit' => 'required|string|max:50',
            'average_usage' => 'nullable|numeric|min:0',
            'lead_time' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $material->update([
'material_name' => $request->name,            
'category' => $request->category,
            'unit' => $request->unit,
            'average_usage' => $request->average_usage ?? 0,
            'lead_time' => $request->lead_time ?? 0,
            'safety_stock' => $request->safety_stock ?? 5,
            'description' => $request->description,
        ]);

        if ($request->source === 'inventory') {
            return back()->with(
                'success',
                'Parameter ROP berhasil diperbarui!'
            );
        }

        return redirect()
            ->route('owner.materials.index')
            ->with('success', 'Material berhasil diperbarui');
    }

    /**
     * Delete material.
     */
    public function destroy(Material $material)
    {
        // Prevent deletion when stock still exists
        if ($material->stock > 0) {
            return back()->with(
                'error',
                'Material tidak bisa dihapus karena masih memiliki stok'
            );
        }

        // Prevent deletion when used in formulas
        if ($material->formulaMaterials()->exists()) {
            return back()->with(
                'error',
                'Material tidak bisa dihapus karena masih digunakan dalam formula'
            );
        }

        $material->delete();

        return redirect()
            ->route('owner.materials.index')
            ->with('success', 'Material berhasil dihapus');
    }
}