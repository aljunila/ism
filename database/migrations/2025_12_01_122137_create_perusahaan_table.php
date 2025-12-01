<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perusahaan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('nama', 100);
            $table->string('kode', 10);
            $table->text('alamat');
            $table->string('email', 50);
            $table->string('telp', 20);
            $table->integer('direktur')->nullable();
            $table->integer('npwp');
            $table->integer('nib');
            $table->string('logo', 100)->nullable();
            $table->enum('status', ['A', 'D'])->default('A');

            $table->index('direktur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perusahaan');
    }
};
