<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Formula;
use App\Models\Material;
use App\Models\Product;
use App\Models\Production;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductionFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function produksi_qc_selesai_menambah_stok_produk()
    {
        // =========================
        // ARRANGE
        // =========================

        // 🧑‍💼 Admin login
        $admin = Admin::factory()->create([
            'must_change_password' => false,
        ]);

        $this->actingAs($admin, 'admin');

        // 📦 Bahan baku
        $material = Material::factory()->create([
            'stok' => 100,
        ]);

        // 🧪 Formula
        $formula = Formula::factory()->create([
            'created_by' => $admin->id,
        ]);

        // Relasi formula ↔ material (50%)
        $formula->materials()->attach($material->id, [
            'persentase' => 50,
        ]);

        // 📦 Produk jadi
        $product = Product::factory()->create([
            'formula_id' => $formula->id,
            'stok' => 0,
            'created_by' => $admin->id,
        ]);

        // =========================
        // ACT 1️⃣: BUAT PRODUKSI
        // =========================

        $response = $this->post(
            route('admin.productions.store'),
            [
                'formula_id' => $formula->id,
                'product_id' => $product->id,
                'qty_produksi' => 20,
            ]
        );

        $response->assertSessionHasNoErrors();

        $production = Production::first();

        // 🔍 ASSERT setelah produksi dibuat
        $this->assertNotNull($production);
        $this->assertEquals('diproses', $production->status);

        // Stok bahan baku berkurang
        // 50% × 20 = 10
        $this->assertEquals(90, $material->fresh()->stok);

        // =========================
        // ACT 2️⃣: QC
        // =========================

        $response = $this->put(
            route('admin.productions.qc', $production),
            [
                'qty_qc_lulus' => 18,
                'qty_qc_gagal' => 2,
            ]
        );


        $response->assertSessionHasNoErrors();

        $production->refresh();

        $this->assertEquals(18, $production->qty_qc_lulus);
        $this->assertEquals(2, $production->qty_qc_gagal);

        // =========================
        // ACT 3️⃣: SELESAI PRODUKSI
        // =========================

        $response = $this->put(
            route('admin.productions.selesai', $production->id)
        );

        $response->assertSessionHasNoErrors();

        $production->refresh();
        $product->refresh();

        // =========================
        // ASSERT FINAL
        // =========================

        // Status produksi
        $this->assertEquals('selesai', $production->status);

        // Stok produk bertambah sesuai QC lulus
        $this->assertEquals(18, $product->stok);
    }
}
