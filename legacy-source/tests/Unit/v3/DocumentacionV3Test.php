<?php

namespace Tests\Unit\v3;

use PHPUnit\Framework\TestCase;

/**
 * @group v3
 */
class DocumentacionV3Test extends TestCase
{
    public function testLosDocumentosObligatoriosExistenYNoEstanVacios()
    {
        $raiz = dirname(__DIR__, 3) . '/docs/v3/';
        $documentos = [
            'README.md',
            'ARQUITECTURA.md',
            'INSTALACION.md',
            'OPERACION_MONITOREO.md',
            'GUIA_DESARROLLO.md',
            'MATRIZ_MADUREZ.md',
            'MANUAL_FUNCIONAL.md',
            'SEGURIDAD_PERMISOS.md',
            'DESPLIEGUE.md',
            'SOPORTE_INCIDENTES.md',
            'DICCIONARIO_DATOS.md',
            'PRUEBAS.md',
            'CHECKLIST_DESPLIEGUE.md',
        ];

        foreach ($documentos as $documento) {
            $ruta = $raiz . $documento;
            $this->assertFileExists($ruta, $documento . ' no existe');
            $this->assertGreaterThan(100, filesize($ruta), $documento . ' esta vacio');
        }
    }

    public function testElManualFuncionalDocumentaOpcionesRealesYNoSoloRutasGenerales()
    {
        $raiz = dirname(__DIR__, 3);
        $manual = file_get_contents($raiz . '/docs/v3/MANUAL_FUNCIONAL.md');
        $controlador = file_get_contents($raiz . '/app/Http/Controllers/v3/contable/DocumentacionController.php');

        $rutasConfirmadas = [
            '/v3/configuraciones/usuarios',
            '/v3/ventas/listadeventas',
            '/v3/compras/listadecompras',
            '/v3/cajabancos/conciliacion',
            '/v3/tributario/gestiondocumentos',
            '/v3/guiaderemision/listadeguias',
        ];

        foreach ($rutasConfirmadas as $ruta) {
            $this->assertNotFalse(strpos($manual, $ruta));
            $this->assertNotFalse(strpos($controlador, "'ruta' => '" . $ruta . "'"));
        }

        $this->assertFalse(strpos($controlador, "'ruta' => '/v3/configuraciones',"));
        $this->assertFalse(strpos($controlador, "'ruta' => '/v3/ventas',"));
        $this->assertFalse(strpos($controlador, "'ruta' => '/v3/compras',"));
        $this->assertFalse(strpos($controlador, "'ruta' => '/v3/tributario',"));
    }
}
