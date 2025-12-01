<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_hadir_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->integer('id_daftar_hadir')->nullable();
            $table->unsignedInteger('id_karyawan');
            $table->integer('id_jabatan');
            $table->date('tanggal')->nullable();
            $table->enum('status', ['A', 'D'])->default('A');
            $table->integer('created_by');
            $table->dateTime('created_date');
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_daftar_hadir');
            $table->index('id_karyawan');
            $table->index('id_jabatan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_hadir_detail');
    }
};
