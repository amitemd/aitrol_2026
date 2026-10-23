<?php

namespace Tests\Unit\v3;

use PHPUnit\Framework\TestCase;
use Sis_medico\v3\contable\MatrizMadurez;

/**
 * @group v3
 */
class MatrizMadurezTest extends TestCase
{
    public function testLosPesosSumanCienPorCiento()
    {
        $peso = 0;
        foreach (MatrizMadurez::dimensiones() as $dimension) {
            $peso += $dimension['peso'];
        }

        $this->assertSame(100, $peso);
    }

    public function testLosValoresEstanDentroDelRangoPermitido()
    {
        $nombres = [];
        foreach (MatrizMadurez::dimensiones() as $dimension) {
            $this->assertGreaterThanOrEqual(0, $dimension['madurez']);
            $this->assertLessThanOrEqual(100, $dimension['madurez']);
            $this->assertGreaterThan(0, $dimension['peso']);
            $nombres[] = $dimension['dimension'];
        }

        $this->assertCount(8, $nombres);
        $this->assertCount(8, array_unique($nombres));
    }

    public function testCalculaElTotalPonderadoVigente()
    {
        $resultado = MatrizMadurez::calcular(MatrizMadurez::dimensiones());

        $this->assertEquals(52.7, $resultado['total']);
        $this->assertEquals(53, $resultado['total_redondeado']);
        $this->assertEquals(18.5, $resultado['dimensiones'][0]['aporte']);
    }

    public function testElCalculoNoModificaLaEntradaOriginal()
    {
        $entrada = [
            ['dimension' => 'Ejemplo', 'peso' => 40, 'madurez' => 50],
        ];

        $resultado = MatrizMadurez::calcular($entrada);

        $this->assertArrayNotHasKey('aporte', $entrada[0]);
        $this->assertEquals(20, $resultado['dimensiones'][0]['aporte']);
    }

    public function testLaAuditoriaIncluyeLosModulosV3ConEvidenciaYPrioridad()
    {
        $modulos = MatrizMadurez::modulos();
        $nombres = [];
        $campos = [
            'modulo', 'ruta', 'grupo', 'madurez', 'estado', 'color',
            'dictamen', 'prioridad', 'evidencia', 'falta', 'inicio',
        ];

        foreach ($modulos as $modulo) {
            foreach ($campos as $campo) {
                $this->assertArrayHasKey($campo, $modulo);
                $this->assertNotSame('', $modulo[$campo]);
            }
            $this->assertGreaterThanOrEqual(0, $modulo['madurez']);
            $this->assertLessThanOrEqual(100, $modulo['madurez']);
            $this->assertContains($modulo['prioridad'], ['P0', 'P1', 'P2', 'P3']);
            $nombres[] = $modulo['modulo'];
        }

        $this->assertCount(23, $modulos);
        $this->assertCount(23, array_unique($nombres));
    }

    public function testResumeLasPrioridadesDeLaAuditoriaModular()
    {
        $resumen = MatrizMadurez::resumirModulos(MatrizMadurez::modulos());

        $this->assertSame(23, $resumen['total']);
        $this->assertEquals(49, $resumen['promedio']);
        $this->assertSame(0, $resumen['prioridades']['P0']);
        $this->assertSame(13, $resumen['prioridades']['P1']);
        $this->assertArrayNotHasKey('No apto para produccion', $resumen['dictamenes']);
    }

    public function testCadaRevisionTieneEvidenciaYReferenciaUnModuloValido()
    {
        $nombres = ['Todos los modulos v3'];
        foreach (MatrizMadurez::modulos() as $modulo) {
            $nombres[] = $modulo['modulo'];
        }

        $campos = [
            'fecha', 'modulo', 'tipo', 'cambio', 'evidencia', 'resultado',
            'madurez_anterior', 'madurez_nueva',
        ];
        foreach (MatrizMadurez::revisiones() as $revision) {
            foreach ($campos as $campo) {
                $this->assertArrayHasKey($campo, $revision);
            }
            $this->assertContains($revision['modulo'], $nombres);
            $this->assertNotSame('', $revision['cambio']);
            $this->assertNotSame('', $revision['evidencia']);
            $this->assertNotSame('', $revision['resultado']);
            $this->assertGreaterThanOrEqual(0, $revision['madurez_nueva']);
            $this->assertLessThanOrEqual(100, $revision['madurez_nueva']);
        }
    }

    public function testLaMatrizMuestraLaUltimaRevisionDeCadaModulo()
    {
        $modulos = MatrizMadurez::aplicarRevisiones(
            MatrizMadurez::modulos(),
            MatrizMadurez::revisiones()
        );

        foreach ($modulos as $modulo) {
            $this->assertArrayHasKey('ultima_revision', $modulo);
            $this->assertArrayHasKey('ultimo_resultado', $modulo);
            $this->assertNotSame('', $modulo['ultima_revision']);
            $this->assertNotSame('', $modulo['ultimo_resultado']);
        }
    }

    public function testLaMadurezVisibleCoincideConLaUltimaRevisionDelModulo()
    {
        $ultimas = [];
        foreach (MatrizMadurez::revisiones() as $revision) {
            if (!isset($ultimas[$revision['modulo']]) || $revision['fecha'] >= $ultimas[$revision['modulo']]['fecha']) {
                $ultimas[$revision['modulo']] = $revision;
            }
        }

        foreach (MatrizMadurez::modulos() as $modulo) {
            if (isset($ultimas[$modulo['modulo']])) {
                $this->assertSame(
                    $ultimas[$modulo['modulo']]['madurez_nueva'],
                    $modulo['madurez'],
                    'La ficha de ' . $modulo['modulo'] . ' no coincide con su ultima revision.'
                );
            }
        }
    }

    public function testElPlanIniciaConApisSinAutorizarCambiosIncompatibles()
    {
        $plan = MatrizMadurez::planMejora();

        $this->assertSame(1, $plan[0]['orden']);
        $this->assertSame('APIs v3', $plan[0]['modulo']);
        $this->assertSame('P1', $plan[0]['prioridad']);
        $this->assertNotFalse(strpos($plan[0]['objetivo'], 'v3:api-check'));
    }
}
