<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Material;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MaterialTest extends TestCase
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
    public function material_dapat_dibuat()
    {
        $response = $this->post(
            route('admin.materials.store'),
            [
                'material_name' => 'Jagung',
                'category' => 'feed',
                'unit' => 'Kg',
                'average_usage' => 10,
                'lead_time' => 5,
                'safety_stock' => 20,
                'description' => 'Bahan baku pakan',
            ]
        );

        $response->assertRedirect(
            route('admin.materials.index')
        );

        $this->assertDatabaseHas(
            'materials',
            [
                'material_name' => 'Jagung',
                'category' => 'feed',
            ]
        );
    }

    /** @test */
    public function nama_material_harus_unik()
    {
        Material::create([
            'material_name' => 'Jagung',
            'category' => 'feed',
            'unit' => 'Kg',
            'stock' => 0,
        ]);

        $response = $this->post(
            route('admin.materials.store'),
            [
                'material_name' => 'Jagung',
                'category' => 'feed',
                'unit' => 'Kg',
            ]
        );

        $response->assertSessionHasErrors(
            'material_name'
        );
    }

    /** @test */
    public function material_dapat_diperbarui()
    {
        $material = Material::factory()->create([
            'material_name' => 'Jagung'
        ]);

        $response = $this->put(
            route('admin.materials.update', $material),
            [
                'material_name' => 'Jagung Premium',
                'category' => 'feed',
                'unit' => 'Kg',
            ]
        );

        $response->assertRedirect(
            route('admin.materials.index')
        );

        $this->assertDatabaseHas(
            'materials',
            [
                'id' => $material->id,
                'material_name' => 'Jagung Premium',
            ]
        );
    }

    /** @test */
    public function material_dengan_stok_tidak_bisa_dihapus()
    {
        $material = Material::factory()->create([
            'stock' => 100
        ]);

        $response = $this->delete(
            route('admin.materials.destroy', $material)
        );

        $response->assertSessionHas(
            'error'
        );

        $this->assertDatabaseHas(
            'materials',
            [
                'id' => $material->id
            ]
        );
    }

    /** @test */
    public function material_dapat_dihapus_jika_stok_kosong()
    {
        $material = Material::factory()->create([
            'stock' => 0
        ]);

        $response = $this->delete(
            route('admin.materials.destroy', $material)
        );

        $response->assertRedirect(
            route('admin.materials.index')
        );

        $this->assertDatabaseMissing(
            'materials',
            [
                'id' => $material->id
            ]
        );
    }

    /** @test */
    public function activity_log_dibuat_saat_material_ditambahkan()
    {
        $this->post(
            route('admin.materials.store'),
            [
                'material_name' => 'Dedak',
                'category' => 'feed',
                'unit' => 'Kg',
            ]
        );

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'type' => 'material_created',
                'module' => 'material',
            ]
        );
    }

    /** @test */
    public function activity_log_dibuat_saat_material_diupdate()
    {
        $material = Material::factory()->create([
            'material_name' => 'Jagung Test'
        ]);

        $response = $this->put(
            route('admin.materials.update', $material),
            [
                'material_name' => 'Material-' . uniqid(),
                'category' => 'feed',
                'unit' => 'Kg',
            ]
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'type' => 'material_updated',
                'module' => 'material',
            ]
        );
    }

    /** @test */
    public function activity_log_dibuat_saat_material_dihapus()
    {
        $material = Material::factory()->create([
            'stock' => 0
        ]);

        $this->delete(
            route('admin.materials.destroy', $material)
        );

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'type' => 'material_deleted',
                'module' => 'material',
            ]
        );
    }
}