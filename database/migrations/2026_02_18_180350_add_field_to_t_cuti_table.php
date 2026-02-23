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
        Schema::table('t_cuti', function (Blueprint $table) {
             $table->unsignedInteger('id_kapal')->nullable()->after('id_jabatan');
            $table->json('data')->nullable()->after('id_kapal');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_cuti', function (Blueprint $table) {
            //
        });
    }
};
