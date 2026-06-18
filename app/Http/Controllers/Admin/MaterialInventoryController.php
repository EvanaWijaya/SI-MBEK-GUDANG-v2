<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialInventoryController extends Controller
{
    /**
     * 📊 List semua bahan + stok
     */
    public function index()
    {
        $materials = Material::orderBy('material_name')->get();

        return view('admin.inventory.material.index', compact('materials'));
    }

    /**
     * 🔍 Detail 1 bahan (batch + movement)
     */
    public function show(Material $material)
    {
        $batches = $material->materialStocks()
            ->orderBy('received_date', 'asc')
            ->get();

        $movements = $material->stockMovements()
            ->latest()
            ->get();

        return view('admin.inventory.material.show', compact('material', 'batches', 'movements'));
    }

    /**
     * ➕ / ➖ Adjustment Manual
     */
    public function adjust(Request $request, Material $material)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',

            'expiration_date' => 'required_if:type,in|nullable|date|after_or_equal:today',
            'note' => 'nullable|string|max:255',
        ], [
            // Custom pesan error biar lebih jelas
            'expiration_date.required_if' => 'Expired date wajib diisi untuk penambahan stok barang baru.',
        ]);

        DB::transaction(function () use ($request, $material) {

            if ($request->type === 'in') {

                // Tambah batch baru
                MaterialStock::create([
                    'material_id' => $material->id,
                    'quantity' => $request->quantity,
                    'received_date' => now(),
                    'expiration_date' => $request->expiration_date,
                    'created_by' => auth('admin')->id(),
                ]);

            } else {

                $remaining = $request->quantity;

                /** @var \Illuminate\Database\Eloquent\Collection<int, MaterialStock> $batches */
                $batches = MaterialStock::where('material_id', $material->id)
                    ->where('quantity', '>', 0)
                    ->orderBy('received_date', 'asc')
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {

                    if ($remaining <= 0)
                        break;

                    if ($batch->quantity >= $remaining) {
                        $batch->decrement('qty', $remaining);
                        $remaining = 0;
                    } else {
                        $remaining -= $batch->quantity;
                        $batch->update(['quantity' => 0]);
                    }
                }

                if ($remaining > 0) {
                    throw new \Exception('Stok tidak mencukupi');
                }
            }

            // 🔥 Sync summary stok
            $material->stock = $material->materialStocks()->sum('quantity');
            $material->save();

            // Catat movement
            StockMovement::create([
                'stockable_id' => $material->id,
                'stockable_type' => Material::class,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'source' => 'adjustment',
                'movement_date' => now(),
                'notes' => $request->notes,
                'created_by' => auth('admin')->id(),
            ]);
        });

        return redirect()->back()->with('success', 'Stok berhasil disesuaikan');
    }

    /**
     * 🔄 Sync stok manual (untuk audit)
     */
    public function sync(Material $material)
    {
        $material->stock = $material->materialStocks()->sum('quantity');
        $material->save();

        return redirect()->back()->with('success', 'Stok berhasil disinkronisasi');
    }
}