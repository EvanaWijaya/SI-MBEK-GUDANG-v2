<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductAllocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAllocationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Login admin
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');
    }

    /** @test */
    public function pemakaian_internal_valid_mengurangi_stok_dan_alokasi()
    {
        // ARRANGE
        $product = Product::factory()->create([
            'stok' => 100,
        ]);

        ProductAllocation::create([
            'product_id' => $product->id,
            'type'       => 'internal',
            'qty'        => 40,
            'created_by' => auth('admin')->id(),
        ]);

        // ACT
        $response = $this->post(
            route('admin.product.allocations.use-internal', $product),
            ['qty' => 10]
        );

        // ASSERT
        $response->assertSessionHasNoErrors();

        $product->refresh();
        $allocation = $product->allocations()
            ->where('type', 'internal')
            ->first();

        $this->assertEquals(90, $product->stok);
        $this->assertEquals(30, $allocation->qty);
    }

    /** @test */
    public function pemakaian_internal_gagal_jika_melebihi_alokasi()
    {
        // ARRANGE
        $product = Product::factory()->create([
            'stok' => 50,
        ]);

        ProductAllocation::create([
            'product_id' => $product->id,
            'type'       => 'internal',
            'qty'        => 10,
            'created_by' => auth('admin')->id(),
        ]);

        // ACT
        $response = $this->post(
            route('admin.product.allocations.use-internal', $product),
            ['qty' => 20]
        );

        // ASSERT
        $response->assertSessionHasErrors('qty');

        $product->refresh();
        $allocation = $product->allocations()
            ->where('type', 'internal')
            ->first();

        $this->assertEquals(50, $product->stok);
        $this->assertEquals(10, $allocation->qty);
    }

    /** @test */
    public function penjualan_valid_mengurangi_stok_dan_alokasi_jual()
    {
        // ARRANGE
        $product = Product::factory()->create([
            'stok' => 80,
        ]);

        ProductAllocation::create([
            'product_id' => $product->id,
            'type'       => 'jual',
            'qty'        => 50,
            'created_by' => auth('admin')->id(),
        ]);

        // ACT
        $response = $this->post(
            route('admin.product.allocations.sell', $product),
            ['qty' => 20]
        );

        // ASSERT
        $response->assertSessionHasNoErrors();

        $product->refresh();
        $allocation = $product->allocations()
            ->where('type', 'jual')
            ->first();

        $this->assertEquals(60, $product->stok);
        $this->assertEquals(30, $allocation->qty);
    }

    /** @test */
    public function penjualan_gagal_jika_melebihi_alokasi()
    {
        // ARRANGE
        $product = Product::factory()->create([
            'stok' => 30,
        ]);

        ProductAllocation::create([
            'product_id' => $product->id,
            'type'       => 'jual',
            'qty'        => 15,
            'created_by' => auth('admin')->id(),
        ]);

        // ACT
        $response = $this->post(
            route('admin.product.allocations.sell', $product),
            ['qty' => 25]
        );

        // ASSERT
        $response->assertSessionHasErrors('qty');

        $product->refresh();
        $allocation = $product->allocations()
            ->where('type', 'jual')
            ->first();

        $this->assertEquals(30, $product->stok);
        $this->assertEquals(15, $allocation->qty);
    }
}
