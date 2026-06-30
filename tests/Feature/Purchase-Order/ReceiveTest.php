<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Owner;
use App\Models\Product;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\MaterialStock;
use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\ActivityLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReceiveTest extends TestCase
{
    use DatabaseTransactions;
    protected Admin $admin;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();

        $this->supplier = Supplier::factory()->create();
    }

    private function createMaterialPO(): PurchaseOrder
    {
        $material = Material::factory()->create([
            'stock' => 0,
        ]);

        $po = PurchaseOrder::factory()->create([

            'supplier_id' => $this->supplier->id,

            'type' => 'material',

            'status' => 'ordered',

        ]);

        PurchaseOrderItem::factory()->create([

            'purchase_order_id' => $po->id,

            'material_id' => $material->id,

            'quantity' => 10,

            'unit_price' => 5000,

        ]);

        return $po;
    }

    private function createProductPO(): PurchaseOrder
    {
        $product = Product::factory()->create([

            'category' => 'medicine',

            'source' => 'purchase',

            'stock' => 0,

        ]);

        $po = PurchaseOrder::factory()->create([

            'supplier_id' => $this->supplier->id,

            'type' => 'product',

            'status' => 'ordered',

        ]);

        PurchaseOrderItem::factory()->create([

            'purchase_order_id' => $po->id,

            'product_id' => $product->id,

            'quantity' => 15,

        ]);

        return $po;
    }

    /** @test */
    public function admin_dapat_menerima_material()
    {
        $po = $this->createMaterialPO();

        $item = $po->items()->first();

        $response = $this

            ->actingAs($this->admin, 'admin')

            ->post(

                route('admin.purchase-orders.receive', $po),

                [

                    'items' => [

                        [

                            'id' => $item->id,

                            'received_quantity' => 10,

                            'expiration_date' => now()->addYear()->toDateString(),

                        ]

                    ]

                ]

            );

        $response->assertSessionHas('success');

        $po->refresh();

        $this->assertEquals('received', $po->status);
    }

    /** @test */
    public function material_stock_dibuat_setelah_receive()
    {
        $po = $this->createMaterialPO();

        $item = $po->items()->first();

        $this

            ->actingAs($this->admin, 'admin')

            ->post(route('admin.purchase-orders.receive', $po), [

                'items' => [

                    [

                        'id' => $item->id,

                        'received_quantity' => 10,

                        'expiration_date' => now()->addYear()->toDateString(),

                    ]

                ]

            ]);

        $this->assertDatabaseHas('material_stocks', [

            'material_id' => $item->material_id,

            'quantity' => 10,

        ]);
    }

    /** @test */
    public function stok_material_bertambah()
    {
        $po = $this->createMaterialPO();

        $item = $po->items()->first();

        $material = $item->material;

        $this

            ->actingAs($this->admin, 'admin')

            ->post(route('admin.purchase-orders.receive', $po), [

                'items' => [

                    [

                        'id' => $item->id,

                        'received_quantity' => 10,

                        'expiration_date' => now()->addYear()->toDateString(),

                    ]

                ]

            ]);

        $material->refresh();

        $this->assertEquals(10, $material->stock);
    }

    /** @test */
    public function stock_movement_dibuat()
    {
        $po = $this->createMaterialPO();

        $item = $po->items()->first();

        $this

            ->actingAs($this->admin, 'admin')

            ->post(route('admin.purchase-orders.receive', $po), [

                'items' => [

                    [

                        'id' => $item->id,

                        'received_quantity' => 10,

                        'expiration_date' => now()->addYear()->toDateString(),

                    ]

                ]

            ]);

        $this->assertDatabaseHas('stock_movements', [

            'reference_id' => $po->id,

            'type' => 'in',

            'quantity' => 10,

        ]);
    }

    /** @test */
    public function activity_log_dibuat_saat_receive()
    {
        $po = $this->createMaterialPO();

        $item = $po->items()->first();

        $this

            ->actingAs($this->admin, 'admin')

            ->post(route('admin.purchase-orders.receive', $po), [

                'items' => [

                    [

                        'id' => $item->id,

                        'received_quantity' => 10,

                        'expiration_date' => now()->addYear()->toDateString(),

                    ]

                ]

            ]);

        $this->assertDatabaseHas('activity_logs', [

            'type' => 'po_received',

            'module' => 'purchase_order',

            'actor_id' => $this->admin->id,

            'actor_type' => Admin::class,

        ]);
    }
}