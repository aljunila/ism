<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_hadir', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('kode', 20);
            $table->integer('id_perusahaan');
            $table->integer('id_kapal')->nullable();
            $table->integer('id_notulen')->nullable();
            $table->date('date')->nullable();
            $table->string('tempat', 30)->nullable();
            $table->enum('status', ['A', 'D']);
            $table->integer('created_by');
            $table->dateTime('created_date');
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_perusahaan');
            $table->index('id_kapal');
            $table->index('id_notulen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_hadir');
    }
};
