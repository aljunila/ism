<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reset_password', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_user');
            $table->dateTime('tgl_ajuan');
            $table->dateTime('tgl_reset')->nullable();
            $table->enum('status', ['N', 'R'])->default('N');

            $table->index('id_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reset_password');
    }
};
