<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Material;
use App\Models\MaterialStock;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Production;
use App\Models\Disposal;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DisposalTest extends TestCase
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
    public function material_batch_dapat_didisposal()
    {
        $material = Material::factory()->create([
            'stock' => 100,
        ]);

        $batch = MaterialStock::factory()->create([
            'material_id' => $material->id,
            'quantity' => 100,
        ]);

        $response = $this->post("/admin/disposal/material/{$batch->id}", [
            'reason' => 'qc_failed',
            'notes' => 'Batch rusak',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('disposals', [
            'disposable_id' => $batch->id,
            'disposable_type' => MaterialStock::class,
            'quantity' => 100,
            'reason' => 'qc_failed',
        ]);

        $this->assertDatabaseHas('material_stocks', [
            'id' => $batch->id,
            'quantity' => 0,
        ]);

        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'stock' => 0,
        ]);
    }

    /** @test */
    public function material_disposal_expired_menggunakan_catatan_otomatis()
    {
        $material = Material::factory()->create([
            'stock' => 50,
        ]);

        $batch = MaterialStock::factory()->expired()->create([
            'material_id' => $material->id,
            'quantity' => 50,
        ]);

        $this->post("/admin/disposal/material/{$batch->id}", [
            'reason' => 'expired',
            'notes' => 'Ini harus diabaikan',
        ]);

        $this->assertDatabaseHas('disposals', [
            'disposable_id' => $batch->id,
            'reason' => 'expired',
            'notes' => 'Otomatis dibuang karena batch bahan ini sudah melewati masa kadaluarsa (Expired).',
        ]);
    }

    /** @test */
    public function batch_material_yang_sudah_habis_tidak_bisa_didisposal()
    {
        $material = Material::factory()->create();

        $batch = MaterialStock::factory()->create([
            'material_id' => $material->id,
            'quantity' => 0,
        ]);

        $response = $this->from('/admin/disposal')
            ->post("/admin/disposal/material/{$batch->id}", [
                'reason' => 'qc_failed',
            ]);

        $response
            ->assertRedirect('/admin/disposal')
            ->assertSessionHasErrors('stock');

        $this->assertDatabaseMissing('disposals', [
            'disposable_id' => $batch->id,
        ]);
    }

    /** @test */
    public function activity_log_dibuat_saat_material_disposal()
    {
        $material = Material::factory()->create([
            'stock' => 20,
        ]);

        $batch = MaterialStock::factory()->create([
            'material_id' => $material->id,
            'quantity' => 20,
        ]);

        $this->post("/admin/disposal/material/{$batch->id}", [
            'reason' => 'qc_failed',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'actor_id' => $this->admin->id,
            'type' => 'disposal_created',
            'module' => 'disposal',
        ]);
    }

    /** @test */
    public function production_dapat_didisposal()
    {
        $product = Product::factory()->create([
            'stock' => 80,
        ]);

        $production = Production::factory()->create([
            'product_id' => $product->id,
            'status' => 'completed',
        ]);

        $batch = ProductStock::factory()->create([
            'product_id' => $product->id,
            'source' => 'production',
            'reference_id' => $production->id,
            'quantity' => 80,
        ]);

        $response = $this->post(
            "/admin/disposal/production/{$production->id}",
            [
                'reason' => 'qc_failed',
                'notes' => 'Produk rusak',
            ]
        );

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('disposals', [
            'disposable_id' => $production->id,
            'disposable_type' => Production::class,
            'quantity' => 80,
            'reason' => 'qc_failed',
        ]);

        $this->assertDatabaseHas('product_stocks', [
            'id' => $batch->id,
            'quantity' => 0,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 0,
        ]);

        $this->assertDatabaseHas('productions', [
            'id' => $production->id,
            'status' => 'rejected',
        ]);
    }

    /** @test */
    public function production_disposal_expired_menggunakan_catatan_otomatis()
    {
        $product = Product::factory()->create([
            'stock' => 40,
        ]);

        $production = Production::factory()->create([
            'product_id' => $product->id,
        ]);

        ProductStock::factory()->create([
            'product_id' => $product->id,
            'source' => 'production',
            'reference_id' => $production->id,
            'quantity' => 40,
        ]);

        $this->post(
            "/admin/disposal/production/{$production->id}",
            [
                'reason' => 'expired',
                'notes' => 'Harus diganti',
            ]
        );

        $this->assertDatabaseHas('disposals', [
            'disposable_id' => $production->id,
            'reason' => 'expired',
            'notes' => 'Otomatis dibuang karena produk ini sudah melewati masa kadaluarsa (Expired).',
        ]);
    }

    /** @test */
    public function production_tidak_bisa_didisposal_jika_batch_habis()
    {
        $product = Product::factory()->create([
            'stock' => 0,
        ]);

        $production = Production::factory()->create([
            'product_id' => $product->id,
        ]);

        ProductStock::factory()->create([
            'product_id' => $product->id,
            'source' => 'production',
            'reference_id' => $production->id,
            'quantity' => 0,
        ]);

        $response = $this->from('/admin/disposal')
            ->post(
                "/admin/disposal/production/{$production->id}",
                [
                    'reason' => 'qc_failed',
                ]
            );

        $response
            ->assertRedirect('/admin/disposal')
            ->assertSessionHasErrors('stock');

        $this->assertDatabaseCount('disposals', 0);
    }

    /** @test */
    public function activity_log_dibuat_saat_production_disposal()
    {
        $product = Product::factory()->create([
            'stock' => 30,
        ]);

        $production = Production::factory()->create([
            'product_id' => $product->id,
        ]);

        ProductStock::factory()->create([
            'product_id' => $product->id,
            'source' => 'production',
            'reference_id' => $production->id,
            'quantity' => 30,
        ]);

        $this->post(
            "/admin/disposal/production/{$production->id}",
            [
                'reason' => 'qc_failed',
            ]
        );

        $this->assertDatabaseHas('activity_logs', [
            'actor_id' => $this->admin->id,
            'type' => 'disposal_created',
            'module' => 'disposal',
        ]);
    }

    /** @test */
    public function product_batch_dapat_didisposal()
    {
        $product = Product::factory()->create([
            'stock' => 120,
        ]);

        $batch = ProductStock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 120,
        ]);

        $response = $this->post(
            "/admin/disposal/product-batch/{$batch->id}",
            [
                'reason' => 'qc_failed',
                'notes' => 'Kemasan rusak',
            ]
        );

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('disposals', [
            'disposable_id' => $batch->id,
            'disposable_type' => ProductStock::class,
            'quantity' => 120,
            'reason' => 'qc_failed',
        ]);

        $this->assertDatabaseHas('product_stocks', [
            'id' => $batch->id,
            'quantity' => 0,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 0,
        ]);
    }

    /** @test */
    public function product_batch_expired_menggunakan_catatan_otomatis()
    {
        $product = Product::factory()->create([
            'stock' => 50,
        ]);

        $batch = ProductStock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 50,
        ]);

        $this->post(
            "/admin/disposal/product-batch/{$batch->id}",
            [
                'reason' => 'expired',
                'notes' => 'Harus diabaikan',
            ]
        );

        $this->assertDatabaseHas('disposals', [
            'disposable_id' => $batch->id,
            'reason' => 'expired',
            'notes' => 'Otomatis dibuang karena batch produk ini sudah melewati masa kadaluarsa (Expired).',
        ]);
    }

    /** @test */
    public function batch_produk_yang_sudah_habis_tidak_bisa_didisposal()
    {
        $product = Product::factory()->create();

        $batch = ProductStock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 0,
        ]);

        $response = $this->from('/admin/disposal')
            ->post(
                "/admin/disposal/product-batch/{$batch->id}",
                [
                    'reason' => 'qc_failed',
                ]
            );

        $response
            ->assertRedirect('/admin/disposal')
            ->assertSessionHasErrors('stock');

        $this->assertDatabaseCount('disposals', 0);
    }

    /** @test */
    public function activity_log_dibuat_saat_product_batch_disposal()
    {
        $product = Product::factory()->create([
            'stock' => 25,
        ]);

        $batch = ProductStock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 25,
        ]);

        $this->post(
            "/admin/disposal/product-batch/{$batch->id}",
            [
                'reason' => 'qc_failed',
            ]
        );

        $this->assertDatabaseHas('activity_logs', [
            'actor_id' => $this->admin->id,
            'type' => 'disposal_created',
            'module' => 'disposal',
        ]);
    }
}