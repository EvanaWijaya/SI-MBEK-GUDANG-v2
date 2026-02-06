<?php

namespace Database\Factories;

use App\Models\FormulaMaterial;
use App\Models\Formula;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormulaMaterialFactory extends Factory
{
    protected $model = FormulaMaterial::class;

    public function definition(): array
    {
        return [
            'formula_id' => Formula::factory(),
            'material_id' => Material::factory(),
            'qty_per_unit' => $this->faker->numberBetween(1, 10),
        ];
    }
}
