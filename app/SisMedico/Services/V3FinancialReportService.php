<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class V3FinancialReportService
{
    const TOLERANCE = 0.01;

    public function ledger($companyId, $from, $to, $accountPrefix = null)
    {
        list($from, $to) = $this->period($from, $to);
        $query = $this->activeLines($companyId)
            ->whereBetween('ac.fecha_asiento', array($from.' 00:00:00', $to.' 23:59:59'));
        if ($accountPrefix !== null && $accountPrefix !== '') {
            $query->where('pe.plan', 'like', $this->validPrefix($accountPrefix).'%');
        }

        return $query->orderBy('ac.fecha_asiento')->orderBy('ac.id')->orderBy('ad.id')->get();
    }

    public function generalLedger($companyId, $from, $to, $accountPrefix)
    {
        list($from, $to) = $this->period($from, $to);
        $prefix = $this->validPrefix($accountPrefix);
        $opening = $this->activeLines($companyId)
            ->where('ac.fecha_asiento', '<', $from.' 00:00:00')
            ->where('pe.plan', 'like', $prefix.'%')
            ->select(DB::raw('COALESCE(SUM(ad.debe),0) AS debe'), DB::raw('COALESCE(SUM(ad.haber),0) AS haber'))
            ->first();
        $lines = $this->ledger($companyId, $from, $to, $prefix);
        $balance = round((float) $opening->debe - (float) $opening->haber, 2);
        foreach ($lines as $line) {
            $balance = round($balance + (float) $line->debe - (float) $line->haber, 2);
            $line->saldo = $balance;
        }

        return array('desde' => $from, 'hasta' => $to, 'saldo_inicial' => round((float) $opening->debe - (float) $opening->haber, 2),
            'movimientos' => $lines, 'saldo_final' => $balance);
    }

    public function trialBalance($companyId, $from, $to)
    {
        list($from, $to) = $this->period($from, $to);
        $rows = $this->activeLines($companyId)
            ->where('ac.fecha_asiento', '<=', $to.' 23:59:59')
            ->groupBy('pe.id_plan', 'pe.plan', 'pe.nombre', 'pe.naturaleza', 'pe.categoria_niif', 'pe.flujo_efectivo_categoria', 'pe.es_efectivo')
            ->orderBy('pe.plan')
            ->select(array('pe.id_plan', 'pe.plan', 'pe.nombre', 'pe.naturaleza', 'pe.categoria_niif', 'pe.flujo_efectivo_categoria', 'pe.es_efectivo',
                DB::raw("ROUND(SUM(CASE WHEN ac.fecha_asiento >= '".$from." 00:00:00' THEN ad.debe ELSE 0 END),2) AS debe"),
                DB::raw("ROUND(SUM(CASE WHEN ac.fecha_asiento >= '".$from." 00:00:00' THEN ad.haber ELSE 0 END),2) AS haber"),
                DB::raw("ROUND(SUM(CASE WHEN ac.fecha_asiento < '".$from." 00:00:00' THEN ad.debe-ad.haber ELSE 0 END),2) AS saldo_inicial"),
                DB::raw('ROUND(SUM(ad.debe-ad.haber),2) AS saldo_final')))
            ->get();
        $totals = array('debe' => 0.0, 'haber' => 0.0, 'deudor' => 0.0, 'acreedor' => 0.0,
            'inicial_deudor' => 0.0, 'inicial_acreedor' => 0.0);
        foreach ($rows as $row) {
            $net = round((float) $row->saldo_final, 2);
            $row->deudor = max($net, 0);
            $row->acreedor = max(-$net, 0);
            $row->inicial_deudor = max((float) $row->saldo_inicial, 0);
            $row->inicial_acreedor = max(-(float) $row->saldo_inicial, 0);
            foreach (array('debe', 'haber', 'deudor', 'acreedor', 'inicial_deudor', 'inicial_acreedor') as $key)
                $totals[$key] = round($totals[$key] + (float) $row->{$key}, 2);
        }
        $difference = round($totals['debe'] - $totals['haber'], 2);
        $balanceDifference = round($totals['deudor'] - $totals['acreedor'], 2);

        return array('desde' => $from, 'hasta' => $to, 'cuentas' => $rows, 'totales' => $totals,
            'diferencia' => $difference, 'diferencia_saldos' => $balanceDifference,
            'cuadrado' => abs($difference) <= self::TOLERANCE && abs($balanceDifference) <= self::TOLERANCE);
    }

    public function incomeStatement($companyId, $from, $to)
    {
        $trial = $this->trialBalance($companyId, $from, $to);
        $groups = array('INGRESO' => 0.0, 'COSTO' => 0.0, 'GASTO' => 0.0);
        $details = array('INGRESO' => array(), 'COSTO' => array(), 'GASTO' => array());
        foreach ($trial['cuentas'] as $row) {
            $category = $this->category($row);
            $amount = 0.0;
            if ($category === 'INGRESO') {
                $amount = (float) $row->haber - (float) $row->debe;
                $groups['INGRESO'] += $amount;
            } elseif (isset($groups[$category])) {
                $amount = (float) $row->debe - (float) $row->haber;
                $groups[$category] += $amount;
            }
            if (isset($details[$category]) && abs($amount) > self::TOLERANCE) $details[$category][] = array(
                'id_plan' => $row->id_plan, 'plan' => $row->plan, 'nombre' => $row->nombre,
                'valor' => round($amount, 2));
        }
        foreach ($groups as $key => $amount) {
            $groups[$key] = round($amount, 2);
        }
        $result = round($groups['INGRESO'] - $groups['COSTO'] - $groups['GASTO'], 2);

        return array('desde' => $trial['desde'], 'hasta' => $trial['hasta'], 'categorias' => $groups,
            'detalle' => $details, 'resultado' => $result);
    }

    public function financialPosition($companyId, $asOf)
    {
        $asOf = Carbon::parse($asOf)->toDateString();
        $first = DB::table('ct_asientos_cabecera')->where('id_empresa', $companyId)->where('estado', 1)
            ->where('fecha_asiento', '<=', $asOf.' 23:59:59')->min('fecha_asiento');
        if (!$first) return array('fecha' => $asOf, 'categorias' => array('ACTIVO' => 0.0, 'PASIVO' => 0.0, 'PATRIMONIO' => 0.0),
            'detalle' => array('ACTIVO' => array(), 'PASIVO' => array(), 'PATRIMONIO' => array()),
            'diferencia' => 0.0, 'cuadrado' => true);
        list($from, $to) = $this->period($first ? date('Y-m-d', strtotime($first)) : '1900-01-01', $asOf);
        $trial = $this->trialBalance($companyId, $from, $to);
        $groups = array('ACTIVO' => 0.0, 'PASIVO' => 0.0, 'PATRIMONIO' => 0.0);
        $details = array('ACTIVO' => array(), 'PASIVO' => array(), 'PATRIMONIO' => array());
        $periodResult = array('INGRESO' => 0.0, 'COSTO' => 0.0, 'GASTO' => 0.0);
        foreach ($trial['cuentas'] as $row) {
            $category = $this->category($row);
            $debitBalance = (float) $row->deudor;
            $creditBalance = (float) $row->acreedor;
            if ($category === 'ACTIVO') {
                $value = $debitBalance - $creditBalance; $groups[$category] += $value;
                if (abs($value) > self::TOLERANCE) $details[$category][] = array('plan' => $row->plan, 'nombre' => $row->nombre, 'valor' => round($value, 2));
            } elseif (isset($groups[$category])) {
                $value = $creditBalance - $debitBalance; $groups[$category] += $value;
                if (abs($value) > self::TOLERANCE) $details[$category][] = array('plan' => $row->plan, 'nombre' => $row->nombre, 'valor' => round($value, 2));
            } elseif ($category === 'INGRESO') {
                $periodResult[$category] += $creditBalance - $debitBalance;
            } elseif (isset($periodResult[$category])) {
                $periodResult[$category] += $debitBalance - $creditBalance;
            }
        }
        $currentResult = round($periodResult['INGRESO'] - $periodResult['COSTO'] - $periodResult['GASTO'], 2);
        $groups['PATRIMONIO'] += $currentResult;
        if (abs($currentResult) > self::TOLERANCE) {
            $details['PATRIMONIO'][] = array('plan' => '', 'nombre' => 'Resultado acumulado hasta la fecha', 'valor' => $currentResult);
        }
        foreach ($groups as $key => $amount) {
            $groups[$key] = round($amount, 2);
        }
        $difference = round($groups['ACTIVO'] - $groups['PASIVO'] - $groups['PATRIMONIO'], 2);

        return array('fecha' => $to, 'categorias' => $groups, 'detalle' => $details, 'diferencia' => $difference,
            'cuadrado' => abs($difference) <= self::TOLERANCE);
    }

    public function cashFlow($companyId, $from, $to)
    {
        list($from, $to) = $this->period($from, $to);
        $opening = $this->cashBalance($companyId, null, Carbon::parse($from)->subDay()->toDateString());
        $closing = $this->cashBalance($companyId, null, $to);
        $activities = array('OPERACION' => 0.0, 'INVERSION' => 0.0, 'FINANCIACION' => 0.0);
        $unclassified = array();
        $rows = $this->activeLines($companyId)
            ->whereBetween('ac.fecha_asiento', array($from.' 00:00:00', $to.' 23:59:59'))
            ->where('pe.es_efectivo', 0)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))->from('ct_asientos_detalle as cash_line')
                    ->join('plan_cuentas_empresa as cash_account', function ($join) {
                        $join->on('cash_account.id_plan', '=', 'cash_line.id_plan_cuenta')
                            ->on('cash_account.id_empresa', '=', 'ac.id_empresa');
                    })->whereRaw('cash_line.id_asiento_cabecera = ac.id')
                    ->where('cash_line.estado', 1)->where('cash_account.es_efectivo', 1);
            })
            ->groupBy('ac.id', 'ac.modulo', 'pe.id_plan', 'pe.plan', 'pe.nombre', 'pe.flujo_efectivo_categoria')
            ->select(array('ac.id as entry_id', 'ac.modulo', 'pe.id_plan', 'pe.plan', 'pe.nombre', 'pe.flujo_efectivo_categoria',
                DB::raw('ROUND(SUM(ad.haber-ad.debe),2) AS flujo')))
            ->get();
        $cashByEntry = $this->activeLines($companyId)->where('pe.es_efectivo', 1)
            ->whereBetween('ac.fecha_asiento', [$from.' 00:00:00', $to.' 23:59:59'])
            ->groupBy('ac.id')->select('ac.id', DB::raw('ROUND(SUM(ad.debe-ad.haber),2) as efectivo'))->get()->keyBy('id');
        foreach ($rows->groupBy('entry_id') as $entryId => $entryRows) {
            $cashAmount = (float) $cashByEntry->get($entryId)->efectivo;
            if (abs($cashAmount) <= self::TOLERANCE) continue;
            // En un aporte mixto, el activo entregado en especie no es una salida
            // de efectivo. Distribuir solo el efectivo contra las lineas opuestas.
            $counterparts = $entryRows->filter(fn ($row) => (float) $row->flujo * $cashAmount > 0)->values();
            $weights = (float) $counterparts->sum(fn ($row) => abs((float) $row->flujo));
            $allocated = 0.0;
            foreach ($counterparts as $index => $row) {
                $amount = $index === $counterparts->count()-1 ? round($cashAmount-$allocated,2)
                    : round($cashAmount*abs((float)$row->flujo)/$weights,2);
                $allocated += $amount;
                $activity = strtoupper((string) $row->flujo_efectivo_categoria);
                // El pago de un activo mantiene su naturaleza de inversion aunque
                // se cancele por la misma cuenta de proveedores que otras compras.
                if ($row->modulo === 'PAGOS_PROVEEDORES' && $amount < 0) {
                    $assetPayment = (float) DB::table('ct_detalle_comprobante_egreso as d')
                        ->join('ct_comprobante_egreso as h', 'h.id', '=', 'd.id_comprobante')
                        ->join('ct_compras as c', 'c.id', '=', 'd.id_compra')
                        ->where('h.id_empresa', $companyId)->where('h.id_asiento_cabecera', $entryId)
                        ->where('h.estado',1)->where('d.estado',1)->where('c.tipo',2)->sum('d.abono');
                    $investment = min(abs($amount), round($assetPayment,2));
                    $activities['INVERSION'] = round($activities['INVERSION']-$investment,2);
                    $amount = round($amount+$investment,2);
                }
                if (isset($activities[$activity])) {
                    $activities[$activity] = round($activities[$activity] + $amount, 2);
                } elseif (abs($amount) > self::TOLERANCE) {
                    $unclassified[] = array('id_plan'=>$row->id_plan,'plan'=>$row->plan,'nombre'=>$row->nombre,'valor'=>$amount);
                }
            }
        }
        $net = round(array_sum($activities), 2);
        $bookVariation = round($closing - $opening, 2);
        $difference = round($bookVariation - $net, 2);

        return array('desde' => $from, 'hasta' => $to, 'efectivo_inicial' => $opening, 'actividades' => $activities,
            'variacion_actividades' => $net, 'efectivo_final' => $closing, 'variacion_mayor' => $bookVariation,
            'diferencia' => $difference, 'conciliado' => empty($unclassified) && abs($difference) <= self::TOLERANCE,
            'cuentas_sin_clasificar' => $unclassified);
    }

    public function equityChanges($companyId, $from, $to)
    {
        list($from, $to) = $this->period($from, $to);
        $accounts = DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)
            ->whereIn('estado', [1, 2])->where('categoria_niif', 'PATRIMONIO')->orderBy('plan')->get();
        $components = array();
        foreach ($accounts as $account) {
            $opening = $this->accountBalance($companyId, $account->id_plan, null, Carbon::parse($from)->subDay()->toDateString(), true);
            $movement = $this->accountBalance($companyId, $account->id_plan, $from, $to, true);
            $components[] = array('plan' => $account->plan, 'nombre' => $account->nombre, 'saldo_inicial' => $opening,
                'movimiento' => $movement, 'saldo_final' => round($opening + $movement, 2));
        }
        $positionOpening = $this->financialPosition($companyId, Carbon::parse($from)->subDay()->toDateString());
        $configuredOpening = round(array_sum(array_column($components, 'saldo_inicial')), 2);
        $priorUnclosedResult = round($positionOpening['categorias']['PATRIMONIO'] - $configuredOpening, 2);
        if (abs($priorUnclosedResult) > self::TOLERANCE) {
            $components[] = array('plan' => '', 'nombre' => 'Resultados acumulados no cerrados', 'saldo_inicial' => $priorUnclosedResult,
                'movimiento' => 0.0, 'saldo_final' => $priorUnclosedResult);
        }
        $periodResult = $this->incomeStatement($companyId, $from, $to)['resultado'];
        if (abs($periodResult) > self::TOLERANCE) {
            $components[] = array('plan' => '', 'nombre' => 'Resultado del período', 'saldo_inicial' => 0.0,
                'movimiento' => $periodResult, 'saldo_final' => $periodResult);
        }
        $openingTotal = round(array_sum(array_column($components, 'saldo_inicial')), 2);
        $movementTotal = round(array_sum(array_column($components, 'movimiento')), 2);
        $closingTotal = round(array_sum(array_column($components, 'saldo_final')), 2);
        $position = $this->financialPosition($companyId, $to);
        $difference = round($position['categorias']['PATRIMONIO'] - $closingTotal, 2);

        return array('desde' => $from, 'hasta' => $to, 'componentes' => $components, 'saldo_inicial' => $openingTotal,
            'movimientos' => $movementTotal, 'saldo_final' => $closingTotal, 'patrimonio_situacion' => $position['categorias']['PATRIMONIO'],
            'diferencia' => $difference, 'conciliado' => abs($difference) <= self::TOLERANCE);
    }

    public function certifiedPackage($companyId, $from, $to)
    {
        $trial = $this->trialBalance($companyId, $from, $to);
        $income = $this->incomeStatement($companyId, $from, $to);
        $position = $this->financialPosition($companyId, $to);
        $cash = $this->cashFlow($companyId, $from, $to);
        $equity = $this->equityChanges($companyId, $from, $to);

        return array('balance_comprobacion' => $trial, 'resultados' => $income, 'situacion_financiera' => $position,
            'flujo_efectivo' => $cash, 'cambios_patrimonio' => $equity,
            'certificado' => $trial['cuadrado'] && $position['cuadrado'] && $cash['conciliado'] && $equity['conciliado']);
    }

    public function journalRows($companyId, $from, $to)
    {
        $rows = $this->ledger($companyId, $from, $to);
        $totalDebit = round((float) $rows->sum('debe'), 2);
        $totalCredit = round((float) $rows->sum('haber'), 2);
        $entryTotals = array();
        foreach ($rows as $row) {
            if (!isset($entryTotals[$row->id_asiento_cabecera])) $entryTotals[$row->id_asiento_cabecera] = array(0.0, 0.0);
            $entryTotals[$row->id_asiento_cabecera][0] += (float) $row->debe;
            $entryTotals[$row->id_asiento_cabecera][1] += (float) $row->haber;
        }
        foreach ($rows as $row) {
            $row->id = $row->id_asiento_cabecera; $row->nombre_cuenta = $row->nombre;
            $row->total_debe = $totalDebit; $row->total_haber = $totalCredit;
            $row->cuadrado = abs($entryTotals[$row->id][0] - $entryTotals[$row->id][1]) <= self::TOLERANCE ? 1 : 0;
        }
        return $rows;
    }

    public function majorizationRows($companyId, $from, $to)
    {
        $rows = $this->journalRows($companyId, $from, $to);
        foreach ($rows as $row) { $row->nombre_cuenta = $row->nombre; }
        return $rows;
    }

    private function activeLines($companyId)
    {
        if ($companyId === null || $companyId === '') {
            throw new InvalidArgumentException('No existe una empresa activa.');
        }
        return DB::table('ct_asientos_detalle as ad')->join('ct_asientos_cabecera as ac', 'ac.id', '=', 'ad.id_asiento_cabecera')
            ->join('plan_cuentas_empresa as pe', function ($join) {
                $join->on('pe.id_plan', '=', 'ad.id_plan_cuenta')->on('pe.id_empresa', '=', 'ac.id_empresa');
            })->leftJoin('users as creator', 'creator.id', '=', 'ac.id_usuariocrea')
            ->leftJoin('users as modifier', 'modifier.id', '=', 'ac.id_usuariomod')
            ->where('ac.id_empresa', $companyId)->where('ac.estado', 1)->where('ad.estado', 1)
            ->select('ad.id', 'ad.id_asiento_cabecera', 'ad.id_plan_cuenta', 'ad.descripcion', 'ad.debe', 'ad.haber',
                'ac.fecha_asiento', 'ac.observacion', 'pe.plan', 'pe.nombre', 'pe.naturaleza', 'pe.categoria_niif',
                'pe.flujo_efectivo_categoria', 'pe.es_efectivo', 'ac.created_at', 'ac.updated_at', 'ac.aparece_sri',
                DB::raw("CONCAT(COALESCE(creator.nombre1,''),' ',COALESCE(creator.apellido1,'')) AS usuariocrea"),
                DB::raw("CONCAT(COALESCE(modifier.nombre1,''),' ',COALESCE(modifier.apellido1,'')) AS usuariomod"));
    }

    private function cashBalance($companyId, $from, $to)
    {
        $query = $this->activeLines($companyId)->where('pe.es_efectivo', 1)->where('ac.fecha_asiento', '<=', $to.' 23:59:59');
        if ($from) {
            $query->where('ac.fecha_asiento', '>=', $from.' 00:00:00');
        }
        $row = $query->select(DB::raw('COALESCE(SUM(ad.debe-ad.haber),0) AS saldo'))->first();
        return round((float) $row->saldo, 2);
    }

    private function accountBalance($companyId, $accountId, $from, $to, $creditNature)
    {
        $query = $this->activeLines($companyId)->where('ad.id_plan_cuenta', $accountId)->where('ac.fecha_asiento', '<=', $to.' 23:59:59');
        if ($from) {
            $query->where('ac.fecha_asiento', '>=', $from.' 00:00:00');
        }
        $expression = $creditNature ? 'ad.haber-ad.debe' : 'ad.debe-ad.haber';
        $row = $query->select(DB::raw('COALESCE(SUM('.$expression.'),0) AS saldo'))->first();
        return round((float) $row->saldo, 2);
    }

    private function category($row)
    {
        if (!empty($row->categoria_niif)) {
            return strtoupper($row->categoria_niif);
        }
        return array('1' => 'ACTIVO', '2' => 'PASIVO', '3' => 'PATRIMONIO', '4' => 'INGRESO', '5' => 'COSTO', '6' => 'GASTO')[(string) substr($row->plan, 0, 1)] ?? null;
    }

    private function validPrefix($prefix)
    {
        $prefix = trim((string) $prefix);
        if (!preg_match('/^[0-9]+(?:\.[0-9]+)*$/', $prefix)) {
            throw new InvalidArgumentException('El código contable no tiene un formato válido.');
        }
        return $prefix;
    }

    private function period($from, $to)
    {
        try {
            $from = Carbon::createFromFormat('Y-m-d', (string) $from)->toDateString();
            $to = Carbon::createFromFormat('Y-m-d', (string) $to)->toDateString();
        } catch (\Exception $e) {
            throw new InvalidArgumentException('El período contable no es válido.');
        }
        if ($from > $to) {
            throw new InvalidArgumentException('La fecha inicial no puede ser posterior a la final.');
        }
        return array($from, $to);
    }
}
