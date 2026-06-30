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
        Schema::create('media', function (Blueprint $table) {
            $table->id();

            // polymorphic relation
            $table->morphs('mediable');

            $table->string('file_name');
            $table->text('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->enum('type', ['image','document',])->default('image');
            $table->boolean('is_primary')
                ->default(false);
            $table->unsignedSmallInteger('sort_order')
                ->default(0);
            $table->timestamps();
            $table->index([
                'mediable_id',
                'mediable_type',
                'is_primary'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};