<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_login', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('perusahaan_id')->nullable();
            $table->unsignedInteger('id_kapal')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->dateTime('last_login_at')->nullable();
            $table->dateTime('access_token_expires_at')->nullable();
            $table->dateTime('refresh_token_expires_at')->nullable();
            $table->string('device', 100)->nullable();
            $table->string('platform', 50)->nullable();

            $table->index('user_id');
            $table->index('perusahaan_id');
            $table->index('id_kapal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_login');
    }
};
