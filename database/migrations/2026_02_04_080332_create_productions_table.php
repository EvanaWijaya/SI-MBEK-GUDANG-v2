<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();

            // Formula yang diproduksi
            $table->foreignId('formula_id')
                ->constrained()
                ->cascadeOnDelete();

            // Produk jadi (material hasil produksi)
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Jumlah produksi
            $table->integer('qty_produksi');      // kg direncanakan
            $table->integer('qty_qc_lulus')->default(0);
            $table->integer('qty_qc_gagal')->default(0);

            // Status produksi
            $table->enum('status', [
                'diproses',
                'selesai',
            ])->default('diproses');

            // Dicatat oleh admin
            $table->foreignId('created_by')
                ->constrained('admins');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
