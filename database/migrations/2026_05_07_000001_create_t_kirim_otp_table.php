<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_kirim_otp', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50)->unique();
            $table->unsignedInteger('id_penerima');
            $table->string('otp_code', 10);
            $table->dateTime('expires_at');
            $table->dateTime('used_at')->nullable();
            $table->unsignedInteger('id_kirim')->nullable();
            $table->tinyInteger('is_delete')->default(0);
            $table->string('created_by', 30)->nullable();
            $table->dateTime('created_date');
            $table->string('changed_by', 30)->nullable();
            $table->timestamp('changed_date')->nullable()->useCurrentOnUpdate();

            $table->index(['id_penerima', 'used_at']);
            $table->index(['created_by', 'id_penerima']);
            $table->index('otp_code');
            $table->index('id_kirim');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_kirim_otp');
    }
};
