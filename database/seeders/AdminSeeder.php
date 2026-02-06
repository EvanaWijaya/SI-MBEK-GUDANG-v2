<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Super Admin
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@peternakan.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'must_change_password' => false,
        ]);
        
        // Admin Biasa
        Admin::create([
            'name' => 'Admin Biasa',
            'email' => 'admin@peternakan.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'must_change_password' => true, // wajib ganti password
        ]);
    }
}