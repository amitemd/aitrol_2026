<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class V3BiopsyReportService
{
    public function filters(Request $request)
    {
        $today = date('Y-m-d'); $month = date('Y-m-01');
        $filters = array(
            'desde' => $this->date($request->input('desde', $request->input('fecha', $month)), $month),
            'hasta' => $this->date($request->input('hasta', $request->input('fecha_hasta', $today)), $today),
            'paciente' => trim((string) $request->input('paciente', $request->input('nombres', ''))),
            'cedula' => trim((string) $request->input('cedula', '')),
        );
        if ($filters['desde'] > $filters['hasta']) { $copy = $filters['desde']; $filters['desde'] = $filters['hasta']; $filters['hasta'] = $copy; }
        return $filters;
    }

    public function paginate(array $filters, $companyId)
    {
        return $this->query($filters, $companyId)->orderBy('b.created_at', 'desc')->paginate(25);
    }

    public function query(array $filters, $companyId)
    {
        $query = DB::table('hc4_biopsias as b')->join('paciente as p', 'p.id', '=', 'b.id_paciente')
            ->join('historiaclinica as hc', 'hc.hcid', '=', 'b.hcid')->join('agenda as a', 'a.id', '=', 'hc.id_agenda')
            ->leftJoin('clinical_encounters as ce', 'ce.id', '=', 'hc.encounter_id')
            ->leftJoin('users as u', 'u.id', '=', 'b.id_doctor')->leftJoin('seguros as s', 's.id', '=', 'hc.id_seguro')
            ->where('a.id_empresa', $companyId)->where(function ($encounterCompany) use ($companyId) {
                $encounterCompany->whereNull('ce.id')->orWhere('ce.id_empresa', $companyId);
            })
            ->whereBetween('b.created_at', array($filters['desde'].' 00:00:00', $filters['hasta'].' 23:59:59'))
            ->select(array('b.*', 'p.nombre1 as patient_name1', 'p.nombre2 as patient_name2', 'p.apellido1 as patient_lastname1', 'p.apellido2 as patient_lastname2',
                'u.nombre1 as doctor_name1', 'u.apellido1 as doctor_lastname1', 's.nombre as insurance_name', 'a.id as agenda_id'));
        $this->applyClinicalAccess($query, 'ce', 'DOCUMENT_VIEW');
        if ($filters['cedula'] !== '') $query->where('b.id_paciente', 'like', '%'.$this->escapeLike($filters['cedula']).'%');
        if ($filters['paciente'] !== '') {
            foreach (preg_split('/\s+/', $filters['paciente']) as $term) {
                if ($term === '') continue; $like = '%'.$this->escapeLike($term).'%';
                $query->where(function ($patient) use ($like) {
                    $patient->where('p.nombre1', 'like', $like)->orWhere('p.nombre2', 'like', $like)
                        ->orWhere('p.apellido1', 'like', $like)->orWhere('p.apellido2', 'like', $like);
                });
            }
        }
        return $query;
    }

    private function applyClinicalAccess($query, $encounterAlias, $action)
    {
        $user = Auth::user();
        if (!$user) { $query->whereRaw('1 = 0'); return; }
        if (method_exists($user, 'isSystemUser') && $user->isSystemUser()) return;
        if (!Schema::hasTable('clinical_access_scopes') || !Schema::hasTable('clinical_break_glass_accesses')) {
            $query->whereRaw('1 = 0'); return;
        }
        $now = date('Y-m-d H:i:s');
        $query->where(function ($allowed) use ($user, $action, $now, $encounterAlias) {
            $allowed->where($encounterAlias.'.id_usuariocrea', $user->id)
                ->orWhereExists(function ($scope) use ($user, $action, $now, $encounterAlias) {
                    $scope->select(DB::raw(1))->from('clinical_access_scopes as cas')->where('cas.active', 1)
                        ->where(function ($owner) use ($user) {
                            $owner->where('cas.user_id', $user->id)->orWhere('cas.role_id', $user->id_tipo_usuario);
                        })->where(function ($permission) use ($action) {
                            $permission->where('cas.action', $action)->orWhere('cas.action', '*');
                        })->whereRaw('(cas.encounter_id IS NULL OR cas.encounter_id = '.$encounterAlias.'.id)')
                        ->whereRaw('(cas.care_setting IS NULL OR cas.care_setting = '.$encounterAlias.'.care_setting)')
                        ->where(function ($validity) use ($now) { $validity->whereNull('cas.valid_from')->orWhere('cas.valid_from', '<=', $now); })
                        ->where(function ($validity) use ($now) { $validity->whereNull('cas.valid_until')->orWhere('cas.valid_until', '>=', $now); });
                })->orWhereExists(function ($exception) use ($user, $action, $now, $encounterAlias) {
                    $exception->select(DB::raw(1))->from('clinical_break_glass_accesses as cbg')
                        ->whereRaw('cbg.encounter_id = '.$encounterAlias.'.id')->where('cbg.user_id', $user->id)
                        ->where('cbg.status', 'ACTIVE')->where('cbg.expires_at', '>=', $now)
                        ->where(function ($permission) use ($action) {
                            $permission->where('cbg.action', $action)->orWhere('cbg.action', '*');
                        });
                });
        });
    }

    private function date($value, $default)
    {
        $value = str_replace('/', '-', trim((string) $value)); $date = \DateTime::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $value : $default;
    }

    private function escapeLike($value) { return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $value); }
}
