<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();

            $table->string('po_code')->unique();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['material', 'product'])->default('material');
            $table->date('order_date');
            $table->enum('status', [
                'draft',
                'ordered',
                'received',
                'cancelled'
            ])->default('draft');
            // Owner or Admin who placed the order
            $table->morphs('ordered_by');
            // Owner or Admin who recorded the order
            $table->morphs('recorded_by');
            $table->text('notes')->nullable();
            $table->date('approved_date')->nullable();
            $table->date('received_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};

