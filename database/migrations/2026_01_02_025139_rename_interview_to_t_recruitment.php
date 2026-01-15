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
        Schema::rename('interview', 't_recruitment');

        Schema::table('t_recruitment', function (Blueprint $table) {
            $table->string('alamat', 100)->nullable()->after('nama');
            $table->string('telp', 20)->nullable()->after('alamat');
            $table->tinyInteger('is_delete')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       
    }
};
