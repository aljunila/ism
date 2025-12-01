<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 50);
            $table->text('password')->nullable();
            $table->string('nama', 50);
            $table->text('pic')->nullable();
            $table->unsignedInteger('id_previllage')->nullable()->default(4);
            $table->enum('status', ['A', 'D'])->default('A');
            $table->unsignedInteger('id_karyawan');
            $table->integer('id_perusahaan');
            $table->integer('id_kapal')->nullable();
            $table->string('created_by', 35)->nullable();
            $table->dateTime('created_date')->useCurrent();
            $table->string('changed_by', 35)->nullable();
            $table->timestamp('changed_date')->nullable();

            $table->index('id_previllage');
            $table->index('id_karyawan');
            $table->index('id_perusahaan');
            $table->index('id_kapal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
