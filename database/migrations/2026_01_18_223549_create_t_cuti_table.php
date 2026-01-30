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
        Schema::create('t_cuti', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->unsignedInteger('id_perusahaan');
            $table->unsignedInteger('id_karyawan');
            $table->unsignedInteger('id_jabatan')->nullable();
            $table->unsignedInteger('id_m_cuti');
            $table->string('note')->nullable();
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->integer('jml_hari');
            $table->unsignedInteger('id_pengganti')->nullable();
            $table->tinyInteger('is_delete')->default(0);
            $table->integer('created_by')->nullable();
            $table->datetime('created_date');
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_cuti');
    }
};
