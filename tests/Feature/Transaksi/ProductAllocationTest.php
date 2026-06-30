<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductAllocation;
use App\Models\ProductStock;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductAllocationTest extends TestCase
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
    public function alokasi_penjualan_dapat_dibuat()
    {
        $product = Product::factory()->create([
            'stock' => 100
        ]);

        $response = $this->post(
            route('admin.product.allocations.set', $product),
            [
                'type' => 'sale',
                'quantity' => 40,
            ]
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas(
            'product_allocations',
            [
                'product_id' => $product->id,
                'type' => 'sale',
                'quantity' => 40,
            ]
        );
    }

    /** @test */
    public function alokasi_internal_dapat_dibuat()
    {
        $product = Product::factory()->create([
            'stock' => 100
        ]);

        $this->post(
            route('admin.product.allocations.set', $product),
            [
                'type' => 'internal',
                'quantity' => 30,
            ]
        );

        $this->assertDatabaseHas(
            'product_allocations',
            [
                'type' => 'internal',
                'quantity' => 30,
            ]
        );
    }

    /** @test */
    public function total_alokasi_tidak_boleh_melebihi_stok()
    {
        $product = Product::factory()->create([
            'stock' => 100
        ]);

        ProductAllocation::create([
            'product_id' => $product->id,
            'type' => 'sale',
            'quantity' => 80,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->from('/')->post(
            route('admin.product.allocations.set', $product),
            [
                'type' => 'internal',
                'quantity' => 30,
            ]
        );

        $response->assertSessionHasErrors('quantity');
    }

    /** @test */
    public function alokasi_dapat_diupdate()
    {
        $product = Product::factory()->create([
            'stock' => 100
        ]);

        ProductAllocation::create([
            'product_id' => $product->id,
            'type' => 'sale',
            'quantity' => 10,
            'created_by' => $this->admin->id,
        ]);

        $this->post(
            route('admin.product.allocations.set', $product),
            [
                'type' => 'sale',
                'quantity' => 70,
            ]
        );

        $this->assertDatabaseHas(
            'product_allocations',
            [
                'quantity' => 70
            ]
        );
    }

    /** @test */
    public function pemakaian_internal_berhasil()
    {
        $product = Product::factory()->create([
            'stock' => 100
        ]);

        ProductStock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 100,
        ]);

        ProductAllocation::create([
            'product_id' => $product->id,
            'type' => 'internal',
            'quantity' => 50,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->post(
            route('admin.product.allocations.use-internal', $product),
            [
                'quantity' => 20
            ]
        );

        $response->assertSessionHas('success');

        $product->refresh();

        $this->assertEquals(80, $product->stock);

        $this->assertDatabaseHas(
            'product_allocations',
            [
                'quantity' => 30
            ]
        );
    }

    /** @test */
    public function tidak_bisa_memakai_internal_tanpa_alokasi()
    {
        $product = Product::factory()->create([
            'stock' => 100
        ]);

        $response = $this->from('/')->post(
            route('admin.product.allocations.use-internal', $product),
            [
                'quantity' => 10
            ]
        );

        $response->assertSessionHasErrors('quantity');
    }

    /** @test */
    public function qty_internal_tidak_boleh_melebihi_alokasi()
    {
        $product = Product::factory()->create([
            'stock' => 100
        ]);

        ProductAllocation::create([
            'product_id' => $product->id,
            'type' => 'internal',
            'quantity' => 20,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->from('/')->post(
            route('admin.product.allocations.use-internal', $product),
            [
                'quantity' => 30
            ]
        );

        $response->assertSessionHasErrors('quantity');
    }

    /** @test */
    public function penjualan_produk_berhasil()
    {
        $product = Product::factory()->create([
            'stock' => 100
        ]);

        ProductStock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 100
        ]);

        ProductAllocation::create([
            'product_id' => $product->id,
            'type' => 'sale',
            'quantity' => 60,
            'created_by' => $this->admin->id,
        ]);

        $this->post(
            route('admin.product.allocations.sell', $product),
            [
                'quantity' => 25
            ]
        );

        $product->refresh();

        $this->assertEquals(75, $product->stock);

        $this->assertDatabaseHas(
            'product_allocations',
            [
                'quantity' => 35
            ]
        );
    }

    /** @test */
    public function qty_penjualan_tidak_boleh_melebihi_alokasi()
    {
        $product = Product::factory()->create([
            'stock' => 100
        ]);

        ProductAllocation::create([
            'product_id' => $product->id,
            'type' => 'sale',
            'quantity' => 15,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->from('/')->post(
            route('admin.product.allocations.sell', $product),
            [
                'quantity' => 20
            ]
        );

        $response->assertSessionHasErrors('quantity');
    }

    /** @test */
    public function activity_log_dibuat_saat_membuat_alokasi()
    {
        $product = Product::factory()->create([
            'stock' => 100
        ]);

        $this->post(
            route('admin.product.allocations.set', $product),
            [
                'type' => 'sale',
                'quantity' => 40
            ]
        );

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'module' => 'product_allocation',
                'type' => 'sale'
            ]
        );
    }

    /** @test */
    public function activity_log_dibuat_saat_internal_usage()
    {
        $product = Product::factory()->create([
            'stock' => 100
        ]);

        ProductStock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 100
        ]);

        ProductAllocation::create([
            'product_id' => $product->id,
            'type' => 'internal',
            'quantity' => 50,
            'created_by' => $this->admin->id,
        ]);

        $this->post(
            route('admin.product.allocations.use-internal', $product),
            [
                'quantity' => 10
            ]
        );

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'module' => 'product_allocation',
                'type' => 'internal_usage'
            ]
        );
    }

    /** @test */
    public function activity_log_dibuat_saat_penjualan()
    {
        $product = Product::factory()->create([
            'stock' => 100
        ]);

        ProductStock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 100
        ]);

        ProductAllocation::create([
            'product_id' => $product->id,
            'type' => 'sale',
            'quantity' => 80,
            'created_by' => $this->admin->id,
        ]);

        $this->post(
            route('admin.product.allocations.sell', $product),
            [
                'quantity' => 10
            ]
        );

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'module' => 'product_allocation',
                'type' => 'sale'
            ]
        );
    }

    /** @test */
    public function muncul_warning_jika_stok_mencapai_rop_setelah_penjualan()
    {
        $product = Product::factory()->create([
            'stock' => 20,
            'reorder_point' => 15
        ]);

        ProductStock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 20
        ]);

        ProductAllocation::create([
            'product_id' => $product->id,
            'type' => 'sale',
            'quantity' => 20,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->post(
            route('admin.product.allocations.sell', $product),
            [
                'quantity' => 10
            ]
        );

        $response->assertSessionHas('warning');
    }

    /** @test */
    public function muncul_warning_jika_stok_mencapai_rop_setelah_internal_usage()
    {
        $product = Product::factory()->create([
            'stock' => 20,
            'reorder_point' => 15
        ]);

        ProductStock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 20
        ]);

        ProductAllocation::create([
            'product_id' => $product->id,
            'type' => 'internal',
            'quantity' => 20,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->post(
            route('admin.product.allocations.use-internal', $product),
            [
                'quantity' => 10
            ]
        );

        $response->assertSessionHas('warning');
    }
}