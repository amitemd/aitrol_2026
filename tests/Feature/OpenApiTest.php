<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class OpenApiTest extends TestCase
{
    public function test_swagger_lists_all_api_endpoints_and_their_responses(): void
    {
        $this->get('/docs/api')->assertOk()->assertSee('/openapi.json')->assertSee('SwaggerUIBundle');
        $documento = $this->getJson('/openapi.json')->assertOk()->assertJsonPath('openapi', '3.1.0')->json();
        $operaciones = [];

        foreach (Route::getRoutes() as $ruta) {
            if (! str_starts_with($ruta->uri(), 'api/')) {
                continue;
            }

            $path = '/'.$ruta->uri();

            foreach ($ruta->methods() as $method) {
                if ($method === 'HEAD' && in_array('GET', $ruta->methods(), true)) {
                    continue;
                }

                $operacion = $documento['paths'][$path][strtolower($method)] ?? null;
                $this->assertNotNull($operacion, "Falta documentar {$method} {$path} en OpenAPI.");
                $this->assertNotEmpty($operacion['summary'] ?? null, "Falta el resumen de {$method} {$path}.");
                $this->assertNotEmpty($operacion['responses'] ?? null, "Faltan las respuestas de {$method} {$path}.");
                $this->assertNotEmpty($operacion['operationId'] ?? null, "Falta operationId para {$method} {$path}.");
                $operaciones[] = $operacion['operationId'];
            }
        }

        $this->assertNotEmpty($operaciones);
        $this->assertSame($operaciones, array_values(array_unique($operaciones)), 'Los operationId deben ser únicos.');
        $this->assertSame('bearer', $documento['components']['securitySchemes']['BearerToken']['scheme']);

        $nodos = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($documento));

        foreach ($nodos as $clave => $valor) {
            if ($clave !== '$ref' || ! str_starts_with($valor, '#/')) {
                continue;
            }

            $referencia = $documento;

            foreach (explode('/', substr($valor, 2)) as $segmento) {
                $segmento = strtr($segmento, ['~1' => '/', '~0' => '~']);
                $this->assertArrayHasKey($segmento, $referencia, "Referencia OpenAPI inexistente: {$valor}");
                $referencia = $referencia[$segmento];
            }
        }
    }
}
