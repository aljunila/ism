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
        Schema::create('m_pelabuhan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_cabang')->nullable();
            $table->string('nama', 30)->nullable();
            $table->tinyInteger('is_delete')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_pelabuhan');
    }
};
