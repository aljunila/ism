<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Normalisasi data yang memakai 0 sebagai placeholder kapal pada kolom nullable
        foreach (['refrensi_doc', 'file_upload', 'ganti_kkm', 'sampah', 'user', 'karyawan', 'minyak_bekas', 'daftar_hadir', 'pelatihan', 'notulen', 'peta'] as $table) {
            DB::statement("UPDATE `{$table}` SET `id_kapal` = NULL WHERE `id_kapal` = 0");
        }

        $perusahaanColumns = [
            ['table' => 'bbm', 'nullable' => false],
            ['table' => 'checklist_data', 'nullable' => true],
            ['table' => 'checklist_penggantian', 'nullable' => false],
            ['table' => 'daftar_hadir', 'nullable' => false],
            ['table' => 'file_upload', 'nullable' => true],
            ['table' => 'form_aturan', 'nullable' => false],
            ['table' => 'ganti_kkm', 'nullable' => false],
            ['table' => 'interview', 'nullable' => false],
            ['table' => 'karyawan', 'nullable' => false],
            ['table' => 'minyak_bekas', 'nullable' => false],
            ['table' => 'notulen', 'nullable' => false],
            ['table' => 'pelatihan', 'nullable' => false],
            ['table' => 'periode_kondite', 'nullable' => false],
            ['table' => 'peta', 'nullable' => false],
            ['table' => 'prosedur', 'nullable' => false, 'default' => 1],
            ['table' => 'refrensi_doc', 'nullable' => false],
            ['table' => 'review', 'nullable' => false],
            ['table' => 'sampah', 'nullable' => false],
            ['table' => 'user', 'nullable' => false],
        ];

        foreach ($perusahaanColumns as $column) {
            $nullClause = $column['nullable'] ? 'NULL' : 'NOT NULL';
            $defaultClause = array_key_exists('default', $column) ? ' DEFAULT ' . (int) $column['default'] : '';
            DB::statement("ALTER TABLE `{$column['table']}` MODIFY `id_perusahaan` INT UNSIGNED {$nullClause}{$defaultClause}");

            Schema::table($column['table'], function (Blueprint $table) {
                $table->foreign('id_perusahaan')->references('id')->on('perusahaan');
            });
        }

        $kapalColumns = [
            ['table' => 'bbm', 'nullable' => false],
            ['table' => 'checklist_data', 'nullable' => false],
            ['table' => 'checklist_penggantian', 'nullable' => false],
            ['table' => 'daftar_hadir', 'nullable' => true],
            ['table' => 'file_upload', 'nullable' => true],
            ['table' => 'ganti_kkm', 'nullable' => true],
            ['table' => 'karyawan', 'nullable' => true],
            ['table' => 'minyak_bekas', 'nullable' => true],
            ['table' => 'notulen', 'nullable' => true],
            ['table' => 'pelatihan', 'nullable' => true],
            ['table' => 'periode_kondite', 'nullable' => false],
            ['table' => 'peta', 'nullable' => true],
            ['table' => 'refrensi_doc', 'nullable' => true],
            ['table' => 'review', 'nullable' => false],
            ['table' => 'sampah', 'nullable' => true],
            ['table' => 'user', 'nullable' => true],
        ];

        foreach ($kapalColumns as $column) {
            $nullClause = $column['nullable'] ? 'NULL' : 'NOT NULL';
            DB::statement("ALTER TABLE `{$column['table']}` MODIFY `id_kapal` INT UNSIGNED {$nullClause}");

            Schema::table($column['table'], function (Blueprint $table) {
                $table->foreign('id_kapal')->references('id')->on('kapal');
            });
        }
    }

    public function down(): void
    {
        $perusahaanTables = [
            'bbm',
            'checklist_data',
            'checklist_penggantian',
            'daftar_hadir',
            'file_upload',
            'form_aturan',
            'ganti_kkm',
            'interview',
            'karyawan',
            'minyak_bekas',
            'notulen',
            'pelatihan',
            'periode_kondite',
            'peta',
            'prosedur',
            'refrensi_doc',
            'review',
            'sampah',
            'user',
        ];

        foreach ($perusahaanTables as $table) {
            Schema::table($table, function (Blueprint $tableObj) {
                $tableObj->dropForeign(['id_perusahaan']);
            });
        }

        $kapalTables = [
            'bbm',
            'checklist_data',
            'checklist_penggantian',
            'daftar_hadir',
            'file_upload',
            'ganti_kkm',
            'karyawan',
            'minyak_bekas',
            'notulen',
            'pelatihan',
            'periode_kondite',
            'peta',
            'refrensi_doc',
            'review',
            'sampah',
            'user',
        ];

        foreach ($kapalTables as $table) {
            Schema::table($table, function (Blueprint $tableObj) {
                $tableObj->dropForeign(['id_kapal']);
            });
        }
    }
};
