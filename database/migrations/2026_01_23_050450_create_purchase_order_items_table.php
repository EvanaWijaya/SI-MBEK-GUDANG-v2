<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_po')->unique();
            $table->foreignId('supplier_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('tanggal_pesan');
            $table->enum('status', [
                'draft',
                'dipesan',
                'diterima',
                'dibatalkan'
            ])->default('draft');

            // owner & admin
            $table->morphs('dipesan_oleh');

            $table->foreignId('dicatat_oleh')
                ->constrained('admins');

            $table->text('catatan_owner')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
