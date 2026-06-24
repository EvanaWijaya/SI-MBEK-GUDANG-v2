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
        Schema::create('domba', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->date('tanggal_lahir')->nullable();

            $table->string('name');

            $table->integer('age')->nullable();
            $table->integer('age_now')->nullable();

            $table->decimal('weight_now', 8, 2)->nullable();

            $table->enum('jenis_kelamin', [
                'Jantan',
                'Betina'
            ]);

            $table->string('image')->nullable();

            $table->enum('for_sale', [
                'yes',
                'no'
            ])->default('no');

            $table->string('imageCaption')->nullable();

            $table->enum('type_domba', [
                'Garut',
                'Ekor Gemuk',
                'Ekor Tipis',
                'Texel',
                'Dorper'
            ]);

            $table->decimal('weight', 8, 2)->nullable();

            $table->string('faksin_status', 50)->nullable();

            $table->string('healt_status')->nullable();

            $table->bigInteger('harga')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domba');
    }
};