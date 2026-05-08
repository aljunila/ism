<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_notifikasi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50)->unique();
            $table->unsignedInteger('id_user');
            $table->string('tipe', 50)->default('info');
            $table->string('judul', 150);
            $table->text('pesan')->nullable();
            $table->string('url', 255)->nullable();
            $table->dateTime('read_at')->nullable();
            $table->tinyInteger('is_delete')->default(0);
            $table->string('created_by', 30)->nullable();
            $table->dateTime('created_date');
            $table->string('changed_by', 30)->nullable();
            $table->timestamp('changed_date')->nullable()->useCurrentOnUpdate();

            $table->index(['id_user', 'read_at']);
            $table->index(['id_user', 'is_delete']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_notifikasi');
    }
};
