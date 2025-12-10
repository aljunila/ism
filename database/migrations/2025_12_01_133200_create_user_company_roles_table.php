<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_company_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('perusahaan_id');
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('id_kapal')->nullable();
            $table->enum('status', ['A', 'D'])->default('A');

            $table->index('user_id');
            $table->index('perusahaan_id');
            $table->index('role_id');
            $table->index('id_kapal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_company_roles');
    }
};
