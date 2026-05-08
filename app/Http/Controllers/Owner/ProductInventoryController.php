<?php

namespace App\Http\Controllers\Owner;

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

        return view('owner.inventory.product.index', compact('products'));
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

        return view('owner.inventory.product.show', compact(
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
    
}