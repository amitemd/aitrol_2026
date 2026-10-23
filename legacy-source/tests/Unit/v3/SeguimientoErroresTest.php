<?php

namespace Tests\Unit\v3;

use PHPUnit\Framework\TestCase;
use Sis_medico\v3\contable\SeguimientoErrores;

class SeguimientoErroresTest extends TestCase
{
    /** @test */
    public function oculta_un_error_corregido_y_lo_reabre_si_vuelve_a_ocurrir()
    {
        $directorio = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'seguimiento-v3-' . uniqid();
        $archivo = $directorio . DIRECTORY_SEPARATOR . 'errores.json';
        $error = [
            'firma' => sha1('error-de-prueba'),
            'fecha' => date('Y-m-d H:i:s', time() - 60),
            'nivel' => 'ERROR',
            'mensaje' => 'Error de prueba',
            'origen' => 'app/Prueba.php',
            'linea' => 10,
            'categoria' => 'Aplicacion',
            'ocurrencias' => 1,
        ];

        SeguimientoErrores::marcar($archivo, $error, 1, 'Administrador', 'Prueba corregida');
        $clasificacion = SeguimientoErrores::clasificar([$error], $archivo);

        $this->assertCount(0, $clasificacion['pendientes']);
        $this->assertCount(1, $clasificacion['corregidos']);

        $error['fecha'] = date('Y-m-d H:i:s', time() + 60);
        $clasificacion = SeguimientoErrores::clasificar([$error], $archivo);

        $this->assertCount(1, $clasificacion['pendientes']);
        $this->assertTrue($clasificacion['pendientes'][0]['reabierto']);

        @unlink($archivo);
        @rmdir($directorio);
    }
}
