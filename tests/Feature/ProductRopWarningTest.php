<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductAllocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRopWarningTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function warning_muncul_jika_stok_mencapai_rop()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $product = Product::factory()->create([
            'stok' => 20,
            'rop'  => 15,
        ]);

        ProductAllocation::create([
            'product_id' => $product->id,
            'type'       => 'jual',
            'qty'        => 20,
            'created_by' => $admin->id,
        ]);

        $response = $this->post(
            route('admin.product.allocations.sell', $product),
            ['qty' => 10]
        );

        $response->assertSessionHas('warning');

        $product->refresh();
        $this->assertEquals(10, $product->stok);
        $this->assertTrue($product->isBelowRop());
    }
}
