<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prosedur', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->integer('id_perusahaan')->default(1);
            $table->string('kode', 25);
            $table->string('judul', 100);
            $table->string('no_dokumen', 30)->nullable();
            $table->string('edisi', 20)->nullable();
            $table->date('tgl_terbit');
            $table->string('status_manual', 50)->nullable();
            $table->text('cover')->nullable();
            $table->text('isi')->nullable();
            $table->integer('prepered_by');
            $table->integer('enforced_by');
            $table->enum('status', ['A', 'D'])->default('A');
            $table->text('file')->nullable();
            $table->string('url', 10)->nullable();
            $table->string('created_by', 30);
            $table->dateTime('created_date');
            $table->string('changed_by', 30)->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_perusahaan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prosedur');
    }
};
