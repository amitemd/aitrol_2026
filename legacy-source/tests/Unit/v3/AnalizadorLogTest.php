<?php

namespace Tests\Unit\v3;

use PHPUnit\Framework\TestCase;
use Sis_medico\v3\contable\AnalizadorLog;

class AnalizadorLogTest extends TestCase
{
    /** @test */
    public function agrupa_errores_repetidos_y_muestra_el_origen_sin_secretos()
    {
        $contenido = implode("\n", [
            '[2026-07-15 10:00:00] production.ERROR: Fallo para usuario 15 token=abc123 in C:\\app\\app\\Servicios\\Pago.php:48',
            '#0 C:\\app\\vendor\\laravel\\framework.php(20): ejecutar()',
            '[2026-07-15 10:01:00] production.WARNING: Advertencia de prueba',
            '[2026-07-15 10:02:00] production.ERROR: Fallo para usuario 27 token=def456 in C:\\app\\app\\Servicios\\Pago.php:48',
            '[2026-07-15 10:03:00] production.CRITICAL: Base no disponible password=secreto in /var/www/app/Conexion.php:91',
        ]);

        $resultado = AnalizadorLog::resumir($contenido);

        $this->assertSame(3, $resultado['total']);
        $this->assertSame(2, $resultado['unicos']);
        $this->assertSame('CRITICAL', $resultado['errores'][0]['nivel']);
        $this->assertSame('app/Conexion.php', $resultado['errores'][0]['origen']);
        $this->assertSame(91, $resultado['errores'][0]['linea']);
        $this->assertContains('[PROTEGIDO]', $resultado['errores'][0]['mensaje']);
        $this->assertSame(2, $resultado['errores'][1]['ocurrencias']);
        $this->assertContains('[PROTEGIDO]', $resultado['errores'][1]['mensaje']);
        $this->assertNotContains('secreto', $resultado['errores'][0]['mensaje']);
    }

    /** @test */
    public function sugiere_una_correccion_segun_el_tipo_de_error()
    {
        $contenido = '[2026-07-15 10:00:00] production.ERROR: SQLSTATE[42S02]: Base table or view not found: 1146 Table ventas_detalle does not exist';

        $resultado = AnalizadorLog::resumir($contenido);

        $this->assertSame('Base de datos', $resultado['errores'][0]['categoria']);
        $this->assertSame('Alta', $resultado['errores'][0]['prioridad']);
        $this->assertContains('nombre de tabla', $resultado['errores'][0]['sugerencia']);
    }
}
