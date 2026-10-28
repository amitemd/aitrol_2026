<?php

namespace App\Modules\SupportIntegrations\Infrastructure\Observability;

use App\Modules\SupportIntegrations\Application\Contracts\RequestExecutionTracer;
use Closure;
use Illuminate\Support\Str;
use Throwable;

final class InMemoryRequestExecutionTracer implements RequestExecutionTracer
{
    private bool $active = false;

    private int $startedAtNanoseconds = 0;

    /** @var list<array<string, mixed>> */
    private array $spans = [];

    /** @var list<string> */
    private array $parentStack = [];

    public function start(int $startedAtNanoseconds): void
    {
        $this->active = true;
        $this->startedAtNanoseconds = $startedAtNanoseconds;
        $this->spans = [];
        $this->parentStack = [];
    }

    public function record(
        string $name,
        string $type,
        float $durationMilliseconds,
        ?float $offsetMilliseconds = null,
        array $context = [],
    ): void {
        if (! $this->active || ! is_finite($durationMilliseconds) || $durationMilliseconds < 0) {
            return;
        }

        $maximumSpans = max(10, (int) config('observability.request_trace_max_spans', 250));

        if (count($this->spans) >= $maximumSpans) {
            return;
        }

        $elapsedMilliseconds = (hrtime(true) - $this->startedAtNanoseconds) / 1_000_000;
        $offset = $offsetMilliseconds ?? max(0.0, $elapsedMilliseconds - $durationMilliseconds);

        $this->spans[] = [
            'id' => (string) Str::uuid(),
            'parent_id' => $this->parentStack[array_key_last($this->parentStack)] ?? null,
            'name' => Str::limit(Str::squish($name), 160, '…'),
            'type' => Str::limit(Str::squish($type), 40, '…'),
            'duration_ms' => round($durationMilliseconds, 3),
            'offset_ms' => round(max(0.0, $offset), 3),
            'depth' => count($this->parentStack) + 1,
            'context' => $context,
        ];
    }

    public function measure(string $name, string $type, Closure $callback, array $context = []): mixed
    {
        if (! $this->active) {
            return $callback();
        }

        $spanId = (string) Str::uuid();
        $startedAt = hrtime(true);
        $offset = ($startedAt - $this->startedAtNanoseconds) / 1_000_000;
        $parentId = $this->parentStack[array_key_last($this->parentStack)] ?? null;
        $depth = count($this->parentStack) + 1;
        $this->parentStack[] = $spanId;
        $failed = false;

        try {
            return $callback();
        } catch (Throwable $exception) {
            $failed = true;

            throw $exception;
        } finally {
            array_pop($this->parentStack);
            $duration = (hrtime(true) - $startedAt) / 1_000_000;
            $maximumSpans = max(10, (int) config('observability.request_trace_max_spans', 250));

            if (count($this->spans) < $maximumSpans) {
                $this->spans[] = [
                    'id' => $spanId,
                    'parent_id' => $parentId,
                    'name' => Str::limit(Str::squish($name), 160, '…'),
                    'type' => Str::limit(Str::squish($type), 40, '…'),
                    'duration_ms' => round($duration, 3),
                    'offset_ms' => round(max(0.0, $offset), 3),
                    'depth' => $depth,
                    'context' => [...$context, 'failed' => $failed],
                ];
            }
        }
    }

    public function spans(): array
    {
        return $this->spans;
    }

    public function stop(): void
    {
        $this->active = false;
        $this->startedAtNanoseconds = 0;
        $this->spans = [];
        $this->parentStack = [];
    }
}
