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
            $table->integer('id_menu')->nullable()->after('id_perusahaan');
            $table->string('group', 20)->nullable()->after('link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kode_form', function (Blueprint $table) {
            $table->integer('id_perusahaan')->nullable();
        });
    }
};
