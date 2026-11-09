<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\Budget;
use Sis_medico\v3\contable\BudgetCommitment;
use Sis_medico\v3\contable\BudgetLine;

class BudgetExecutionService
{
    public function build(Budget $budget)
    {
        $budget->load(array('lines.months', 'lines.costCenter'));
        $movements = $this->accountingMovements($budget);
        $reserved = DB::table('v3_budget_commitments')
            ->where('id_empresa', $budget->id_empresa)
            ->where('id_presupuesto', $budget->id)
            ->where('estado', BudgetCommitment::STATUS_RESERVED)
            ->groupBy('id_linea_presupuesto')
            ->select('id_linea_presupuesto', DB::raw('SUM(valor) as valor'))
            ->pluck('valor', 'id_linea_presupuesto');

        $monthly = array();
        for ($month = 1; $month <= 12; $month++) {
            $monthly[$month] = array(
                'mes' => $month, 'plan_ingreso' => 0, 'real_ingreso' => 0,
                'plan_gasto' => 0, 'real_gasto' => 0, 'comprometido' => 0,
            );
        }
        $totals = array(
            'inicial_ingreso' => 0, 'inicial_gasto' => 0,
            'plan_ingreso' => 0, 'plan_gasto' => 0,
            'real_ingreso' => 0, 'real_gasto' => 0,
            'comprometido' => 0, 'disponible_gasto' => 0,
        );

        foreach ($budget->lines as $line) {
            $initial = 0;
            $current = 0;
            $actual = 0;
            $lineMonthly = array();
            $monthRows = $line->months->keyBy('mes');
            for ($month = 1; $month <= 12; $month++) {
                $monthRow = $monthRows->get($month);
                $monthInitial = $monthRow ? (float) $monthRow->valor_inicial : 0;
                $monthAdjustment = $monthRow ? (float) $monthRow->valor_reforma : 0;
                $monthCurrent = round($monthInitial + $monthAdjustment, 2);
                $movementKey = $this->movementKey($line->id_plan_cuenta, $line->id_centro_costo, $month);
                $movement = isset($movements[$movementKey]) ? $movements[$movementKey] : array('debe' => 0, 'haber' => 0);
                $monthActual = $line->tipo === BudgetLine::TYPE_INCOME
                    ? round($movement['haber'] - $movement['debe'], 2)
                    : round($movement['debe'] - $movement['haber'], 2);
                $lineMonthly[$month] = array(
                    'inicial' => $monthInitial, 'reforma' => $monthAdjustment,
                    'vigente' => $monthCurrent, 'ejecutado' => $monthActual,
                );
                $initial += $monthInitial;
                $current += $monthCurrent;
                $actual += $monthActual;
                $prefix = $line->tipo === BudgetLine::TYPE_INCOME ? 'ingreso' : 'gasto';
                $monthly[$month]['plan_'.$prefix] += $monthCurrent;
                $monthly[$month]['real_'.$prefix] += $monthActual;
            }
            $lineReserved = (float) ($reserved[$line->id] ?? 0);
            $line->valor_inicial = round($initial, 2);
            $line->valor_vigente = round($current, 2);
            $line->valor_ejecutado = round($actual, 2);
            $line->valor_comprometido = round($lineReserved, 2);
            $line->valor_disponible = round($current - $actual - $lineReserved, 2);
            $line->porcentaje_ejecucion = $current > 0 ? round($actual * 100 / $current, 1) : null;
            $line->ejecucion_mensual = $lineMonthly;

            $prefix = $line->tipo === BudgetLine::TYPE_INCOME ? 'ingreso' : 'gasto';
            $totals['inicial_'.$prefix] += $initial;
            $totals['plan_'.$prefix] += $current;
            $totals['real_'.$prefix] += $actual;
            if ($line->tipo === BudgetLine::TYPE_EXPENSE) {
                $totals['comprometido'] += $lineReserved;
                $totals['disponible_gasto'] += $line->valor_disponible;
            }
        }

        $commitmentsByMonth = DB::table('v3_budget_commitments')
            ->where('id_empresa', $budget->id_empresa)->where('id_presupuesto', $budget->id)
            ->where('estado', BudgetCommitment::STATUS_RESERVED)
            ->groupBy(DB::raw('MONTH(fecha)'))
            ->select(DB::raw('MONTH(fecha) as mes'), DB::raw('SUM(valor) as valor'))->get();
        foreach ($commitmentsByMonth as $row) {
            if (isset($monthly[(int) $row->mes])) $monthly[(int) $row->mes]['comprometido'] = (float) $row->valor;
        }

        foreach ($monthly as &$row) {
            foreach (array('plan_ingreso', 'real_ingreso', 'plan_gasto', 'real_gasto', 'comprometido') as $field) {
                $row[$field] = round($row[$field], 2);
            }
            $row['neto_plan'] = round($row['plan_ingreso'] - $row['plan_gasto'], 2);
            $row['neto_real'] = round($row['real_ingreso'] - $row['real_gasto'], 2);
        }
        unset($row);
        foreach ($totals as $field => $value) $totals[$field] = round($value, 2);
        $totals['neto_plan'] = round($totals['plan_ingreso'] - $totals['plan_gasto'], 2);
        $totals['neto_real'] = round($totals['real_ingreso'] - $totals['real_gasto'], 2);

        return array('lines' => $budget->lines, 'monthly' => $monthly, 'totals' => $totals);
    }

    public function availableForLine(BudgetLine $line)
    {
        $report = $this->build($line->budget);
        $reported = $report['lines']->firstWhere('id', $line->id);
        return $reported ? (float) $reported->valor_disponible : 0;
    }

    private function accountingMovements(Budget $budget)
    {
        $rows = DB::table('ct_asientos_detalle as detalle')
            ->join('ct_asientos_cabecera as asiento', 'asiento.id', '=', 'detalle.id_asiento_cabecera')
            ->where('asiento.id_empresa', $budget->id_empresa)
            ->where('asiento.estado', 1)->where('detalle.estado', 1)
            ->whereDate('asiento.fecha_asiento', '>=', $budget->fecha_inicio)
            ->whereDate('asiento.fecha_asiento', '<=', $budget->fecha_fin)
            ->whereIn('detalle.id_plan_cuenta', $budget->lines->pluck('id_plan_cuenta')->unique()->values()->all())
            ->groupBy('detalle.id_plan_cuenta', 'detalle.id_centro_costo', DB::raw('MONTH(asiento.fecha_asiento)'))
            ->select('detalle.id_plan_cuenta', 'detalle.id_centro_costo',
                DB::raw('MONTH(asiento.fecha_asiento) as mes'),
                DB::raw('SUM(detalle.debe) as debe'), DB::raw('SUM(detalle.haber) as haber'))->get();
        $result = array();
        foreach ($rows as $row) {
            $result[$this->movementKey($row->id_plan_cuenta, $row->id_centro_costo, $row->mes)] = array(
                'debe' => (float) $row->debe, 'haber' => (float) $row->haber,
            );
        }
        return $result;
    }

    private function movementKey($account, $center, $month)
    {
        return (string) $account.'|'.($center === null ? 'NULL' : (string) $center).'|'.(int) $month;
    }
}
