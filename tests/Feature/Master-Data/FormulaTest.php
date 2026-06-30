<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Formula;
use App\Models\Material;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FormulaTest extends TestCase
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
    public function formula_dapat_dibuat()
    {
        $material1 = Material::factory()->create([
            'category' => 'feed'
        ]);

        $material2 = Material::factory()->create([
            'category' => 'feed'
        ]);

        $response = $this->post(
            route('admin.formula.store'),
            [
                'formula_name' => 'Pakan Starter',
                'materials' => [
                    [
                        'material_id' => $material1->id,
                        'percentage' => 60
                    ],
                    [
                        'material_id' => $material2->id,
                        'percentage' => 40
                    ]
                ]
            ]
        );

        $response->assertRedirect(
            route('admin.formula.index')
        );

        $this->assertDatabaseHas(
            'formulas',
            [
                'formula_name' => 'Pakan Starter'
            ]
        );
    }

    /** @test */
    public function total_persentase_harus_100_persen()
    {
        $material1 = Material::factory()->create();
        $material2 = Material::factory()->create();

        $response = $this->post(
            route('admin.formula.store'),
            [
                'formula_name' => 'Formula Salah',
                'materials' => [
                    [
                        'material_id' => $material1->id,
                        'percentage' => 50
                    ],
                    [
                        'material_id' => $material2->id,
                        'percentage' => 20
                    ]
                ]
            ]
        );

        $response->assertSessionHasErrors(
            'percentage'
        );
    }

    /** @test */
    public function formula_dapat_diupdate()
    {
        $material1 = Material::factory()->create();
        $material2 = Material::factory()->create();

        $formula = Formula::factory()->create();

        $formula->materials()->attach(
            $material1->id,
            ['percentage' => 100]
        );

        $response = $this->put(
            route('admin.formula.update', $formula),
            [
                'formula_name' => 'Formula Baru',
                'materials' => [
                    [
                        'material_id' => $material1->id,
                        'percentage' => 70
                    ],
                    [
                        'material_id' => $material2->id,
                        'percentage' => 30
                    ]
                ]
            ]
        );

        $response->assertRedirect(
            route('admin.formula.index')
        );

        $this->assertDatabaseHas(
            'formulas',
            [
                'id' => $formula->id,
                'formula_name' => 'Formula Baru'
            ]
        );
    }

    /** @test */
    public function formula_tidak_bisa_diupdate_jika_total_persentase_bukan_100()
    {
        $material = Material::factory()->create();

        $formula = Formula::factory()->create();

        $response = $this->put(
            route('admin.formula.update', $formula),
            [
                'formula_name' => 'Formula Update',
                'materials' => [
                    [
                        'material_id' => $material->id,
                        'percentage' => 80
                    ]
                ]
            ]
        );

        $response->assertSessionHasErrors(
            'percentage'
        );
    }

    /** @test */
    public function formula_dapat_dinonaktifkan()
    {
        $formula = Formula::factory()->create([
            'is_active' => true
        ]);

        $response = $this->delete(
            route('admin.formula.destroy', $formula)
        );

        $response->assertSessionHas(
            'success'
        );

        $this->assertDatabaseHas(
            'formulas',
            [
                'id' => $formula->id,
                'is_active' => false
            ]
        );
    }

    /** @test */
    public function activity_log_dibuat_saat_formula_ditambahkan()
    {
        $material1 = Material::factory()->create();
        $material2 = Material::factory()->create();

        $this->post(
            route('admin.formula.store'),
            [
                'formula_name' => 'Formula Test',
                'materials' => [
                    [
                        'material_id' => $material1->id,
                        'percentage' => 50
                    ],
                    [
                        'material_id' => $material2->id,
                        'percentage' => 50
                    ]
                ]
            ]
        );

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'type' => 'formula_created',
                'module' => 'formula'
            ]
        );
    }

    /** @test */
    public function activity_log_dibuat_saat_formula_diupdate()
    {
        $material = Material::factory()->create();

        $formula = Formula::factory()->create();

        $this->put(
            route('admin.formula.update', $formula),
            [
                'formula_name' => 'Formula Update',
                'materials' => [
                    [
                        'material_id' => $material->id,
                        'percentage' => 100
                    ]
                ]
            ]
        );

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'type' => 'formula_updated',
                'module' => 'formula'
            ]
        );
    }

    /** @test */
    public function activity_log_dibuat_saat_formula_dinonaktifkan()
    {
        $formula = Formula::factory()->create();

        $this->delete(
            route('admin.formula.destroy', $formula)
        );

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'type' => 'formula_deactivated',
                'module' => 'formula'
            ]
        );
    }
}