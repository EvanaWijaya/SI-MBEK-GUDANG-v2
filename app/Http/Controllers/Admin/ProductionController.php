<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formula;
use App\Models\Material;
use App\Models\MaterialStock;
use App\Models\StockMovement;
use App\Models\Production;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ActivityLog;
use App\Models\ProductionQc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    /**
     * Create new production process
     */

    public function store(Request $request)
    {
        $request->validate([
            'formula_id' => 'required|exists:formulas,id',
            'product_id' => 'required|exists:products,id',
            'production_quantity' => 'required|numeric|min:1',
        ]);

        $formula = Formula::with('materials')->findOrFail($request->formula_id);

        // 🔒 Pastikan produk milik formula tersebut
        $product = Product::where('id', $request->product_id)
            ->where('formula_id', $formula->id)
            ->first();

        if (!$product) {
            return back()->withErrors([
                'product' => 'Produk tidak sesuai dengan formula yang dipilih',
            ]);
        }

        DB::beginTransaction();

        try {
            // Validate material availability
            foreach ($formula->materials as $material) {
                $kebutuhan = $request->production_quantity
                    * ($material->pivot->percentage / 100);

                if ($material->stock < $kebutuhan) {
                    throw new \Exception(
                        "Stok bahan {$material->material_name} tidak mencukupi"
                    );
                }
            }

            // 🏭 SIMPAN DATA PRODUKSI
            $production = Production::create([
                'formula_id' => $formula->id,
                'product_id' => $product->id,
                'production_quantity' => $request->production_quantity,
                'production_date' => now(),
                'status' => 'progress',
                'created_by' => auth('admin')->id(),
            ]);

            // Deduct material stock using FIFO method
            foreach ($formula->materials as $material) {

                $kebutuhan = $request->production_quantity
                    * ($material->pivot->percentage / 100);

                $remainingQty = $kebutuhan;

                // Ambil batch paling lama dulu (FIFO)
                /** @var \Illuminate\Database\Eloquent\Collection<int, MaterialStock> $batches */
                $batches = MaterialStock::where('material_id', $material->id)
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
                        // Current batch is sufficient
                        $batch->decrement('quantity', $remainingQty);
                        $remainingQty = 0;
                    } else {
                        // Consume entire batch and continue
                        $remainingQty -= $batch->quantity;
                        $batch->update(['quantity' => 0]);
                    }
                }

                if ($remainingQty > 0) {
                    throw new \Exception(
                        "Batch bahan {$material->material_name} tidak mencukupi"
                    );
                }

                // Synchronize material stock summary
                $material->stock = $material->materialStocks()->sum('quantity');
                $material->save();

                StockMovement::create([
                    'stockable_id' => $material->id,
                    'stockable_type' => Material::class,
                    'type' => 'out',
                    'quantity' => $kebutuhan,
                    'source' => 'production',
                    'notes' => $production->latestQc->notes ?? null,
                    'reference_id' => $production->id,
                    'movement_date' => now(),
                ]);
            }

            $actor = $this->getCurrentActor();

            if ($actor) {
                ActivityLog::create([
                    'actor_id' => $actor->id,
                    'actor_type' => get_class($actor),
                    'type' => 'production_created',
                    'module' => 'production',
                    'description' => 'Membuat Produksi #' . $production->id
                ]);
            }


            DB::commit();

            return redirect()->route('admin.productions.index')
                ->with('success', 'Proses produksi berhasil dimulai. Silakan lanjutkan ke tahap QC.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'produksi' => $e->getMessage(),
            ]);
        }
    }

    public function qc(Request $request, Production $production)
    {
        if ($production->status !== 'progress') {
            return back()->withErrors([
                'status' => 'Produksi tidak dalam status diproses',
            ]);
        }

        $request->validate([
            'indicators' => 'required|array',
            'qc_threshold' => 'required|numeric|min:70|max:90',
        ]);

        $totalNonCritical = 0;
        $lulusNonCritical = 0;
        $status = 'passed';

        foreach ($request->indicators as $indicatorId => $result) {
            $indicator = \App\Models\QcIndicator::findOrFail($indicatorId);

            // Jika indikator critical gagal → langsung tidak layak
            if ($indicator->is_critical && $result === 'failed') {
                $status = 'failed';
            }

            // Hitung non critical untuk persentase
            if (!$indicator->is_critical) {
                $totalNonCritical++;

                if ($result === 'passed') {
                    $lulusNonCritical++;
                }
            }
        }

        $percentage = $totalNonCritical > 0
            ? ($lulusNonCritical / $totalNonCritical) * 100
            : 100;

        // Jika persentase di bawah threshold → tidak layak
        if ($percentage < $request->qc_threshold) {
            $status = 'failed';
        }

        DB::transaction(function () use ($production, $status, $percentage, $request) {

            // Update production QC result
            $production->update([
                'qc_status' => $status,
                'qc_percentage' => $percentage,
                'qc_threshold' => $request->qc_threshold,
                'status' => $status === 'passed' ? 'completed' : 'rejected',
            ]);

            // Store QC record
            ProductionQc::create([
                'production_id' => $production->id,
                'status' => $status,
                'non_critical_score' => $percentage,
                'threshold' => $request->qc_threshold,
                'notes' => $request->catatan ?? null,
                'created_by' => auth('admin')->id(),
            ]);

            // Automatically create disposal for failed QC
            if ($status === 'failed') {

                $production->disposals()->create([
                    'quantity' => $production->production_quantity,
                    'reason' => 'qc_failed',
                    'notes' => 'Otomatis dibuang karena tidak lolos Quality Control',
                    'created_by' => auth('admin')->id(),
                ]);

                // Mark production as rejected
                $production->update([
                    'status' => 'rejected',
                ]);
            }
        });


        return redirect()->back()
            ->with('success', 'QC berhasil disimpan');
    }

    public function selesai(Request $request, Production $production)
    {
        // Validate expiration date
        $request->validate([
            'expiration_date' => 'required|date|after:today',
        ]);

        if ($production->status === 'completed') {
            return back()->withErrors([
                'status' => 'Produksi sudah selesai',
            ]);
        }

        if ($production->status === 'rejected') {
            return back()->withErrors([
                'status' => 'Produksi sudah ditolak dan tidak bisa diselesaikan',
            ]);
        }

        if (!$production->qc_status) {
            return back()->withErrors([
                'qc' => 'QC belum dilakukan',
            ]);
        }

        if ($production->qc_status !== 'passed') {
            return back()->withErrors([
                'qc' => 'Produksi tidak layak untuk diselesaikan',
            ]);
        }

        DB::beginTransaction();

        try {

            // Update production expiration date
            $production->update([
                'status' => 'completed',
                'expiration_date' => $request->expiration_date,
            ]);

            // Increase product stock summary
            $production->product->increment(
                'stock',
                $production->production_quantity
            );

            // Create product stock batch
            ProductStock::create([
                'product_id' => $production->product_id,
                'quantity' => $production->production_quantity,
                'source' => 'production',
                'reference_id' => $production->id,
                'received_date' => $production->production_date ?? now(),
                'expiration_date' => $request->expiration_date,
            ]);

            // Record stock movement
            StockMovement::create([
                'stockable_id' => $production->product->id,
                'stockable_type' => Product::class,
                'type' => 'in',
                'quantity' => $production->production_quantity,
                'source' => 'production',
                'notes' => $production->latestQc->notes ?? null,
                'reference_id' => $production->id,
                'movement_date' => now(),
            ]);

            // Mark production as completed
            $production->update([
                'status' => 'completed',
            ]);

            DB::commit();

            $actor = $this->getCurrentActor();

            if ($actor) {
                ActivityLog::create([
                    'actor_id' => $actor->id,
                    'actor_type' => get_class($actor),
                    'type' => 'production_finished',
                    'module' => 'production',
                    'description' => 'Menyelesaikan Produksi dengan ID' . $production->id
                ]);
            }

            return back()->with('success', 'Produksi selesai & stok produk bertambah');

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function index()
    {
        $productions = Production::with(['product', 'formula'])
            ->latest()
            ->get();

        return view('admin.production.index', compact('productions'));
    }

    // Display production creation form
    public function create()
    {
        $formulas = Formula::where('is_active', true)->get();

        // Map formula_id → list produk (untuk JS dynamic dropdown)
        $formulaProducts = [];
        $formulaMaterials = [];

        foreach ($formulas as $formula) {
            // Produk yang punya formula_id ini
            $formulaProducts[$formula->id] = Product::where('formula_id', $formula->id)
                ->select(
                    'id',
                    'product_code',
                    'product_name'
                )
                ->get()
                ->toArray();

            // Komposisi bahan untuk preview
            $formulaMaterials[$formula->id] = $formula->materials
                ->map(fn($m) => [
                    'material_name' => $m->material_name,
                    'unit' => $m->unit,
                    'percentage' => $m->pivot->percentage,
                ])
                ->toArray();
        }

        return view('admin.production.create', compact(
            'formulas',
            'formulaProducts',
            'formulaMaterials'
        ));
    }

    public function show(Production $production)
    {
        // Eager load production relations and material composition
        $production->load([
            'product',
            'formula.materials',
        ]);

        $qcIndicators = \App\Models\QcIndicator::active()
            ->orderBy('is_critical', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.production.show', compact('production', 'qcIndicators'));
    }

}

