<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('kode', 15);
            $table->integer('id_perusahaan');
            $table->integer('id_kapal');
            $table->string('no_review', 30);
            $table->date('tgl_review');
            $table->date('tgl_diterima')->nullable();
            $table->text('hasil');
            $table->text('ket')->nullable();
            $table->integer('id_nahkoda')->nullable();
            $table->integer('id_dpa')->nullable();
            $table->enum('status', ['A', 'D']);
            $table->integer('created_by')->nullable();
            $table->dateTime('created_date');
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_perusahaan');
            $table->index('id_kapal');
            $table->index('id_nahkoda');
            $table->index('id_dpa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review');
    }
};
