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
        Schema::table('purchase_orders', function (Blueprint $table) {
            // 🔥 Mengubah nama kolom dari purchase_order_code menjadi po_code
            $table->renameColumn('purchase_order_code', 'po_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Balikin lagi kalau misal sewaktu-waktu di-rollback
            $table->renameColumn('po_code', 'purchase_order_code');
        });
    }
};