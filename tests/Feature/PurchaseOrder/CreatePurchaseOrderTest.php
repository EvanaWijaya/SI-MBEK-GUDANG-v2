<?php

namespace Tests\Feature\PurchaseOrder;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Owner;
use App\Models\Supplier;
use App\Models\Material;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreatePurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_purchase_order()
    {
        $admin = Admin::factory()->create([
            'must_change_password' => false,
        ]);

        $supplier = Supplier::factory()->create();
        $material = Material::factory()->create();

        $payload = [
            'supplier_id' => $supplier->id,
            'tanggal_pesan' => now()->format('Y-m-d'),
            'dipesan_oleh_type' => 'Admin',
            'items' => [
                [
                    'material_id' => $material->id,
                    'jumlah' => 10, // sesuaikan dengan nama kolom di database
                    'harga_satuan' => 5000, // sesuaikan dengan nama kolom
                ]
            ],
            'catatan_admin' => 'PO awal'
        ];

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.purchase-orders.store'), $payload);

        $response->assertStatus(302);

        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $supplier->id,
            'status' => 'draft',
            'dipesan_oleh_id' => $admin->id,
            'dicatat_oleh_id' => $admin->id,
        ]);

        $this->assertDatabaseHas('purchase_order_items', [
            'material_id' => $material->id,
            'jumlah' => 10, // sesuaikan dengan nama kolom
            'harga_satuan' => 5000, // sesuaikan dengan nama kolom
        ]);
    }

    public function test_owner_can_create_purchase_order()
    {
        $owner = Owner::factory()->create([
            'must_change_password' => false,
        ]);

        $supplier = Supplier::factory()->create();
        $material = Material::factory()->create();

        $payload = [
            'supplier_id' => $supplier->id,
            'tanggal_pesan' => now()->format('Y-m-d'),
            'dipesan_oleh_type' => 'Owner/CEO',
            'items' => [
                [
                    'material_id' => $material->id,
                    'jumlah' => 5, // sesuaikan dengan nama kolom
                    'harga_satuan' => 4500, // sesuaikan dengan nama kolom
                ]
            ],
            'catatan_owner' => 'PO dari owner'
        ];

        $response = $this->actingAs($owner, 'owner')
            ->post(route('owner.purchase-orders.store'), $payload);

        $response->assertStatus(302);

        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $supplier->id,
            'status' => 'draft',
            'dipesan_oleh_id' => $owner->id,
            'dicatat_oleh_id' => $owner->id,
        ]);
    }
}