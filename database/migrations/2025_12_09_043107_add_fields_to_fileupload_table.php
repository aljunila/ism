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
         Schema::table('file_upload', function (Blueprint $table) {
            $table->date('tgl_terbit')->nullable();
            $table->date('tgl_expired')->nullable();
            $table->string('no')->nullable();
            $table->string('penerbit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    
};
