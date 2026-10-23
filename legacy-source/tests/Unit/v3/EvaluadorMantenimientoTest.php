<?php

namespace Tests\Unit\v3;

use PHPUnit\Framework\TestCase;
use Sis_medico\v3\contable\EvaluadorMantenimiento;

class EvaluadorMantenimientoTest extends TestCase
{
    /** @test */
    public function evalua_mantenimiento_sin_ejecutar_acciones()
    {
        $resultado = EvaluadorMantenimiento::evaluar([
            'entorno' => 'pruebas',
            'storage_escribible' => true,
            'logs_escribibles' => true,
            'canal_log' => 'daily',
            'nivel_log' => 'debug',
            'log_controlado' => true,
            'tamano_log' => '10 MB',
            'maximo_log' => '100 MB',
            'config_cache' => false,
            'respaldo_configurado' => false,
            'respaldo_horas' => null,
            'maximo_respaldo_horas' => 24,
            'scheduler_definido' => true,
        ]);

        $this->assertCount(9, $resultado['controles']);
        $this->assertGreaterThan(0, $resultado['correctos']);
        $this->assertGreaterThan(0, $resultado['revisar']);
        $this->assertSame(0, $resultado['criticos']);
    }
}
