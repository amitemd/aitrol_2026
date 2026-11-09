<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use InvalidArgumentException;
use RuntimeException;

class V3AccountingReconciliationService
{
    const REQUIRED = array('RECEIVABLES', 'PAYABLES', 'INVENTORY', 'FIXED_ASSETS', 'TAXES', 'BANKS');

    private $integrity;

    public function __construct(V3AccountingIntegrityService $integrity)
    {
        $this->integrity = $integrity;
    }

    public function ledgerBalance($companyId, $accountId, $asOf)
    {
        $row = DB::table('ct_asientos_detalle as ad')->join('ct_asientos_cabecera as ac', 'ac.id', '=', 'ad.id_asiento_cabecera')
            ->join('plan_cuentas_empresa as pe', function ($join) {
                $join->on('pe.id_plan', '=', 'ad.id_plan_cuenta')->on('pe.id_empresa', '=', 'ac.id_empresa');
            })->where('ac.id_empresa', $companyId)->where('ad.id_plan_cuenta', $accountId)
            ->where('ac.estado', 1)->where('ad.estado', 1)->where('ac.fecha_asiento', '<=', Carbon::parse($asOf)->endOfDay()->toDateTimeString())
            ->select('pe.naturaleza', DB::raw('COALESCE(SUM(ad.debe),0) AS debe'), DB::raw('COALESCE(SUM(ad.haber),0) AS haber'))->groupBy('pe.naturaleza')->first();
        if (!$row) return 0.0;
        // El plan legado usa 1=deudora y 0=acreedora; tambien admite etiquetas.
        $credit = in_array(strtoupper((string) $row->naturaleza), array('0', 'H', 'HABER', 'ACREEDORA'), true);
        return round($credit ? (float) $row->haber - (float) $row->debe : (float) $row->debe - (float) $row->haber, 2);
    }

    public function record($companyId, $period, $type, $accountId, $auxiliaryBalance, $evidence)
    {
        if (!Schema::hasTable('v3_accounting_aux_reconciliations')) throw new RuntimeException('Falta la migración de gobierno contable.');
        $type = strtoupper(trim((string) $type));
        if (!in_array($type, self::REQUIRED, true)) throw new InvalidArgumentException('El tipo de auxiliar no es válido.');
        if (strlen(trim((string) $evidence)) < 10) throw new InvalidArgumentException('Debe indicar la evidencia del saldo auxiliar.');
        $date = Carbon::createFromFormat('Y-m', $period)->endOfMonth();
        return DB::transaction(function () use ($companyId, $type, $accountId, $auxiliaryBalance, $evidence, $date) {
            $this->integrity->assertPeriodOpen($companyId, $date->toDateString());
            $account = DB::table('plan_cuentas_empresa')->where('id_empresa', $companyId)->where('id_plan', $accountId)->whereIn('estado', [1, 2])->first();
            if (!$account) throw new InvalidArgumentException('La cuenta no pertenece al plan activo de la empresa.');
            $ledger = $this->ledgerBalance($companyId, $accountId, $date->toDateString());
            $difference = round((float) $auxiliaryBalance - $ledger, 2);
            $values = array('saldo_auxiliar' => round((float) $auxiliaryBalance, 2), 'saldo_mayor' => $ledger,
                'diferencia' => $difference, 'estado' => abs($difference) <= 0.01 ? 'RECONCILED' : 'DIFFERENCE',
                'evidencia' => trim($evidence), 'preparado_por' => Auth::check() ? Auth::user()->id : null,
                'aprobado_por' => null, 'aprobado_at' => null, 'updated_at' => date('Y-m-d H:i:s'));
            $existing = DB::table('v3_accounting_aux_reconciliations')->where('id_empresa', $companyId)
                ->where('anio', $date->year)->where('mes', $date->month)->where('tipo_auxiliar', $type)
                ->where('id_plan_cuenta', $accountId)->lockForUpdate()->first();
            if ($existing) {
                if ($existing->estado === 'APPROVED') throw new RuntimeException('La conciliación ya fue aprobada; para modificarla debe reabrirse el periodo y conservar una nueva evidencia.');
                DB::table('v3_accounting_aux_reconciliations')->where('id', $existing->id)->update($values);
                return $existing->id;
            }
            return DB::table('v3_accounting_aux_reconciliations')->insertGetId(array_merge($values, array(
                'id_empresa' => $companyId, 'anio' => $date->year, 'mes' => $date->month, 'tipo_auxiliar' => $type,
                'id_plan_cuenta' => $accountId, 'created_at' => date('Y-m-d H:i:s'))));
        });
    }

    public function approve($companyId, $id)
    {
        return DB::transaction(function () use ($companyId, $id) {
            $row = DB::table('v3_accounting_aux_reconciliations')->where('id', $id)->where('id_empresa', $companyId)->lockForUpdate()->first();
            if (!$row) throw new InvalidArgumentException('La conciliación auxiliar no existe.');
            $this->integrity->assertPeriodOpen($companyId, sprintf('%04d-%02d-01', $row->anio, $row->mes));
            if ($row->estado !== 'RECONCILED') throw new RuntimeException('No se puede aprobar una conciliación con diferencia.');
            $user = Auth::check() ? (string) Auth::user()->id : '';
            if ($user !== '' && (string) $row->preparado_por === $user) throw new RuntimeException('El aprobador debe ser distinto del preparador.');
            DB::table('v3_accounting_aux_reconciliations')->where('id', $row->id)->update(array(
                'estado' => 'APPROVED', 'aprobado_por' => $user ?: null, 'aprobado_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')));
            return true;
        });
    }

    public function status($companyId, $period)
    {
        $date = Carbon::createFromFormat('Y-m', $period);
        $rows = Schema::hasTable('v3_accounting_aux_reconciliations')
            ? DB::table('v3_accounting_aux_reconciliations')->where('id_empresa', $companyId)
                ->where('anio', $date->year)->where('mes', $date->month)->get() : collect();
        $types = array();
        foreach (self::REQUIRED as $type) {
            $matches = $rows->where('tipo_auxiliar', $type);
            $types[$type] = array('registros' => $matches->count(), 'aprobados' => $matches->where('estado', 'APPROVED')->count(),
                'diferencia' => round((float) $matches->sum('diferencia'), 2));
        }
        $missing = array();
        foreach ($types as $type => $summary) if ($summary['registros'] === 0 || $summary['aprobados'] !== $summary['registros'] || abs($summary['diferencia']) > 0.01) $missing[] = $type;
        return array('periodo' => $period, 'tipos' => $types, 'pendientes' => $missing, 'certificado' => empty($missing));
    }
}
