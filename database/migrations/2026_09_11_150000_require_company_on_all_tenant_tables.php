<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RequireCompanyOnAllTenantTables extends Migration
{
    public function up()
    {
        $database = DB::connection()->getDatabaseName();
        $columns = DB::table('information_schema.COLUMNS as column_info')
            ->join('information_schema.TABLES as table_info', function ($join) {
                $join->on('table_info.TABLE_SCHEMA', '=', 'column_info.TABLE_SCHEMA')
                    ->on('table_info.TABLE_NAME', '=', 'column_info.TABLE_NAME');
            })->where('column_info.TABLE_SCHEMA', $database)->where('table_info.TABLE_TYPE', 'BASE TABLE')
            ->where('column_info.COLUMN_NAME', 'id_empresa')->where('column_info.IS_NULLABLE', 'YES')
            ->orderBy('column_info.TABLE_NAME')->get(['column_info.TABLE_NAME', 'column_info.COLUMN_TYPE',
                'column_info.CHARACTER_SET_NAME', 'column_info.COLLATION_NAME']);

        $unresolved = [];
        $globalTables = (array) config('tenancy.global_tables', []);
        $hybridTables = (array) config('tenancy.hybrid_company_tables', []);
        foreach ($columns as $column) {
            if (in_array((string) $column->TABLE_NAME, $globalTables, true)
                || in_array((string) $column->TABLE_NAME, $hybridTables, true)) {
                continue;
            }
            $missing = DB::table($column->TABLE_NAME)->whereNull('id_empresa')
                ->orWhereRaw("TRIM(id_empresa) = ''")->count();
            if ($missing > 0) {
                $unresolved[] = $column->TABLE_NAME.' ('.$missing.')';

                continue;
            }
            $definition = $column->COLUMN_TYPE;
            if ($column->CHARACTER_SET_NAME) {
                $definition .= ' CHARACTER SET '.$column->CHARACTER_SET_NAME;
            }
            if ($column->COLLATION_NAME) {
                $definition .= ' COLLATE '.$column->COLLATION_NAME;
            }
            DB::statement('ALTER TABLE `'.str_replace('`', '``', $column->TABLE_NAME)
                .'` MODIFY `id_empresa` '.$definition.' NOT NULL');
        }
        if ($unresolved) {
            throw new RuntimeException('No se puede hacer obligatorio id_empresa. Tablas con filas sin empresa: '
                .implode(', ', $unresolved).'.');
        }
    }

    public function down()
    {
        // Irreversible deliberadamente: permitir NULL restauraría la fuga de
        // datos que esta migración convierte en un error visible y seguro.
    }
}
