<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table) {
            // ubah enum status menjadi tinyint
            DB::statement("ALTER TABLE `user` MODIFY `status` TINYINT(1) NOT NULL DEFAULT 1");
            $table->tinyInteger('is_delete')->default(0)->after('status');
            $table->dateTime('active_at')->nullable()->after('is_delete');
            $table->dateTime('unactive_at')->nullable()->after('active_at');
            $table->dateTime('deleted_at')->nullable()->after('unactive_at');
            $table->integer('deleted_by')->nullable()->after('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn(['is_delete', 'active_at', 'unactive_at', 'deleted_at', 'deleted_by']);
            DB::statement("ALTER TABLE `user` MODIFY `status` ENUM('A','D') NOT NULL DEFAULT 'A'");
        });
    }
};
