<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaterialStock;
use App\Models\ProductStock;
use App\Models\Production;
use Illuminate\Validation\Rule;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisposalController extends Controller
{
    //🔥 Manual Disposal - Material (Batch)//
    public function disposeMaterial(Request $request, MaterialStock $stock)
    {
        $request->validate([
            'reason' => [
                'required',
                Rule::in([
                    'expired',
                    'qc_failed'
                ]),
            ],
            'notes' => 'nullable|string'
        ]);

        if ($stock->quantity <= 0) {
            return back()->withErrors([
                'stok' => 'Batch bahan sudah habis atau sudah didisposal.',
            ]);
        }

        DB::beginTransaction();

        try {
            $qty = $stock->quantity;

            // 💡 Otomatic Logic Notes
            $notes = $request->notes;
            if ($request->reason === 'expired') {
                $notes = 'Otomatis dibuang karena batch bahan ini sudah melewati masa kadaluarsa (Expired).';
            }

            // Store to disposals (polymorphic) PROPERLY
            $stock->disposals()->create([
                'quantity' => $qty,
                'reason' => $request->reason,
                'notes' => $notes,
                'created_by' => auth('admin')->id(),
            ]);

            // Reduce stock summary in materials
            $stock->material->decrement('stock', $qty);

            // Finish the batch
            $stock->update([
                'quantity' => 0
            ]);

            $actor = $this->getCurrentActor();
            if ($actor) {
                ActivityLog::create([
                    'actor_id' => $actor->id,
                    'actor_type' => get_class($actor),
                    'type' => 'disposal_created',
                    'module' => 'disposal',
                    'description' => 'Membuat disposal untuk bahan ' . $stock->material->name
                ]);
            }

            DB::commit();

            return back()->with('success', 'Bahan berhasil didisposal.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }
    }

    //🔥 Manual Disposal - Production//
    public function disposeProduction(Request $request, Production $production)
    {
        $request->validate([
            'reason' => [
                'required',
                Rule::in([
                    'expired',
                    'qc_failed'
                ]),
            ],
            'notes' => 'nullable|string',
        ]);

        // Search for a product stock batch that matches this production ID
        $batch = ProductStock::where('source', 'production')
            ->where('reference_id', $production->id)
            ->first();

        if (!$batch || $batch->qty <= 0) {
            return back()->withErrors([
                'stok' => 'Produksi sudah tidak memiliki sisa stok di batch ini.',
            ]);
        }

        DB::beginTransaction();

        try {
            // FIX: Use remaining qty in BATCH, not initial production total
            $qty = $batch->qty;

            // 💡 AUTOMATIC NOTE LOGIC
            $notes = $request->notes;
            if ($request->reason === 'expired') {
                $notes = 'Otomatis dibuang karena produk ini sudah melewati masa kadaluarsa (Expired).';
            }

            // Save disposal
            $production->disposals()->create([
                'quantity' => $qty,
                'reason' => $request->reason,
                'notes' => $notes,
                'created_by' => auth('admin')->id(),
            ]);

            // Reduce the main stock of the product
            $production->product->decrement('stok', $qty);

            // Zero out qty in batch table
            $batch->update(['qty' => 0]);

            // Production status update is rejected
            $production->update([
                'status' => 'rejected',
            ]);

            $actor = $this->getCurrentActor();
            if ($actor) {
                ActivityLog::create([
                    'actor_id' => $actor->id,
                    'actor_type' => get_class($actor),
                    'type' => 'disposal_created',
                    'module' => 'disposal',
                    'description' => 'Membuat disposal untuk produk #' . $production->product->kode
                ]);
            }

            DB::commit();

            return back()->with('success', 'Sisa produk berhasil didisposal.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }
    }

    //🔥 Manual Disposal - Product (Batch)//
    public function disposeProductBatch(Request $request, ProductStock $stock)
    {
        $request->validate([
            'reason' => [
                'required',
                Rule::in([
                    'expired',
                    'qc_failed'
                ]),
            ],
            'notes' => 'nullable|string'
        ]);

        if ($stock->quantity <= 0) {
            return back()->withErrors([
                'stok' => 'Batch produk sudah habis atau sudah didisposal.',
            ]);
        }

        DB::beginTransaction();

        try {
            $qty = $stock->quantity;

            $notes = $request->notes;
            if ($request->reason === 'expired') {
                $notes = 'Otomatis dibuang karena batch produk ini sudah melewati masa kadaluarsa (Expired).';
            }

            // Simpan ke disposals
            $stock->disposals()->create([
                'quantity' => $qty,
                'reason' => $request->reason,
                'notes' => $notes,
                'created_by' => auth('admin')->id(),
            ]);

            // Reduce the main stock of the product
            $stock->product->decrement('stok', $qty);

            // Finish the batch
            $stock->update([
                'quantity' => 0
            ]);

            $actor = $this->getCurrentActor();
            if ($actor) {
                ActivityLog::create([
                    'actor_id' => $actor->id,
                    'actor_type' => get_class($actor),
                    'type' => 'disposal_created',
                    'module' => 'disposal',
                    'description' => 'Membuat disposal untuk produk ' . $stock->product->nama
                ]);
            }

            DB::commit();

            return back()->with('success', 'Batch produk berhasil didisposal.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }
    }
}