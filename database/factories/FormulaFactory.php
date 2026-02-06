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
            'kode_formula' => 'FRM-' . strtoupper($this->faker->bothify('###??')),
            'nama_formula' => 'Formula ' . $this->faker->word(),
            'deskripsi' => $this->faker->sentence(),
            'created_by' => Admin::factory(),
            'is_active' => true,
        ];
    }

    /**
     * State: formula non-aktif
     */
    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
