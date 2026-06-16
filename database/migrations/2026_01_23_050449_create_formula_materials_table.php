<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('formula_materials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('formula_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('material_id')
                ->constrained()
                ->restrictOnDelete();

            // Percentage of material composition in a formula (total = 100%)
            $table->decimal('percentage', 5, 2);

            $table->timestamps();

            // Prevent duplicate materials in the same formula
            $table->unique(['formula_id', 'material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formula_materials');
    }
};