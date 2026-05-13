<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Production;
use App\Models\Disposal;
use App\Models\Product;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
   /**
     * 📦 1. Laporan Stok Masuk & Keluar
     */
    public function stock(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate   = $request->end_date   ?? now()->toDateString();

        // 🔥 FIX 1: TAMBAHKAN JAM BIAR TRANSAKSI HARI INI KEBACA
        $start = $startDate . ' 00:00:00';
        $end   = $endDate . ' 23:59:59';

        $query = StockMovement::with('stockable')
            ->whereBetween('movement_date', [$start, $end]);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $movements = $query->latest('movement_date')->paginate(20)->withQueryString();

        // Summary
        $summary = StockMovement::whereBetween('movement_date', [$start, $end])
            ->when($request->filled('source'), fn($q) => $q->where('source', $request->source))
            ->selectRaw('
                SUM(CASE WHEN type="in"  THEN quantity ELSE 0 END) as total_masuk,
                SUM(CASE WHEN type="out" THEN quantity ELSE 0 END) as total_keluar,
                COUNT(*) as total_transaksi
            ')
            ->first();

        // Chart data — per hari
        $chartData = StockMovement::whereBetween('movement_date', [$start, $end])
            ->when($request->filled('source'), fn($q) => $q->where('source', $request->source))
            ->selectRaw('
                DATE(movement_date) as tgl,
                SUM(CASE WHEN type="in"  THEN quantity ELSE 0 END) as masuk,
                SUM(CASE WHEN type="out" THEN quantity ELSE 0 END) as keluar
            ')
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        $sources = StockMovement::distinct()->pluck('source')->sort()->values();

        return view('owner.report.stock', compact(
            'movements', 'summary', 'chartData', 'sources',
            'startDate', 'endDate'
        ));
    }

    /**
     * 🏭 2. Laporan Produksi
     */
    public function production(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate   = $request->end_date   ?? now()->toDateString();

        // 🔥 FIX 1: TAMBAHKAN JAM BIAR TRANSAKSI HARI INI KEBACA
        $start = $startDate . ' 00:00:00';
        $end   = $endDate . ' 23:59:59';

        $query = Production::with(['product', 'formula', 'admin'])
            ->whereBetween('production_date', [$start, $end]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('qc_status')) {
            $query->where('qc_status', $request->qc_status);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $productions = $query->latest('production_date')->paginate(20)->withQueryString();

        // Summary
        $summary = Production::whereBetween('production_date', [$start, $end])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->selectRaw('
                COUNT(*) as total_batch,
                SUM(qty_produksi) as total_qty,
                SUM(CASE WHEN status="selesai" THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN status="diproses" THEN 1 ELSE 0 END) as diproses,
                SUM(CASE WHEN qc_status="layak" THEN 1 ELSE 0 END) as lulus_qc,        -- 🔥 FIX 2: UBAH LULUS JADI LAYAK
                SUM(CASE WHEN qc_status="tidak_layak" THEN 1 ELSE 0 END) as gagal_qc   -- 🔥 FIX 2: UBAH GAGAL JADI TIDAK_LAYAK
            ')
            ->first();

        // Chart — produksi per hari
        $chartData = Production::whereBetween('production_date', [$start, $end])
            ->selectRaw('
                DATE(production_date) as tgl,
                SUM(qty_produksi) as qty,
                SUM(CASE WHEN qc_status="layak" THEN 1 ELSE 0 END) as lulus,          -- 🔥 FIX 2: UBAH LULUS JADI LAYAK
                SUM(CASE WHEN qc_status="tidak_layak" THEN 1 ELSE 0 END) as gagal     -- 🔥 FIX 2: UBAH GAGAL JADI TIDAK_LAYAK
            ')
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        $products = Product::orderBy('nama')->get(['id', 'nama', 'type']);

        return view('owner.report.production', compact(
            'productions', 'summary', 'chartData', 'products',
            'startDate', 'endDate'
        ));
    }

    /**
     * 🗑 3. Laporan Disposal
     */
    public function disposal(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate   = $request->end_date   ?? now()->toDateString();

        $query = Disposal::with(['disposable', 'admin'])
            ->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59',
            ]);

        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        $disposals = $query->latest()->paginate(20)->withQueryString();

        // Summary
        $summary = Disposal::whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59',
            ])
            ->when($request->filled('reason'), fn($q) => $q->where('reason', $request->reason))
            ->selectRaw('
                COUNT(*) as total_disposal,
                SUM(quantity) as total_qty
            ')
            ->first();

        // Chart — disposal per hari
        $chartData = Disposal::whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59',
            ])
            ->selectRaw('DATE(created_at) as tgl, COUNT(*) as jumlah, SUM(quantity) as qty')
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        // Reason breakdown
        $reasonBreakdown = Disposal::whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59',
            ])
            ->selectRaw('reason, COUNT(*) as jumlah, SUM(quantity) as qty')
            ->groupBy('reason')
            ->get();

        $reasons = Disposal::distinct()->pluck('reason')->filter()->sort()->values();

        return view('owner.report.disposal', compact(
            'disposals', 'summary', 'chartData', 'reasonBreakdown', 'reasons',
            'startDate', 'endDate'
        ));
    }

    /**
     * 📊 4. Rekap Periodik (Bulanan & Tahunan)
     */
    public function monthly(Request $request)
    {
        $year = $request->year ?? now()->year;
        $mode = $request->mode ?? 'monthly'; // Default bulanan
        $availableYears = collect(range(now()->year - 4, now()->year))->reverse()->values();

        if ($mode === 'annual') {
            // ==========================================
            // LOGIKA TAHUNAN (5 Tahun Terakhir)
            // ==========================================
            $endYear = $year;
            $startYear = $endYear - 4; 

            $stockSummary = StockMovement::selectRaw('YEAR(movement_date) as tahun, SUM(CASE WHEN type="in" THEN quantity ELSE 0 END) as total_masuk, SUM(CASE WHEN type="out" THEN quantity ELSE 0 END) as total_keluar')
                ->whereBetween(DB::raw('YEAR(movement_date)'), [$startYear, $endYear])->groupBy(DB::raw('YEAR(movement_date)'))->get()->keyBy('tahun');

            $productionSummary = Production::selectRaw('YEAR(production_date) as tahun, COUNT(*) as total_batch, SUM(qty_produksi) as total_produksi')
                ->whereBetween(DB::raw('YEAR(production_date)'), [$startYear, $endYear])->groupBy(DB::raw('YEAR(production_date)'))->get()->keyBy('tahun');

            $disposalSummary = Disposal::selectRaw('YEAR(created_at) as tahun, COUNT(*) as total_disposal, SUM(quantity) as total_qty')
                ->whereBetween(DB::raw('YEAR(created_at)'), [$startYear, $endYear])->groupBy(DB::raw('YEAR(created_at)'))->get()->keyBy('tahun');

            $dataList = collect(range($startYear, $endYear))->map(fn($y) => [
                'label'          => (string)$y, // label tahun
                'stok_masuk'     => $stockSummary[$y]->total_masuk    ?? 0,
                'stok_keluar'    => $stockSummary[$y]->total_keluar   ?? 0,
                'total_produksi' => $productionSummary[$y]->total_produksi ?? 0,
                'total_batch'    => $productionSummary[$y]->total_batch    ?? 0,
                'total_disposal' => $disposalSummary[$y]->total_disposal   ?? 0,
            ])->reverse()->values(); // Dibalik biar tahun terbaru di atas

            $totals = [
                'stok_masuk'     => $dataList->sum('stok_masuk'),
                'stok_keluar'    => $dataList->sum('stok_keluar'),
                'total_produksi' => $dataList->sum('total_produksi'),
                'total_batch'    => $dataList->sum('total_batch'),
                'total_disposal' => $dataList->sum('total_disposal'),
            ];

            return view('admin.report.monthly', compact('mode', 'dataList', 'totals', 'year', 'availableYears', 'startYear', 'endYear'));

        } else {
            // ==========================================
            // LOGIKA BULANAN (12 Bulan di Tahun Tersebut)
            // ==========================================
            $stockSummary = StockMovement::selectRaw('MONTH(movement_date) as bulan, SUM(CASE WHEN type="in" THEN quantity ELSE 0 END) as total_masuk, SUM(CASE WHEN type="out" THEN quantity ELSE 0 END) as total_keluar')
                ->whereYear('movement_date', $year)->groupBy(DB::raw('MONTH(movement_date)'))->get()->keyBy('bulan');

            $productionSummary = Production::selectRaw('MONTH(production_date) as bulan, COUNT(*) as total_batch, SUM(qty_produksi) as total_produksi')
                ->whereYear('production_date', $year)->groupBy(DB::raw('MONTH(production_date)'))->get()->keyBy('bulan');

            $disposalSummary = Disposal::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total_disposal, SUM(quantity) as total_qty')
                ->whereYear('created_at', $year)->groupBy(DB::raw('MONTH(created_at)'))->get()->keyBy('bulan');

            $bulanNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            
            $dataList = collect(range(1, 12))->map(fn($m) => [
                'index'          => $m,
                'label'          => $bulanNames[$m - 1], // label bulan
                'stok_masuk'     => $stockSummary[$m]->total_masuk    ?? 0,
                'stok_keluar'    => $stockSummary[$m]->total_keluar   ?? 0,
                'total_produksi' => $productionSummary[$m]->total_produksi ?? 0,
                'total_batch'    => $productionSummary[$m]->total_batch    ?? 0,
                'total_disposal' => $disposalSummary[$m]->total_disposal   ?? 0,
            ]);

            $totals = [
                'stok_masuk'     => $dataList->sum('stok_masuk'),
                'stok_keluar'    => $dataList->sum('stok_keluar'),
                'total_produksi' => $dataList->sum('total_produksi'),
                'total_batch'    => $dataList->sum('total_batch'),
                'total_disposal' => $dataList->sum('total_disposal'),
            ];

            return view('admin.report.monthly', compact('mode', 'dataList', 'totals', 'year', 'availableYears'));
        }
    }
}
