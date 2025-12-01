<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('kode', 20);
            $table->integer('id_perusahaan')->nullable();
            $table->unsignedInteger('id_karyawan')->nullable();
            $table->integer('id_jabatan')->nullable();
            $table->integer('id_kapal');
            $table->date('date');
            $table->string('time', 20)->nullable();
            $table->text('ket')->nullable();
            $table->text('note')->nullable();
            $table->integer('id_mengetahui');
            $table->integer('id_mentor');
            $table->enum('status', ['A', 'D'])->default('A');
            $table->integer('created_by');
            $table->dateTime('created_date');
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_perusahaan');
            $table->index('id_karyawan');
            $table->index('id_jabatan');
            $table->index('id_kapal');
            $table->index('id_mengetahui');
            $table->index('id_mentor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_data');
    }
};
