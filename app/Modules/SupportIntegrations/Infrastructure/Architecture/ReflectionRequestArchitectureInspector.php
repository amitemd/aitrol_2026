<?php

namespace App\Modules\SupportIntegrations\Infrastructure\Architecture;

use App\Modules\SupportIntegrations\Application\Contracts\RequestArchitectureInspector;
use Illuminate\Contracts\Container\Container;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Throwable;

final class ReflectionRequestArchitectureInspector implements RequestArchitectureInspector
{
    /** @var array<string, array{controller: ?string, method: ?string, components: list<array{type: string, name: string, implementation: ?string}>}> */
    private array $cache = [];

    public function __construct(private Container $container) {}

    public function inspect(?string $action): array
    {
        if ($action === null || ! str_contains($action, '@')) {
            return ['controller' => $action, 'method' => null, 'components' => []];
        }

        if (isset($this->cache[$action])) {
            return $this->cache[$action];
        }

        [$controller, $method] = explode('@', $action, 2);
        $components = [];
        $visited = [];

        try {
            $reflection = new ReflectionClass($controller);
            $this->collectParameters($reflection->getConstructor()?->getParameters() ?? [], $components, $visited, 0);

            if ($reflection->hasMethod($method)) {
                $this->collectParameters(
                    (new ReflectionMethod($controller, $method))->getParameters(),
                    $components,
                    $visited,
                    0,
                );
            }
        } catch (Throwable) {
            return ['controller' => $controller, 'method' => $method, 'components' => []];
        }

        return $this->cache[$action] = [
            'controller' => $controller,
            'method' => $method,
            'components' => array_values($components),
        ];
    }

    /**
     * @param  list<ReflectionParameter>  $parameters
     * @param  array<string, array{type: string, name: string, implementation: ?string}>  $components
     * @param  array<string, true>  $visited
     */
    private function collectParameters(array $parameters, array &$components, array &$visited, int $depth): void
    {
        if ($depth > 2) {
            return;
        }

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
                continue;
            }

            $name = $type->getName();

            if (isset($visited[$name]) || $this->isFrameworkType($name)) {
                continue;
            }

            $visited[$name] = true;
            $implementation = $this->implementationFor($name);
            $components[$name] = [
                'type' => $this->componentType($name, $implementation),
                'name' => $name,
                'implementation' => $implementation !== $name ? $implementation : null,
            ];

            $inspectable = $implementation ?? $name;

            try {
                $reflection = new ReflectionClass($inspectable);
                $this->collectParameters(
                    $reflection->getConstructor()?->getParameters() ?? [],
                    $components,
                    $visited,
                    $depth + 1,
                );
            } catch (Throwable) {
                continue;
            }
        }
    }

    private function implementationFor(string $name): ?string
    {
        if (! interface_exists($name) || ! $this->container->bound($name)) {
            return class_exists($name) ? $name : null;
        }

        try {
            return get_class($this->container->make($name));
        } catch (Throwable) {
            return null;
        }
    }

    private function componentType(string $name, ?string $implementation): string
    {
        $searchable = strtolower($name.' '.($implementation ?? ''));

        return match (true) {
            str_contains($searchable, 'repository') => 'Repositorio',
            str_contains($searchable, '\\application\\contracts\\') => 'Contrato',
            interface_exists($name) => 'Interfaz',
            str_contains($searchable, '\\application\\queries\\') => 'Consulta',
            str_contains($searchable, '\\application\\actions\\'),
            str_contains($searchable, '\\application\\services\\') => 'Servicio',
            default => 'Componente',
        };
    }

    private function isFrameworkType(string $name): bool
    {
        return str_starts_with($name, 'Illuminate\\')
            || str_starts_with($name, 'Symfony\\')
            || str_starts_with($name, 'Psr\\');
    }
}
