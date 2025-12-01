<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kode_form', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode', 20);
            $table->string('nama', 100);
            $table->string('ket', 50);
            $table->text('intruksi')->nullable();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kode_form');
    }
};
