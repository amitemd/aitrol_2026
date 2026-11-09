<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class LegacySchemaInstaller
{
    /**
     * Create a small group of legacy tables and return measurable progress.
     * DDL is intentionally split into short requests to avoid web timeouts.
     */
    public function installBatch($offset, $batchSize = 10)
    {
        $statements = $this->statements();
        $total = count($statements);
        $offset = max(0, min((int) $offset, $total));
        $limit = min($offset + max(1, (int) $batchSize), $total);
        $processed = array();
        $normalized = array();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('SET UNIQUE_CHECKS=0');

        try {
            for ($index = $offset; $index < $limit; $index++) {
                $statement = $statements[$index];
                $table = $statement['table'];

                if (!empty($statement['normalized'])) {
                    $normalized[] = $table;
                }

                if ($table !== 'migrations' && !Schema::hasTable($table)) {
                    DB::unprepared($statement['sql']);
                }

                $processed[] = $table;
            }
        } catch (\Exception $exception) {
            $failedTable = isset($table) ? $table : 'desconocida';
            throw new \RuntimeException(
                'No se pudo crear la tabla ' . $failedTable . ' en ' . $this->databaseVersion() . ': ' . $exception->getMessage(),
                0,
                $exception
            );
        } finally {
            DB::statement('SET UNIQUE_CHECKS=1');
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        return array(
            'offset' => $limit,
            'total' => $total,
            'processed' => $processed,
            'normalized' => $normalized,
            'finished' => $limit >= $total,
        );
    }

    private function statements()
    {
        $path = database_path('schema/aitrol_legacy_structure.sql');
        if (!is_file($path)) {
            throw new \RuntimeException('No se encontró el esquema base: ' . $path);
        }

        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new \RuntimeException('No se pudo leer el esquema base.');
        }

        preg_match_all('/CREATE TABLE `([^`]+)`\s*\([\s\S]*?\) ENGINE=.*?;/i', $sql, $matches, PREG_SET_ORDER);
        if (count($matches) < 800) {
            throw new \RuntimeException('El esquema base está incompleto; se esperaban al menos 800 tablas.');
        }

        $statements = array();
        foreach ($matches as $match) {
            $normalizedSql = LegacySchemaSqlNormalizer::normalize($match[0]);
            $statements[] = array(
                'table' => $match[1],
                'sql' => $normalizedSql,
                'normalized' => $normalizedSql !== $match[0],
            );
        }

        return $statements;
    }

    /**
     * Return a safe engine label to make installation failures actionable.
     *
     * @return string
     */
    private function databaseVersion()
    {
        try {
            $row = DB::selectOne('SELECT VERSION() AS version');
            return 'MySQL/MariaDB ' . (isset($row->version) ? $row->version : 'desconocido');
        } catch (\Exception $exception) {
            return 'MySQL/MariaDB de versión desconocida';
        }
    }
}
