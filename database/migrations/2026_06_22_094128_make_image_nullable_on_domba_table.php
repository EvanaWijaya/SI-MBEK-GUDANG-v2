<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('domba', function (Blueprint $table) {
            // Mengubah kolom image agar boleh bernilai NULL
            $table->string('image')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('domba', function (Blueprint $table) {
            $table->string('image')->nullable(false)->change();
        });
    }
};