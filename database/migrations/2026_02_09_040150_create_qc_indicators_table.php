<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('qc_indicators', function (Blueprint $table) {
            $table->id();

            // QC indicator name
            $table->string('name');

            // Whether the indicator is critical
            $table->boolean('is_critical')
                ->default(false);

            // Indicator active status
            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qc_indicators');
    }
};