<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        Material::insert([
            [
                'material_name' => 'Jagung Giling',
                'category' => 'feed',
                'unit' => 'kg',
                'stock' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_name' => 'Dedak Halus',
                'category' => 'feed',
                'unit' => 'kg',
                'stock' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'material_name' => 'Daun Lamtoro',
                'category' => 'feed',
                'unit' => 'kg',
                'stock' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
