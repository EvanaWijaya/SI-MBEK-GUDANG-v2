<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formula;
use App\Models\Material;
use App\Models\Production;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    /**
     * 1️⃣ Buat produksi (status: diproses)
     * - validasi formula & produk
     * - cek stok bahan baku
     * - kurangi stok bahan baku
     */
    public function store(Request $request)
    {
        $request->validate([
            'formula_id' => 'required|exists:formulas,id',
            'product_id' => 'required|exists:products,id',
            'qty_produksi' => 'required|numeric|min:1',
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
            // 🔍 CEK STOK BAHAN BAKU
            foreach ($formula->materials as $material) {
                $kebutuhan = $request->qty_produksi
                    * ($material->pivot->persentase / 100);

                if ($material->stok < $kebutuhan) {
                    throw new \Exception(
                        "Stok bahan {$material->nama_bahan} tidak mencukupi"
                    );
                }
            }

            // 📉 KURANGI STOK BAHAN BAKU
            foreach ($formula->materials as $material) {
                $kebutuhan = $request->qty_produksi
                    * ($material->pivot->persentase / 100);

                $material->decrement('stok', $kebutuhan);
            }

            // 🏭 SIMPAN DATA PRODUKSI
            Production::create([
                'formula_id' => $formula->id,
                'product_id' => $product->id,
                'qty_produksi' => $request->qty_produksi,
                'status' => 'diproses',
                'created_by' => auth('admin')->id(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Produksi berhasil dimulai');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'produksi' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 2️⃣ Input hasil QC
     * - hanya update data QC
     * - tidak mengubah stok apapun
     */
    public function qc(Request $request, Production $production)
    {
        if ($production->status !== 'diproses') {
            return back()->withErrors([
                'status' => 'Produksi tidak dalam status diproses',
            ]);
        }

        $request->validate([
            'qty_qc_lulus' => 'required|numeric|min:0',
            'qty_qc_gagal' => 'required|numeric|min:0',
        ]);

        if (
            ($request->qty_qc_lulus + $request->qty_qc_gagal)
            !== $production->qty_produksi
        ) {
            return back()->withErrors([
                'qc' => 'Total QC harus sama dengan jumlah produksi',
            ]);
        }

        $production->update([
            'qty_qc_lulus' => $request->qty_qc_lulus,
            'qty_qc_gagal' => $request->qty_qc_gagal,
        ]);

        return redirect()->back()
            ->with('success', 'QC berhasil disimpan');
    }

    /**
     * 3️⃣ Selesaikan produksi
     * - tambah stok produk jadi (hanya QC lulus)
     * - ubah status menjadi selesai
     */
    public function selesai(Production $production)
    {
        if ($production->status === 'selesai') {
            return back()->withErrors([
                'status' => 'Produksi sudah selesai',
            ]);
        }

        if (($production->qty_qc_lulus + $production->qty_qc_gagal) === 0) {
            return back()->withErrors([
                'qc' => 'QC belum diinput',
            ]);
        }

        DB::beginTransaction();

        try {
            // 📦 TAMBAH STOK PRODUK JADI
            $production->product->increment(
                'stok',
                $production->qty_qc_lulus
            );

            $production->update([
                'status' => 'selesai',
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Produksi selesai & stok produk bertambah');

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
