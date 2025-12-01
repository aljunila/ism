<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('previllage', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 30);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('previllage');
    }
};
