<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Formula;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'product_code' => 'PRD-' . strtoupper(Str::random(6)),
            'product_name' => $this->faker->words(2, true),
            'selling_price' => $this->faker->numberBetween(10000, 150000),
            'stock' => 0,
            'reorder_point' => $this->faker->numberBetween(5, 20),

            // default jadi pakan produksi
            'category' => 'feed',
            'source' => 'production',

            'formula_id' => Formula::factory(),
            'created_by' => Admin::factory(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | STATE: PAKAN
    |--------------------------------------------------------------------------
    */
    public function pakan(): static
    {
        return $this->state(fn() => [
            'category' => 'feed',
            'source' => 'production',
            'formula_id' => Formula::factory(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STATE: OBAT
    |--------------------------------------------------------------------------
    */
    public function obat(): static
    {
        return $this->state(fn() => [
            'category' => 'medicine',
            'source' => 'purchase',
            'formula_id' => null,
        ]);
    }
}