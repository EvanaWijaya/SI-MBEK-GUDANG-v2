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
        $productionDate = $this->faker->dateTimeBetween('-1 month', 'now');

        return [
            'formula_id' => Formula::factory(),
            'product_id' => Product::factory(),
            'production_quantity' => $this->faker->numberBetween(10, 100),

            // ======================
            // QC SUMMARY
            // ======================
            'qc_status' => 'pending',
            'qc_percentage' => null,
            'qc_threshold' => 80,

            // ======================
            // TANGGAL
            // ======================
            'production_date' => $productionDate->format('Y-m-d'),

            // Expired 6 bulan setelah produksi (contoh realistis pakan/obat)
            'expiration_date' => (clone $productionDate)
                ->modify('+6 months')
                ->format('Y-m-d'),

            // ======================
            // STATUS PRODUKSI
            // ======================
            'status' => 'progress',

            // Admin pembuat
            'created_by' => Admin::factory(),
        ];
    }

    /**
     * QC Layak
     */
    public function qcLayak(
        float $percentage = 100,
        float $threshold = 80
    ): static {
        return $this->state(fn() => [
            'qc_status' => 'passed',
            'qc_percentage' => $percentage,
            'qc_threshold' => $threshold,
        ]);
    }

    /**
     * QC Tidak Layak
     */
    public function qcTidakLayak(
        float $percentage = 50,
        float $threshold = 80
    ): static {
        return $this->state(fn() => [
            'qc_status' => 'pending',
            'qc_percentage' => $percentage,
            'qc_threshold' => $threshold,
        ]);
    }

    /**
     * Produksi selesai
     */
    public function selesai(): static
    {
        return $this->state(fn() => [
            'status' => 'completed',
        ]);
    }
}
