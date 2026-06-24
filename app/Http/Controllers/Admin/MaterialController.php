<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Display material list.
     */
    public function index()
    {
        $materials = Material::latest()->get();

        return view('admin.material.index', compact('materials'));
    }

    /**
     * Show create material form.
     */
    public function create()
    {
        return view('admin.material.create');
    }

    /**
     * Store a new material.
     */
    public function store(Request $request)
    {
        $request->validate([
            'material_name' => 'required|string|max:255|unique:materials,material_name',
            'category' => 'required|in:feed,medicine',
            'unit' => 'required|string|max:50',
            'average_usage' => 'nullable|numeric|min:0',
            'lead_time' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $material = Material::create([
            'material_name' => $request->material_name,
            'category' => $request->category,
            'unit' => $request->unit,
            'stock' => 0,
            'average_usage' => $request->average_usage ?? 0,
            'lead_time' => $request->lead_time ?? 0,
            'safety_stock' => $request->safety_stock ?? 5,
            'description' => $request->description,
        ]);

        $this->logActivity('material_created', $material);

        return redirect()
            ->route('admin.materials.index')
            ->with('success', 'Material berhasil ditambahkan');
    }

    /**
     * Display material details.
     */
    public function show(Material $material)
    {
        return view('admin.material.show', compact('material'));
    }

    /**
     * Update material data.
     */
    public function update(Request $request, Material $material)
    {
        $request->validate([
            'material_name' => 'required|string|max:255|unique:materials,material_name,' . $material->id,
            'category' => 'required|in:feed,medicine',
            'unit' => 'required|string|max:50',
            'average_usage' => 'nullable|numeric|min:0',
            'lead_time' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $material->update([
            'material_name' => $request->material_name,
            'category' => $request->category,
            'unit' => $request->unit,
            'average_usage' => $request->average_usage ?? 0,
            'lead_time' => $request->lead_time ?? 0,
            'safety_stock' => $request->safety_stock ?? 5,
            'description' => $request->description,
        ]);

        $this->logActivity('material_updated', $material);

        if ($request->source === 'inventory') {
            return back()->with(
                'success',
                'Parameter ROP berhasil diperbarui!'
            );
        }

        return redirect()
            ->route('admin.materials.index')
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

        $this->logActivity('material_deleted', $material);

        $material->delete();

        return redirect()
            ->route('admin.materials.index')
            ->with('success', 'Material berhasil dihapus');
    }

    private function logActivity($type, $material)
    {
        ActivityLog::create([
            'actor_id' => auth('admin')->id(),
            'actor_type' => \App\Models\Admin::class,
            'type' => $type,
            'module' => 'material',
            'description' => match ($type) {
                'material_created' =>
                "Menambahkan bahan baku {$material->material_name}",

                'material_updated' =>
                "Memperbarui bahan baku {$material->material_name}",

                'material_deleted' =>
                "Menghapus bahan baku {$material->material_name}",

                default =>
                "Aktivitas material {$material->material_name}",
            }
        ]);
    }
}