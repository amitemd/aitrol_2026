<?php

namespace App\Modules\SupportIntegrations\Application\Data;

final readonly class HttpRequestAuditEntry
{
    /**
     * @param  array{controller: ?string, method: ?string, components: list<array{type: string, name: string, implementation: ?string}>}  $architecture
     * @param  list<array<string, mixed>>  $spans
     */
    public function __construct(
        public string $correlationId,
        public string $httpMethod,
        public string $path,
        public ?string $route,
        public ?string $controller,
        public int $status,
        public float $durationMilliseconds,
        public ?string $userId,
        public ?string $companyId,
        public ?string $ip,
        public ?string $userAgent,
        public array $architecture,
        public array $spans,
        public ?string $exception = null,
    ) {}
}
