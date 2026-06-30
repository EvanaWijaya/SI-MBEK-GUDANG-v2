<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Owner;
use App\Models\Product;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\ActivityLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseOrderStoreTest extends TestCase
{
    use DatabaseTransactions;

    protected Admin $admin;
    protected Owner $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();

        $this->owner = Owner::factory()->create();
    }

    /**
     * ===================================================
     * HELPER
     * ===================================================
     */

    private function materialPayload(): array
    {
        $supplier = Supplier::factory()->create();

        $material = Material::factory()->create();

        return [

            'supplier_id' => $supplier->id,

            'type' => 'material',

            'order_date' => now()->toDateString(),

            'ordered_by_type' => 'Admin',

            'items' => [

                [

                    'material_id' => $material->id,

                    'quantity' => 10,

                    'unit_price' => 5000,

                ]

            ]

        ];
    }

    private function productPayload(): array
    {
        $supplier = Supplier::factory()->create();

        $product = Product::factory()->create([

            'category' => 'medicine'

        ]);

        return [

            'supplier_id' => $supplier->id,

            'type' => 'product',

            'order_date' => now()->toDateString(),

            'ordered_by_type' => 'Admin',

            'items' => [

                [

                    'product_id' => $product->id,

                    'quantity' => 20,

                    'unit_price' => 10000,

                ]

            ]

        ];
    }

    /**
     * ===================================================
     * STORE
     * ===================================================
     */

    /** @test */
    public function admin_dapat_membuat_purchase_order_material()
    {
        $payload = $this->materialPayload();

        $response = $this
            ->actingAs($this->admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $response->assertRedirect(
            route('admin.purchase-orders.index')
        );

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('purchase_orders', [

            'supplier_id' => $payload['supplier_id'],

            'type' => 'material',

            'status' => 'draft',

        ]);
    }

    /** @test */
    public function admin_dapat_membuat_purchase_order_product()
    {
        $payload = $this->productPayload();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $this->assertDatabaseHas('purchase_orders', [

            'type' => 'product',

            'status' => 'draft',

        ]);
    }

    /** @test */
    public function owner_dapat_membuat_purchase_order()
    {
        $payload = $this->materialPayload();

        $payload['ordered_by_type'] = 'Owner';

        $response = $this
            ->actingAs($this->owner, 'owner')
            ->post(route('owner.purchase-orders.store'), $payload);

        $response->assertRedirect(
            route('owner.purchase-orders.index')
        );
    
        $response->assertSessionHas('success');

        $po = PurchaseOrder::latest('id')->first();

        $this->assertEquals(

            $this->owner->id,

            $po->ordered_by_id

        );
    }

    /** @test */
    public function purchase_order_item_ikut_tersimpan()
    {
        $payload = $this->materialPayload();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $po = PurchaseOrder::latest('id')->first();

        $this->assertDatabaseHas('purchase_order_items', [

            'purchase_order_id' => $po->id,

            'quantity' => 10,

            'unit_price' => 5000,

        ]);
    }

    /** @test */
    public function subtotal_dihitung_otomatis()
    {
        $payload = $this->materialPayload();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $item = PurchaseOrderItem::first();

        $this->assertEquals(

            50000,

            $item->subtotal

        );
    }

    /** @test */
    public function activity_log_dibuat()
    {
        $payload = $this->materialPayload();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $this->assertDatabaseHas('activity_logs', [

            'type' => 'po_created',

            'module' => 'purchase_order',

        ]);
    }

    /**
     * ===================================================
     * VALIDATION
     * ===================================================
     */

    /** @test */
    public function supplier_wajib_diisi()
    {
        $payload = $this->materialPayload();

        unset($payload['supplier_id']);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $response->assertSessionHasErrors('supplier_id');
    }

    /** @test */
    public function type_wajib_diisi()
    {
        $payload = $this->materialPayload();

        unset($payload['type']);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $response->assertSessionHasErrors('type');
    }

    /** @test */
    public function order_date_wajib_diisi()
    {
        $payload = $this->materialPayload();

        unset($payload['order_date']);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $response->assertSessionHasErrors('order_date');
    }

    /** @test */
    public function ordered_by_type_wajib_diisi()
    {
        $payload = $this->materialPayload();

        unset($payload['ordered_by_type']);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $response->assertSessionHasErrors('ordered_by_type');
    }

    /** @test */
    public function items_wajib_diisi()
    {
        $payload = $this->materialPayload();

        $payload['items'] = [];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $response->assertSessionHasErrors('items');
    }

    /** @test */
    public function quantity_minimal_satu()
    {
        $payload = $this->materialPayload();

        $payload['items'][0]['quantity'] = 0;

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $response->assertSessionHasErrors('items.0.quantity');
    }

    /** @test */
    public function material_wajib_jika_type_material()
    {
        $payload = $this->materialPayload();

        unset($payload['items'][0]['material_id']);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $response->assertSessionHasErrors('items.0.material_id');
    }

    /** @test */
    public function product_wajib_jika_type_product()
    {
        $payload = $this->productPayload();

        unset($payload['items'][0]['product_id']);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $response->assertSessionHasErrors('items.0.product_id');
    }

    /**
     * ===================================================
     * BUSINESS RULE
     * ===================================================
     */

    /** @test */
    public function status_default_adalah_draft()
    {
        $payload = $this->materialPayload();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $this->assertEquals(

            'draft',

            PurchaseOrder::first()->status

        );
    }

    /** @test */
    public function po_code_berhasil_dibuat()
    {
        $payload = $this->materialPayload();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $this->assertStringStartsWith(

            'PO-',

            PurchaseOrder::first()->po_code

        );
    }

}