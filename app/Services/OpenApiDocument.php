<?php

namespace App\Services;

use LogicException;

final class OpenApiDocument
{
    /** @return array<string, mixed> */
    public function build(string $baseUrl): array
    {
        $paths = [];
        $components = [];
        $files = glob(base_path('packages/modules/*/openapi.json')) ?: [];
        $rootSpec = base_path('routes/openapi.json');

        if (is_file($rootSpec)) {
            $files[] = $rootSpec;
        }

        foreach ($files as $file) {
            $module = json_decode(file_get_contents($file), true, flags: JSON_THROW_ON_ERROR);

            foreach ($module['paths'] as $path => $operations) {
                foreach ($operations as $method => $operation) {
                    if ($this->isOperation($method) && isset($module['security'])) {
                        $operation['security'] ??= $module['security'];
                    }

                    if (isset($paths[$path][$method]) && $paths[$path][$method] !== $operation) {
                        throw new LogicException("Contrato OpenAPI duplicado para {$method} {$path}.");
                    }

                    $paths[$path][$method] = $operation;
                }
            }

            foreach ($module['components'] ?? [] as $category => $definitions) {
                foreach ($definitions as $name => $definition) {
                    if (isset($components[$category][$name]) && $components[$category][$name] !== $definition) {
                        throw new LogicException("Componente OpenAPI en conflicto: {$category}/{$name}.");
                    }

                    $components[$category][$name] = $definition;
                }
            }
        }

        return [
            'openapi' => '3.1.0',
            'info' => ['title' => 'Aitrol ERP · API de integraciones', 'version' => '1.0.0'],
            'servers' => [['url' => $baseUrl !== '' ? $baseUrl : '/']],
            'paths' => (object) $paths,
            'components' => (object) $components,
        ];
    }

    private function isOperation(string $method): bool
    {
        return in_array($method, ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'], true);
    }
}
