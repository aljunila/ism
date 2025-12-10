<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // jenis: 1=admin perusahaan (previllage 2), 2=user kapal (previllage 3), 3=karyawan (previllage 4)
            $table->tinyInteger('jenis')->nullable()->after('is_superadmin');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }
};
