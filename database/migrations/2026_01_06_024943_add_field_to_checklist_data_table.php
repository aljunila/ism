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
        Schema::table('checklist_data', function (Blueprint $table) {
           $table->json('data')->nullable()->after('time');  
           $table->json('keterangan')->nullable()->after('data');
           $table->json('pj')->nullable()->after('note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklist_data', function (Blueprint $table) {
            
        });
    }
};
