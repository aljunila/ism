<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->integer('kel');
            $table->string('nama', 30);
            $table->enum('status', ['A', 'D']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatan');
    }
};
