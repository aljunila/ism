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
        Schema::create('m_biaya_penumpang', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_pelabuhan')->nullable();
            $table->string('kelas')->nullable();
            $table->unsignedInteger('id_kendaraan')->nullable();
            $table->float('nominal')->nullable();
            $table->tinyInteger('is_delete')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_biaya_penumpang');
    }
};
