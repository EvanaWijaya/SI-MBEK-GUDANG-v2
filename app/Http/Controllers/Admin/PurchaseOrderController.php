<?php

namespace App\Http\Controllers\Admin;

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
     * List all purchase orders
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with([
            'supplier',
            'items',
            'orderedBy',
            'recordedBy'
        ])->latest()->get();

        return view('admin.purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * Display purchase order creation form
     */

    public function create()
    {
        $suppliers = Supplier::all();
        $materials = Material::all();
        $products = Product::where('category', 'obat')->get();

        return view('admin.purchase-orders.create', compact('suppliers', 'materials', 'products'));
    }

    /**
     * Store purchase order and items
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'type' => 'required|in:material,product',
            'order_date' => 'required|date',
            'ordered_by_type' => 'required|in:Admin,Owner',

            'items' => 'required|array|min:1',

            'items.*.material_id' => 'nullable|required_if:type,material|prohibited_if:type,product|exists:materials,id',
            'items.*.product_id' => 'nullable|required_if:type,product|prohibited_if:type,material|exists:products,id',

            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.subtotal' => 'nullable|numeric|min:0',
        ]);
        $purchaseOrder = DB::transaction(function () use ($request) {

            if (Auth::guard('admin')->check()) {
                $recordedBy = Auth::guard('admin')->user();
                $guardRecordedBy = 'admin';
            } elseif (Auth::guard('owner')->check()) {
                $recordedBy = Auth::guard('owner')->user();
                $guardRecordedBy = 'owner';
            } else {
                abort(401, 'Unauthorized');
            }

            if ($guardRecordedBy === 'admin') {

                if ($request->ordered_by_type === 'Owner') {

                    $orderedBy = Owner::first();

                    if (!$orderedBy) {
                        abort(422, 'Data owner tidak ditemukan');
                    }

                } else {

                    $orderedBy = $recordedBy;

                }
            } else {

                $orderedBy = $recordedBy;
            }

            $purchaseOrderCode = 'PO-' . date('Ymd') . '-' . rand(1000, 9999);

            $purchaseOrder = PurchaseOrder::create([
                'po_code' => $purchaseOrderCode,
                'supplier_id' => $request->supplier_id,
                'type' => $request->type,
                'order_date' => $request->order_date,
                'status' => 'draft',

                'ordered_by_id' => $orderedBy->id,
                'ordered_by_type' => get_class($orderedBy),

                'recorded_by_id' => $recordedBy->id,
                'recorded_by_type' => get_class($recordedBy),

                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'material_id' => $purchaseOrder->type === 'material'
                        ? $item['material_id']
                        : null,

                    'product_id' => $purchaseOrder->type === 'product'
                        ? $item['product_id']
                        : null,

                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'] ?? 0,
                    'subtotal' => $item['quantity'] * ($item['unit_price'] ?? 0),
                ]);
            }

            return $purchaseOrder; // 🔥 penting
        });

        $route = Auth::guard('admin')->check()
            ? 'admin.purchase-orders.index'
            : (Auth::guard('owner')->check()
                ? 'owner.purchase-orders.index'
                : abort(401));

        $actor = $this->getCurrentActor();

        if ($actor) {
            ActivityLog::create([
                'actor_id' => $actor->id,
                'actor_type' => get_class($actor),
                'type' => 'po_created',
                'module' => 'purchase_order',
                'description' => "Membuat Purchase Order #{$purchaseOrder->po_code}",
            ]);
        }

        return redirect()->route($route)
            ->with('success', 'Purchase Order berhasil dibuat');
    }


    /**
     * Display purchase order details
     */
    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with([
            'supplier',
            'items.material',
            'orderedBy',
            'recordedBy'
        ])->findOrFail($id);

        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Approve purchase order (Owner only)
     */
    public function approve(PurchaseOrder $purchaseOrder)
    {
        if (!Auth::guard('owner')->check()) {
            abort(403);
        }

        if ($purchaseOrder->status !== 'draft') {
            throw new \Exception(
                'Purchase Order hanya bisa disetujui jika masih draft'
            );
        }

        $purchaseOrder->update([
            'status' => 'ordered',
            'approved_date' => now(),
        ]);

        $actor = Auth::guard('owner')->user();

        ActivityLog::create([
            'actor_id' => $actor->id,
            'actor_type' => get_class($actor),
            'type' => 'po_approved',
            'module' => 'purchase_order',
            'description' => 'Owner Menyetujui Purchase Order #' .
                $purchaseOrder->po_code,
        ]);

        return back()->with(
            'success',
            'Purchase Order berhasil disetujui'
        );
    }

    /**
     * Receive purchased items and update stock (Admin only)
     */
    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Hanya admin yang bisa menerima barang');
        }

        if ($purchaseOrder->status === 'received') {
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
            'items.*.expiration_date' => 'required|date',
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

                $jumlahPesan = $item->quantity;
                $received_quantity = (int) $data['received_quantity'];

                $difference = $received_quantity - $jumlahPesan;

                // Update item (single receive only)
                $item->update([
                    'received_quantity' => $received_quantity,
                    'difference' => $difference,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Material Purchase Order
                |--------------------------------------------------------------------------
                */
                if ($purchaseOrder->type === 'material') {

                    $material = $item->material;

                    if (!$material) {
                        throw new \Exception('Material tidak ditemukan.');
                    }

                    MaterialStock::create([
                        'material_id' => $material->id,
                        'quantity' => $received_quantity,
                        'received_date' => now(),
                        'expiration_date' => $data['expiration_date'] ?? null,
                        'price_per_unit' => $item->unit_price,
                        'created_by' => auth('admin')->id(),
                    ]);

                    $material->stock = $material->materialStocks()->sum('quantity');
                    $material->save();

                    StockMovement::create([
                        'stockable_id' => $material->id,
                        'stockable_type' => Material::class,
                        'type' => 'in',
                        'quantity' => $received_quantity,
                        'source' => 'purchaseOrder',
                        'reference_id' => $purchaseOrder->id,
                        'note' => 'purchaseOrder ' . $purchaseOrder->po_code .
                            ($purchaseOrder->notes ? ' | ' . $purchaseOrder->notes : ''),
                        'movement_date' => now(),
                        'notes' => $purchaseOrder->notes,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Product PO (Only purchased products can be received through PO)
                |--------------------------------------------------------------------------
                */ elseif ($purchaseOrder->type === 'product') {

                    $product = $item->product;

                    if (!$product) {
                        throw new \Exception('Product tidak ditemukan.');
                    }

                    if ($product->category !== 'obat') {
                        throw new \Exception('Hanya produk obat yang boleh via PO.');
                    }

                    if ($product->source !== 'purchase') {
                        throw new \Exception('Produk ini bukan dari pembelian.');
                    }

                    \App\Models\ProductStock::create([
                        'product_id' => $product->id,
                        'quantity' => $received_quantity,
                        'received_date' => now(),
                        'expiration_date' => $data['expiration_date'] ?? null,
                        'source' => 'purchase',
                        'created_by' => auth('admin')->id(), // Pakai admin
                    ]);

                    $product->increment('stock', $received_quantity);

                    StockMovement::create([
                        'stockable_id' => $product->id,
                        'stockable_type' => Product::class,
                        'type' => 'in',
                        'quantity' => $received_quantity,
                        'source' => 'purchaseOrder',
                        'reference_id' => $purchaseOrder->id,
                        'movement_date' => now(),
                        'notes' => $purchaseOrder->notes,
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Update Purchase Order Status
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
                    'status' => 'received',
                    'received_date' => now(),
                ]);
            }
        });

        $purchaseOrder->refresh();

        if ($purchaseOrder->status === 'received') {

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