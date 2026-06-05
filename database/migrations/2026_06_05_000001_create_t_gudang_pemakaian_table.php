<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_gudang_pemakaian', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_gudang');
            $table->integer('qty');
            $table->date('tanggal');
            $table->string('keterangan', 255)->nullable();
            $table->string('created_by', 100)->nullable();
            $table->dateTime('created_date')->nullable();

            $table->index('id_gudang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_gudang_pemakaian');
    }
};
