<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kode_form', function (Blueprint $table) {
            $table->boolean('is_delete')->default(0)->after('intruksi');
        });
    }

    public function down(): void
    {
        Schema::table('kode_form', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
    }
};
