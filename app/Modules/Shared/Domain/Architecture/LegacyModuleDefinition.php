<?php

namespace App\Modules\Shared\Domain\Architecture;

final readonly class LegacyModuleDefinition
{
    /** @param list<string> $capabilities */
    public function __construct(
        public string $name,
        public string $category,
        public int $routeCount,
        public string $source,
        public string $description,
        public array $capabilities,
    ) {}
}
