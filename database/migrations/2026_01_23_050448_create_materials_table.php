<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();

            $table->string('material_name');

            $table->enum('category', ['feed', 'medicine']);

            $table->string('unit');

            $table->integer('stock')->default(0);

            $table->decimal('average_usage', 10, 2)
                ->default(0);

            $table->integer('lead_time')
                ->default(0);

            $table->integer('safety_stock')
                ->default(5);

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};