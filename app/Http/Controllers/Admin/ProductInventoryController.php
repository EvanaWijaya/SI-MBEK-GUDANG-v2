<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductInventoryController extends Controller
{
    /**
     * 📦 List semua produk + stok total
     */
    public function index()
    {
        $products = Product::with(['allocations', 'stocks']) 
        ->withSum('stocks', 'qty')
        ->orderBy('nama')
        ->get();

        return view('admin.inventory.product.index', compact('products'));
    }

    /**
     * 📋 Detail batch per produk
     */
    public function show(Product $product)
    {
        // 🔥 TAMBAHAN: Load relasi stocks dari awal biar jadi Collection utuh
        $product->load(['stocks' => function ($query) {
            $query->orderBy('received_date', 'asc');
        }]);

        // 1. Ambil data batch dari relasi yang udah di-load
        $batches = $product->stocks;

        // 2. Ambil riwayat pergerakan stok (Movements)
        $movements = $product->stockMovements() 
            ->orderBy('movement_date', 'desc')
            ->latest()
            ->get();

        // 3. Ambil data alokasi produk
        $allocations = $product->allocations; 

        return view('admin.inventory.product.show', compact(
            'product',
            'batches',
            'movements',
            'allocations'
        ));
    }

    /**
     * 🔄 Sync summary stok dengan total batch
     */
    public function sync(Product $product)
    {
        DB::transaction(function () use ($product) {

            $realStock = $product->stocks()->sum('qty');

            $product->update([
                'stok' => $realStock
            ]);
        });

        return redirect()->back()->with('success', 'Stok berhasil disinkronkan');
    }

    /**
     * Adjust Manual
     */
    public function adjust(Request $request, Product $product)
    {
       $request->validate([
        'type' => 'required|in:in,out',
        'qty' => 'required|integer|min:1',
        'expired_date' => 'required_if:type,in|nullable|date|after_or_equal:today',
        'reason' => 'nullable|string' // Ini adalah catatan/alasan
    ], [
        'expired_date.required_if' => 'Expired date wajib diisi untuk penambahan stok barang baru.',
    ]);

        DB::transaction(function () use ($request, $product) {
            $jumlahAdjust = $request->qty;

            if ($request->type === 'in') {

                ProductStock::create([
                    'product_id' => $product->id,
                    'qty' => $jumlahAdjust,
                    'source' => 'manual_adjustment',
                    'reference_id' => null,
                    'received_date' => now(),
                    'expired_date' => $request->expired_date,
                    'created_by' => auth('admin')->id(), // 🔥 penting
                ]);

                $product->increment('stok', $request->qty);
            }

            if ($request->type === 'out') {

                $remainingQty = $request->qty;

                /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\MaterialStock> $batches */
                $batches = ProductStock::where('product_id', $product->id)
                    ->where('qty', '>', 0)
                    ->orderBy('received_date', 'asc')
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {

                    if ($remainingQty <= 0)
                        break;

                    if ($batch->qty >= $remainingQty) {
                        $batch->decrement('qty', $remainingQty);
                        $remainingQty = 0;
                    } else {
                        $remainingQty -= $batch->qty;
                        $batch->update(['qty' => 0]);
                    }
                }

                if ($remainingQty > 0) {
                    throw new \Exception('Stok batch tidak mencukupi untuk adjustment');
                }

                $product->decrement('stok', $request->qty);
            }

           StockMovement::create([
                'stockable_id' => $product->id,
                'stockable_type' => get_class($product), // Menggunakan class Product
                'type' => $request->type,
                'quantity' => $jumlahAdjust,
                'source' => 'manual_adjustment',
                'reference_id' => null,
                'movement_date' => now(),
                'catatan' => $request->reason, // Mengambil 'reason' dari form
                'created_by' => auth('admin')->id(),
            ]);
        });

        return redirect()->back()->with('success', 'Stok berhasil disesuaikan');
    }

    public function updateRop(Request $request, Product $product)
{
    $request->validate([
        'rop' => 'required|integer|min:0'
    ]);

    $product->update([
        'rop' => $request->rop
    ]);

    return redirect()->back()->with('success', 'Reorder Point (ROP) berhasil diperbarui');
}
    
}