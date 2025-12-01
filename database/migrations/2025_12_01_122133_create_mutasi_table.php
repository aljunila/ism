<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('kode', 30);
            $table->integer('dari_perusahaan');
            $table->integer('dari_kapal');
            $table->unsignedInteger('id_karyawan');
            $table->integer('id_jabatan');
            $table->date('tgl_naik');
            $table->date('tgl_turun');
            $table->integer('ke_perusahaan');
            $table->integer('ke_kapal');
            $table->text('ket')->nullable();
            $table->enum('status', ['A', 'D'])->default('A');
            $table->integer('created_by');
            $table->dateTime('created_date');
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_karyawan');
            $table->index('id_jabatan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi');
    }
};
