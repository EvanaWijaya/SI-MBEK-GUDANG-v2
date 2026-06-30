<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Material;
use App\Models\MaterialStock;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MaterialInventoryTest extends TestCase
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
    public function stok_bahan_dapat_ditambahkan()
    {
        $material = Material::factory()->create([
            'stock' => 0,
        ]);

        $response = $this->post(
            route('admin.inventory.material.adjust', $material),
            [
                'type' => 'in',
                'quantity' => 100,
                'expiration_date' => now()->addMonth()->toDateString(),
            ]
        );

        $response->assertSessionHas('success');

        $this->assertDatabaseHas(
            'material_stocks',
            [
                'material_id' => $material->id,
                'quantity' => 100,
            ]
        );

        $this->assertDatabaseHas(
            'materials',
            [
                'id' => $material->id,
                'stock' => 100,
            ]
        );
    }

    /** @test */
    public function expired_date_wajib_saat_stok_masuk()
    {
        $material = Material::factory()->create();

        $response = $this->from('/dummy')->post(
            route('admin.inventory.material.adjust', $material),
            [
                'type' => 'in',
                'quantity' => 100,
            ]
        );

        $response->assertSessionHasErrors(
            'expiration_date'
        );
    }

    /** @test */
    public function stok_bahan_dapat_dikurangi()
    {
        $material = Material::factory()->create([
            'stock' => 100
        ]);

        MaterialStock::factory()->create([
            'material_id' => $material->id,
            'quantity' => 100,
        ]);

        $response = $this->post(
            route('admin.inventory.material.adjust', $material),
            [
                'type' => 'out',
                'quantity' => 40,
            ]
        );

        $response->assertSessionHas('success');

        $this->assertDatabaseHas(
            'materials',
            [
                'id' => $material->id,
                'stock' => 60,
            ]
        );
    }

    /** @test */
    public function tidak_bisa_mengurangi_stok_melebihi_yang_tersedia()
    {
        $this->withoutExceptionHandling();

        $material = Material::factory()->create();

        MaterialStock::factory()->create([
            'material_id' => $material->id,
            'quantity' => 10,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stok tidak mencukupi');

        $this->post(
            route('admin.inventory.material.adjust', $material),
            [
                'type' => 'out',
                'quantity' => 20,
            ]
        );
    }

    /** @test */
    public function activity_log_dibuat_saat_stok_masuk()
    {
        $material = Material::factory()->create();

        $this->post(
            route('admin.inventory.material.adjust', $material),
            [
                'type' => 'in',
                'quantity' => 50,
                'expiration_date' => now()->addMonth()->toDateString(),
            ]
        );

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'type' => 'material_stock_in',
                'module' => 'inventory_material',
            ]
        );
    }

    /** @test */
    public function activity_log_dibuat_saat_stok_keluar()
    {
        $material = Material::factory()->create([
            'stock' => 100
        ]);

        MaterialStock::factory()->create([
            'material_id' => $material->id,
            'quantity' => 100,
        ]);

        $this->post(
            route('admin.inventory.material.adjust', $material),
            [
                'type' => 'out',
                'quantity' => 20,
            ]
        );

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'type' => 'material_stock_out',
                'module' => 'inventory_material',
            ]
        );
    }

    /** @test */
    public function stok_dapat_disinkronisasi()
    {
        $material = Material::factory()->create([
            'stock' => 0
        ]);

        MaterialStock::factory()->create([
            'material_id' => $material->id,
            'quantity' => 75,
        ]);

        $response = $this->post(
            route('admin.inventory.material.sync', $material)
        );

        $response->assertSessionHas('success');

        $this->assertDatabaseHas(
            'materials',
            [
                'id' => $material->id,
                'stock' => 75,
            ]
        );
    }
}