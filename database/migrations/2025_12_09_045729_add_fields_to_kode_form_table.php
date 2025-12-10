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
        Schema::table('kode_form', function (Blueprint $table) {
            $table->string('pj')->nullable();
            $table->string('kode_file')->nullable();
            $table->string('periode')->nullable();
            $table->integer('id_perusahaan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
   
};
