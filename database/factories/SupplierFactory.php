<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'supplier_name' => fake()->company(),
            'contact' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'province' => fake()->state(),
        ];
    }
}

