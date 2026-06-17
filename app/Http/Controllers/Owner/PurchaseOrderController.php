<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Material;
use App\Models\Product;
use App\Models\MaterialStock;
use App\Models\Supplier;
use App\Models\Owner;
use App\Models\ActivityLog;
use App\Models\StockMovement;
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
            'orderedBy',
            'recordedBy'
        ])->latest()->get();

        return view('owner.purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * ➕ Form buat PO
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $materials = Material::all();
        $products = Product::where('type', 'obat')->get();

        return view('owner.purchase-orders.create', compact('suppliers', 'materials', 'products'));
    }

    /**
     * 💾 Simpan PO + item
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'type' => 'required|in:material,product',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'nullable|required_if:type,material|exists:materials,id',
            'items.*.product_id' => 'nullable|required_if:type,product|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        $po = DB::transaction(function () use ($request) {

            // 🔥 Langsung ambil data Owner yang lagi login
            $owner = Auth::guard('owner')->user();

            $po_code = 'PO-' . date('Ymd') . '-' . rand(1000, 9999);

            $po = PurchaseOrder::create([
                'po_code' => $po_code,
                'supplier_id' => $request->supplier_id,
                'type' => $request->type,
                'order_date' => $request->order_date,
                'status' => 'draft',
                'ordered_by_id' => $owner->id,
                'ordered_by_type' => get_class($owner),
                'recorded_by_id' => $owner->id,
                'recorded_by_type' => get_class($owner),
                'notes' => $request->notes_owner,
            ]);

            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'material_id' => $po->type === 'material' ? ($item['material_id'] ?? null) : null,
                    'product_id' => $po->type === 'product' ? ($item['product_id'] ?? null) : null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'] ?? 0,
                    'subtotal' => $item['quantity'] * ($item['unit_price'] ?? 0),
                ]);
            }

            return $po;
        });

        // Catat Log Aktivitas
        $actor = Auth::guard('owner')->user();
        if ($actor) {
            ActivityLog::create([
                'actor_id' => $actor->id,
                'actor_type' => get_class($actor),
                'type' => 'po_created',
                'module' => 'purchase_order',
                'description' => 'Membuat Purchase Order #' . $po->kode_po
            ]);
        }

        return redirect()->route('owner.purchase-orders.index')
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
            'orderedBy',
            'recordedBy'
        ])->findOrFail($id);

        return view('owner.purchase-orders.show', compact('po'));
    }

    /**
     * ✅ Approve PO (Owner Only)
     */
    public function approve(PurchaseOrder $purchaseOrder)
    {
        if (!Auth::guard('owner')->check()) {
            abort(403);
        }


        if ($purchaseOrder->status !== 'draft') {
            throw new \Exception('Purchase Order hanya bisa disetujui jika masih draft');
        }

        $purchaseOrder->update([
            'status' => 'ordered',
            'order_date' => now(),
        ]);

        // 🔥 Tambahkan ini
        $actor = Auth::guard('owner')->user();

        ActivityLog::create([
            'actor_id' => $actor->id,
            'actor_type' => get_class($actor),
            'type' => 'po_approved',
            'module' => 'purchase_order',
            'description' => 'Owner Menyetujui Purchase Order #' . $purchaseOrder->kode_po
        ]);

        return back()->with('success', 'Purchase Order berhasil disetujui');
    }

    /**
     * 📦 Barang datang → stok masuk (Admin Only)
     */
    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Hanya admin yang bisa menerima barang');
        }

        if ($purchaseOrder->status === 'recivied') {
            return back()->with('error', 'Purchase Order sudah selesai.');
        }

        if ($purchaseOrder->status !== 'ordered') {
            return back()->with('error', 'Purchase Order belum disetujui');
        }

        if (empty($request->items)) {
            return back()->with('error', 'Items cannot be empty');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:purchase_order_items,id',
            'items.*.received_quantity' => 'required|integer|min:1',
            'items.*.expiration_date' => 'nullable|date',
        ]);

        foreach ($request->items as $data) {

            $item = PurchaseOrderItem::where('id', $data['id'])
                ->where('purchase_order_id', $purchaseOrder->id)
                ->first();

            if (!$item) {
                return back()->with('error', 'Item tidak ditemukan.');
            }

            if (!empty($item->received_quantity)) {
                return back()->with('error', 'Item sudah pernah diterima.');
            }
        }

        DB::transaction(function () use ($request, $purchaseOrder) {

            foreach ($request->items as $data) {

                $item = PurchaseOrderItem::where('id', $data['id'])
                    ->where('purchase_order_id', $purchaseOrder->id)
                    ->firstOrFail();

                $orderQuantity = $item->quantity;
                $recivedQuantity = (int) $data['received_quantity'];

                $difference = $recivedQuantity - $orderQuantity;

                // Update item (single receive only)
                $item->update([
                    'received_quantity' => $recivedQuantity,
                    'difference' => $difference,
                ]);

                /*
                |--------------------------------------------------------------------------
                | PO TYPE: MATERIAL
                |--------------------------------------------------------------------------
                */
                if ($purchaseOrder->type === 'material') {

                    $material = $item->material;

                    if (!$material) {
                        throw new \Exception('Material tidak ditemukan.');
                    }

                    MaterialStock::create([
                        'material_id' => $material->id,
                        'quantity' => $recivedQuantity,
                        'received_date' => now(),
                        'expiration_date' => $data['expired_date'] ?? null,
                        'created_by' => auth('admin')->id(),
                    ]);

                    $material->stock = $material->materialStocks()->sum('quantity');
                    $material->save();

                    StockMovement::create([
                        'stockable_id' => $material->id,
                        'stockable_type' => Material::class,
                        'type' => 'in',
                        'quantity' => $recivedQuantity,
                        'source' => 'PO',
                        'reference_id' => $purchaseOrder->id,
                        'note' => 'PO ' . $purchaseOrder->po_code .
                            ($purchaseOrder->notes ? ' | ' . $purchaseOrder->notes : ''),
                        'movement_date' => now(),
                        'notes' => $purchaseOrder->notes,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | PO TYPE: PRODUCT (HANYA OBAT & SOURCE PEMBELIAN)
                |--------------------------------------------------------------------------
                */ elseif ($purchaseOrder->type === 'product') {

                    $product = $item->product;

                    if (!$product) {
                        throw new \Exception('Product tidak ditemukan.');
                    }

                    if ($product->type !== 'obat') {
                        throw new \Exception('Hanya produk obat yang boleh via PO.');
                    }

                    if ($product->source !== 'purchase') {
                        throw new \Exception('Produk ini bukan dari pembelian.');
                    }

                    \App\Models\ProductStock::create([
                        'product_id' => $product->id,
                        'quantity' => $recivedQuantity,
                        'received_date' => now(),
                        'expired_date' => $data['expired_date'] ?? null,
                        'source' => 'purchase', // Biar labelnya muncul "PO" warna ungu
                        'created_by' => auth('owner')->id(), // 🔥 Pastikan ini pakai 'owner'
                    ]);

                    $product->increment('stok', $recivedQuantity);

                    StockMovement::create([
                        'stockable_id' => $product->id,
                        'stockable_type' => Product::class,
                        'type' => 'in',
                        'quantity' => $recivedQuantity,
                        'source' => 'PO',
                        'reference_id' => $purchaseOrder->id,
                        'movement_date' => now(),
                        'notes' => $purchaseOrder->notes,
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Update Status PO
            |--------------------------------------------------------------------------
            */

            $allReceived = $purchaseOrder->items()
                ->where(function ($q) {
                    $q->whereNull('received_quantity')
                        ->orWhere('received_quantity', '<=', 0);
                })
                ->count() === 0;

            if ($allReceived) {
                $purchaseOrder->update([
                    'status' => 'recived',
                    'recivied_date' => now(),
                ]);
            }
        });

        $purchaseOrder->refresh();

        if ($purchaseOrder->status === 'recived') {

            $actor = $this->getCurrentActor();

            if ($actor) {
                ActivityLog::create([
                    'actor_id' => $actor->id,
                    'actor_type' => get_class($actor),
                    'type' => 'po_received',
                    'module' => 'purchase_order',
                    'description' => 'Menerima Purchase Order #' . $purchaseOrder->po_code
                ]);
            }
        }

        return back()->with('success', 'Barang berhasil diterima dan stok telah diperbarui');
    }
}