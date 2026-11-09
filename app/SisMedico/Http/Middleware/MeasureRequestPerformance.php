<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Sis_medico\Support\RequestQueryMetrics;

class MeasureRequestPerformance
{
    public function handle($request, Closure $next)
    {
        if (!config('performance.enabled', true) || !$this->monitored($request)) {
            return $next($request);
        }

        $startedAt = microtime(true);
        $memoryAtStart = memory_get_usage(true);
        $metrics = RequestQueryMetrics::start($request);
        $response = null;
        $failed = false;

        try {
            $response = $next($request);
            return $response;
        } catch (\Throwable $exception) {
            $failed = true;
            throw $exception;
        } finally {
            $duration = (microtime(true) - $startedAt) * 1000;
            $memoryDelta = max(0, memory_get_peak_usage(true) - $memoryAtStart);
            $sample = array(
                'timestamp' => date('c'),
                'method' => $request->method(),
                'path' => $request->path(),
                'route' => $request->route() ? $request->route()->getName() : null,
                'duration_ms' => round($duration, 2),
                'query_count' => $metrics->count,
                'query_time_ms' => round($metrics->time, 2),
                'duplicate_query_count' => $metrics->duplicates,
                'duplicate_query_fingerprints' => $metrics->duplicateSamples,
                'query_fingerprints_truncated' => $metrics->truncated,
                'memory_peak_delta_mb' => round($memoryDelta / 1048576, 2),
                'status' => $response && method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null,
                'failed' => $failed,
                'slow_queries' => $metrics->slow,
            );

            if (config('performance.log_all', false)
                || $failed
                || $duration >= (float) config('performance.slow_request_ms', 1200)
                || $metrics->duplicates >= (int) config('performance.duplicate_query_threshold', 10)
                || count($metrics->slow) > 0) {
                $this->append($sample);
            }

            if ($response && config('performance.response_headers', true) && isset($response->headers)) {
                $response->headers->set('Server-Timing', 'app;dur='.round($duration, 2).', db;dur='.round($metrics->time, 2));
                $response->headers->set('X-Aitrol-Query-Count', (string) $metrics->count);
                $response->headers->set('X-Aitrol-Duplicate-Query-Count', (string) $metrics->duplicates);
            }
            $request->attributes->remove(RequestQueryMetrics::ATTRIBUTE);
        }
    }

    private function monitored($request)
    {
        foreach ((array) config('performance.paths', array()) as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }
        return false;
    }

    private function append(array $sample)
    {
        $directory = storage_path('logs');
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }
        $file = $directory.DIRECTORY_SEPARATOR.'performance-'.date('Y-m-d').'.log';
        @file_put_contents($file, json_encode($sample).PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
