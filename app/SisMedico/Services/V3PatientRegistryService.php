<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\clinical\Patient;

class V3PatientRegistryService
{
    public function filters(Request $request)
    {
        return array(
            'id' => trim((string) $request->input('id', '')),
            'nombres' => trim((string) $request->input('nombres', '')),
            'apellidos' => trim((string) $request->input('apellidos', '')),
            'correo' => trim((string) $request->input('correo', '')),
            'pendientes_emergencia' => $request->boolean('pendientes_emergencia'),
        );
    }

    public function paginate(array $filters, $companyId)
    {
        $query = Patient::query()->from('paciente as p')
            ->leftJoin('users as pu', 'pu.id', '=', 'p.id_usuario')
            ->leftJoin('users as legacy_user', 'legacy_user.id', '=', 'p.id')
            ->leftJoin('users as creator', 'creator.id', '=', 'p.id_usuariocrea')
            ->where('p.id', '<>', '9999999999')
            ->select(array('p.*', DB::raw('COALESCE(pu.email, legacy_user.email) as patient_email'), 'creator.nombre1 as creator_name', 'creator.apellido1 as creator_lastname'));

        app(PatientTenantService::class)->constrain($query, 'p.id', $companyId);
        if (!empty($filters['pendientes_emergencia'])) $query->where('p.registro_emergencia_pendiente', 1);

        if ($filters['id'] !== '') $query->where('p.id', 'like', '%'.$this->escapeLike($filters['id']).'%');
        $this->nameFilter($query, $filters['nombres'], array('p.nombre1', 'p.nombre2'));
        $this->nameFilter($query, $filters['apellidos'], array('p.apellido1', 'p.apellido2'));
        if ($filters['correo'] !== '') {
            $email = '%'.$this->escapeLike($filters['correo']).'%';
            $query->where(function ($part) use ($email) { $part->where('pu.email', 'like', $email)->orWhere('legacy_user.email', 'like', $email); });
        }

        $patients = $query->orderBy('p.created_at', 'desc')->orderBy('p.apellido1')->paginate(25);
        $this->attachCompanyActivity($patients->getCollection(), $companyId);
        return $patients;
    }

    private function attachCompanyActivity($patients, $companyId)
    {
        $ids = $patients->pluck('id')->filter()->all();
        if (!$ids) return;
        $activity = DB::table('agenda')->where('id_empresa', $companyId)->whereIn('id_paciente', $ids)
            ->select('id_paciente', DB::raw('COUNT(*) as total'), DB::raw('MAX(fechaini) as last_care'))
            ->groupBy('id_paciente')->get()->keyBy('id_paciente');
        foreach ($patients as $patient) {
            $row = $activity->get($patient->id);
            $patient->company_care_count = $row ? (int) $row->total : 0;
            $patient->company_last_care = $row ? $row->last_care : null;
        }
    }

    private function nameFilter($query, $value, array $columns)
    {
        if ($value === '') return;
        foreach (preg_split('/\s+/', $value) as $term) {
            if ($term === '') continue;
            $like = '%'.$this->escapeLike($term).'%';
            $query->where(function ($part) use ($columns, $like) {
                foreach ($columns as $index => $column) {
                    if ($index === 0) $part->where($column, 'like', $like);
                    else $part->orWhere($column, 'like', $like);
                }
            });
        }
    }

    private function escapeLike($value) { return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $value); }
}
