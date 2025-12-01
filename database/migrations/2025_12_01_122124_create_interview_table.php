<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interview', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('kode', 10);
            $table->integer('id_perusahaan');
            $table->string('nama', 50);
            $table->integer('id_jabatan');
            $table->text('note')->nullable();
            $table->date('tgl_periksa')->nullable();
            $table->integer('id_periksa')->nullable();
            $table->integer('id_menyetujui')->nullable();
            $table->enum('status', ['A', 'D']);
            $table->integer('created_by');
            $table->dateTime('created_date');
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_perusahaan');
            $table->index('id_jabatan');
            $table->index('id_periksa');
            $table->index('id_menyetujui');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview');
    }
};
