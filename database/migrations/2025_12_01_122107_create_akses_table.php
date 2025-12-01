<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_karyawan');
            $table->integer('id_menu');

            $table->index('id_karyawan');
            $table->index('id_menu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akses');
    }
};
