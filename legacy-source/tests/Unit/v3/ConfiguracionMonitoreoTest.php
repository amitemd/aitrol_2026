<?php

namespace Tests\Unit\v3;

use PHPUnit\Framework\TestCase;

/**
 * @group v3
 */
class ConfiguracionMonitoreoTest extends TestCase
{
    public function testLaConfiguracionContieneUmbralesSeguros()
    {
        $configuracion = require dirname(__DIR__, 3) . '/config/monitoreo_v3.php';
        $claves = [
            'slow_request_ms',
            'slow_query_ms',
            'audit_window_days',
            'minimum_free_disk_percent',
            'maximum_log_size_mb',
            'log_tail_bytes',
        ];

        foreach ($claves as $clave) {
            $this->assertArrayHasKey($clave, $configuracion);
            $this->assertGreaterThan(0, $configuracion[$clave]);
        }

        $this->assertLessThanOrEqual(100, $configuracion['minimum_free_disk_percent']);
        $this->assertGreaterThanOrEqual(1024, $configuracion['log_tail_bytes']);
    }
}
