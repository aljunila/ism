<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kondite', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->integer('id_periode');
            $table->unsignedInteger('id_karyawan');
            $table->integer('id_jabatan');
            $table->date('tgl_nilai')->nullable();
            $table->string('rekomendasi', 50)->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['A', 'D'])->default('A');
            $table->integer('id_penilai_1')->nullable();
            $table->integer('id_penilai_2')->nullable();
            $table->integer('id_mengetahui')->nullable();
            $table->integer('created_by')->nullable();
            $table->date('created_date')->nullable();
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_periode');
            $table->index('id_karyawan');
            $table->index('id_jabatan');
            $table->index('id_penilai_1');
            $table->index('id_penilai_2');
            $table->index('id_mengetahui');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kondite');
    }
};
