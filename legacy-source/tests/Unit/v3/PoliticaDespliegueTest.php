<?php

namespace Tests\Unit\v3;

use PHPUnit\Framework\TestCase;
use Sis_medico\v3\contable\PoliticaDespliegue;

/**
 * @group v3
 */
class PoliticaDespliegueTest extends TestCase
{
    public function testAceptaLasVersionesDelServidor()
    {
        $resultado = PoliticaDespliegue::evaluarVersiones('7.1.33', 'Laravel Framework 5.4.36');

        $this->assertTrue($resultado['php']);
        $this->assertTrue($resultado['laravel']);
    }

    public function testRechazaVersionesDistintas()
    {
        $resultado = PoliticaDespliegue::evaluarVersiones('8.2.12', 'Laravel Framework 10.0');

        $this->assertFalse($resultado['php']);
        $this->assertFalse($resultado['laravel']);
    }

    public function testDeclaraLosComponentesProtegidosDelServidor()
    {
        $rutas = [];
        foreach (PoliticaDespliegue::componentesServidor() as $componente) {
            $rutas[] = $componente['ruta'];
        }

        $this->assertContains('.env', $rutas);
        $this->assertContains('vendor/autoload.php', $rutas);
        $this->assertContains('storage', $rutas);
    }
}
