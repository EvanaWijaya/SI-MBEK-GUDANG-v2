<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Formula;
use App\Models\Admin;

class FormulaSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first();

        Formula::firstOrCreate(
            ['formula_code' => 'FRM-001'],
            [
                'formula_name' => 'Formula Starter',
                'description' => 'Formula starter',
                'created_by' => $admin->id,
                'is_active' => true,
            ]
        );

        Formula::firstOrCreate(
            ['formula_code' => 'FRM-002'],
            [
                'formula_name' => 'Formula Grower',
                'description' => 'Formula grower',
                'created_by' => $admin->id,
                'is_active' => true,
            ]
        );
    }
}
