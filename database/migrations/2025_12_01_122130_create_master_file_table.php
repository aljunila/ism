<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_file', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['P', 'K', 'S']);
            $table->string('nama', 50);
            $table->string('ket', 50)->nullable();
            $table->enum('status', ['A', 'D']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_file');
    }
};
