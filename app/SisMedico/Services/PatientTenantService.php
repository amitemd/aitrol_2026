<?php

namespace Sis_medico\Services;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class PatientTenantService
{
    const TABLE = 'paciente_empresa';

    public function companyId()
    {
        $id = trim((string) session('id_empresa'));
        return $id !== '' ? $id : null;
    }

    public function constrain($query, $patientColumn = 'paciente.id', $companyId = null)
    {
        $companyId = $companyId ?: $this->companyId();
        if (!$companyId) {
            return $query;
        }

        // Si la migración todavía no fue ejecutada, se cierra el acceso en vez
        // de exponer pacientes pertenecientes a otras empresas.
        if (!Schema::hasTable(self::TABLE)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereExists(function ($membership) use ($patientColumn, $companyId) {
            $membership->select(DB::raw(1))
                ->from(self::TABLE . ' as patient_company')
                ->whereColumn('patient_company.id_paciente', $patientColumn)
                ->where('patient_company.id_empresa', $companyId);
        });
    }

    public function constrainEloquent(EloquentBuilder $query, $companyId = null)
    {
        $from = (string) $query->getQuery()->from;
        $qualifier = $query->getModel()->getTable();
        if (preg_match('/\s+as\s+([A-Za-z0-9_]+)$/i', $from, $matches)) {
            $qualifier = $matches[1];
        }

        return $this->constrain($query, $qualifier . '.id', $companyId);
    }

    public function associate($patientId, $companyId = null, $actorId = null, $ip = null)
    {
        $patientId = trim((string) $patientId);
        $companyId = trim((string) ($companyId ?: $this->companyId()));
        if ($patientId === '') {
            return false;
        }
        if ($companyId === '') {
            if (auth()->check()) throw new \RuntimeException('Seleccione una empresa antes de registrar al paciente.');
            return false;
        }
        if (!Schema::hasTable(self::TABLE)) {
            throw new \RuntimeException('Ejecute la migración de paciente_empresa antes de registrar pacientes.');
        }

        $now = date('Y-m-d H:i:s');
        $actor = $actorId ?: (auth()->check() ? auth()->id() : null);
        $requestIp = $ip ?: (app()->runningInConsole() ? '127.0.0.1' : request()->ip());
        $key = array('id_paciente' => $patientId, 'id_empresa' => $companyId);
        $membership = DB::table(self::TABLE)->where($key)->first();

        if ($membership) {
            DB::table(self::TABLE)->where('id', $membership->id)->update(array(
                'id_usuariomod' => $actor,
                'ip_modificacion' => $requestIp,
                'updated_at' => $now,
            ));
        } else {
            DB::table(self::TABLE)->insert(array_merge($key, array(
                'id_usuariocrea' => $actor,
                'id_usuariomod' => $actor,
                'ip_creacion' => $requestIp,
                'ip_modificacion' => $requestIp,
                'created_at' => $now,
                'updated_at' => $now,
            )));
        }

        $personId = DB::table('paciente')->where('id', $patientId)->value('persona_id');
        if ($personId) {
            app(PersonRoleTenantService::class)->setRole(
                $personId, 'PACIENTE', true, $companyId, $actorId, $ip
            );
        }

        return true;
    }

    public function isAccessible($patientId, $companyId = null)
    {
        $companyId = $companyId ?: $this->companyId();
        if (!$companyId || !Schema::hasTable(self::TABLE)) {
            return false;
        }

        return DB::table(self::TABLE)
            ->where('id_paciente', $patientId)
            ->where('id_empresa', $companyId)
            ->exists();
    }

    public function requireAccessible($patientId, $companyId = null)
    {
        if (!$this->isAccessible($patientId, $companyId)) {
            abort(404, 'Paciente no encontrado en la empresa activa.');
        }

        return true;
    }
}
