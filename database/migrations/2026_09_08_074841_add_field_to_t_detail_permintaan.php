<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('t_detail_permintaan', function (Blueprint $table) {
            $table->string('del_reason')->nullable()->after('is_delete');
            $table->string('delete_by',30)->nullable()->after('changed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_detail_permintaan', function (Blueprint $table) {
            //
        });
    }
};
