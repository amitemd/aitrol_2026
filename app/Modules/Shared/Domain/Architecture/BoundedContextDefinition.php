<?php

namespace App\Modules\Shared\Domain\Architecture;

final readonly class BoundedContextDefinition
{
    /**
     * @param  list<string>  $dependencies
     * @param  list<LegacyModuleDefinition>  $legacyModules
     */
    public function __construct(
        public string $key,
        public string $name,
        public string $status,
        public array $dependencies,
        public array $legacyModules,
    ) {}

    public function routeCount(): int
    {
        return array_sum(array_map(
            fn (LegacyModuleDefinition $module): int => $module->routeCount,
            $this->legacyModules,
        ));
    }
}
