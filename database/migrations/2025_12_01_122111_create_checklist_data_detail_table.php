<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_data_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->string('kode', 15);
            $table->integer('checklist_data_id')->nullable();
            $table->integer('bbm_id')->nullable();
            $table->integer('checklist_item_id');
            $table->integer('value');
            $table->text('ket')->nullable();
            $table->enum('status', ['A', 'D'])->default('A');
            $table->integer('created_by');
            $table->timestamp('created_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('checklist_data_id');
            $table->index('bbm_id');
            $table->index('checklist_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_data_detail');
    }
};
