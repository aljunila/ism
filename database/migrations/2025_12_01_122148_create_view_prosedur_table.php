<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('view_prosedur', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_user');
            $table->integer('id_prosedur');
            $table->integer('jml_lihat')->default(0);
            $table->integer('jml_download')->default(0);
            $table->dateTime('update_lihat')->nullable();
            $table->dateTime('update_download')->nullable();

            $table->index('id_user');
            $table->index('id_prosedur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('view_prosedur');
    }
};
