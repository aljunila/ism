<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_upload', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_perusahaan')->nullable();
            $table->integer('id_kapal')->nullable();
            $table->unsignedInteger('id_karyawan')->nullable();
            $table->integer('id_file');
            $table->string('file', 100);
            $table->enum('status', ['A', 'D']);
            $table->integer('created_by');
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_perusahaan');
            $table->index('id_kapal');
            $table->index('id_karyawan');
            $table->index('id_file');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_upload');
    }
};
