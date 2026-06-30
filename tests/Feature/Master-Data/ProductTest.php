<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Formula;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductTest extends TestCase
{
    use DatabaseTransactions;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();

        $this->actingAs(
            $this->admin,
            'admin'
        );
    }

    /** @test */
    public function produk_dapat_dibuat()
    {
        $formula = Formula::factory()->create([
            'is_active' => true
        ]);

        $response = $this->post(
            route('admin.products.store'),
            [
                'product_name' => 'Pakan Premium',
                'selling_price' => 50000,
                'formula_id' => $formula->id,
                'category' => 'feed',
                'source' => 'production'
            ]
        );

        $response->assertRedirect(
            route('admin.products.index')
        );

        $this->assertDatabaseHas(
            'products',
            [
                'product_name' => 'Pakan Premium'
            ]
        );
    }

    /** @test */
    public function produk_produksi_wajib_memiliki_formula()
    {
        $response = $this->post(
            route('admin.products.store'),
            [
                'product_name' => 'Pakan Premium',
                'category' => 'feed',
                'source' => 'production'
            ]
        );

        $response->assertSessionHasErrors(
            'formula_id'
        );
    }

    /** @test */
    public function produk_dapat_diupdate()
    {
        $product = Product::factory()->create();

        $response = $this->put(
            route('admin.products.update', $product),
            [
                'product_name' => 'Produk Baru',
                'category' => 'feed',
                'source' => 'purchase'
            ]
        );

        $response->assertRedirect(
            route('admin.products.index')
        );

        $this->assertDatabaseHas(
            'products',
            [
                'id' => $product->id,
                'product_name' => 'Produk Baru'
            ]
        );
    }

    /** @test */
    public function produk_dengan_stok_tidak_bisa_dihapus()
    {
        $product = Product::factory()->create([
            'stock' => 50
        ]);

        $response = $this->delete(
            route('admin.products.destroy', $product)
        );

        $response->assertSessionHas(
            'error'
        );

        $this->assertDatabaseHas(
            'products',
            [
                'id' => $product->id
            ]
        );
    }

    /** @test */
    public function produk_dapat_dihapus()
    {
        $product = Product::factory()->create([
            'stock' => 0
        ]);

        $response = $this->delete(
            route('admin.products.destroy', $product)
        );

        $response->assertRedirect(
            route('admin.products.index')
        );

        $this->assertDatabaseMissing(
            'products',
            [
                'id' => $product->id
            ]
        );
    }

    /** @test */
    public function activity_log_dibuat_saat_produk_ditambahkan()
    {
        $formula = Formula::factory()->create([
            'is_active' => true
        ]);

        $this->post(
            route('admin.products.store'),
            [
                'product_name' => 'Produk Test',
                'formula_id' => $formula->id,
                'category' => 'feed',
                'source' => 'production'
            ]
        );

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'type' => 'product_created',
                'module' => 'product'
            ]
        );
    }

    /** @test */
    public function activity_log_dibuat_saat_produk_diupdate()
    {
        $product = Product::factory()->create();

        $this->put(
            route('admin.products.update', $product),
            [
                'product_name' => 'Produk Update',
                'category' => 'feed',
                'source' => 'purchase'
            ]
        );

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'type' => 'product_updated',
                'module' => 'product'
            ]
        );
    }

    /** @test */
    public function activity_log_dibuat_saat_produk_dihapus()
    {
        $product = Product::factory()->create([
            'stock' => 0
        ]);

        $this->delete(
            route('admin.products.destroy', $product)
        );

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'type' => 'product_deleted',
                'module' => 'product'
            ]
        );
    }
}