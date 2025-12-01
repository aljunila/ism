<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_kondite', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('kode', 10);
            $table->integer('id_perusahaan');
            $table->integer('id_kapal');
            $table->string('bulan', 5);
            $table->integer('tahun');
            $table->enum('status', ['A', 'D']);
            $table->integer('created_by')->nullable();
            $table->dateTime('created_date')->nullable();
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_perusahaan');
            $table->index('id_kapal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_kondite');
    }
};
