<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Owner;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    public function run()
    {   
        // Owner
        Owner::create([
            'name' => 'Owner Peternakan',
            'email' => 'owner@peternakan.com',
            'password' => Hash::make('password'),
            'must_change_password' => true,
        ]);
    }
}