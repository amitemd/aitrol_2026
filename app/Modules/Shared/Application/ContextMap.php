<?php

namespace App\Modules\Shared\Application;

use InvalidArgumentException;

final readonly class ContextMap
{
    /**
     * @param  array{
     *     migration_order: list<string>,
     *     contexts: array<string, array{
     *         name: string,
     *         status: string,
     *         depends_on: list<string>,
     *         legacy_modules: list<string>
     *     }>
     * }  $definition
     */
    public function __construct(private array $definition)
    {
    }

    /** @return list<string> */
    public function migrationOrder(): array
    {
        return $this->definition['migration_order'];
    }

    /**
     * @return array<string, array{
     *     name: string,
     *     status: string,
     *     depends_on: list<string>,
     *     legacy_modules: list<string>
     * }>
     */
    public function contexts(): array
    {
        return $this->definition['contexts'];
    }

    /**
     * @return array{
     *     name: string,
     *     status: string,
     *     depends_on: list<string>,
     *     legacy_modules: list<string>
     * }
     */
    public function context(string $key): array
    {
        return $this->definition['contexts'][$key]
            ?? throw new InvalidArgumentException("El contexto [{$key}] no está definido.");
    }

    /**
     * @return array{
     *     key: string,
     *     name: string,
     *     status: string,
     *     depends_on: list<string>,
     *     legacy_modules: list<string>
     * }|null
     */
    public function contextForLegacyModule(string $moduleName): ?array
    {
        foreach ($this->contexts() as $key => $context) {
            if (in_array($moduleName, $context['legacy_modules'], true)) {
                return ['key' => $key, ...$context];
            }
        }

        return null;
    }
}
