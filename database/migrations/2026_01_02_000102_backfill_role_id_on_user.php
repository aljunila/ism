<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('user')
            ->whereNull('role_id')
            ->whereNotNull('id_previllage')
            ->update(['role_id' => DB::raw('id_previllage')]);
    }

    public function down(): void
    {
        // Tidak perlu rollback data
    }
};
