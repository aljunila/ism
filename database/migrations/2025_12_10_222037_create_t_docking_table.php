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
        Schema::create('t_docking', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->integer('id_kapal');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->string('file', 100);
            $table->integer('is_delete');
            $table->string('created_by', 30);
            $table->dateTime('created_date');
            $table->string('changed_by', 30)->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_docking');
    }
};
