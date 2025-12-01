<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_detail_508', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->integer('checklist_data_id');
            $table->string('nama', 50);
            $table->enum('status', ['A', 'D'])->default('A');
            $table->integer('created_by')->nullable();
            $table->dateTime('created_date')->nullable();
            $table->integer('changed_by')->nullable();
            $table->timestamp('changed_date')->useCurrent()->useCurrentOnUpdate();

            $table->index('checklist_data_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_detail_508');
    }
};
