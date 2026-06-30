<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Disposal;
use App\Models\Production;
use App\Models\ProductionQc;
use App\Models\QcIndicator;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductionQcTest extends TestCase
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

    private function createQcIndicators(): array
    {
        QcIndicator::query()->delete();

        $critical = QcIndicator::factory()->critical()->create();

        $non1 = QcIndicator::factory()->nonCritical()->create();

        $non2 = QcIndicator::factory()->nonCritical()->create();

        return compact(
            'critical',
            'non1',
            'non2'
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
            ->post(
                route('admin.qc.store', $production),
                [
                    'threshold' => 80,

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

        $this->assertEquals(100, $production->qc_percentage);

        $this->assertEquals('progress', $production->status);
    }

    /** @test */
    public function qc_record_tersimpan()
    {
        extract($this->createQcIndicators());

        $production = Production::factory()->create();

        $this->actingAs($this->admin, 'admin')
            ->post(
                route('admin.qc.store', $production),
                [
                    'threshold' => 80,

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

            'non_critical_score' => 100,

            'threshold' => 80,
        ]);
    }

    /** @test */
    public function persentase_qc_dihitung_dengan_benar()
    {
        extract($this->createQcIndicators());

        $production = Production::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.qc.store', $production), [

                'threshold' => 80,

                'indicators' => [
                    $critical->id => 'passed',
                    $non1->id => 'passed',
                    $non2->id => 'failed',
                ],
            ]);

        $response->assertSessionHasNoErrors();

        $production->refresh();

        $this->assertSame(
            50.0,
            (float) $production->qc_percentage
        );

        $this->assertEquals('failed', $production->qc_status);
    }

    /** @test */
    public function threshold_wajib_diisi()
    {
        extract($this->createQcIndicators());

        $production = Production::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.productions.show', $production))
            ->post(
                route('admin.qc.store', $production),
                [

                    'indicators' => [

                        $critical->id => 'passed',

                        $non1->id => 'passed',

                        $non2->id => 'passed',

                    ]

                ]
            );

        $response->assertSessionHasErrors('threshold');
    }

    /** @test */
    public function semua_indikator_harus_diisi()
    {
        extract($this->createQcIndicators());

        $production = Production::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.productions.show', $production))
            ->post(
                route('admin.qc.store', $production),
                [

                    'threshold' => 80,

                    'indicators' => [

                        $critical->id => 'passed',

                    ]

                ]
            );

        $response->assertSessionHasErrors([
            "indicators.{$non1->id}",
            "indicators.{$non2->id}",
        ]);
    }

    /** @test */
    public function qc_gagal_jika_indikator_critical_gagal()
    {
        extract($this->createQcIndicators());

        $production = Production::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.qc.store', $production), [

                'threshold' => 80,

                'indicators' => [
                    $critical->id => 'failed',
                    $non1->id => 'passed',
                    $non2->id => 'passed',
                ],
            ]);

        $response->assertSessionHas('warning');

        $production->refresh();

        $this->assertEquals('failed', $production->qc_status);
        $this->assertEquals('rejected', $production->status);
        $this->assertEquals(0, (float) $production->qc_percentage);
    }

    /** @test */
    public function production_qc_tercatat()
    {
        extract($this->createQcIndicators());

        $production = Production::factory()->create();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.qc.store', $production), [

                'threshold' => 80,

                'indicators' => [
                    $critical->id => 'passed',
                    $non1->id => 'passed',
                    $non2->id => 'passed',
                ],
            ]);

        $this->assertDatabaseHas('production_qcs', [

            'production_id' => $production->id,
            'status' => 'passed',
            'non_critical_score' => 100,
            'threshold' => 80,
        ]);
    }

    /** @test */
    public function disposal_otomatis_dibuat_jika_qc_gagal()
    {
        extract($this->createQcIndicators());

        $production = Production::factory()->create([
            'production_quantity' => 25,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.qc.store', $production), [

                'threshold' => 80,

                'indicators' => [
                    $critical->id => 'failed',
                    $non1->id => 'passed',
                    $non2->id => 'passed',
                ],
            ]);

        $this->assertDatabaseHas('disposals', [

            'disposable_id' => $production->id,
            'disposable_type' => Production::class,
            'reason' => 'qc_failed',
            'quantity' => 25,
        ]);
    }

    /** @test */
    public function activity_log_dibuat_setelah_qc()
    {
        extract($this->createQcIndicators());

        $production = Production::factory()->create();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.qc.store', $production), [

                'threshold' => 80,

                'indicators' => [
                    $critical->id => 'passed',
                    $non1->id => 'passed',
                    $non2->id => 'passed',
                ],
            ]);

        $this->assertDatabaseHas('activity_logs', [

            'type' => 'qc_checked',
            'module' => 'production_qc',
        ]);
    }
}