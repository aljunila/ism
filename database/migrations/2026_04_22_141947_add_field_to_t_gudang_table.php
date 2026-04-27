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
        Schema::table('t_gudang', function (Blueprint $table) {
            $table->integer('baik')->nullable()->after('jumlah');
            $table->integer('habis')->nullable()->after('baik');
            $table->string('keterangan')->nullable()->after('habis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_gudang', function (Blueprint $table) {
            //
        });
    }
};
