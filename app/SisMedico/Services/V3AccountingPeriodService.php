<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use InvalidArgumentException;
use RuntimeException;

class V3AccountingPeriodService
{
    private $certification;

    public function __construct(V3AccountingCertificationService $certification)
    {
        $this->certification = $certification;
    }

    public function getOrCreate($companyId, $period)
    {
        $date = $this->date($period);
        if (!Schema::hasTable('v3_accounting_periods')) throw new RuntimeException('Falta ejecutar la migración de gobierno contable.');
        return DB::transaction(function () use ($companyId, $date) {
            $row = DB::table('v3_accounting_periods')->where('id_empresa', $companyId)
                ->where('anio', $date->year)->where('mes', $date->month)->lockForUpdate()->first();
            if ($row) return $row;
            $id = DB::table('v3_accounting_periods')->insertGetId(array('id_empresa' => $companyId,
                'anio' => $date->year, 'mes' => $date->month, 'estado' => 'OPEN', 'version' => 1,
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')));
            return DB::table('v3_accounting_periods')->where('id', $id)->first();
        });
    }

    public function prepare($companyId, $period)
    {
        $date = $this->date($period);
        return DB::transaction(function () use ($companyId, $period, $date) {
            $row = $this->locked($companyId, $period);
            if ($row->estado !== 'OPEN') throw new RuntimeException('El periodo debe estar OPEN para pasar a PREPARED.');
            $snapshot = $this->certification->evaluate($companyId, $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString());
            if (!$snapshot['certificado']) throw new RuntimeException('El periodo no puede prepararse hasta resolver integridad, reportes y auxiliares pendientes.');
            DB::table('v3_accounting_periods')->where('id', $row->id)->update(array('estado' => 'PREPARED',
                'version' => (int) $row->version + 1, 'preparado_por' => $this->userId(),
                'preparado_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')));
            return true;
        });
    }

    public function approve($companyId, $period)
    {
        return $this->transition($companyId, $period, 'PREPARED', 'APPROVED', function ($row, $user) {
            if ((string) $row->preparado_por === (string) $user) throw new RuntimeException('El aprobador debe ser distinto del preparador.');
            return array('aprobado_por' => $user, 'aprobado_at' => date('Y-m-d H:i:s'));
        });
    }

    public function close($companyId, $period)
    {
        $date = $this->date($period);
        $from = $date->copy()->startOfMonth()->toDateString();
        $to = $date->copy()->endOfMonth()->toDateString();
        return DB::transaction(function () use ($companyId, $period, $from, $to) {
            $row = $this->locked($companyId, $period);
            if ($row->estado !== 'APPROVED') throw new RuntimeException('El periodo debe estar APPROVED para pasar a CLOSED.');
            $user = $this->userId();
            if ((string) $row->preparado_por === (string) $user) throw new RuntimeException('Quien preparó el periodo no puede cerrarlo.');
            $certificationId = $this->certification->certify($companyId, $from, $to);
            DB::table('v3_accounting_periods')->where('id', $row->id)->update(array('estado' => 'CLOSED',
                'version' => (int) $row->version + 1, 'cerrado_por' => $user, 'cerrado_at' => date('Y-m-d H:i:s'),
                'certificacion_id' => $certificationId, 'updated_at' => date('Y-m-d H:i:s')));
            return true;
        });
    }

    public function reopen($companyId, $period, $reason)
    {
        if (strlen(trim((string) $reason)) < 15) throw new InvalidArgumentException('La reapertura requiere un motivo de al menos 15 caracteres.');
        return DB::transaction(function () use ($companyId, $period, $reason) {
            $row = $this->locked($companyId, $period);
            if ($row->estado !== 'CLOSED') throw new RuntimeException('Solo se puede reabrir un periodo cerrado.');
            DB::table('v3_accounting_periods')->where('id', $row->id)->update(array('estado' => 'OPEN',
                'version' => (int) $row->version + 1, 'reabierto_por' => $this->userId(), 'reabierto_at' => date('Y-m-d H:i:s'),
                'motivo_reapertura' => trim($reason), 'preparado_por' => null, 'preparado_at' => null,
                'aprobado_por' => null, 'aprobado_at' => null, 'cerrado_por' => null, 'cerrado_at' => null,
                'certificacion_id' => null,
                'updated_at' => date('Y-m-d H:i:s')));
            return true;
        });
    }

    private function transition($companyId, $period, $expected, $next, $changes)
    {
        return DB::transaction(function () use ($companyId, $period, $expected, $next, $changes) {
            $row = $this->locked($companyId, $period);
            if ($row->estado !== $expected) throw new RuntimeException('El periodo debe estar '.$expected.' para pasar a '.$next.'.');
            $values = call_user_func($changes, $row, $this->userId());
            $values['estado'] = $next; $values['version'] = (int) $row->version + 1; $values['updated_at'] = date('Y-m-d H:i:s');
            DB::table('v3_accounting_periods')->where('id', $row->id)->update($values);
            return true;
        });
    }

    private function locked($companyId, $period)
    {
        $date = $this->date($period);
        $this->getOrCreate($companyId, $period);
        return DB::table('v3_accounting_periods')->where('id_empresa', $companyId)
            ->where('anio', $date->year)->where('mes', $date->month)->lockForUpdate()->first();
    }

    private function date($period)
    {
        if (!preg_match('/^20[0-9]{2}-(0[1-9]|1[0-2])$/', (string) $period)) throw new InvalidArgumentException('El periodo debe tener formato AAAA-MM.');
        return Carbon::createFromFormat('Y-m', $period)->startOfMonth();
    }

    private function userId()
    {
        if (!Auth::check()) throw new RuntimeException('Se requiere un usuario autenticado para cambiar el periodo.');
        return Auth::user()->id;
    }
}
