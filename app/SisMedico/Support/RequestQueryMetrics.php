<?php

namespace Sis_medico\Support;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;

/** Contadores por solicitud; nunca conserva SQL ni valores clínicos en el registro. */
class RequestQueryMetrics
{
    const ATTRIBUTE = 'aitrol.query_metrics';
    private static $dispatchers;
    public $count = 0;
    public $time = 0.0;
    public $duplicates = 0;
    public $slow = [];
    public $duplicateSamples = [];
    public $truncated = false;
    private $seen = [];

    public static function start($request)
    {
        $metrics = new self;
        $request->attributes->set(self::ATTRIBUTE, $metrics);
        $dispatcher = app('events');
        self::$dispatchers = self::$dispatchers ?: new \WeakMap;
        if (!isset(self::$dispatchers[$dispatcher])) {
            self::$dispatchers[$dispatcher] = true;
            $dispatcher->listen(QueryExecuted::class, static function ($query) {
                $request = app()->bound('request') ? app('request') : null;
                $metrics = $request ? $request->attributes->get(self::ATTRIBUTE) : null;
                if ($metrics instanceof self) $metrics->record($query);
            });
            $dispatcher->listen([TransactionBeginning::class, TransactionCommitted::class, TransactionRolledBack::class], static function () {
                $request = app()->bound('request') ? app('request') : null;
                $metrics = $request ? $request->attributes->get(self::ATTRIBUTE) : null;
                if ($metrics instanceof self) $metrics->seen = [];
            });
        }
        return $metrics;
    }

    private function record($query)
    {
        $this->count++;
        $duration = (float) $query->time;
        $this->time += $duration;
        $fingerprint = hash('sha256', (string) $query->sql);
        if ($duration >= (float) config('performance.slow_query_ms', 250)
            && count($this->slow) < (int) config('performance.slow_query_sample_limit', 20)) {
            $this->slow[] = ['duration_ms' => round($duration, 2), 'fingerprint' => $fingerprint,
                'connection' => $query->connectionName ?: 'default'];
        }
        // Dos consultas con parámetros distintos no son duplicados exactos.
        // Tras escribir, una nueva lectura puede ser necesaria para ver el cambio.
        if (!preg_match('/^\s*(select|show|describe|desc|explain)\b/i', $query->sql)) {
            $this->seen = [];
            return;
        }
        $key = hash('sha256', serialize([spl_object_id($query->connection),
            $query->connection->getDatabaseName(), $query->sql, $query->bindings]));
        if (isset($this->seen[$key])) {
            $this->duplicates++;
            if (isset($this->duplicateSamples[$fingerprint])
                || count($this->duplicateSamples) < (int) config('performance.slow_query_sample_limit', 20)) {
                $this->duplicateSamples[$fingerprint] = ($this->duplicateSamples[$fingerprint] ?? 0) + 1;
            }
        } elseif (count($this->seen) < max(1, (int) config('performance.query_fingerprint_limit', 2048))) {
            $this->seen[$key] = true;
        } else {
            $this->truncated = true;
        }
    }
}
