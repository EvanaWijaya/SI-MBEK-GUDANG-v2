<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Production;
use App\Models\ProductionQc;
use App\Models\Admin;

class ProductionQcSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::query()->firstOrFail();

        $productions = Production::all();

        foreach ($productions as $production) {

            ProductionQc::create([
                'production_id' => $production->id,
                'non_critical_score' => 95,
                'threshold' => 80,
                'status' => 'passed',
                'notes' => 'Produk memenuhi standar quality control.',
                'created_by' => $admin->id,
            ]);
        }
    }
}
