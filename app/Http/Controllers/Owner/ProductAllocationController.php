<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductAllocation;
use App\Models\ActivityLog;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductAllocationController extends Controller
{
    /**
     * 1️⃣ Simpan / update alokasi stok
     */
    public function storeOrUpdate(Request $request, Product $product)
    {
        $validated = $request->validate([
            'type' => 'required|in:sale,internal',
            'quantity' => 'required|integer|min:0',
        ]);

        try {
            // Ambil alokasi lain (selain type yang sedang diubah)
            $allocatedOther = $product->allocations()
                ->where('type', '!=', $request->type)
                ->sum('quantity');

            $totalAfter = $allocatedOther + $request->quantity;

            // ❗ VALIDASI TOTAL ALOKASI
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
                    'created_by' => auth('owner')->id(),
                ]
            );

            $actor = $this->getCurrentActor();
            if ($actor) {
                ActivityLog::create([
                    'actor_id' => $actor->id,
                    'actor_type' => get_class($actor),
                    'type' => 'allocation_created',
                    'module' => 'product_allocation',
                    'description' => 'Membuat alokasi produk #' . $allocation->id
                ]);
            }


            DB::commit();

            return back()->with('success', 'Alokasi stok berhasil disimpan');

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 2️⃣ Pemakaian internal
     * - kurangi stok produk
     * - kurangi alokasi internal
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
                    throw new \Exception('Alokasi internal belum tersedia');
                }

                if ($request->quantity > $allocation->quantity) {
                    throw new \Exception('Qty melebihi alokasi internal');
                }

                if ($request->quantity > $product->stok) {
                    throw new \Exception('Stok produk tidak mencukupi');
                }

                $remainingQty = $request->quantity;

                /** @var \Illuminate\Database\Eloquent\Collection<int, ProductStock> $batches */
                $batches = ProductStock::where('product_id', $product->id)
                    ->where('quantity', '>', 0)
                    ->where(function ($q) {
                        $q->whereNull('expiration_date')
                            ->orWhere('expiration_date', '>=', now());
                    })
                    ->orderBy('received_date', 'asc') // FIFO
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {

                    if ($remainingQty <= 0)
                        break;

                    if ($batch->quantity >= $remainingQty) {
                        $batch->decrement('quantity', $remainingQty);
                        $remainingQty = 0;
                    } else {
                        $remainingQty -= $batch->quantity;
                        $batch->update(['quantity' => 0]);
                    }
                }

                if ($remainingQty > 0) {
                    throw new \Exception('Batch produk tidak mencukupi');
                }

                $product->decrement('stock', $request->quantity);
                $allocation->decrement('quantity', $request->quantity);

                $allocation->decrement('quantity', $request->quantity);

                StockMovement::create([
                    'stockable_id' => $product->id,
                    'stockable_type' => Product::class,
                    'type' => 'out',
                    'quantity' => $request->quantity,
                    'source' => 'pemakaian_internal',
                    'reference_id' => null,
                    'movement_date' => now(),
                ]);
            });

            // 🔔 ROP CHECK
            $product->refresh();

            if ($product->isBelowRop()) {
                return back()->with([
                    'success' => 'Pemakaian internal berhasil',
                    'warning' => '⚠️ Stok produk sudah mencapai batas minimum (ROP)',
                ]);
            }

            return back()->with('success', 'Pemakaian internal berhasil dicatat');

        } catch (\Throwable $e) {
            return back()->withErrors([
                'quantity' => $e->getMessage(),
            ]);
        }
    }


    /**
     * 3️⃣ Penjualan produk
     * - kurangi stok produk
     * - kurangi alokasi jual
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
                    throw new \Exception('Alokasi jual belum tersedia');
                }

                if ($request->quantity > $allocation->quantity) {
                    throw new \Exception('Qty melebihi alokasi jual');
                }

                if ($request->quantity > $product->stock) {
                    throw new \Exception('Stok produk tidak mencukupi');
                }

                $product->decrement('stock', $request->quantity);
                $allocation->decrement('quantity', $request->quantity);

                StockMovement::create([
                    'stockable_id' => $product->id,
                    'stockable_type' => Product::class,
                    'type' => 'out',
                    'quantity' => $request->quantity,
                    'source' => 'jual',
                    'reference_id' => null,
                    'movement_date' => now(),
                ]);
            });

            // 🔔 ROP CHECK
            $product->refresh();

            if ($product->isBelowRop()) {
                return back()->with([
                    'success' => 'Penjualan berhasil',
                    'warning' => '⚠️ Stok produk sudah mencapai batas minimum (ROP)',
                ]);
            }

            return back()->with('success', 'Penjualan berhasil dicatat');

        } catch (\Throwable $e) {
            return back()->withErrors([
                'quantity' => $e->getMessage(),
            ]);
        }
    }
}