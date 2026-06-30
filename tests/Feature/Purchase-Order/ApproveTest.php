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

class ApproveTest extends TestCase
{
    use DatabaseTransactions;


    protected Admin $admin;
    protected Owner $owner;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();

        $this->owner = Owner::factory()->create();

        $this->supplier = Supplier::factory()->create();
    }

    private function createDraftPO(): PurchaseOrder
    {
        return PurchaseOrder::factory()->create([

            'supplier_id' => $this->supplier->id,

            'status' => 'draft',

            'ordered_by_id' => $this->owner->id,

            'ordered_by_type' => Owner::class,

            'recorded_by_id' => $this->admin->id,

            'recorded_by_type' => Admin::class,

        ]);
    }
    /** @test */
    public function owner_dapat_menyetujui_purchase_order()
    {
        $po = $this->createDraftPO();

        $response = $this
            ->actingAs($this->owner, 'owner')
            ->patch(route('owner.purchase-orders.approve', $po));

        $response->assertSessionHas('success');

        $po->refresh();

        $this->assertEquals('ordered', $po->status);

        $this->assertNotNull($po->approved_date);
    }

    /** @test */
    public function admin_tidak_boleh_menyetujui_purchase_order()
    {
        $po = $this->createDraftPO();

        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.purchase-orders.approve', $po))
            ->assertForbidden();
    }

    /** @test */
    public function purchase_order_yang_bukan_draft_tidak_bisa_diapprove()
    {
        $this->withoutExceptionHandling();

        $po = $this->createDraftPO();

        $po->update([
            'status' => 'ordered'
        ]);

        $this->expectException(\Exception::class);

        $this->expectExceptionMessage(
            'Purchase Order hanya bisa disetujui jika masih draft'
        );

        $this->actingAs($this->owner, 'owner')
            ->patch(route('owner.purchase-orders.approve', $po));
    }

    /** @test */
    public function activity_log_dibuat_saat_purchase_order_disetujui()
    {
        $po = $this->createDraftPO();

        $this->actingAs($this->owner, 'owner')
            ->patch(route('owner.purchase-orders.approve', $po));

        $this->assertDatabaseHas('activity_logs', [

            'type' => 'po_approved',

            'module' => 'purchase_order',

            'actor_id' => $this->owner->id,

            'actor_type' => Owner::class,

        ]);
    }

    /** @test */
    public function approved_date_otomatis_terisi()
    {
        $po = $this->createDraftPO();

        $this->actingAs($this->owner, 'owner')
            ->patch(route('owner.purchase-orders.approve', $po));

        $po->refresh();

        $this->assertNotNull($po->approved_date);
    }
}