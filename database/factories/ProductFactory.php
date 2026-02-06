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
            'kode' => 'PRD-' . strtoupper(Str::random(6)),
            'nama' => $this->faker->words(2, true),
            'stok' => 0, // stok nambah dari produksi
            'formula_id' => Formula::factory(),
            'created_by' => Admin::factory(),
        ];
    }
}
