<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 50);
            $table->string('kode', 30);
            $table->string('link', 50);
            $table->string('icon', 50)->nullable();
            $table->integer('id_parent')->default(0);
            $table->integer('no');
            $table->enum('menu_user', ['Y', 'N'])->default('N');
            $table->enum('status', ['A', 'D'])->default('A');

            $table->index('id_parent');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
