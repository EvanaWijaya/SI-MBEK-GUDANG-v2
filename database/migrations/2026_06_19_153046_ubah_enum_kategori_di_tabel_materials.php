<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buka gerbang ENUM untuk menampung bahasa Inggris dan Indonesia sementara
        DB::statement("ALTER TABLE materials MODIFY COLUMN category ENUM('pakan', 'obat', 'feed', 'medicine') NOT NULL");

        // 2. Ubah otomatis data lama yang nyangkut di database dari Bindo ke Inggris
        DB::table('materials')->where('category', 'pakan')->update(['category' => 'feed']);
        DB::table('materials')->where('category', 'obat')->update(['category' => 'medicine']);

        // 3. Kunci ENUM-nya secara permanen hanya untuk bahasa Inggris
        DB::statement("ALTER TABLE materials MODIFY COLUMN category ENUM('feed', 'medicine') NOT NULL");
    }

    public function down(): void
    {
        // Logika sebaliknya (Rollback) jika sewaktu-waktu mau dibalikin ke Bindo
        DB::statement("ALTER TABLE materials MODIFY COLUMN category ENUM('pakan', 'obat', 'feed', 'medicine') NOT NULL");
        DB::table('materials')->where('category', 'feed')->update(['category' => 'pakan']);
        DB::table('materials')->where('category', 'medicine')->update(['category' => 'obat']);
        DB::statement("ALTER TABLE materials MODIFY COLUMN category ENUM('pakan', 'obat') NOT NULL");
    }
};