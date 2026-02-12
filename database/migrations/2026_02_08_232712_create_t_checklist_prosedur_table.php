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
        Schema::create('t_checklist_prosedur', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_kapal')->nullable();
            $table->unsignedInteger('id_karyawan')->nullable();
            $table->unsignedInteger('id_jabatan')->nullable();
            $table->unsignedInteger('id_prosedur')->nullable();
            $table->datetime('last_seen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_checklist_prosedur');
    }
};
