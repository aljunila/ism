<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_kirim_barang', function (Blueprint $table) {
            if (!Schema::hasColumn('t_kirim_barang', 'id_penerima')) {
                $table->unsignedInteger('id_penerima')->nullable()->after('id_kapal');
                $table->index('id_penerima');
            }

            if (!Schema::hasColumn('t_kirim_barang', 'otp_code')) {
                $table->string('otp_code', 10)->nullable()->after('id_penerima');
            }

            if (!Schema::hasColumn('t_kirim_barang', 'otp_verified_at')) {
                $table->dateTime('otp_verified_at')->nullable()->after('otp_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('t_kirim_barang', function (Blueprint $table) {
            if (Schema::hasColumn('t_kirim_barang', 'id_penerima')) {
                $table->dropIndex(['id_penerima']);
            }

            if (Schema::hasColumn('t_kirim_barang', 'otp_verified_at')) {
                $table->dropColumn('otp_verified_at');
            }

            if (Schema::hasColumn('t_kirim_barang', 'otp_code')) {
                $table->dropColumn('otp_code');
            }

            if (Schema::hasColumn('t_kirim_barang', 'id_penerima')) {
                $table->dropColumn('id_penerima');
            }
        });
    }
};
