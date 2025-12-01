<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_aturan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('kode', 30);
            $table->integer('id_perusahaan');
            $table->string('nama', 100);
            $table->text('isi')->nullable();
            $table->integer('enforced_by');
            $table->string('file', 100)->nullable();
            $table->enum('publish', ['Y', 'N'])->default('Y');
            $table->enum('status', ['A', 'D'])->default('A');
            $table->integer('created_by');
            $table->dateTime('created_date');
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_perusahaan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_aturan');
    }
};
