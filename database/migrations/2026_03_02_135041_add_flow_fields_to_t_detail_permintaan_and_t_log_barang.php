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
            if (!Schema::hasColumn('t_detail_permintaan', 'procurement_channel')) {
                // workshop | po | purchasing
                $table->string('procurement_channel', 20)->nullable()->after('kode_po');
            }
            if (!Schema::hasColumn('t_detail_permintaan', 'flow_stage')) {
                // permintaan | logistik | po | purchasing | gudang | naik_kapal | selesai
                $table->string('flow_stage', 30)->nullable()->after('procurement_channel');
            }
        });

        Schema::table('t_log_barang', function (Blueprint $table) {
            if (!Schema::hasColumn('t_log_barang', 'event_code')) {
                $table->string('event_code', 50)->nullable()->after('status');
            }
            if (!Schema::hasColumn('t_log_barang', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('event_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_log_barang', function (Blueprint $table) {
            if (Schema::hasColumn('t_log_barang', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
            if (Schema::hasColumn('t_log_barang', 'event_code')) {
                $table->dropColumn('event_code');
            }
        });

        Schema::table('t_detail_permintaan', function (Blueprint $table) {
            if (Schema::hasColumn('t_detail_permintaan', 'flow_stage')) {
                $table->dropColumn('flow_stage');
            }
            if (Schema::hasColumn('t_detail_permintaan', 'procurement_channel')) {
                $table->dropColumn('procurement_channel');
            }
        });
    }
};
