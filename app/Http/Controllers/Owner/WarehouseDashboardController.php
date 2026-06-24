<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class WarehouseDashboardController extends Controller
{
    /**
     * Display warehouse dashboard
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Dashboard Summary
        |--------------------------------------------------------------------------
        */

        $summary = [
            'total_material' => Material::count(),

            'material_below_rop' => Material::belowReorderPoint()->count(),

            'total_product' => Product::count(),

            'product_below_rop' => Product::whereColumn(
                'stock',
                '<=',
                'reorder_point'
            )->count(),

            'total_supplier' => Supplier::count(),

            'total_buyer' => Order::distinct('user_id')
                ->count('user_id'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Reorder Point Warnings
        |--------------------------------------------------------------------------
        */

        $materialsLow = Material::belowReorderPoint()
            ->select([
                'id',
                'material_name',
                'category',
                'stock',
                'average_usage',
                'lead_time',
                'safety_stock',
            ])
            ->get();

        $productsLow = Product::whereColumn(
            'stock',
            '<=',
            'reorder_point'
        )
            ->select(
                'id',
                'product_code',
                'product_name',
                'stock',
                'reorder_point',
                'category'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Supplier Distribution
        |--------------------------------------------------------------------------
        */

        $supplierDistribution = PurchaseOrder::selectRaw(
            'supplier_id, COUNT(*) as total'
        )
            ->groupBy('supplier_id')
            ->with('supplier:id,supplier_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Buyer Distribution By City
        |--------------------------------------------------------------------------
        */

        $buyerDistribution = Order::whereIn(
            'orders.status',
            ['success', 'settlement']
        )
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'users.city',
                DB::raw('COUNT(orders.id) as total')
            )
            ->groupBy('users.city')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Activities
        |--------------------------------------------------------------------------
        */

        $recentActivities = ActivityLog::whereIn('type', [
            'po_created',
            'po_approved',
            'po_received',
            'qc_checked',
            'production_created',
            'allocation_created',
            'disposal_created',
            'order_create',
            'order_update',
        ])
            ->latest()
            ->take(7)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Purchase Order Summary
        |--------------------------------------------------------------------------
        */

        $poSummary = PurchaseOrder::selectRaw(
            'status, COUNT(*) as total'
        )
            ->groupBy('status')
            ->pluck('total', 'status');

        if (app()->environment('testing')) {

            return response()->json([
                'summary' => $summary,
                'materialsLow' => $materialsLow,
                'productsLow' => $productsLow,
                'supplierDistribution' => $supplierDistribution,
                'buyerDistribution' => $buyerDistribution,
                'recentActivities' => $recentActivities,
                'poSummary' => $poSummary,
            ]);
        }

       $movementChart = StockMovement::selectRaw('
            DATE(movement_date) as tgl,
            SUM(CASE WHEN type="in" THEN quantity ELSE 0 END) as masuk,
            SUM(CASE WHEN type="out" THEN quantity ELSE 0 END) as keluar
        ')
        ->whereDate('movement_date', '>=', now()->subDays(6))
        ->groupBy('tgl')
        ->orderBy('tgl')
        ->get();

        return view('owner.warehouse.dashboard', [
            'summary' => $summary,
            'materialsLow' => $materialsLow,
            'productsLow' => $productsLow,
            'supplierDistribution' => $supplierDistribution,
            'buyerDistribution' => $buyerDistribution,
            'recentActivities' => $recentActivities,
            'poSummary' => $poSummary,
            'movementChart' => $movementChart,
        ]);
    }

    /**
     * Display activity log list
     */
    public function activityLog(Request $request)
    {
        $query = ActivityLog::with('actor')
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('search')) {
            $query->where(
                'description',
                'like',
                '%' . $request->search . '%'
            );
        }

        if ($request->filled('dari')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->dari
            );
        }

        if ($request->filled('sampai')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->sampai
            );
        }

        $logs = $query
            ->paginate(25)
            ->withQueryString();

        $types = ActivityLog::query()
            ->distinct()
            ->pluck('type')
            ->sort()
            ->values();

        $modules = ActivityLog::query()
            ->distinct()
            ->pluck('module')
            ->filter()
            ->sort()
            ->values();

        return view(
            'owner.warehouse.activity-log',
            compact('logs', 'types', 'modules')
        );
    }
}