<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('nama', 50);
            $table->bigInteger('nik');
            $table->string('nip', 20)->nullable();
            $table->string('telp', 20)->nullable();
            $table->string('email', 50)->nullable();
            $table->text('alamat')->nullable();
            $table->string('tmp_lahir', 30)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->enum('jk', ['P', 'W'])->nullable()->default('P');
            $table->string('agama', 20)->nullable();
            $table->string('gol_darah', 10)->nullable();
            $table->string('status_kawin', 20)->nullable();
            $table->integer('status_ptkp')->nullable();
            $table->string('pend', 10)->nullable();
            $table->string('institusi_pend', 50)->nullable();
            $table->string('jurusan', 30)->nullable();
            $table->string('sertifikat', 50)->nullable();
            $table->enum('status_karyawan', ['TP', 'TC', 'K', 'F', 'M'])->nullable();
            $table->string('nama_bank', 30)->nullable();
            $table->string('nama_rekening', 50)->nullable();
            $table->bigInteger('no_rekening')->nullable();
            $table->string('cabang_bank', 30)->nullable();
            $table->bigInteger('npwp')->nullable();
            $table->bigInteger('bpjs_kes')->nullable();
            $table->bigInteger('bpjs_tk')->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->integer('id_jabatan');
            $table->integer('id_perusahaan');
            $table->integer('id_kapal')->nullable();
            $table->string('tanda_tangan', 50)->nullable();
            $table->string('foto', 50)->nullable();
            $table->enum('status', ['A', 'D'])->default('A');
            $table->enum('resign', ['Y', 'N'])->default('N');
            $table->integer('created_by');
            $table->dateTime('created_date');
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_jabatan');
            $table->index('id_perusahaan');
            $table->index('id_kapal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
