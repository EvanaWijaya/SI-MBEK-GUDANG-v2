<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Production;
use App\Models\ProductionQc;
use App\Models\QcIndicator;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionQcController extends Controller
{
    /**
     * Simpan hasil QC produksi
     */
    public function store(Request $request, Production $production)
    {
        // 1. 🔥 AMBIL INDIKATOR AKTIF DI AWAL 🔥
        $indicators = QcIndicator::active()->get();

        // 2. 🔥 BIKIN ATURAN VALIDASI DINAMIS 🔥
        $rules = [
            'threshold' => 'required|integer|min:70|max:90',
            'catatan' => 'nullable|string',
        ];

        // Paksa setiap ID indikator yang aktif wajib dipilih statusnya (tidak boleh kosong)
        foreach ($indicators as $indicator) {
            $rules["indicators.{$indicator->id}"] = 'required|in:lulus,gagal';
        }

        // 3. 🔥 JALANKAN VALIDASI DENGAN PESAN KUSTOM BAHASA INDONESIA 🔥
        $validated = $request->validate($rules, [
            'threshold.required' => 'Ambang kelulusan wajib diisi.',
            'threshold.min'      => 'Ambang kelulusan minimal 70%.',
            'threshold.max'      => 'Ambang kelulusan maksimal 90%.',
            'indicators.*.required' => 'Status kelulusan indikator QC wajib dipilih (Lulus/Gagal).',
        ]);

        $threshold = (int) $validated['threshold'];

        DB::beginTransaction();

        try {
            $failedCritical = false;
            $totalNonCritical = 0;
            $passedNonCritical = 0;

            foreach ($indicators as $indicator) {
                // Default jika tidak dikirim dianggap gagal
                $isPassed = ($validated['indicators'][$indicator->id] ?? 'gagal') === 'lulus';

                // 🔴 Jika indikator kritis gagal → langsung tidak layak
                if ($indicator->is_critical && !$isPassed) {
                    $failedCritical = true;
                    break;
                }

                // 🟡 Hitung non-kritis
                if (!$indicator->is_critical) {
                    $totalNonCritical++;

                    if ($isPassed) {
                        $passedNonCritical++;
                    }
                }
            }

            // Tentukan status akhir
            if ($failedCritical) {
                $percentage = 0;
                $status = 'tidak_layak';
            } else {
                // Hitung persentase dan bulatkan dulu sebelum dibandingkan
                $rawPercentage = $totalNonCritical > 0
                    ? ($passedNonCritical / $totalNonCritical) * 100
                    : 100;
                
                $percentage = round($rawPercentage, 2);

                // Bandingkan angka bulat dengan angka bulat untuk keamanan
                $status = (floatval($percentage) >= floatval($threshold)) 
                    ? 'layak' 
                    : 'tidak_layak';
            }

            // ✅ Simpan log QC (tambahkan score_non_kritis agar tidak error DB)
            $qc = ProductionQc::create([
                'production_id' => $production->id,
                'created_by' => auth('admin')->id(),
                'status' => $status,
                'percentage' => $percentage,
                'score_non_kritis' => $percentage, // WAJIB isi untuk DB
                'threshold' => $threshold,
                'catatan' => $validated['catatan'] ?? null,
            ]);

            // ✅ Update ringkasan di tabel productions
            $production->update([
                'qc_status' => $status,
                'qc_percentage' => $percentage,
                'qc_threshold' => $threshold,
                'status'        => $status === 'layak' ? 'diproses' : 'rejected'
            ]);

            // 🔥 AUTO DISPOSAL JIKA QC GAGAL DITAMBAHKAN DI SINI
            if ($status === 'tidak_layak') {
                $production->disposals()->create([
                    'quantity' => $production->qty_produksi,
                    'reason' => 'gagal_qc', // pastikan alasan ini sesuai dengan filter di laporan
                    'notes' => 'Otomatis dibuang karena tidak lolos Quality Control (Skor: '.$percentage.'%).',
                    'created_by' => auth('admin')->id(),
                ]);
            }

            $actor = $this->getCurrentActor();
            if ($actor) {
                ActivityLog::create([
                    'actor_id' => $actor->id,
                    'actor_type' => get_class($actor),
                    'type' => 'qc_checked',
                    'module' => 'production_qc',
                    'description' => 'Melakukan QC untuk Prosedur Produksi #' . $qc->production->id
                ]);
            }

            DB::commit();

            return back()->with(
                $status === 'layak' ? 'success' : 'warning',
                "QC selesai. Status: " . strtoupper($status)
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}