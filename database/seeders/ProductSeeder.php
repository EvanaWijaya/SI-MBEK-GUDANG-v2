<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Formula;
use App\Models\Admin;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::query()->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | AMBIL FORMULA DARI SEEDER
        |--------------------------------------------------------------------------
        */

        $formulaStarter = Formula::where(
            'formula_name',
            'Formula Starter'
        )->firstOrFail();

        $formulaGrower = Formula::where(
            'formula_name',
            'Formula Grower'
        )->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | PRODUK PAKAN (PRODUKSI)
        |--------------------------------------------------------------------------
        */

        Product::create([
            'product_code' => 'PKN-0001',
            'product_name' => 'Pakan Starter',
            'selling_price' => 85000,
            'stock' => 100,
            'reorder_point' => 20,
            'formula_id' => $formulaStarter->id,
            'category' => 'feed',
            'source' => 'production',
            'created_by' => $admin->id,
        ]);

        Product::create([
            'product_code' => 'PKN-0002',
            'product_name' => 'Pakan Grower',
            'selling_price' => 90000,
            'stock' => 80,
            'reorder_point' => 15,
            'formula_id' => $formulaGrower->id,
            'category' => 'feed',
            'source' => 'production',
            'created_by' => $admin->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | PRODUK OBAT (PEMBELIAN)
        |--------------------------------------------------------------------------
        */

        Product::create([
            'product_code' => 'OBT-0001',
            'product_name' => 'Vitamin C Ternak',
            'selling_price' => 45000,
            'stock' => 50,
            'reorder_point' => 10,
            'formula_id' => null,
            'category' => 'medicine',
            'source' => 'purchase',
            'created_by' => $admin->id,
        ]);

        Product::create([
            'product_code' => 'OBT-0002',
            'product_name' => 'Antibiotik Ternak',
            'selling_price' => 75000,
            'stock' => 40,
            'reorder_point' => 8,
            'formula_id' => null,
            'category' => 'medicine',
            'source' => 'purchase',
            'created_by' => $admin->id,
        ]);
    }
}