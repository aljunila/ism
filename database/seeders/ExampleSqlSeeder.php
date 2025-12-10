<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExampleSqlSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $sql = file_get_contents(database_path('seeders/example_inserts.sql'));
        $statements = array_filter(array_map('trim', preg_split('/;\\s*\\n/', $sql)));

        $tableStatements = [];
        foreach ($statements as $statement) {
            $statement = str_ends_with($statement, ';') ? $statement : $statement . ';';

            if (preg_match('/INSERT INTO `([^`]+)`/i', $statement, $matches)) {
                $table = $matches[1];
                $tableStatements[$table][] = $statement;
            }
        }

        // Bersihkan tabel-tabel target agar seeding idempotent
        foreach (array_keys($tableStatements) as $table) {
            DB::table($table)->truncate();
        }

        $orderedTables = [
            'status_ptkp',
            'previllage',
            'roles',
            'perusahaan',
            'jabatan',
            'kapal',
            'karyawan',
            'user',
            'menu',
        ];

        foreach ($orderedTables as $table) {
            if (isset($tableStatements[$table])) {
                DB::unprepared(implode(PHP_EOL, $tableStatements[$table]));
                unset($tableStatements[$table]);
            }
        }

        foreach ($tableStatements as $statementsForTable) {
            DB::unprepared(implode(PHP_EOL, $statementsForTable));
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
