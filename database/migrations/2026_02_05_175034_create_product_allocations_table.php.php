<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_allocations', function (Blueprint $table) {
            $table->id();

            // Produk yang dialokasikan
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // Jenis alokasi stok
            $table->enum('type', ['jual', 'internal']);

            // Jumlah stok dialokasikan
            $table->integer('qty');

            // Admin yang mengatur alokasi
            $table->foreignId('created_by')
                ->constrained('admins')
                ->restrictOnDelete();

            $table->timestamps();

            // 1 produk hanya boleh punya 1 alokasi per type
            $table->unique(['product_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_allocations');
    }
};