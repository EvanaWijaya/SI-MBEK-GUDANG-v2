<?php

namespace Tests\Feature\PurchaseOrder;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReceiveGoodsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_receive_purchase_order_and_stock_is_updated()
    {
        // 🧑‍💼 admin login
        $admin = Admin::factory()->create([
            'must_change_password' => false,
        ]);

        $this->actingAs($admin, 'admin');

        // 🏭 supplier
        $supplier = Supplier::factory()->create();

        // 📦 material
        $material = Material::factory()->create([
            'stok' => 0,
        ]);

        // 🧾 PO approved
        $purchaseOrder = PurchaseOrder::factory()->create([
            'supplier_id' => $supplier->id,
            'status' => 'dipesan',
        ]);

        // 📄 item PO
        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $purchaseOrder->id,
            'material_id' => $material->id,
            'jumlah' => 10,
            'harga_satuan' => 5000,
            'subtotal' => 50000,
        ]);

        // 🚚 receive barang (route name yang benar)
        $response = $this->put(
            route('admin.purchase-orders.receive', $purchaseOrder->id),
            [
                'items' => [
                    [
                        'id' => $item->id,
                        'jumlah_diterima' => 8, // lebih jelas dari 'subtotal'
                    ],
                ],
            ]
        );

        // 🔁 reload data
        $item->refresh();
        $material->refresh();
        $purchaseOrder->refresh();

        // ✅ assert
        $response->assertRedirect();
        // Sesuaikan assertion dengan logic di controller
        // Misalnya jika jumlah_diterima disimpan di kolom 'jumlah_diterima'
        $this->assertEquals(8, $item->jumlah_diterima);
        $this->assertEquals(8, $material->stok);
        $this->assertEquals('diterima', $purchaseOrder->status);
    }
}