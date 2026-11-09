<?php

namespace Sis_medico\Support;

use Closure;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Lecturas explícitas por petición, invalidadas al escribir sus dependencias. */
class RequestQueryCache
{
    private const ATTRIBUTE = 'aitrol.request_query_cache';
    private static $dispatchers;

    public static function enabled()
    {
        $request = app()->bound('request') ? app('request') : null;
        // El instalador y los comandos pueden crear tablas/datos y volver a
        // comprobarlos dentro de la misma ejecución. Nunca memorizar ese flujo.
        if (app()->runningInConsole() || !$request instanceof Request
            || $request->is('instalacion', 'instalacion/*', 'configuracion-inicial', 'configuracion-inicial/*')) {
            return false;
        }

        // Las listas y búsquedas también usan POST. Las escrituras y los
        // cambios de transacción invalidan las lecturas, independientemente
        // del verbo HTTP o del controlador que las ejecute.
        return true;
    }

    // Declarar todas las tablas leídas, incluidas relaciones y dependencias
    // indirectas. Sin dependencias explícitas, cualquier escritura invalida.
    public static function remember($key, Closure $read, $connection = null, $schema = false, array $tables = [])
    {
        if (!self::enabled()) {
            return $read();
        }

        $request = app('request');
        $connection = $connection ?: DB::connection();
        $dispatcher = $connection->getEventDispatcher();
        // Sin eventos no es posible garantizar la invalidación tras guardar.
        if (!$dispatcher || $connection->pretending()) {
            return $read();
        }
        self::listen($dispatcher);
        $attribute = self::ATTRIBUTE.($schema ? '.schema' : '.data');
        $generation = $request->attributes->get($attribute.'.generation', 0);
        $key = json_encode([
            spl_object_id($connection), $connection->getDatabaseName(),
            $connection->getTablePrefix(), $key,
            $schema || !$request->hasSession() ? null : $request->session()->get('id_empresa'),
        ]);
        $values = $request->attributes->get($attribute, []);
        if (array_key_exists($key, $values)) {
            return $values[$key]['value'];
        }

        $value = $read();
        // Una lectura compuesta puede provocar escrituras o una transacción.
        // No guardar un resultado calculado antes de esa invalidación.
        if ($request->attributes->get($attribute.'.generation', 0) !== $generation) {
            return $value;
        }
        // La lectura puede haber memorizado otras consultas anidadas.
        $values = $request->attributes->get($attribute, []);
        $values[$key] = ['value' => $value, 'tables' => $tables];
        $request->attributes->set($attribute, $values);

        return $value;
    }

    private static function listen($dispatcher)
    {
        self::$dispatchers = self::$dispatchers ?: new \WeakMap;
        if (isset(self::$dispatchers[$dispatcher])) return;
        self::$dispatchers[$dispatcher] = true;

        // Un listener por dispatcher, sin capturar solicitudes ni conexiones.
        $dispatcher->listen(QueryExecuted::class, static function ($query) {
            if ($query->connection->pretending()) return;
            if (preg_match('/^\s*(select|show|describe|desc|explain)\b/i', $query->sql)) return;
            // DML no cambia columnas/tablas. DDL, SQL con comentarios, CTE,
            // procedimientos y sentencias desconocidas invalidan todo.
            $schema = !preg_match('/^\s*(insert|update|delete|replace)\b/i', $query->sql);
            $table = null;
            if (!$schema && strpos($query->sql, ';') === false
                && preg_match('/^\s*(?:insert\s+into|replace\s+into|update|delete\s+from)\s+[`"\[]?([A-Za-z0-9_]+)[`"\]]?\s*(?=\(|set\b|where\b|values\b|$)/i', $query->sql, $match)) {
                $table = $match[1];
                $prefix = $query->connection->getTablePrefix();
                if ($prefix !== '' && strpos($table, $prefix) === 0) $table = substr($table, strlen($prefix));
            }
            self::forget($schema, $table);
        });
        $dispatcher->listen([TransactionBeginning::class, TransactionCommitted::class], static function () {
            self::forget(false);
        });
        $dispatcher->listen(TransactionRolledBack::class, static function () {
            self::forget(true);
        });
    }

    public static function forget($schema = true, $writtenTable = null)
    {
        $request = app()->bound('request') ? app('request') : null;
        if (!$request instanceof Request) return;
        foreach ($schema ? ['.data', '.schema'] : ['.data'] as $group) {
            $attribute = self::ATTRIBUTE.$group;
            if (!$schema && $writtenTable !== null) {
                $values = $request->attributes->get($attribute, []);
                foreach ($values as $key => $entry) {
                    // Solo las lecturas con dependencias declaradas sobreviven
                    // a escrituras ajenas (p. ej., la auditoría de cada paciente).
                    if (!$entry['tables'] || in_array(strtolower($writtenTable), array_map('strtolower', $entry['tables']), true)) {
                        unset($values[$key]);
                    }
                }
                $request->attributes->set($attribute, $values);
            } else {
                $request->attributes->remove($attribute);
            }
            $request->attributes->set($attribute.'.generation', $request->attributes->get($attribute.'.generation', 0) + 1);
        }
    }
}
