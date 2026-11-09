<?php

namespace Sis_medico\Support;

use Illuminate\Support\Facades\DB;

/** Inspección de tablas/columnas compartida por los controles de una petición. */
class RequestSchema
{
    public static function hasTable($table)
    {
        $connection = DB::connection();

        return RequestQueryCache::remember('schema.table:'.$table, function () use ($connection, $table) {
            $driver = $connection->getDriverName();
            if (RequestQueryCache::enabled() && strpos($table, '.') === false
                && in_array($driver, ['mysql', 'mariadb', 'sqlite'], true)) {
                // Solo nombres: evita pedir tamaños, índices o estadísticas de todas las tablas.
                $tables = RequestQueryCache::remember('schema.table_names', function () use ($connection, $driver) {
                    $rows = $driver === 'sqlite'
                        ? $connection->select("select name from main.sqlite_master where type = 'table'")
                        : $connection->select("select table_name as name from information_schema.tables where table_schema = ? and table_type in ('BASE TABLE', 'SYSTEM VERSIONED')", [$connection->getDatabaseName()]);
                    $names = [];
                    foreach ($rows as $row) {
                        $name = $row->name;
                        $names[$driver === 'sqlite' ? $name : strtolower($name)] = true;
                    }
                    return $names;
                }, $connection, true);
                $name = $connection->getTablePrefix().$table;
                return isset($tables[$driver === 'sqlite' ? $name : strtolower($name)]);
            }
            return $connection->getSchemaBuilder()->hasTable($table);
        }, $connection, true);
    }

    public static function hasColumn($table, $column)
    {
        $columns = array_map('strtolower', self::getColumnListing($table));
        return in_array(strtolower($column), $columns, true);
    }

    public static function getColumnListing($table)
    {
        $connection = DB::connection();
        return RequestQueryCache::remember('schema.columns:'.$table, function () use ($connection, $table) {
            return $connection->getSchemaBuilder()->getColumnListing($table);
        }, $connection, true);
    }

    public static function hasColumns($table, array $columns)
    {
        return !array_diff(array_map('strtolower', $columns), array_map('strtolower', self::getColumnListing($table)));
    }
}
