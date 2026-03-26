<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('m_status_barang', function (Blueprint $table) {
            if (!Schema::hasColumn('m_status_barang', 'flag_permintaan')) {
                $table->tinyInteger('flag_permintaan')->default(0)->after('nama');
            }
            if (!Schema::hasColumn('m_status_barang', 'flag_proses')) {
                $table->tinyInteger('flag_proses')->default(0)->after('flag_permintaan');
            }
            if (!Schema::hasColumn('m_status_barang', 'flag_berlangsung')) {
                $table->tinyInteger('flag_berlangsung')->default(0)->after('flag_proses');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_status_barang', function (Blueprint $table) {
            if (Schema::hasColumn('m_status_barang', 'flag_berlangsung')) {
                $table->dropColumn('flag_berlangsung');
            }
            if (Schema::hasColumn('m_status_barang', 'flag_proses')) {
                $table->dropColumn('flag_proses');
            }
            if (Schema::hasColumn('m_status_barang', 'flag_permintaan')) {
                $table->dropColumn('flag_permintaan');
            }
        });
    }
};
