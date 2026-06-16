<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();

            // Related product
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // Remaining quantity in this batch
            $table->integer('quantity');

            // Stock source
            $table->enum('source', [
                'production',
                'purchase',
                'sale',
                'manual_adjustment'
            ]);

            // Reference ID (production_id, purchase_order_id, etc.)
            $table->unsignedBigInteger('reference_id')->nullable();

            // Date received into inventory
            $table->date('received_date');

            // Expiration date
            $table->date('expiration_date')->nullable();

            // Unit price (optional, useful for reporting)
            $table->decimal('price_per_unit', 12, 2)->nullable();

            $table->timestamps();

            // FIFO optimization index
            $table->index(['product_id', 'received_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};