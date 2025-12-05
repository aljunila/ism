<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode', 30)->unique();
            $table->string('nama', 50);
            $table->enum('status', ['A', 'D'])->default('A');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
