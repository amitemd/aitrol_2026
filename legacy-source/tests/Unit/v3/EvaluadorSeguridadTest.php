<?php

namespace Tests\Unit\v3;

use PHPUnit\Framework\TestCase;
use Sis_medico\v3\contable\EvaluadorSeguridad;

class EvaluadorSeguridadTest extends TestCase
{
    /** @test */
    public function distingue_controles_correctos_de_configuraciones_por_revisar()
    {
        $resultado = EvaluadorSeguridad::evaluar([
            'rutas' => "Route::get('v3/publica', 'Publica@index');\nRoute::group(['middleware' => ['operacion.v3', 'general']], function () { Route::get('v3/operacion-monitoreo', 'Operacion@index'); Route::match(['get', 'post'], 'v3/modulo', 'Modulo@index'); });",
            'csrf' => "protected \$except = ['/maquina'];",
            'kernel' => 'VerifyCsrfToken::class',
            'controlador' => 'if (!Auth::check() || Auth::user()->id_tipo_usuario !== 1) { abort(403); }',
            'console_kernel' => "schedule->exec('wget ' . config('url') . ' --no-check-certificate');",
            'entorno' => 'pruebas',
            'debug' => true,
            'app_key' => 'base64:clave-de-prueba',
            'session_http_only' => true,
            'session_secure' => false,
        ]);

        $this->assertGreaterThan(0, $resultado['correctos']);
        $this->assertGreaterThan(0, $resultado['revisar']);
        $this->assertSame(0, $resultado['criticos']);
        $this->assertCount(11, $resultado['controles']);
    }
}
