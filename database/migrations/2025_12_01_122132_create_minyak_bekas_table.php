<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('minyak_bekas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('kode', 30);
            $table->integer('id_perusahaan');
            $table->integer('id_kapal')->nullable();
            $table->date('tanggal');
            $table->integer('jml_drum');
            $table->string('pelabuhan', 50);
            $table->integer('id_pj');
            $table->string('penerima', 100);
            $table->enum('status', ['A', 'D'])->default('A');
            $table->integer('created_by');
            $table->dateTime('created_date');
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_perusahaan');
            $table->index('id_kapal');
            $table->index('id_pj');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('minyak_bekas');
    }
};
