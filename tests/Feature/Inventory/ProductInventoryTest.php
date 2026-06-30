<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductInventoryTest extends TestCase
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
    public function stok_produk_dapat_ditambahkan()
    {
        $product = Product::factory()->create([
            'stock' => 0
        ]);

        $response = $this->post(
            route('admin.inventory.product.adjust', $product),
            [
                'type' => 'in',
                'quantity' => 100,
                'expiration_date' => now()->addMonth()->toDateString(),
            ]
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'quantity' => 100,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 100,
        ]);
    }

    /** @test */
    public function expired_date_wajib_saat_stok_masuk()
    {
        $product = Product::factory()->create();

        $response = $this->from('/dummy')->post(
            route('admin.inventory.product.adjust', $product),
            [
                'type' => 'in',
                'quantity' => 50,
            ]
        );

        $response->assertSessionHasErrors('expiration_date');
    }

    /** @test */
    public function stok_produk_dapat_dikurangi()
    {
        $product = Product::factory()->create([
            'stock' => 100
        ]);

        ProductStock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 100,
            'received_date' => now()->subMonth(),
        ]);

        $response = $this->post(
            route('admin.inventory.product.adjust', $product),
            [
                'type' => 'out',
                'quantity' => 40,
            ]
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 60,
        ]);
    }

    /** @test */
    public function tidak_bisa_mengurangi_stok_melebihi_yang_tersedia()
    {
        $product = Product::factory()->create([
            'stock' => 10
        ]);

        ProductStock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        $response = $this->post(
            route('admin.inventory.product.adjust', $product),
            [
                'type' => 'out',
                'quantity' => 50,
            ]
        );

        $response->assertStatus(500);
    }

    /** @test */
    public function activity_log_dibuat_saat_stok_masuk()
    {
        $product = Product::factory()->create();

        $this->post(
            route('admin.inventory.product.adjust', $product),
            [
                'type' => 'in',
                'quantity' => 25,
                'expiration_date' => now()->addMonth()->toDateString(),
            ]
        );

        $this->assertDatabaseHas('activity_logs', [
            'type' => 'product_stock_in',
            'module' => 'product_inventory',
        ]);
    }

    /** @test */
    public function activity_log_dibuat_saat_stok_keluar()
    {
        $product = Product::factory()->create([
            'stock' => 100
        ]);

        ProductStock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 100,
        ]);

        $this->post(
            route('admin.inventory.product.adjust', $product),
            [
                'type' => 'out',
                'quantity' => 20,
            ]
        );

        $this->assertDatabaseHas('activity_logs', [
            'type' => 'product_stock_out',
            'module' => 'product_inventory',
        ]);
    }

    /** @test */
    public function stok_dapat_disinkronisasi()
    {
        $product = Product::factory()->create([
            'stock' => 0
        ]);

        ProductStock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 75,
        ]);

        $response = $this->post(
            route('admin.inventory.product.sync', $product)
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 75,
        ]);
    }

    /** @test */
    public function reorder_point_dapat_diperbarui()
    {
        $product = Product::factory()->create([
            'reorder_point' => 10
        ]);

        $response = $this->post(
            route('admin.inventory.product.update-rop', $product),
            [
                'reorder_point' => 50
            ]
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'reorder_point' => 50,
        ]);
    }

    /** @test */
    public function activity_log_dibuat_saat_update_rop()
    {
        $product = Product::factory()->create([
            'reorder_point' => 10
        ]);

        $this->post(
            route('admin.inventory.product.update-rop', $product),
            [
                'reorder_point' => 50
            ]
        );

        $this->assertDatabaseHas('activity_logs', [
            'type' => 'product_rop_updated',
            'module' => 'product_inventory',
        ]);
    }
}