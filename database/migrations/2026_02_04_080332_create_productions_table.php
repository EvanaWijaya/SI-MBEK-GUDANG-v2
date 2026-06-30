<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            // Formula used for production
            $table->foreignId('formula_id')->constrained()->cascadeOnDelete();
            // Finished product
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            // Production quantity (kg)
            $table->integer('production_quantity');
            //QC RESULT (SUMMARY)
            $table->enum('qc_status', ['pending','passed','failed',])->default('pending');
            $table->decimal('qc_percentage', 5, 2)->nullable()->comment('Percentage of passed non-critical indicators');
            $table->decimal('qc_threshold', 5, 2)->default(80)->comment('Minimum QC passing percentage');
            // Actual production date
            $table->date('production_date');
            // Expiration date
            $table->date('expiration_date')->nullable();
            //PRODUCTION STATUS
            $table->enum('status', ['progress','completed','rejected',])->default('progress');
            // Recorded by admin
            $table->foreignId('created_by')
                ->constrained('admins');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};

