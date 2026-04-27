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
        Schema::table('m_kel_barang', function (Blueprint $table) {
            $table->string('kode', 50)->nullable()->after('kategori');
            $table->string('ket', 100)->nullable()->after('kode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_kel_barang', function (Blueprint $table) {
            //
        });
    }
};
