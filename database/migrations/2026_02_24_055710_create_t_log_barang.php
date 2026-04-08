<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_log_barang', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->unsignedInteger('id_detail_permintaan');
            $table->integer('status');
            $table->date('tanggal');
            $table->string('img', 100);
            $table->tinyInteger('is_delete')->default(0);
            $table->string('created_by', 30);
            $table->dateTime('created_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_log_barang');
    }
};
