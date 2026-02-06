<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Drop foreign key dan kolom lama
            $table->dropForeign(['dicatat_oleh']);
            $table->dropColumn('dicatat_oleh');
            
            // Tambah kolom polymorphic baru
            $table->unsignedBigInteger('dicatat_oleh_id')->nullable()->after('dipesan_oleh_type');
            $table->string('dicatat_oleh_type')->nullable()->after('dicatat_oleh_id');
            
            // Index untuk performance
            $table->index(['dicatat_oleh_id', 'dicatat_oleh_type']);
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['dicatat_oleh_id', 'dicatat_oleh_type']);
            
            $table->unsignedBigInteger('dicatat_oleh')->nullable();
            $table->foreign('dicatat_oleh')
                  ->references('id')
                  ->on('admins');
        });
    }
};