<?php

namespace Tests\Feature;

use App\Services\VickyModelo;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

final class VickyModeloTest extends TestCase
{
    private const URL = 'http://192.0.2.10:11434/api/chat';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.vicky.url' => 'http://192.0.2.10:11434/', 'services.vicky.model' => 'prueba:latest']);
        Http::preventStrayRequests();
    }

    public function test_consulta_el_chat_en_la_url_configurada(): void
    {
        Http::fake([self::URL => Http::response(['message' => ['content' => 'Respuesta de Ollama']])]);

        $this->assertSame('Respuesta de Ollama', app(VickyModelo::class)->estrategia('Ideas de prueba'));
        Http::assertSent(fn (Request $request): bool => $request->url() === self::URL
            && $request['model'] === 'prueba:latest' && $request['stream'] === false);
    }

    public function test_registra_el_estado_http_sin_incluir_el_cuerpo_de_la_respuesta(): void
    {
        Http::fake([self::URL => Http::response(['error' => 'Contenido privado'], 404)]);
        Log::shouldReceive('warning')->once()->with('vicky.ollama.http_fallido', ['estado_http' => 404]);

        $this->assertNull(app(VickyModelo::class)->estrategia('Ideas de prueba'));
    }

    public function test_aplica_el_tiempo_configurado_sin_aumentar_la_espera_de_conexion(): void
    {
        config(['services.vicky.timeout' => '90']);
        Http::fake([self::URL => function (Request $request, array $opciones) {
            $this->assertSame(90, $opciones['timeout']);
            $this->assertSame(3, $opciones['connect_timeout']);

            return Http::response(['message' => ['content' => 'OK']]);
        }]);

        $this->assertSame('OK', app(VickyModelo::class)->estrategia('Ideas de prueba'));
    }

    public function test_registra_fallo_de_conexion_sin_incluir_el_mensaje_de_la_excepcion(): void
    {
        Http::fake([self::URL => Http::failedConnection('cURL error 7: detalle privado')]);
        Log::shouldReceive('warning')->once()->with('vicky.ollama.conexion_fallida', ['codigo_curl' => '7']);

        $this->assertNull(app(VickyModelo::class)->estrategia('Ideas de prueba'));
    }
}
