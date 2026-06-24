<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\ProductAllocation;
use App\Models\ProductStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductAllocationController extends Controller
{
    /**
     * Save or update stock allocation
     */
    public function storeOrUpdate(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:sale,internal',
            'quantity' => 'required|integer|min:0',
        ]);

        try {

            $allocatedOther = $product->allocations()
                ->where('type', '!=', $request->type)
                ->sum('quantity');

            $totalAfter = $allocatedOther + $request->quantity;

            if ($totalAfter > $product->stock) {
                return back()->withErrors([
                    'quantity' => 'Total alokasi melebihi stok produk',
                ]);
            }

            $allocation = ProductAllocation::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'type' => $request->type,
                ],
                [
                    'quantity' => $request->quantity,
                    'created_by' => auth('admin')->id(),
                ]
            );

            ActivityLog::create([
                'actor_id' => auth('admin')->id(),
                'actor_type' => \App\Models\Admin::class,
                'type' => $allocation->type,
                'module' => 'product_allocation',
                'description' => "Membuat alokasi produk dengan ID {$allocation->product_id} sebanyak ({$allocation->quantity})",
            ]);

            return back()->with(
                'success',
                'Alokasi stok berhasil disimpan'
            );

        } catch (\Throwable $e) {

            return back()->withErrors([
                'quantity' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Process internal product usage
     */
    public function useInternal(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {

            DB::transaction(function () use ($product, $request) {

                $allocation = $product->allocations()
                    ->where('type', 'internal')
                    ->lockForUpdate()
                    ->first();

                if (!$allocation) {
                    throw new \Exception(
                        'Alokasi internal belum tersedia'
                    );
                }

                if ($request->quantity > $allocation->quantity) {
                    throw new \Exception(
                        'Qty melebihi alokasi internal'
                    );
                }

                if ($request->quantity > $product->stock) {
                    throw new \Exception(
                        'Stok produk tidak mencukupi'
                    );
                }

                $remainingQty = $request->quantity;

                $batches = ProductStock::where(
                    'product_id',
                    $product->id
                )
                    ->where('quantity', '>', 0)
                    ->where(function ($query) {
                        $query->whereNull('expiration_date')
                            ->orWhere(
                                'expiration_date',
                                '>=',
                                now()
                            );
                    })
                    ->orderBy('received_date', 'asc')
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {

                    if ($remainingQty <= 0) {
                        break;
                    }

                    if ($batch->quantity >= $remainingQty) {

                        $batch->decrement(
                            'quantity',
                            $remainingQty
                        );

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
                        'Batch produk tidak mencukupi'
                    );
                }

                $product->decrement(
                    'stock',
                    $request->quantity
                );

                $allocation->decrement(
                    'quantity',
                    $request->quantity
                );

                StockMovement::create([
                    'stockable_id' => $product->id,
                    'stockable_type' => Product::class,
                    'type' => 'out',
                    'quantity' => $request->quantity,
                    'source' => 'internal_usage',
                    'reference_id' => null,
                    'notes' => 'Pemakaian internal produk',
                    'movement_date' => now(),
                ]);

                ActivityLog::create([
                    'actor_id' => auth('admin')->id(),
                    'actor_type' => \App\Models\Admin::class,
                    'type' => 'internal_usage',
                    'module' => 'product_allocation',
                    'description' => 'Pemakaian internal produk dengan ID ' . $product->id,
                ]);
            });

            $product->refresh();

            if ($product->isBelowReorderPoint()) {

                return back()->with([
                    'success' => 'Pemakaian internal berhasil',
                    'warning' => '⚠️ Stok produk sudah mencapai batas minimum (ROP)',
                ]);
            }

            return back()->with(
                'success',
                'Pemakaian internal berhasil dicatat'
            );

        } catch (\Throwable $e) {

            return back()->withErrors([
                'quantity' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Process product sale
     */
    public function sell(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {

            DB::transaction(function () use ($product, $request) {

                $allocation = $product->allocations()
                    ->where('type', 'sale')
                    ->lockForUpdate()
                    ->first();

                if (!$allocation) {
                    throw new \Exception(
                        'Alokasi jual belum tersedia'
                    );
                }

                if ($request->quantity > $allocation->quantity) {
                    throw new \Exception(
                        'Qty melebihi alokasi jual'
                    );
                }

                if ($request->quantity > $product->stock) {
                    throw new \Exception(
                        'Stok produk tidak mencukupi'
                    );
                }

                $remainingQty = $request->quantity;

                $batches = ProductStock::where(
                    'product_id',
                    $product->id
                )
                    ->where('quantity', '>', 0)
                    ->where(function ($query) {
                        $query->whereNull('expiration_date')
                            ->orWhere(
                                'expiration_date',
                                '>=',
                                now()
                            );
                    })
                    ->orderBy('received_date', 'asc')
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {

                    if ($remainingQty <= 0) {
                        break;
                    }

                    if ($batch->quantity >= $remainingQty) {

                        $batch->decrement(
                            'quantity',
                            $remainingQty
                        );

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
                        'Batch produk tidak mencukupi'
                    );
                }

                $product->decrement(
                    'stock',
                    $request->quantity
                );

                $allocation->decrement(
                    'quantity',
                    $request->quantity
                );

                StockMovement::create([
                    'stockable_id' => $product->id,
                    'stockable_type' => Product::class,
                    'type' => 'out',
                    'quantity' => $request->quantity,
                    'source' => 'sale',
                    'reference_id' => null,
                    'notes' => 'Penjualan produk',
                    'movement_date' => now(),
                ]);

                ActivityLog::create([
                    'actor_id' => auth('admin')->id(),
                    'actor_type' => \App\Models\Admin::class,
                    'type' => 'sale',
                    'module' => 'product_allocation',
                    'description' => 'Penjualan produk dengan ID ' . $product->id,
                ]);
            });

            $product->refresh();

            if ($product->isBelowReorderPoint()) {

                return back()->with([
                    'success' => 'Penjualan berhasil',
                    'warning' => '⚠️ Stok produk sudah mencapai batas minimum (ROP)',
                ]);
            }

            return back()->with(
                'success',
                'Penjualan berhasil dicatat'
            );

        } catch (\Throwable $e) {

            return back()->withErrors([
                'quantity' => $e->getMessage(),
            ]);
        }
    }
}