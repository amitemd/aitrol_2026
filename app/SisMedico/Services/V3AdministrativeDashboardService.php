<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class V3AdministrativeDashboardService
{
    public function resumen($idEmpresa, $periodo)
    {
        $periodo = (int) $periodo;
        if ($periodo < 2000 || $periodo > 2100) {
            $periodo = (int) date('Y');
        }

        // El tablero solo presenta agregados informativos. Una ventana corta
        // evita repetir cinco consultas pesadas al navegar o recargar V3 y la
        // clave mantiene totalmente separados empresa y período.
        $cacheKey = 'v3:admin:resumen:' . (string) $idEmpresa . ':' . $periodo;
        return Cache::remember($cacheKey, 20, function () use ($idEmpresa, $periodo) {
            return $this->calcularResumen($idEmpresa, $periodo);
        });
    }

    private function calcularResumen($idEmpresa, $periodo)
    {

        $ventas = $this->serieVentas($idEmpresa, $periodo);
        $movimientos = $this->serieComprasGastos($idEmpresa, $periodo);
        $compras = $movimientos['compras'];
        $gastos = $movimientos['gastos'];
        $resultado = array();
        for ($mes = 0; $mes < 12; $mes++) {
            $resultado[$mes] = round($ventas[$mes] - $compras[$mes] - $gastos[$mes], 2);
        }

        $cartera = $this->cartera($idEmpresa, $periodo);
        $obligaciones = $this->obligaciones($idEmpresa, $periodo);
        $mesActual = $periodo === (int) date('Y') ? (int) date('n') - 1 : 11;

        return array(
            'periodo' => $periodo,
            'generado_en' => date('Y-m-d H:i:s'),
            'series' => array(
                'ventas' => $ventas,
                'compras' => $compras,
                'gastos' => $gastos,
                'resultado' => $resultado,
            ),
            'indicadores' => array(
                'ventas_mes' => $ventas[$mesActual],
                'egresos_mes' => round($compras[$mesActual] + $gastos[$mesActual], 2),
                'cuentas_cobrar' => $cartera['saldo'],
                'cuentas_pagar' => $obligaciones['saldo'],
                'resultado_mes' => $resultado[$mesActual],
            ),
            'alertas' => array(
                'cartera_vencida' => $cartera['vencido'],
                'facturas_vencidas' => $cartera['documentos_vencidos'],
                'obligaciones_vencidas' => $obligaciones['vencido'],
                'compras_vencidas' => $obligaciones['documentos_vencidos'],
                'documentos_electronicos_pendientes' => $this->documentosElectronicosPendientes($idEmpresa, $periodo),
            ),
        );
    }

    private function serieVentas($idEmpresa, $periodo)
    {
        $filas = DB::table('ct_ventas')
            ->selectRaw('MONTH(fecha) as mes, SUM(COALESCE(subtotal_0,0) + COALESCE(subtotal_12,0) - COALESCE(descuento,0)) as total')
            ->where('id_empresa', $idEmpresa)
            ->where('estado', 1)
            ->whereIn('tipo', array('VEN-FA', 'VENFA-CO'))
            ->whereYear('fecha', $periodo)
            ->groupBy(DB::raw('MONTH(fecha)'))
            ->get();

        return $this->completarSerie($filas);
    }

    private function serieComprasGastos($idEmpresa, $periodo)
    {
        $filas = DB::table('ct_compras')
            ->selectRaw('MONTH(fecha) as mes, tipo, SUM(COALESCE(subtotal_0,0) + COALESCE(subtotal_12,0) - COALESCE(descuento,0)) as total')
            ->where('id_empresa', $idEmpresa)
            ->where('estado', 1)
            ->whereIn('tipo', array(1, 2))
            ->whereYear('fecha', $periodo)
            ->groupBy(DB::raw('MONTH(fecha)'))
            ->groupBy('tipo')
            ->get();

        $compras = array_fill(0, 12, 0.0);
        $gastos = array_fill(0, 12, 0.0);
        foreach ($filas as $fila) {
            $indice = (int) $fila->mes - 1;
            if ($indice < 0 || $indice > 11) {
                continue;
            }
            if ((int) $fila->tipo === 1) {
                $compras[$indice] = round((float) $fila->total, 2);
            } elseif ((int) $fila->tipo === 2) {
                $gastos[$indice] = round((float) $fila->total, 2);
            }
        }

        return array('compras' => $compras, 'gastos' => $gastos);
    }

    private function cartera($idEmpresa, $periodo)
    {
        $fila = DB::table('ct_ventas')
            ->where('id_empresa', $idEmpresa)
            ->where('estado', 1)
            ->whereYear('fecha', $periodo)
            ->where('valor_contable', '>', 0)
            ->selectRaw('COALESCE(SUM(valor_contable),0) as saldo')
            ->selectRaw('COALESCE(SUM(CASE WHEN DATE_ADD(fecha, INTERVAL COALESCE(dias_plazo,0) DAY) < CURDATE() THEN valor_contable ELSE 0 END),0) as vencido')
            ->selectRaw('SUM(CASE WHEN DATE_ADD(fecha, INTERVAL COALESCE(dias_plazo,0) DAY) < CURDATE() THEN 1 ELSE 0 END) as documentos_vencidos')
            ->first();

        return array(
            'saldo' => round((float) $fila->saldo, 2),
            'vencido' => round((float) $fila->vencido, 2),
            'documentos_vencidos' => (int) $fila->documentos_vencidos,
        );
    }

    private function obligaciones($idEmpresa, $periodo)
    {
        $fila = DB::table('ct_compras')
            ->where('id_empresa', $idEmpresa)
            ->where('estado', 1)
            ->whereYear('fecha', $periodo)
            ->where('valor_contable', '>', 0)
            ->selectRaw('COALESCE(SUM(valor_contable),0) as saldo')
            ->selectRaw('COALESCE(SUM(CASE WHEN COALESCE(fecha_termino,fecha) < CURDATE() THEN valor_contable ELSE 0 END),0) as vencido')
            ->selectRaw('SUM(CASE WHEN COALESCE(fecha_termino,fecha) < CURDATE() THEN 1 ELSE 0 END) as documentos_vencidos')
            ->first();

        return array(
            'saldo' => round((float) $fila->saldo, 2),
            'vencido' => round((float) $fila->vencido, 2),
            'documentos_vencidos' => (int) $fila->documentos_vencidos,
        );
    }

    private function documentosElectronicosPendientes($idEmpresa, $periodo)
    {
        return (int) DB::table('ct_ventas')
            ->where('id_empresa', $idEmpresa)
            ->where('estado', 1)
            ->whereYear('fecha', $periodo)
            ->where('electronica', 1)
            ->where('doc_electronico', '<>', 5)
            ->count();
    }

    private function completarSerie($filas)
    {
        $serie = array_fill(0, 12, 0.0);
        foreach ($filas as $fila) {
            $indice = (int) $fila->mes - 1;
            if ($indice >= 0 && $indice <= 11) {
                $serie[$indice] = round((float) $fila->total, 2);
            }
        }
        return $serie;
    }
}
