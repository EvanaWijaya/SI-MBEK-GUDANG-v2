<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Admin;
use App\Models\Formula;
use App\Models\Product;
use App\Models\Material;
use App\Models\MaterialStock;
use App\Models\ProductStock;
use App\Models\Production;
use App\Models\QcIndicator;
use App\Models\ProductionQc;
use App\Models\Disposal;
use App\Models\ActivityLog;
use App\Models\StockMovement;

class ProductionTest extends TestCase
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

    private function createFormulaWithMaterial()
    {
        $formula = Formula::factory()->create();

        $material = Material::factory()->create([
            'stock' => 100,
        ]);

        $formula->materials()->attach(
            $material->id,
            [
                'percentage' => 50,
            ]
        );

        MaterialStock::factory()->create([
            'material_id' => $material->id,
            'quantity' => 100,
            'received_date' => now()->subDay(),
        ]);

        $product = Product::factory()->create([
            'formula_id' => $formula->id,
        ]);

        return compact(
            'formula',
            'material',
            'product'
        );
    }

    private function createQcIndicators(): array
    {
        $critical = QcIndicator::factory()->critical()->create();

        $non1 = QcIndicator::factory()->nonCritical()->create();

        $non2 = QcIndicator::factory()->nonCritical()->create();

        return compact('critical', 'non1', 'non2');
    }

    /** @test */
    public function produksi_dapat_dibuat()
    {
        extract($this->createFormulaWithMaterial());

        $response = $this->post(
            route('admin.productions.store'),
            [
                'formula_id' => $formula->id,
                'product_id' => $product->id,
                'production_quantity' => 20,
            ]
        );

        $response->assertRedirect(
            route('admin.productions.index')
        );

        $this->assertDatabaseHas(
            'productions',
            [
                'formula_id' => $formula->id,
                'product_id' => $product->id,
                'production_quantity' => 20,
                'status' => 'progress',
            ]
        );
    }

    /** @test */
    public function validasi_store_production()
    {
        $response = $this->from('/production/create')
            ->post(
                route('admin.productions.store'),
                []
            );

        $response->assertSessionHasErrors([
            'formula_id',
            'product_id',
            'production_quantity',
        ]);
    }

    /** @test */
    public function produk_harus_sesuai_formula()
    {
        $formula = Formula::factory()->create();

        $formulaLain = Formula::factory()->create();

        $product = Product::factory()->create([
            'formula_id' => $formulaLain->id,
        ]);

        $response = $this->post(
            route('admin.productions.store'),
            [
                'formula_id' => $formula->id,
                'product_id' => $product->id,
                'production_quantity' => 10,
            ]
        );

        $response->assertSessionHasErrors(
            'product'
        );
    }

    /** @test */
    public function produksi_gagal_jika_stok_material_tidak_cukup()
    {
        $formula = Formula::factory()->create();

        $material = Material::factory()->create([
            'stock' => 5,
        ]);

        $formula->materials()->attach(
            $material->id,
            [
                'percentage' => 100,
            ]
        );

        MaterialStock::factory()->create([
            'material_id' => $material->id,
            'quantity' => 5,
        ]);

        $product = Product::factory()->create([
            'formula_id' => $formula->id,
        ]);

        $response = $this->post(
            route('admin.productions.store'),
            [
                'formula_id' => $formula->id,
                'product_id' => $product->id,
                'production_quantity' => 10,
            ]
        );

        $response->assertSessionHasErrors(
            'produksi'
        );

        $this->assertDatabaseMissing(
            'productions',
            [
                'product_id' => $product->id,
                'production_quantity' => 10,
            ]
        );
    }

    /** @test */
    public function stok_material_berkurang_setelah_produksi()
    {
        extract($this->createFormulaWithMaterial());

        $this->post(
            route('admin.productions.store'),
            [
                'formula_id' => $formula->id,
                'product_id' => $product->id,
                'production_quantity' => 20,
            ]
        );

        $material->refresh();

        $this->assertEquals(
            90,
            $material->stock
        );
    }

    /** @test */
    public function stock_movement_dibuat_saat_produksi()
    {
        extract($this->createFormulaWithMaterial());

        $this->post(
            route('admin.productions.store'),
            [
                'formula_id' => $formula->id,
                'product_id' => $product->id,
                'production_quantity' => 20,
            ]
        );

        $this->assertDatabaseHas(
            'stock_movements',
            [
                'stockable_id' => $material->id,
                'stockable_type' => Material::class,
                'type' => 'out',
                'source' => 'production',
            ]
        );
    }

    /** @test */
    public function activity_log_dibuat_saat_produksi()
    {
        extract($this->createFormulaWithMaterial());

        $this->post(
            route('admin.productions.store'),
            [
                'formula_id' => $formula->id,
                'product_id' => $product->id,
                'production_quantity' => 20,
            ]
        );

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'type' => 'production_created',
                'module' => 'production',
            ]
        );
    }

    /** @test */
    public function qc_berhasil()
    {
        extract($this->createQcIndicators());

        $production = Production::factory()->create([
            'status' => 'progress',
            'qc_status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.productions.show', $production))
            ->put(
                route('admin.productions.qc', $production),
                [
                    'qc_threshold' => 80,
                    'indicators' => [
                        $critical->id => 'passed',
                        $non1->id => 'passed',
                        $non2->id => 'passed',
                    ],
                ]
            );

        $response->assertSessionHas('success');

        $production->refresh();

        $this->assertEquals('passed', $production->qc_status);
        $this->assertEquals('completed', $production->status);
    }

    /** @test */
    public function qc_gagal_karena_critical()
    {
        extract($this->createQcIndicators());

        $production = Production::factory()->create([
            'status' => 'progress',
            'qc_status' => 'pending',
            'production_quantity' => 25,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->put(
                route('admin.productions.qc', $production),
                [
                    'qc_threshold' => 80,
                    'indicators' => [
                        $critical->id => 'failed',
                        $non1->id => 'passed',
                        $non2->id => 'passed',
                    ],
                ]
            );

        $production->refresh();

        $this->assertEquals('failed', $production->qc_status);
        $this->assertEquals('rejected', $production->status);
    }

    /** @test */
    public function qc_gagal_karena_persentase()
    {
        extract($this->createQcIndicators());

        $production = Production::factory()->create([
            'status' => 'progress',
            'qc_status' => 'pending',
        ]);

        $this->actingAs($this->admin, 'admin')
            ->put(
                route('admin.productions.qc', $production),
                [
                    'qc_threshold' => 80,
                    'indicators' => [
                        $critical->id => 'passed',
                        $non1->id => 'failed',
                        $non2->id => 'passed',
                    ],
                ]
            );

        $production->refresh();

        $this->assertEquals('failed', $production->qc_status);
        $this->assertEquals(50, $production->qc_percentage);
    }

    /** @test */
    public function tidak_bisa_qc_jika_status_bukan_progress()
    {
        $production = Production::factory()->create([
            'status' => 'completed',
            'qc_status' => 'passed',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.productions.show', $production))
            ->put(
                route('admin.productions.qc', $production),
                [
                    'qc_threshold' => 80,
                    'indicators' => [],
                ]
            );

        $response->assertSessionHasErrors('status');
    }

    /** @test */
    public function validasi_input_qc()
    {
        $production = Production::factory()->create([
            'status' => 'progress',
            'qc_status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.productions.show', $production))
            ->put(
                route('admin.productions.qc', $production),
                []
            );

        $response->assertSessionHasErrors([
            'indicators',
            'qc_threshold',
        ]);
    }

    /** @test */
    public function production_qc_tercatat()
    {
        extract($this->createQcIndicators());

        $production = Production::factory()->create([
            'status' => 'progress',
            'qc_status' => 'pending',
        ]);

        $this->actingAs($this->admin, 'admin')
            ->put(
                route('admin.productions.qc', $production),
                [
                    'qc_threshold' => 80,
                    'indicators' => [
                        $critical->id => 'passed',
                        $non1->id => 'passed',
                        $non2->id => 'passed',
                    ],
                ]
            );

        $this->assertDatabaseHas('production_qcs', [
            'production_id' => $production->id,
            'status' => 'passed',
        ]);
    }

    /** @test */
    public function disposal_otomatis_dibuat_jika_qc_gagal()
    {
        extract($this->createQcIndicators());

        $production = Production::factory()->create([
            'status' => 'progress',
            'qc_status' => 'pending',
            'production_quantity' => 30,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->put(
                route('admin.productions.qc', $production),
                [
                    'qc_threshold' => 80,
                    'indicators' => [
                        $critical->id => 'failed',
                        $non1->id => 'passed',
                        $non2->id => 'passed',
                    ],
                ]
            );

        $this->assertDatabaseHas('disposals', [
            'disposable_id' => $production->id,
            'disposable_type' => Production::class,
            'reason' => 'qc_failed',
            'quantity' => 30,
        ]);
    }

    /** @test */
    public function produksi_dapat_diselesaikan()
    {
        $production = Production::factory()->create([
            'status' => 'progress',
            'qc_status' => 'passed',
            'production_quantity' => 20,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.productions.show', $production))
            ->put(
                route('admin.productions.selesai', $production),
                [
                    'expiration_date' => now()->addMonths(6)->toDateString(),
                ]
            );

        $response->assertSessionHas('success');

        $production->refresh();

        $this->assertEquals('completed', $production->status);
    }

    /** @test */
    public function stok_produk_bertambah_setelah_produksi_selesai()
    {
        $product = Product::factory()->create([
            'stock' => 5,
        ]);

        $production = Production::factory()->create([
            'product_id' => $product->id,
            'status' => 'progress',
            'qc_status' => 'passed',
            'production_quantity' => 20,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->put(
                route('admin.productions.selesai', $production),
                [
                    'expiration_date' => now()->addMonths(6)->toDateString(),
                ]
            );

        $product->refresh();

        $this->assertEquals(25, $product->stock);
    }

    /** @test */
    public function product_stock_batch_dibuat()
    {
        $product = Product::factory()->create();

        $production = Production::factory()->create([
            'product_id' => $product->id,
            'status' => 'progress',
            'qc_status' => 'passed',
            'production_quantity' => 40,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->put(
                route('admin.productions.selesai', $production),
                [
                    'expiration_date' => now()->addMonths(6)->toDateString(),
                ]
            );

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'quantity' => 40,
            'source' => 'production',
            'reference_id' => $production->id,
        ]);
    }

    /** @test */
    public function stock_movement_produk_dibuat()
    {
        $product = Product::factory()->create();

        $production = Production::factory()->create([
            'product_id' => $product->id,
            'status' => 'progress',
            'qc_status' => 'passed',
            'production_quantity' => 30,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->put(
                route('admin.productions.selesai', $production),
                [
                    'expiration_date' => now()->addMonths(6)->toDateString(),
                ]
            );

        $this->assertDatabaseHas('stock_movements', [
            'stockable_id' => $product->id,
            'stockable_type' => Product::class,
            'type' => 'in',
            'source' => 'production',
        ]);
    }

    /** @test */
    public function activity_log_dibuat_saat_produksi_selesai()
    {
        $production = Production::factory()->create([
            'status' => 'progress',
            'qc_status' => 'passed',
        ]);

        $this->actingAs($this->admin, 'admin')
            ->put(
                route('admin.productions.selesai', $production),
                [
                    'expiration_date' => now()->addMonths(6)->toDateString(),
                ]
            );

        $this->assertDatabaseHas('activity_logs', [
            'type' => 'production_finished',
            'module' => 'production',
        ]);
    }

    /** @test */
    public function tidak_bisa_menyelesaikan_produksi_yang_sudah_completed()
    {
        $production = Production::factory()->create([
            'status' => 'completed',
            'qc_status' => 'passed',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.productions.show', $production))
            ->put(
                route('admin.productions.selesai', $production),
                [
                    'expiration_date' => now()->addMonths(6)->toDateString(),
                ]
            );

        $response->assertSessionHasErrors('status');
    }

    /** @test */
    public function tidak_bisa_menyelesaikan_produksi_yang_rejected()
    {
        $production = Production::factory()->create([
            'status' => 'rejected',
            'qc_status' => 'failed',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.productions.show', $production))
            ->put(
                route('admin.productions.selesai', $production),
                [
                    'expiration_date' => now()->addMonths(6)->toDateString(),
                ]
            );

        $response->assertSessionHasErrors('status');
    }

    /** @test */
    public function tidak_bisa_menyelesaikan_produksi_jika_qc_belum_dilakukan()
    {
        $production = Production::factory()->create([
            'status' => 'progress',
            'qc_status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.productions.show', $production))
            ->put(
                route('admin.productions.selesai', $production),
                [
                    'expiration_date' => now()->addMonths(6)->toDateString(),
                ]
            );

        $response->assertSessionHasErrors('qc');
    }

    /** @test */
    public function tidak_bisa_menyelesaikan_produksi_jika_qc_failed()
    {
        $production = Production::factory()->create([
            'status' => 'progress',
            'qc_status' => 'failed',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.productions.show', $production))
            ->put(
                route('admin.productions.selesai', $production),
                [
                    'expiration_date' => now()->addMonths(6)->toDateString(),
                ]
            );

        $response->assertSessionHasErrors('qc');
    }

    /** @test */
    public function expiration_date_wajib_diisi()
    {
        $production = Production::factory()->create([
            'status' => 'progress',
            'qc_status' => 'passed',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.productions.show', $production))
            ->put(
                route('admin.productions.selesai', $production),
                []
            );

        $response->assertSessionHasErrors('expiration_date');
    }
}