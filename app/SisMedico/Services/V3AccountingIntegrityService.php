<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use InvalidArgumentException;
use RuntimeException;

class V3AccountingIntegrityService
{
    const TOLERANCE = 0.01;

    public function assertPeriodOpen($companyId, $date)
    {
        $date = Carbon::parse($date);
        if (!Schema::hasTable('v3_accounting_periods')) return true;
        DB::statement('INSERT IGNORE INTO v3_accounting_periods (id_empresa, anio, mes, estado, version, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
            array($companyId, $date->year, $date->month, 'OPEN', 1, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')));
        $period = DB::table('v3_accounting_periods')->where('id_empresa', $companyId)
            ->where('anio', $date->year)->where('mes', $date->month)->lockForUpdate()->first();
        if ($period && $period->estado !== 'OPEN') {
            throw new RuntimeException('El periodo contable '.$date->format('Y-m').' se encuentra '.$period->estado.'.');
        }
        return true;
    }

    public function assertBalancedEntry($entryId)
    {
        if (!$entryId) throw new InvalidArgumentException('No se recibió el asiento que debe validarse.');
        $entry = DB::table('ct_asientos_cabecera')->where('id', $entryId)->lockForUpdate()->first();
        if (!$entry) throw new InvalidArgumentException('El asiento contable no existe.');
        $totals = DB::table('ct_asientos_detalle')->where('id_asiento_cabecera', $entryId)
            ->where('estado', 1)->select(DB::raw('COUNT(*) AS lineas'), DB::raw('COALESCE(SUM(debe),0) AS debe'),
                DB::raw('COALESCE(SUM(haber),0) AS haber'),
                DB::raw('SUM(CASE WHEN debe < 0 OR haber < 0 OR (debe > 0 AND haber > 0) THEN 1 ELSE 0 END) AS invalidas'))->first();
        if (!$totals || (int) $totals->lineas < 2) throw new RuntimeException('El asiento debe contener al menos dos líneas activas.');
        if ((int) $totals->invalidas > 0) throw new RuntimeException('El asiento contiene valores negativos o líneas con debe y haber simultáneos.');
        if (abs((float) $totals->debe - (float) $totals->haber) > self::TOLERANCE) {
            throw new RuntimeException('El asiento no cuadra. Debe: '.number_format($totals->debe, 2).' Haber: '.number_format($totals->haber, 2).'.');
        }
        if ((float) $totals->debe <= 0 || (float) $totals->haber <= 0) throw new RuntimeException('El asiento no contiene valores contables válidos.');
        $foreignAccounts = DB::table('ct_asientos_detalle as ad')
            ->leftJoin('plan_cuentas_empresa as pe', function ($join) use ($entry) {
                $join->on('pe.id_plan', '=', 'ad.id_plan_cuenta')->where('pe.id_empresa', '=', $entry->id_empresa);
            })->where('ad.id_asiento_cabecera', $entryId)->where('ad.estado', 1)->whereNull('pe.id')->count();
        if ($foreignAccounts) throw new RuntimeException('El asiento utiliza una cuenta que no pertenece al plan de la empresa.');
        return array('lineas' => (int) $totals->lineas, 'debe' => round((float) $totals->debe, 2), 'haber' => round((float) $totals->haber, 2));
    }

    public function audit($companyId, $from, $to)
    {
        $from = Carbon::parse($from)->startOfDay()->toDateTimeString();
        $to = Carbon::parse($to)->endOfDay()->toDateTimeString();
        $base = DB::table('ct_asientos_cabecera as ac')->where('ac.id_empresa', $companyId)
            ->where('ac.estado', 1)->whereBetween('ac.fecha_asiento', array($from, $to));
        $unbalancedRows = (clone $base)->join('ct_asientos_detalle as ad', 'ad.id_asiento_cabecera', '=', 'ac.id')
            ->where('ad.estado', 1)->select('ac.id')->groupBy('ac.id')
            ->havingRaw('ABS(SUM(ad.debe)-SUM(ad.haber)) > 0.01')->get();
        $unbalanced = count($unbalancedRows);
        $withoutLines = (clone $base)->whereNotExists(function ($query) {
            $query->select(DB::raw(1))->from('ct_asientos_detalle as ad')
                ->whereRaw('ad.id_asiento_cabecera = ac.id')->where('ad.estado', 1);
        })->count();
        $invalidLines = DB::table('ct_asientos_detalle as ad')->join('ct_asientos_cabecera as ac', 'ac.id', '=', 'ad.id_asiento_cabecera')
            ->where('ac.id_empresa', $companyId)->where('ac.estado', 1)->where('ad.estado', 1)
            ->whereBetween('ac.fecha_asiento', array($from, $to))->where(function ($query) {
                $query->where('ad.debe', '<', 0)->orWhere('ad.haber', '<', 0)
                    ->orWhere(function ($line) { $line->where('ad.debe', '>', 0)->where('ad.haber', '>', 0); });
            })->count();
        $missingClassification = Schema::hasColumn('plan_cuentas_empresa', 'categoria_niif')
            ? DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)->where('estado', 1)
                ->where(function ($query) { $query->whereNull('categoria_niif')->orWhere('categoria_niif', ''); })->count() : 1;
        $invalidClassification = Schema::hasColumn('plan_cuentas_empresa', 'categoria_niif')
            ? DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)->where('estado', 1)
                ->whereNotNull('categoria_niif')->where('categoria_niif', '<>', '')
                ->whereNotIn('categoria_niif', array('ACTIVO', 'PASIVO', 'PATRIMONIO', 'INGRESO', 'COSTO', 'GASTO'))->count() : 0;
        $possibleDuplicates = (clone $base)->whereNotNull('ac.fact_numero')->where('ac.fact_numero', '<>', '')
            ->select('ac.fecha_asiento', 'ac.sucursal', 'ac.punto_emision', 'ac.fact_numero', 'ac.valor', DB::raw('COUNT(*) AS cantidad'))
            ->groupBy('ac.fecha_asiento', 'ac.sucursal', 'ac.punto_emision', 'ac.fact_numero', 'ac.valor')->havingRaw('COUNT(*) > 1')->get()->count();
        return array('asientos_descuadrados' => $unbalanced, 'asientos_sin_detalle' => $withoutLines,
            'lineas_invalidas' => $invalidLines, 'cuentas_sin_categoria_niif' => $missingClassification,
            'cuentas_categoria_niif_invalida' => $invalidClassification, 'grupos_posibles_duplicados' => $possibleDuplicates,
            'integro' => $unbalanced === 0 && $withoutLines === 0 && $invalidLines === 0
                && $missingClassification === 0 && $invalidClassification === 0);
    }
}
