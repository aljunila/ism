<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluasi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->integer('checklist_data_id');
            $table->string('ket', 100)->nullable();
            $table->text('note')->nullable();
            $table->date('tanggal');
            $table->integer('id_nahkoda');
            $table->integer('id_instruktur');
            $table->integer('id_kepala')->nullable();
            $table->enum('status', ['A', 'D']);
            $table->integer('created_by');
            $table->dateTime('created_date');
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('checklist_data_id');
            $table->index('id_nahkoda');
            $table->index('id_instruktur');
            $table->index('id_kepala');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi');
    }
};
