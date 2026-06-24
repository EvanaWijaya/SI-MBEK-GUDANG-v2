<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('domba', function (Blueprint $table) {
            // Mengubah kolom agar boleh bernilai NULL
            $table->string('imageCaption')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('domba', function (Blueprint $table) {
            $table->string('imageCaption')->nullable(false)->change();
        });
    }
};