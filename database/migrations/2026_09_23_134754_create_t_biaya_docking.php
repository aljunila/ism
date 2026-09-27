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
        Schema::create('t_biaya_docking', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->unsignedInteger('id_docking');
            $table->string('id_subjob');
            $table->text('deskripsi');
            $table->string('unit');
            $table->decimal('volume', 10, 2)->nullable();
            $table->integer('harga');
            $table->biginteger('total');
            $table->text('keterangan');
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
        Schema::dropIfExists('t_biaya_docking');
    }
};
