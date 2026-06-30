<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique();
            $table->string('product_name');
            $table->text('description')->nullable();
            // Selling price
            $table->decimal('selling_price', 12, 2)->nullable();
            // Finished product stock (kg)
            $table->decimal('stock', 10, 2)->default(0);
            // Reorder Point (ROP)
            $table->integer('reorder_point')
                ->default(0);
            // Related formula
            $table->foreignId('formula_id')
                ->nullable()
                ->constrained('formulas')
                ->nullOnDelete();
            // Product type
            $table->enum('category', ['feed', 'medicine']);
            // Product source
            $table->enum('source', ['production', 'purchase']);
            // Admin who created the product
            $table->foreignId('created_by')
                ->constrained('admins')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

