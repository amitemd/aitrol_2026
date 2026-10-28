<?php

namespace App\Modules\SupportIntegrations\Application\Contracts;

use Closure;

interface RequestExecutionTracer
{
    public function start(int $startedAtNanoseconds): void;

    /**
     * @param  array<string, scalar|null>  $context
     */
    public function record(
        string $name,
        string $type,
        float $durationMilliseconds,
        ?float $offsetMilliseconds = null,
        array $context = [],
    ): void;

    /**
     * @template TResult
     *
     * @param  Closure(): TResult  $callback
     * @param  array<string, scalar|null>  $context
     * @return TResult
     */
    public function measure(string $name, string $type, Closure $callback, array $context = []): mixed;

    /** @return list<array<string, mixed>> */
    public function spans(): array;

    public function stop(): void;
}
