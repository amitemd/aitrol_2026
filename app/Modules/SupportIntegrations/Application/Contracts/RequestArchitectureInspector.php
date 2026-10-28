<?php

namespace App\Modules\SupportIntegrations\Application\Contracts;

interface RequestArchitectureInspector
{
    /**
     * @return array{controller: ?string, method: ?string, components: list<array{type: string, name: string, implementation: ?string}>}
     */
    public function inspect(?string $action): array;
}
