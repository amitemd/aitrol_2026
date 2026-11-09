<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IndexAllTenantCompanyColumns extends Migration
{
    public function up()
    {
        $database = DB::connection()->getDatabaseName();
        $tables = DB::table('information_schema.COLUMNS')->where('TABLE_SCHEMA', $database)
            ->where('COLUMN_NAME', 'id_empresa')->orderBy('TABLE_NAME')->pluck('TABLE_NAME');
        foreach ($tables as $table) {
            $table = (string) $table;
            $indexed = DB::table('information_schema.STATISTICS')->where('TABLE_SCHEMA', $database)
                ->where('TABLE_NAME', $table)->where('COLUMN_NAME', 'id_empresa')->exists();
            if ($indexed) {
                continue;
            }
            Schema::table($table, function (Blueprint $definition) {
                $definition->index('id_empresa', 'mt_id_empresa_idx');
            });
        }
    }

    public function down()
    {
        $database = DB::connection()->getDatabaseName();
        $tables = DB::table('information_schema.STATISTICS')->where('TABLE_SCHEMA', $database)
            ->where('INDEX_NAME', 'mt_id_empresa_idx')->pluck('TABLE_NAME');
        foreach ($tables as $table) {
            Schema::table((string) $table, function (Blueprint $definition) {
                $definition->dropIndex('mt_id_empresa_idx');
            });
        }
    }
}
