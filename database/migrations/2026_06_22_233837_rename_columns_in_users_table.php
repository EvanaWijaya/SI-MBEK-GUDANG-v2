<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        // Sistem bakal ngecek dulu, kalau kolom 'alamat' ada baru di-rename
        if (Schema::hasColumn('users', 'alamat')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('alamat', 'address');
            });
        }

        if (Schema::hasColumn('users', 'phone')) {
    Schema::table('users', function (Blueprint $table) {
        $table->renameColumn('phone', 'phone_number');
    });
    }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'address')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('address', 'alamat');
            });
        }

       if (Schema::hasColumn('users', 'phone')) {
    Schema::table('users', function (Blueprint $table) {
        $table->renameColumn('phone', 'phone_number');
    });
    }
    }
};