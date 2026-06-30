<?php

namespace Database\Factories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    protected $model = Material::class;

    public function definition(): array
    {
        $pemakaian = $this->faker->numberBetween(1, 20);
        $leadTime = $this->faker->numberBetween(1, 7);
        $safetyStock = $this->faker->numberBetween(0, 30);

        return [
            'material_name' => $this->faker->words(2, true),
            'category' => $this->faker->randomElement(['feed', 'medicine']),
            'unit' => $this->faker->randomElement(['kg', 'liter', 'pcs']),
            'stock' => 0,

            'average_usage' => $pemakaian,
            'lead_time' => $leadTime,
            'safety_stock' => $safetyStock,

            'description' => $this->faker->sentence(),
        ];
    }

    /**
     * STATE: stok berada di bawah ROP
     */
    public function belowRop(): static
    {
        return $this->state(function (array $attributes) {
            $rop = ($attributes['average_usage'] * $attributes['lead_time'])
                + $attributes['safety_stock'];

            return [
                'stock' => max(0, $rop - 1),
            ];
        });
    }
}
