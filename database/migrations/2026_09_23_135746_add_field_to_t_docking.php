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
        Schema::table('t_docking', function (Blueprint $table) {
            $table->string('bulan')->nullable()->after('id_kapal');
            $table->integer('tahun')->nullable()->after('bulan');
            $table->string('tempat')->nullable()->after('tahun');
            $table->integer('durasi')->nullable()->after('tgl_selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_docking', function (Blueprint $table) {
            //
        });
    }
};
