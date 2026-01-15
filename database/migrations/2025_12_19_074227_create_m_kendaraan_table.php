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
        Schema::create('m_kendaraan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode', 10)->nullable();
            $table->string('nama', 50)->nullable();
            $table->tinyInteger('is_delete')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_kendaraan');
    }
};
