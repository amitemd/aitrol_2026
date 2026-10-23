<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class NotaCreditoClienteInventarioTest extends TestCase
{
    private function contenido($ruta)
    {
        return file_get_contents(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $ruta));
    }

    public function testElFlujoTieneRutasSeparadasDeLasNotasExistentes()
    {
        $rutas = $this->contenido('routes/contable/web_clientes.php');

        $this->assertNotFalse(strpos($rutas, 'nota_credito_cliente.inventario.create'));
        $this->assertNotFalse(strpos($rutas, 'nota_credito_cliente.inventario.detalle'));
        $this->assertNotFalse(strpos($rutas, 'nota_credito_cliente.inventario.store'));
        $this->assertNotFalse(strpos($rutas, 'NotaCreditoClienteInventarioController@store'));
    }

    public function testLaDevolucionSeOriginaEnLaSalidaYConservaSuTrazabilidad()
    {
        $controlador = $this->contenido('app/Http/Controllers/contable/NotaCreditoClienteInventarioController.php');

        $this->assertNotFalse(strpos($controlador, "Venta_Kardex::where('id_venta'"));
        $this->assertNotFalse(strpos($controlador, 'id_detalle_origen'));
        $this->assertNotFalse(strpos($controlador, 'cantidadDevuelta'));
        $this->assertNotFalse(strpos($controlador, 'lockForUpdate'));
        $this->assertNotFalse(strpos($controlador, 'InvKardex::setKardex'));
    }

    public function testRegistraElReversoContableDelCostoDeVenta()
    {
        $controlador = $this->contenido('app/Http/Controllers/contable/NotaCreditoClienteInventarioController.php');

        $this->assertNotFalse(strpos($controlador, 'COSTO_DEBE_PROCEDIMIENTO'));
        $this->assertNotFalse(strpos($controlador, 'COSTO_HABER_PROCEDIMIENTO'));
        $this->assertNotFalse(strpos($controlador, 'crearAsientoReversoCosto'));
        $this->assertNotFalse(strpos($controlador, 'inventario al Debe y costo de venta al Haber'));
    }
}
