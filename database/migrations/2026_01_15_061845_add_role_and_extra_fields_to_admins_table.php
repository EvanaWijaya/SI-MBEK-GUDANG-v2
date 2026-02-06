<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Tambah kolom role (untuk bedain super_admin vs admin)
            $table->enum('role', ['super_admin', 'admin'])
                  ->default('admin')
                  ->after('email_verified_at');
            
            // Tambah kolom tambahan (opsional, untuk konsistensi dengan owner)
            $table->string('phone', 20)->nullable()->after('password');
            $table->string('profile_picture')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'profile_picture']);
        });
    }
};