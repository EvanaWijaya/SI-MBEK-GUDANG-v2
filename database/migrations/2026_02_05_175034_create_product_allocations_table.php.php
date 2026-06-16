<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_allocations', function (Blueprint $table) {
            $table->id();

            // Allocated product
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // Stock allocation type
            $table->enum('type', ['sale', 'internal']);

            // Allocated quantity
            $table->integer('quantity');

            // Admin who created the allocation
            $table->foreignId('created_by')
                ->constrained('admins')
                ->restrictOnDelete();

            $table->timestamps();

            // One product can only have one allocation per type
            $table->unique(['product_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_allocations');
    }
};