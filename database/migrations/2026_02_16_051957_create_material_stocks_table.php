<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('material_stocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('material_id')
                ->constrained()
                ->cascadeOnDelete();

            // Quantity received in the batch
            $table->integer('quantity');

            // Date the material was received
            $table->date('received_date');

            // Material expiration date
            $table->date('expiration_date')->nullable();

            // Unit price (optional, useful for reporting)
            $table->decimal('price_per_unit', 12, 2)->nullable();

            // Admin who recorded the stock
            $table->foreignId('created_by')
                ->constrained('admins');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_stocks');
    }
};