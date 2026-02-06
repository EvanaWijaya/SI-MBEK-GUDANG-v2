<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AdminFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'admin', // default
            'must_change_password' => false,
        ];
    }

    // state khusus kalau dibutuhkan
    public function superAdmin()
    {
        return $this->state(fn () => [
            'role' => 'super_admin',
            'must_change_password' => false,
        ]);
    }
}
