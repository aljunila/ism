<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('kode', 20);
            $table->integer('id_notulen');
            $table->string('agenda', 100);
            $table->string('ket', 100);
            $table->enum('status', ['A', 'D'])->default('A');
            $table->integer('created_by');
            $table->dateTime('created_date');
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_notulen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda');
    }
};
