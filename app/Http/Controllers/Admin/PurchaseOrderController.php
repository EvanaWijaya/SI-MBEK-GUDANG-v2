<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * 📄 List semua PO
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with([
            'supplier',
            'dipesanOleh',
            'dicatatOleh'
        ])->latest()->get();

        return view('admin.purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * ➕ Form buat PO
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $materials = Material::all();

        return view('admin.purchase-orders.create', compact('suppliers', 'materials'));
    }

    /**
     * 💾 Simpan PO + item
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal_pesan' => 'required|date',
            'items' => 'required|array',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {

            // 🔐 Tentukan siapa yang LOGIN (yang input/catat ke sistem)
            if (Auth::guard('admin')->check()) {
                $pencatat = Auth::guard('admin')->user();
                $guardPencatat = 'admin';
            } elseif (Auth::guard('owner')->check()) {
                $pencatat = Auth::guard('owner')->user();
                $guardPencatat = 'owner';
            } else {
                abort(401, 'Unauthorized');
            }

            // 🎯 Tentukan PO ini ATAS NAMA siapa (Admin bisa pilih, Owner otomatis)
            if ($guardPencatat === 'admin' && $request->filled('dipesan_oleh_type')) {
                // Admin bisa pilih: PO atas nama siapa?
                if ($request->dipesan_oleh_type === 'Owner') {
                    $pemesan = Owner::findOrFail($request->dipesan_oleh_id);
                } else {
                    $pemesan = $pencatat; // Admin pesan untuk dirinya sendiri
                }
            } else {
                // Owner login → otomatis atas nama owner sendiri
                $pemesan = $pencatat;
            }

            $kode_po = 'PO-' . date('Ymd') . '-' . rand(1000, 9999);

            $po = PurchaseOrder::create([
                'kode_po' => $kode_po,
                'supplier_id' => $request->supplier_id,
                'tanggal_pesan' => $request->tanggal_pesan,
                'status' => 'draft',

                // 📝 PO ini ATAS NAMA siapa (bisa Admin/Owner)
                'dipesan_oleh_id' => $pemesan->id,
                'dipesan_oleh_type' => get_class($pemesan),

                // ✍️ Siapa yang LOGIN/INPUT ke sistem (Admin/Owner yang login)
                'dicatat_oleh_id' => $pencatat->id,
                'dicatat_oleh_type' => get_class($pencatat),

                'catatan_owner' => $request->catatan_owner,
            ]);

            // Simpan items
            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'material_id' => $item['material_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['jumlah'] * $item['harga_satuan'],
                ]);
            }
        });

        $route = Auth::guard('owner')->check()
            ? 'owner.purchase-orders.index'
            : 'admin.purchase-orders.index';

        return redirect()->route($route)
            ->with('success', 'Purchase Order berhasil dibuat');
    }

    /**
     * 🔍 Detail PO
     */
    public function show($id)
    {
        $po = PurchaseOrder::with([
            'supplier',
            'items.material',
            'dipesanOleh',
            'dicatatOleh'
        ])->findOrFail($id);

        return view('admin.purchase-orders.show', compact('po'));
    }

    /**
     * ✅ Approve PO (Owner Only)
     */
    public function approve(PurchaseOrder $purchaseOrder)
    {
        // Pastikan yang approve adalah owner dan bukan admin
        if (Auth::guard('admin')->check()) {
            abort(403, 'Admin tidak boleh approve Purchase Order');
        }

        if (!Auth::guard('owner')->check()) {
            abort(403, 'Hanya owner yang bisa approve Purchase Order');
        }

        // Pastikan status masih draft
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Purchase Order hanya bisa disetujui jika masih berstatus draft');
        }

        $purchaseOrder->update([
            'status' => 'dipesan',
            'tanggal_disetujui' => now(),
        ]);

        return back()->with('success', 'Purchase Order berhasil disetujui');
    }

    /**
     * 📦 Barang datang → stok masuk (Admin Only)
     */
    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Load relasi yang dibutuhkan
        $purchaseOrder->load('items.material');

        // ⛔ Pastikan PO sudah approved
        if ($purchaseOrder->status !== 'dipesan') {
            return back()->with('error', 'Purchase Order belum disetujui atau sudah diterima');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:purchase_order_items,id',
            'items.*.jumlah_diterima' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request, $purchaseOrder) {

            foreach ($request->items as $data) {
                $item = PurchaseOrderItem::findOrFail($data['id']);

                // Update jumlah diterima
                $item->update([
                    'jumlah_diterima' => $data['jumlah_diterima']
                ]);

                // Update stok material
                $material = $item->material;
                $material->increment('stok', $data['jumlah_diterima']);
            }

            $purchaseOrder->update([
                'status' => 'diterima',
                'tanggal_diterima' => now(),
            ]);
        });

        return back()->with('success', 'Barang berhasil diterima dan stok telah diperbarui');
    }
}