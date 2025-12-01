<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_penggantian', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('kode', 10);
            $table->integer('id_perusahaan');
            $table->integer('id_kapal');
            $table->integer('id_dari');
            $table->integer('id_kepada');
            $table->string('pelabuhan', 30);
            $table->date('date');
            $table->string('jam', 15);
            $table->text('note')->nullable();
            $table->enum('status', ['A', 'D']);
            $table->integer('created_by');
            $table->dateTime('created_date');
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_perusahaan');
            $table->index('id_kapal');
            $table->index('id_dari');
            $table->index('id_kepada');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_penggantian');
    }
};
