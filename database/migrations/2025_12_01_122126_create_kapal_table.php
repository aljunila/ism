<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kapal', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('nama', 30)->nullable();
            $table->string('pendaftaran', 30)->nullable();
            $table->string('no_siup', 30)->nullable();
            $table->string('no_akte', 30)->nullable();
            $table->string('dikeluarkan_di', 30)->nullable();
            $table->string('selar', 30)->nullable();
            $table->integer('pemilik');
            $table->string('call_sign', 30)->nullable();
            $table->string('galangan', 30)->nullable();
            $table->string('konstruksi', 30)->nullable();
            $table->string('type', 30)->nullable();
            $table->float('loa')->nullable();
            $table->float('lbp')->nullable();
            $table->float('lebar')->nullable();
            $table->float('dalam')->nullable();
            $table->float('summer_draft')->nullable();
            $table->float('winter_draft')->nullable();
            $table->float('draft_air_tawar')->nullable();
            $table->float('tropical_draft')->nullable();
            $table->float('isi_kotor')->nullable();
            $table->float('bobot_mati')->nullable();
            $table->float('nt')->nullable();
            $table->string('merk_mesin_induk', 75)->nullable();
            $table->integer('tahun_mesin_induk')->nullable();
            $table->string('no_mesin_induk', 30)->nullable();
            $table->string('merk_mesin_bantu', 75)->nullable();
            $table->integer('tahun_mesin_bantu')->nullable();
            $table->string('no_mesin_bantu', 30)->nullable();
            $table->float('max_speed')->nullable();
            $table->float('normal_speed')->nullable();
            $table->float('min_speed')->nullable();
            $table->string('bahan_bakar', 30)->nullable();
            $table->integer('jml_butuh')->nullable();
            $table->string('berkas', 100)->nullable();
            $table->enum('status', ['A', 'D']);
            $table->string('created_by', 30);
            $table->date('created_date');
            $table->string('changed_by', 30)->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('pemilik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kapal');
    }
};
