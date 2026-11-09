<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;

class V3ProcedureProductionService
{
    public function years($companyId)
    {
        return DB::table('excel_valores as ev')
            ->join('orden_valores as ov', 'ov.id_excel_valores', '=', 'ev.id')
            ->join('orden as o', 'o.id', '=', 'ov.id_orden')
            ->where('ev.id_empresa', $companyId)
            ->whereNotNull('ev.anio')
            ->select('ev.anio')->distinct()->orderBy('ev.anio', 'desc')->get()
            ->pluck('anio')->map(function ($year) { return (int) $year; })->values()->all();
    }

    public function monthly($companyId, $year)
    {
        return $this->base($companyId)->where('ev.anio', (int) $year)
            ->whereBetween('ev.mes', array(1, 12))
            ->select('ev.anio', 'ev.mes')
            ->selectRaw($this->aggregateSql())
            ->groupBy('ev.anio', 'ev.mes')->orderBy('ev.mes')->get();
    }

    public function doctors($companyId, $year, $month)
    {
        return $this->base($companyId)
            ->leftJoin('users as d', 'd.id', '=', 'o.id_doctor')
            ->where('ev.anio', (int) $year)->where('ev.mes', (int) $month)
            ->select('o.id_doctor', 'd.nombre1', 'd.nombre2', 'd.apellido1', 'd.apellido2')
            ->selectRaw($this->aggregateSql())
            ->groupBy('o.id_doctor', 'd.nombre1', 'd.nombre2', 'd.apellido1', 'd.apellido2')
            ->orderBy('d.apellido1')->orderBy('d.nombre1')->get();
    }

    public function totals($rows)
    {
        $totals = array('orders' => 0, 'public_orders' => 0, 'private_orders' => 0, 'particular_orders' => 0, 'value' => 0.0);
        foreach ($rows as $row) {
            $totals['orders'] += (int) $row->orders_total;
            $totals['public_orders'] += (int) $row->public_orders;
            $totals['private_orders'] += (int) $row->private_orders;
            $totals['particular_orders'] += (int) $row->particular_orders;
            $totals['value'] += (float) $row->value_total;
        }
        return $totals;
    }

    private function base($companyId)
    {
        return DB::table('orden as o')
            ->join('orden_valores as ov', 'ov.id_orden', '=', 'o.id')
            ->join('excel_valores as ev', 'ev.id', '=', 'ov.id_excel_valores')
            ->leftJoin('seguros as s', 's.id', '=', 'ev.id_seguro')
            ->where('ev.id_empresa', $companyId);
    }

    private function aggregateSql()
    {
        return 'COUNT(DISTINCT o.id) as orders_total,
            COUNT(DISTINCT CASE WHEN s.tipo = 0 THEN o.id END) as public_orders,
            COUNT(DISTINCT CASE WHEN s.tipo = 1 THEN o.id END) as private_orders,
            COUNT(DISTINCT CASE WHEN s.tipo = 2 THEN o.id END) as particular_orders,
            SUM(CASE WHEN s.tipo = 0 THEN COALESCE(ev.valor, 0) ELSE 0 END) as public_value,
            SUM(CASE WHEN s.tipo = 1 THEN COALESCE(ev.valor, 0) ELSE 0 END) as private_value,
            SUM(CASE WHEN s.tipo = 2 THEN COALESCE(ev.valor, 0) ELSE 0 END) as particular_value,
            SUM(COALESCE(ev.valor, 0)) as value_total';
    }
}
