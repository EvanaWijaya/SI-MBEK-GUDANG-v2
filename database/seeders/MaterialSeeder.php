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
                'nama_bahan' => 'Jagung Giling',
                'kategori' => 'pakan',
                'satuan' => 'kg',
                'stok' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Dedak Halus',
                'kategori' => 'pakan',
                'satuan' => 'kg',
                'stok' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Vitamin Ternak A',
                'kategori' => 'obat',
                'satuan' => 'botol',
                'stok' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
