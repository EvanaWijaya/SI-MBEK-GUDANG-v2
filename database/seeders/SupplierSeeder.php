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
                'supplier_name' => 'PT Pakan Ternak Nusantara',
                'contact' => '081234567890',
                'address' => 'Jalan tanjung bintang',
                'city' => 'Lampung Selatan',
                'province' => 'Lampung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supplier_name' => 'CV Obat Hewan Sejahtera',
                'contact' => '082233445566',
                'address' => 'Jalan Mawar',
                'city' => 'Palembang',
                'province' => 'Sumatera Selatan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
