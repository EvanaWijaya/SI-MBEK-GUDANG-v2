<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Kambing;
use App\Models\Domba;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display owner dashboard with read-only data
     */
    public function index()
    {
        $owner = auth('owner')->user();

        // Statistik umum
        $totalKambing = Kambing::count();
        $totalDomba = Domba::count();
        $kambingDijual = Kambing::where('status', 'dijual')->count();
        $dombaDijual = Domba::where('status', 'dijual')->count();
        
        // Order statistics
        $totalOrders = Order::count();
        $pendingOrders = Order::where('order_status', 'pending')->count();
        $completedOrders = Order::where('order_status', 'success')->count();
        
        // Revenue (if needed)
        $totalRevenue = Order::where('order_status', 'success')->sum('gross_amount');

        return view('owner.dashboard', compact(
            'owner',
            'totalKambing',
            'totalDomba',
            'kambingDijual',
            'dombaDijual',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'totalRevenue'
        ));
    }

    /**
     * Display penjualan data (read-only)
     */
    public function penjualan()
    {
        $orders = Order::with(['user', 'kambing', 'domba'])
            ->latest()
            ->paginate(20);

        return view('owner.penjualan', compact('orders'));
    }

    /**
     * Kambing report
     */
    public function kambingReport()
    {
        $kambings = Kambing::with(['user', 'histories'])
            ->latest()
            ->get();

        $stats = [
            'total' => $kambings->count(),
            'dijual' => $kambings->where('status', 'dijual')->count(),
            'terjual' => $kambings->where('status', 'terjual')->count(),
            'titipan' => $kambings->where('status', 'titipan')->count(),
        ];

        return view('owner.reports.kambing', compact('kambings', 'stats'));
    }

    /**
     * Domba report
     */
    public function dombaReport()
    {
        $dombas = Domba::with(['user', 'histories'])
            ->latest()
            ->get();

        $stats = [
            'total' => $dombas->count(),
            'dijual' => $dombas->where('status', 'dijual')->count(),
            'terjual' => $dombas->where('status', 'terjual')->count(),
            'titipan' => $dombas->where('status', 'titipan')->count(),
        ];

        return view('owner.reports.domba', compact('dombas', 'stats'));
    }

    /**
     * Penjualan report
     */
    public function penjualanReport()
    {
        $orders = Order::with(['user', 'kambing', 'domba'])
            ->whereIn('order_status', ['settlement', 'success'])
            ->latest()
            ->get();

        $stats = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('gross_amount'),
            'average_order' => $orders->avg('gross_amount'),
            'kambing_sold' => $orders->whereNotNull('kambing_id')->count(),
            'domba_sold' => $orders->whereNotNull('domba_id')->count(),
        ];

        return view('owner.reports.penjualan', compact('orders', 'stats'));
    }
}