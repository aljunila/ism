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
        Schema::create('t_kriteria_kru', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_perusahaan')->nullable();
            $table->unsignedInteger('id_jabatan')->nullable();
            $table->integer('kriteria');
            $table->string('des');
            $table->tinyInteger('is_delete')->default(0);
            $table->integer('created_by')->nullable();
            $table->datetime('created_date');
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_kriteria_kru');
    }
};
