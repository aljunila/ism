<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ganti_kkm', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('nomer', 20);
            $table->integer('id_kepada');
            $table->integer('id_perusahaan');
            $table->integer('id_kapal')->nullable();
            $table->date('tanggal');
            $table->string('jam', 20);
            $table->integer('id_lama');
            $table->string('fo', 30)->nullable();
            $table->string('do', 30)->nullable();
            $table->string('fw', 30)->nullable();
            $table->integer('id_baru');
            $table->enum('status', ['A', 'D']);
            $table->integer('created_by');
            $table->dateTime('created_date');
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_kepada');
            $table->index('id_perusahaan');
            $table->index('id_kapal');
            $table->index('id_lama');
            $table->index('id_baru');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ganti_kkm');
    }
};
