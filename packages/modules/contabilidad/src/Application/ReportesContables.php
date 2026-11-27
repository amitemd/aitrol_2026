<?php

namespace Aitrol\Contabilidad\Application;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

final readonly class ReportesContables
{
    public function __construct(private DatosContables $datos) {}

    public function consulta(string $reporte, array $filtros): array
    {
        $desde = $filtros['desde'];
        $hasta = $filtros['hasta'];

        return match ($reporte) {
            'libro-diario' => $this->diario($desde, $hasta),
            'mayorizacion' => $this->mayor($desde, $hasta, $filtros['id_cuenta'] ?? null),
            'balance-comprobacion' => $this->comprobacion($desde, $hasta),
            'estado-resultados' => $this->resultados($desde, $hasta),
            'balance-financiero' => $this->situacion($hasta),
            'flujo-caja' => $this->efectivo($desde, $hasta),
            'cambios-patrimonio' => $this->patrimonio($desde, $hasta),
        };
    }

    private function diario(string $desde, string $hasta): array
    {
        $consulta = $this->datos->lineas()->whereBetween('asiento.fecha', [$desde, $hasta]);
        $totales = (clone $consulta)->select([])->selectRaw('coalesce(sum(debe),0) as debe, coalesce(sum(haber),0) as haber')->first();

        return ['filas' => $consulta->orderBy('asiento.fecha')->orderBy('asiento.numero')->orderBy('co_lineas.id'), 'totales' => (array) $totales];
    }

    private function mayor(string $desde, string $hasta, int|string|null $cuenta): array
    {
        if ($cuenta === null) {
            return ['filas' => null, 'totales' => []];
        }
        $this->datos->fila('co_cuentas', (int) $cuenta);
        $orden = $this->datos->lineas()->where('co_lineas.id_cuenta', $cuenta)->where('asiento.fecha', '<=', $hasta)
            ->selectRaw('sum(debe - haber) over (order by asiento.fecha, asiento.numero, co_lineas.id rows unbounded preceding) as saldo');
        $filas = DB::query()->fromSub($orden, 'mayor')->where('fecha', '>=', $desde)->orderBy('fecha')->orderBy('numero')->orderBy('id');
        $saldos = $this->acumulados($desde, $hasta)->where('co_lineas.id_cuenta', $cuenta)
            ->select([])->selectRaw('coalesce(sum(case when asiento.fecha < ? then debe - haber else 0 end),0) as apertura', [$desde])
            ->selectRaw('coalesce(sum(case when asiento.fecha >= ? then debe else 0 end),0) as debe', [$desde])
            ->selectRaw('coalesce(sum(case when asiento.fecha >= ? then haber else 0 end),0) as haber', [$desde])->first();

        return ['filas' => $filas, 'totales' => (array) $saldos + ['cierre' => $saldos->apertura + $saldos->debe - $saldos->haber]];
    }

    private function comprobacion(string $desde, string $hasta): array
    {
        $filas = $this->porCuenta($desde, $hasta)
            ->selectRaw('apertura + debe - haber as cierre')->orderBy('codigo')->orderBy('id');
        $totales = DB::query()->fromSub((clone $filas)->reorder(), 'saldos')->selectRaw(
            'coalesce(sum(apertura),0) as apertura, coalesce(sum(debe),0) as debe, coalesce(sum(haber),0) as haber, coalesce(sum(cierre),0) as cierre'
        )->first();

        return ['filas' => $filas, 'totales' => (array) $totales];
    }

    private function resultados(string $desde, string $hasta): array
    {
        $filas = $this->porCuenta($desde, $hasta)->whereIn('clase', ['ingreso', 'gasto'])
            ->whereRaw('(debe + haber) > 0')->selectRaw("case when clase = 'ingreso' then haber - debe else debe - haber end as importe")
            ->orderByDesc('clase')->orderBy('codigo')->orderBy('id');
        $totales = DB::query()->fromSub((clone $filas)->reorder(), 'resultado')
            ->selectRaw("coalesce(sum(case when clase = 'ingreso' then importe else 0 end),0) as ingresos, coalesce(sum(case when clase = 'gasto' then importe else 0 end),0) as gastos")
            ->first();

        return ['filas' => $filas, 'totales' => (array) $totales + ['resultado' => $totales->ingresos - $totales->gastos]];
    }

    private function situacion(string $hasta): array
    {
        $filas = $this->porCuenta($hasta, $hasta)->whereIn('clase', ['activo', 'pasivo', 'patrimonio'])
            ->whereRaw('apertura + debe - haber <> 0')
            ->selectRaw("case when clase = 'activo' then apertura + debe - haber else -(apertura + debe - haber) end as importe")
            ->orderBy('clase')->orderBy('codigo')->orderBy('id');
        $totales = DB::query()->fromSub((clone $filas)->reorder(), 'situacion')->selectRaw(
            "coalesce(sum(case when clase = 'activo' then importe else 0 end),0) as activos, coalesce(sum(case when clase = 'pasivo' then importe else 0 end),0) as pasivos,
            coalesce(sum(case when clase = 'patrimonio' then importe else 0 end),0) as patrimonio_registrado"
        )->first();
        $resultado = $this->resultadoAcumulado($hasta);
        $patrimonio = $totales->patrimonio_registrado + $resultado;

        return ['filas' => $filas, 'totales' => (array) $totales + [
            'resultado_acumulado' => $resultado, 'patrimonio' => $patrimonio, 'pasivo_patrimonio' => $totales->pasivos + $patrimonio,
            'diferencia' => $totales->activos - $totales->pasivos - $patrimonio,
        ]];
    }

    private function efectivo(string $desde, string $hasta): array
    {
        $lineas = $this->acumulados($desde, $hasta)->where('cuenta.efectivo', true);
        $saldos = (clone $lineas)->select([])
            ->selectRaw('coalesce(sum(case when asiento.fecha < ? then debe - haber else 0 end),0) as apertura', [$desde])
            ->selectRaw('coalesce(sum(debe - haber),0) as cierre')->first();
        $porAsiento = $lineas->where('asiento.fecha', '>=', $desde)->whereNotNull('asiento.flujo')->where('asiento.flujo', '<>', 'traslado')
            ->select('asiento.flujo', 'asiento.id')->selectRaw('sum(debe - haber) as neto')->groupBy('asiento.flujo', 'asiento.id');
        $agrupadas = DB::query()->fromSub($porAsiento, 'movimientos')->select('flujo')
            ->selectRaw('sum(case when neto > 0 then neto else 0 end) as ingresos, sum(case when neto < 0 then -neto else 0 end) as egresos, sum(neto) as neto')
            ->groupBy('flujo')->get()->keyBy('flujo');
        $filas = [];
        foreach (['operacion', 'inversion', 'financiacion', 'apertura'] as $clase) {
            $fila = $agrupadas->get($clase);
            $filas[] = ['flujo' => $clase, 'ingresos' => (int) ($fila->ingresos ?? 0), 'egresos' => (int) ($fila->egresos ?? 0), 'neto' => (int) ($fila->neto ?? 0)];
        }

        return ['filas' => collect($filas), 'totales' => (array) $saldos + [
            'neto' => $saldos->cierre - $saldos->apertura, 'diferencia' => $saldos->cierre - $saldos->apertura - array_sum(array_column($filas, 'neto')),
        ]];
    }

    private function patrimonio(string $desde, string $hasta): array
    {
        $consulta = $this->acumulados($desde, $hasta)->where('cuenta.clase', 'patrimonio')->select('cuenta.codigo', 'cuenta.nombre as cuenta', 'cuenta.id')
            ->selectRaw('sum(case when asiento.fecha < ? then haber - debe else 0 end) as apertura', [$desde])
            ->selectRaw('sum(haber - debe) as cierre')->groupBy('cuenta.codigo', 'cuenta.nombre', 'cuenta.id')->orderBy('cuenta.codigo');
        $movimientos = array_keys(config('contabilidad.patrimonio'));
        foreach ($movimientos as $campo) {
            $consulta->selectRaw('sum(case when asiento.fecha >= ? and movimiento_patrimonio = ? then haber - debe else 0 end) as '.$campo, [$desde, $campo]);
        }
        $filas = $consulta->get()->map(fn ($fila) => (array) $fila)->all();
        $previo = $this->resultadoAcumulado($desde, estricto: true);
        $posterior = $this->resultadoAcumulado($hasta);
        $filas[] = ['codigo' => null, 'cuenta' => 'Resultado acumulado pendiente de traspaso', 'apertura' => $previo, 'resultado' => $posterior - $previo, 'cierre' => $posterior];
        $totales = [];
        foreach (['apertura', ...$movimientos, 'resultado', 'cierre'] as $campo) {
            $totales[$campo] = array_sum(array_column($filas, $campo));
        }

        return ['filas' => collect($filas), 'totales' => $totales];
    }

    private function porCuenta(string $desde, string $hasta): Builder
    {
        $agregada = $this->acumulados($desde, $hasta)->select('cuenta.id', 'cuenta.codigo', 'cuenta.nombre as cuenta', 'cuenta.clase')
            ->selectRaw('sum(case when asiento.fecha < ? then debe - haber else 0 end) as apertura', [$desde])
            ->selectRaw('sum(case when asiento.fecha >= ? then debe else 0 end) as debe', [$desde])
            ->selectRaw('sum(case when asiento.fecha >= ? then haber else 0 end) as haber', [$desde])
            ->groupBy('cuenta.id', 'cuenta.codigo', 'cuenta.nombre', 'cuenta.clase');

        return DB::query()->fromSub($agregada, 'cuentas_con_saldo')->select('cuentas_con_saldo.*');
    }

    private function acumulados(string $desde, string $hasta): Builder
    {
        return $this->datos->lineas()->where('asiento.fecha', '<=', $hasta);
    }

    private function resultadoAcumulado(string $fecha, bool $estricto = false): int
    {
        return (int) $this->datos->lineas()->whereIn('cuenta.clase', ['ingreso', 'gasto'])
            ->where('asiento.fecha', $estricto ? '<' : '<=', $fecha)->select([])
            ->selectRaw('coalesce(sum(haber - debe),0) as resultado')->first()->resultado;
    }
}
