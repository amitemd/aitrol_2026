<?php

namespace App\Modules\Shared\Infrastructure\Configuration;

use App\Modules\Shared\Application\ContextMap;
use App\Modules\Shared\Application\Contracts\ArchitectureCatalog;
use App\Modules\Shared\Domain\Architecture\BoundedContextDefinition;
use App\Modules\Shared\Domain\Architecture\LegacyModuleDefinition;
use Illuminate\Contracts\Config\Repository;
use RuntimeException;

final readonly class LaravelArchitectureCatalog implements ArchitectureCatalog
{
    public function __construct(
        private Repository $config,
        private ContextMap $contextMap,
    ) {}

    public function contexts(): array
    {
        $inventory = collect($this->config->get('modules.items', []))->keyBy('name');

        return collect($this->contextMap->contexts())
            ->map(function (array $context, string $key) use ($inventory): BoundedContextDefinition {
                $legacyModules = collect($context['legacy_modules'])
                    ->map(function (string $moduleName) use ($inventory): LegacyModuleDefinition {
                        $module = $inventory->get($moduleName);

                        if (! is_array($module)) {
                            throw new RuntimeException("El módulo legado [{$moduleName}] no existe en el inventario.");
                        }

                        return new LegacyModuleDefinition(
                            name: $module['name'],
                            category: $module['category'],
                            routeCount: (int) $module['routes'],
                            source: $module['source'],
                            description: $module['description'],
                            capabilities: $module['capabilities'],
                        );
                    })
                    ->values()
                    ->all();

                return new BoundedContextDefinition(
                    key: $key,
                    name: $context['name'],
                    status: $context['status'],
                    dependencies: $context['depends_on'],
                    legacyModules: $legacyModules,
                );
            })
            ->values()
            ->all();
    }
}
