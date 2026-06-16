<?php

namespace Database\Factories;

use App\Models\Formula;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormulaFactory extends Factory
{
    protected $model = Formula::class;

    public function definition(): array
    {
        return [
            'formula_code' => 'FRM-' . strtoupper($this->faker->bothify('###??')),
            'formula_name' => 'Formula ' . $this->faker->word(),
            'description' => $this->faker->sentence(),
            'created_by' => Admin::factory(),
            'is_active' => true,
        ];
    }

    /**
     * State: formula non-aktif
     */
    public function inactive(): static
    {
        return $this->state(fn() => [
            'is_active' => false,
        ]);
    }
}
