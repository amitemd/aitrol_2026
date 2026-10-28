<?php

namespace App\Modules\SupportIntegrations\Application\Data;

final readonly class SystemObservabilityReport
{
    /**
     * @param  array<string, array{technical: list<array<string, mixed>>, activities: list<array<string, mixed>>, warnings: list<string>}>  $auditByProcess
     */
    public function __construct(
        public SystemLogPage $logs,
        public array $auditByProcess,
    ) {}
}
