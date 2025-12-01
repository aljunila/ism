<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notulen', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('kode', 10);
            $table->integer('id_perusahaan');
            $table->integer('id_kapal')->nullable();
            $table->date('tanggal');
            $table->string('tempat', 50);
            $table->string('hal', 30);
            $table->text('materi');
            $table->integer('id_nahkoda');
            $table->integer('id_dpa')->nullable();
            $table->integer('id_notulen');
            $table->enum('status', ['A', 'D']);
            $table->integer('created_by');
            $table->dateTime('created_date');
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_perusahaan');
            $table->index('id_kapal');
            $table->index('id_nahkoda');
            $table->index('id_dpa');
            $table->index('id_notulen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notulen');
    }
};
