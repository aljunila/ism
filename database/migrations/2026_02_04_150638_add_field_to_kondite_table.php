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
        Schema::rename('kondite', 't_kondite');

        Schema::table('t_kondite', function (Blueprint $table) {
            $table->json('data')->nullable()->after('tgl_nilai');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kondite', function (Blueprint $table) {
            //
        });
    }
};
