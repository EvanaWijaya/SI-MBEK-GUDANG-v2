<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Production;
use App\Models\Product;
use App\Models\Admin;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::firstOrFail();

        $starter = Product::where(
            'product_code',
            'PKN-0001'
        )->firstOrFail();

        Production::create([
            'formula_id' => $starter->formula_id,
            'product_id' => $starter->id,
            'production_quantity' => 100,
            'qc_status' => 'passed',
            'qc_percentage' => 95,
            'qc_threshold' => 80,
            'production_date' => now()->subDays(10),
            'expiration_date' => now()->addMonths(6),
            'status' => 'completed',
            'created_by' => $admin->id,
        ]);
    }
}