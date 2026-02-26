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
        Schema::create('t_detail_permintaan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->unsignedInteger('id_permintaan');
            $table->unsignedInteger('id_barang');
            $table->integer('jumlah');
            $table->integer('status');
            $table->tinyInteger('is_delete')->default(0);
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
        Schema::dropIfExists('t_detail_permintaan');
    }
};
