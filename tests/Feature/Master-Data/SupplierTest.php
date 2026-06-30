<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierTest extends TestCase
{
    use DatabaseTransactions;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();

        $this->actingAs($this->admin, 'admin');
    }

    /** @test */
    public function supplier_dapat_dibuat()
    {
        $response = $this->post(
            route('admin.suppliers.store'),
            [
                'supplier_name' => 'PT Supplier Jaya',
                'contact' => '08123456789',
                'city' => 'Bandar Lampung',
            ]
        );

        $response->assertRedirect(
            route('admin.suppliers.index')
        );

        $this->assertDatabaseHas(
            'suppliers',
            [
                'supplier_name' => 'PT Supplier Jaya'
            ]
        );
    }

    /** @test */
    public function nama_supplier_wajib_diisi()
    {
        $response = $this->post(
            route('admin.suppliers.store'),
            [
                'supplier_name' => '',
            ]
        );

        $response->assertSessionHasErrors(
            'supplier_name'
        );
    }

    /** @test */
    public function supplier_dapat_diperbarui()
    {
        $supplier = Supplier::factory()->create([
            'supplier_name' => 'Supplier Lama'
        ]);

        $response = $this->put(
            route('admin.suppliers.update', $supplier),
            [
                'supplier_name' => 'Supplier Baru',
                'contact' => '08111111111',
            ]
        );

        $response->assertRedirect(
            route('admin.suppliers.index')
        );

        $this->assertDatabaseHas(
            'suppliers',
            [
                'id' => $supplier->id,
                'supplier_name' => 'Supplier Baru'
            ]
        );
    }

    /** @test */
    public function supplier_dapat_dihapus_jika_tidak_memiliki_purchase_order()
    {
        $supplier = Supplier::factory()->create();

        $response = $this->delete(
            route('admin.suppliers.destroy', $supplier)
        );

        $response->assertRedirect(
            route('admin.suppliers.index')
        );

        $this->assertDatabaseMissing(
            'suppliers',
            [
                'id' => $supplier->id
            ]
        );
    }

    /** @test */
    public function supplier_tidak_bisa_dihapus_jika_memiliki_purchase_order()
    {
        $supplier = Supplier::factory()->create();

        PurchaseOrder::create([
            'po_code' => 'PO-001',
            'supplier_id' => $supplier->id,
            'type' => 'material',
            'order_date' => now(),

            'ordered_by_id' => $this->admin->id,
            'ordered_by_type' => Admin::class,

            'recorded_by_id' => $this->admin->id,
            'recorded_by_type' => Admin::class,
        ]);

        $response = $this->delete(
            route('admin.suppliers.destroy', $supplier)
        );

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
        ]);
    }
}