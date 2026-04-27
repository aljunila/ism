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
        Schema::create('t_gudang', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->unsignedInteger('id_cabang');
            $table->unsignedInteger('id_barang');
            $table->integer('jumlah');
            $table->tinyInteger('is_delete')->default(0);
            $table->dateTime('changed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_t_gudang');
    }
};
