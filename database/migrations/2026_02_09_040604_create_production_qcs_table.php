<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('production_qcs', function (Blueprint $table) {
            $table->id();

            // Relationship to production (1 production = 1 QC)
            $table->foreignId('production_id')
                ->constrained()
                ->cascadeOnDelete()
                ->unique();

            // Final QC status
            $table->enum('status', ['passed', 'failed']);

            // Percentage of fulfilled non-critical indicators
            $table->decimal('non_critical_score', 5, 2)
                ->comment('Percentage of passed non-critical indicators');

            // Passing threshold (default 80%, configurable by admin)
            $table->decimal('threshold', 5, 2)
                ->default(80);

            // Additional QC notes
            $table->text('notes')->nullable();

            // Admin who performed QC
            $table->foreignId('created_by')
                ->constrained('admins');

            $table->timestamps(); // created_at = QC timestamp
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_qcs');
    }
};