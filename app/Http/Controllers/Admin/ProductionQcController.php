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
     * Store production QC result
     */
    public function store(Request $request, Production $production)
    {
        // Retrieve active QC indicators
        $indicators = QcIndicator::active()->orderByDesc('is_critical')->orderBy('name')->get();

        // Build dynamic validation rules
        $rules = [
            'threshold' => 'required|integer|min:70|max:90',
            'notes' => 'nullable|string',
        ];

        // Require result selection for every active indicator
        foreach ($indicators as $indicator) {
            $rules["indicators.{$indicator->id}"] = 'required|in:passed,failed';
        }

        // Execute request validation
        $validated = $request->validate($rules, [
            'threshold.required' => 'Ambang kelulusan wajib diisi.',
            'threshold.min' => 'Ambang kelulusan minimal 70%.',
            'threshold.max' => 'Ambang kelulusan maksimal 90%.',
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
                $isPassed = ($validated['indicators'][$indicator->id] ?? 'failed') === 'passed';

                // Critical indicator failure immediately fails QC
                if ($indicator->is_critical && !$isPassed) {
                    $failedCritical = true;
                    break;
                }

                // Calculate non-critical score
                if (!$indicator->is_critical) {
                    $totalNonCritical++;

                    if ($isPassed) {
                        $passedNonCritical++;
                    }
                }
            }

            // Determine final QC status
            if ($failedCritical) {
                $percentage = 0;
                $status = 'failed';
            } else {
                // Calculate and round percentage score
                $rawPercentage = $totalNonCritical > 0
                    ? ($passedNonCritical / $totalNonCritical) * 100
                    : 100;

                $percentage = round($rawPercentage, 2);

                $status = (floatval($percentage) >= floatval($threshold))
                    ? 'passed'
                    : 'failed';
            }

            // Store QC record
            $qc = ProductionQc::create([
                'production_id' => $production->id,
                'created_by' => auth('admin')->id(),
                'status' => $status,
                'percentage' => $percentage,
                'non_critical_score' => $percentage, // WAJIB isi untuk DB
                'threshold' => $threshold,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update production QC summary
            $production->update([
                'qc_status' => $status,
                'qc_percentage' => $percentage,
                'qc_threshold' => $threshold,
                'status' => $status === 'passed' ? 'progress' : 'rejected'
            ]);

            // Automatically create disposal for failed QC
            if ($status === 'failed') {
                $production->disposals()->create([
                    'quantity' => $production->production_quantity,
                    'reason' => 'qc_failed', // pastikan alasan ini sesuai dengan filter di laporan
                    'notes' => 'Otomatis dibuang karena tidak lolos Quality Control (Skor: ' . $percentage . '%).',
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
                    'description' => 'Melakukan QC untuk Prosedur Produksi dengan ID ' . $qc->production->id
                ]);
            }

            DB::commit();

            return back()->with(
                $status === 'passed' ? 'success' : 'warning',
                "QC selesai. Status: " . strtoupper($status)
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}