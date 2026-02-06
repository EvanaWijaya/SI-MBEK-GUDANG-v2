<?php

namespace Database\Factories;

use App\Models\Production;
use App\Models\Formula;
use App\Models\Product;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductionFactory extends Factory
{
    protected $model = Production::class;

    public function definition(): array
    {
        return [
            'formula_id'   => Formula::factory(),
            'product_id'   => Product::factory(),
            'qty_produksi' => $this->faker->numberBetween(50, 500),
            'qty_qc_lulus' => 0,
            'qty_qc_gagal' => 0,
            'status'       => 'diproses',
            'created_by'   => Admin::factory(),
        ];
    }

    /**
     * Produksi sudah selesai + QC
     */
    public function selesai(): static
    {
        return $this->state(function (array $attr) {
            $lulus = (int) ($attr['qty_produksi'] * 0.9);

            return [
                'qty_qc_lulus' => $lulus,
                'qty_qc_gagal' => $attr['qty_produksi'] - $lulus,
                'status'       => 'selesai',
            ];
        });
    }
}
