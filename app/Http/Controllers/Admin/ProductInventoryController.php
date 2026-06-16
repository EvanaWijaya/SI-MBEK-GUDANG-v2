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
     * Display product inventory list
     */
    public function index()
    {
        $products = Product::with([
            'allocations',
            'stocks'
        ])
            ->withSum('stocks', 'quantity')
            ->orderBy('product_name')
            ->get();

        return view('admin.inventory.product.index', compact('products'));
    }

    /**
     * Display product stock details
     */
    public function show(Product $product)
    {
        $product->load([
            'stocks' => function ($query) {
                $query->orderBy('received_date', 'asc');
            }
        ]);

        $batches = $product->stocks;

        $movements = $product->stockMovements()
            ->orderBy('movement_date', 'desc')
            ->get();

        $allocations = $product->allocations;

        return view('admin.inventory.product.show', compact(
            'product',
            'batches',
            'movements',
            'allocations'
        ));
    }

    /**
     * Synchronize stock summary with stock batches
     */
    public function sync(Product $product)
    {
        DB::transaction(function () use ($product) {

            $realStock = $product->stocks()->sum('quantity');

            $product->update([
                'stock' => $realStock
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'Stok berhasil disinkronkan');
    }

    /**
     * Perform manual stock adjustment
     */
    public function adjust(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'expiration_date' => 'required_if:type,in|nullable|date|after_or_equal:today',
            'reason' => 'nullable|string'
        ], [
            'expired_date.required_if' => 'Expired date wajib diisi untuk penambahan stok barang baru.',
        ]);

        DB::transaction(function () use ($request, $product) {

            $adjustQty = $request->quantity;

            if ($request->type === 'in') {

                ProductStock::create([
                    'product_id' => $product->id,
                    'quantity' => $adjustQty,
                    'source' => 'manual_adjustment',
                    'reference_id' => null,
                    'received_date' => now(),
                    'expiration_date' => $request->expired_date,
                    'price_per_unit' => 0,
                ]);

                $product->increment('stock', $adjustQty);
            }

            if ($request->type === 'out') {

                $remainingQty = $adjustQty;

                $batches = ProductStock::where('product_id', $product->id)
                    ->where('quantity', '>', 0)
                    ->orderBy('received_date', 'asc')
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {

                    if ($remainingQty <= 0) {
                        break;
                    }

                    if ($batch->quantity >= $remainingQty) {

                        $batch->decrement('quantity', $remainingQty);

                        $remainingQty = 0;

                    } else {

                        $remainingQty -= $batch->quantity;

                        $batch->update([
                            'quantity' => 0
                        ]);
                    }
                }

                if ($remainingQty > 0) {
                    throw new \Exception(
                        'Stok batch tidak mencukupi untuk adjustment'
                    );
                }

                $product->decrement('stock', $adjustQty);
            }

            StockMovement::create([
                'stockable_id' => $product->id,
                'stockable_type' => Product::class,
                'type' => $request->type,
                'quantity' => $adjustQty,
                'source' => 'manual_adjustment',
                'reference_id' => null,
                'notes' => $request->reason,
                'movement_date' => now(),
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'Stok berhasil disesuaikan');
    }

    /**
     * Update reorder point value
     */
    public function updateRop(Request $request, Product $product)
    {
        $request->validate([
            'reorder_point' => 'required|integer|min:0'
        ]);

        $product->update([
            'reorder_point' => $request->reorder_point
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Reorder Point (ROP) berhasil diperbarui'
            );
    }
}