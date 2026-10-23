<?php

namespace Tests\Unit\v3;

use PHPUnit\Framework\TestCase;

/**
 * @group v3
 */
class ApiV3SeguridadTest extends TestCase
{
    public function testLosPrototiposConEfectosSoloSeDeclaranComoPost()
    {
        $rutas = file_get_contents(base_path('routes/v3/contable/api.php'));

        $this->assertStringContainsString("Route::post('enviocorreo'", $rutas);
        $this->assertStringContainsString("Route::post('emisionDocumentos'", $rutas);
        $this->assertStringNotContainsString("Route::get('enviocorreo'", $rutas);
        $this->assertStringNotContainsString("Route::get('emisionDocumentos'", $rutas);
    }

    public function testElPrototipoDeCorreoNoEjecutaProcesosNiTieneSalidasDirectas()
    {
        $contenido = file_get_contents(
            base_path('app/Http/Controllers/v3/contable/Apis/ApiEnvioCorreosController.php')
        );

        $this->assertStringContainsString("'code' => 410", $contenido);
        $this->assertStringNotContainsString('Mail::send', $contenido);
        $this->assertStringNotContainsString('echo ', $contenido);
        $this->assertStringNotContainsString('exit;', $contenido);
    }

    public function testLaEmisionConservaSuLogicaPeroSuEntradaEstaDeshabilitada()
    {
        $contenido = file_get_contents(
            base_path('app/Http/Controllers/v3/contable/Apis/ApiEmisionDocumentosController.php')
        );
        $entrada = strstr($contenido, 'private function retenciones', true);

        $this->assertStringContainsString("'code' => 410", $entrada);
        $this->assertStringNotContainsString('$this->retenciones()', $entrada);
        $this->assertStringContainsString('private function retenciones', $contenido);
        $this->assertStringContainsString('private function facturas', $contenido);
        $this->assertStringContainsString('$comprobante->generarXml()', $contenido);
    }

    public function testElArchivoDePrototiposNoSeCargaAutomaticamente()
    {
        $proveedor = file_get_contents(base_path('app/Providers/RouteServiceProvider.php'));

        $this->assertStringNotContainsString('routes/v3/contable/api.php', $proveedor);
    }

    public function testElCertificadorEsSoloLecturaYEstaRegistrado()
    {
        $comando = file_get_contents(
            base_path('app/Console/Commands/VerificarApiTributariaV3.php')
        );
        $kernel = file_get_contents(base_path('app/Console/Kernel.php'));

        $this->assertStringContainsString("v3:api-check", $comando);
        $this->assertStringContainsString("'solo_lectura' => true", $comando);
        $this->assertStringNotContainsString('->insert(', $comando);
        $this->assertStringNotContainsString('->update(', $comando);
        $this->assertStringNotContainsString('->delete(', $comando);
        $this->assertStringNotContainsString('generarXml(', $comando);
        $this->assertStringNotContainsString('firmarXml(', $comando);
        $this->assertStringNotContainsString('validarSri(', $comando);
        $this->assertStringContainsString('VerificarApiTributariaV3::class', $kernel);
    }

    public function testElContratoDelResumenMantieneSusDoceClaves()
    {
        $controlador = file_get_contents(
            base_path('app/Http/Controllers/v3/contable/Apis/ApiInicioController.php')
        );

        $this->assertStringContainsString('const CLAVES_CONTRATO', $controlador);
        preg_match('/const CLAVES_CONTRATO\s*=\s*\[(.*?)\];/s', $controlador, $bloque);
        preg_match_all("/'[^']+'\s*,/", $bloque[1], $claves);
        $this->assertCount(12, $claves[0]);
    }
}
