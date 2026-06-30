<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\QcIndicator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QcIndicatorController extends Controller
{
    /**
     * Tampilkan daftar semua indikator QC.
     */
    public function index()
    {
        $indicators = QcIndicator::orderByDesc('is_critical')
            ->orderBy('name')
            ->get();

        return view('owner.qc-indicators.index', compact('indicators'));
    }

    /**
     * Halaman detail + edit indikator.
     */
    public function show(QcIndicator $qcIndicator)
    {
        return view('owner.qc-indicators.show', compact('qcIndicator'));
    }

}