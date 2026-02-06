<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {

            $table->decimal('pemakaian_rata_rata', 10, 2)
                ->default(0)
                ->after('stok');

            $table->integer('lead_time')
                ->default(0)
                ->after('pemakaian_rata_rata');

            $table->integer('safety_stock')
                ->default(5)
                ->after('lead_time');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn([
                'pemakaian_rata_rata',
                'lead_time',
                'safety_stock',
            ]);
        });
    }
};
