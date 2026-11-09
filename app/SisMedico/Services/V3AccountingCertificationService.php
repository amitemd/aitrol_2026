<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use RuntimeException;

class V3AccountingCertificationService
{
    private $reports;
    private $integrity;
    private $reconciliations;

    public function __construct(V3FinancialReportService $reports, V3AccountingIntegrityService $integrity, V3AccountingReconciliationService $reconciliations)
    {
        $this->reports = $reports;
        $this->integrity = $integrity;
        $this->reconciliations = $reconciliations;
    }

    public function evaluate($companyId, $from, $to)
    {
        $from = Carbon::parse($from)->toDateString();
        $to = Carbon::parse($to)->toDateString();
        $financial = $this->reports->certifiedPackage($companyId, $from, $to);
        $integrity = $this->integrity->audit($companyId, $from, $to);
        $auxiliary = $this->reconciliations->status($companyId, Carbon::parse($to)->format('Y-m'));
        $certified = $financial['certificado'] && $integrity['integro'] && $auxiliary['certificado'];
        return array('empresa' => $companyId, 'desde' => $from, 'hasta' => $to, 'financiero' => $financial,
            'integridad' => $integrity, 'auxiliares' => $auxiliary, 'certificado' => $certified,
            'evaluado_at' => date('c'));
    }

    public function certify($companyId, $from, $to)
    {
        if (!Schema::hasTable('v3_accounting_certifications')) throw new RuntimeException('Falta ejecutar la migración de gobierno contable.');
        $periodDate = Carbon::parse($to);
        $period = DB::table('v3_accounting_periods')->where('id_empresa', $companyId)
            ->where('anio', $periodDate->year)->where('mes', $periodDate->month)->first();
        if (!$period || !in_array($period->estado, array('APPROVED', 'CLOSED'), true)) {
            throw new RuntimeException('La certificación formal requiere que el periodo esté APPROVED o CLOSED.');
        }
        $snapshot = $this->evaluate($companyId, $from, $to);
        if (!$snapshot['certificado']) throw new RuntimeException('El periodo contiene diferencias y no puede certificarse.');
        $encoded = json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $hashable = $snapshot;
        unset($hashable['evaluado_at']);
        $hash = hash('sha256', json_encode($hashable, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $existing = DB::table('v3_accounting_certifications')->where('id_empresa', $companyId)
            ->where('desde', $snapshot['desde'])->where('hasta', $snapshot['hasta'])->where('snapshot_hash', $hash)->first();
        if ($existing) return $existing->id;
        return DB::table('v3_accounting_certifications')->insertGetId(array(
            'id_empresa' => $companyId, 'desde' => $snapshot['desde'], 'hasta' => $snapshot['hasta'], 'estado' => 'CERTIFIED',
            'snapshot_hash' => $hash, 'snapshot' => $encoded, 'certificado_por' => Auth::check() ? Auth::user()->id : null,
            'certificado_at' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')));
    }

    public function latest($companyId, $from, $to)
    {
        return Schema::hasTable('v3_accounting_certifications') ? DB::table('v3_accounting_certifications')
            ->where('id_empresa', $companyId)->where('desde', $from)->where('hasta', $to)
            ->orderBy('id', 'desc')->first() : null;
    }
}
