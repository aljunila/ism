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
            $table->integer('status')->after('id_pengganti');
            $table->unsignedInteger('approved_by')->nullable();
            $table->date('approved_date')->nullable();
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
