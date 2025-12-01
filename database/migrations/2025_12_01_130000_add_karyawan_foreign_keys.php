<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akses', function (Blueprint $table) {
            $table->foreign('id_karyawan')->references('id')->on('karyawan');
        });

        Schema::table('checklist_data', function (Blueprint $table) {
            $table->foreign('id_karyawan')->references('id')->on('karyawan');
        });

        Schema::table('file_upload', function (Blueprint $table) {
            $table->foreign('id_karyawan')->references('id')->on('karyawan');
        });

        Schema::table('daftar_hadir_detail', function (Blueprint $table) {
            $table->foreign('id_karyawan')->references('id')->on('karyawan');
        });

        Schema::table('mutasi', function (Blueprint $table) {
            $table->foreign('id_karyawan')->references('id')->on('karyawan');
        });

        Schema::table('kondite', function (Blueprint $table) {
            $table->foreign('id_karyawan')->references('id')->on('karyawan');
        });

        Schema::table('pelatihan', function (Blueprint $table) {
            $table->foreign('id_karyawan')->references('id')->on('karyawan');
        });

        Schema::table('user', function (Blueprint $table) {
            $table->foreign('id_karyawan')->references('id')->on('karyawan');
        });
    }

    public function down(): void
    {
        Schema::table('akses', function (Blueprint $table) {
            $table->dropForeign(['id_karyawan']);
        });

        Schema::table('checklist_data', function (Blueprint $table) {
            $table->dropForeign(['id_karyawan']);
        });

        Schema::table('file_upload', function (Blueprint $table) {
            $table->dropForeign(['id_karyawan']);
        });

        Schema::table('daftar_hadir_detail', function (Blueprint $table) {
            $table->dropForeign(['id_karyawan']);
        });

        Schema::table('mutasi', function (Blueprint $table) {
            $table->dropForeign(['id_karyawan']);
        });

        Schema::table('kondite', function (Blueprint $table) {
            $table->dropForeign(['id_karyawan']);
        });

        Schema::table('pelatihan', function (Blueprint $table) {
            $table->dropForeign(['id_karyawan']);
        });

        Schema::table('user', function (Blueprint $table) {
            $table->dropForeign(['id_karyawan']);
        });
    }
};
