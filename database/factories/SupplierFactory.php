<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_supplier' => fake()->company(),
            'kontak' => fake()->phoneNumber(),
            'alamat' => fake()->address(),
            'kota' => fake()->city(),
            'provinsi' => fake()->state(),
        ];
    }
}

