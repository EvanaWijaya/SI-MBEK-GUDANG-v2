<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::insert([
            [
                'nama_supplier' => 'PT Pakan Ternak Nusantara',
                'kontak' => '081234567890',
                'alamat' => 'Jalan tanjung bintang',
                'kota' => 'Lampung Selatan',
                'provinsi' => 'Lampung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'CV Obat Hewan Sejahtera',
                'kontak' => '082233445566',
                'alamat' => 'Jalan Mawar',
                'kota' => 'Palembang',
                'provinsi' => 'Sumatera Selatan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
