<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class ClinicalEncounterSelectorService
{
    public function available()
    {
        return Schema::hasTable('clinical_encounters')
            && Schema::hasTable('clinical_access_scopes')
            && Schema::hasTable('clinical_break_glass_accesses');
    }

    /**
     * Lista exclusivamente episodios activos visibles para la acción pedida.
     */
    public function paginateForAction($action, Request $request)
    {
        $user = Auth::user();
        if (!$this->available() || !$user) {
            return null;
        }

        $now = date('Y-m-d H:i:s');
        $query = DB::table('clinical_encounters as ce')
            ->leftJoin('paciente as p', 'p.id', '=', 'ce.id_paciente')
            ->where('ce.status', ClinicalEncounterService::ACTIVE)
            ->where(function ($allowed) use ($user, $action, $now) {
                $allowed->where('ce.id_usuariocrea', $user->id)
                    ->orWhereExists(function ($scope) use ($user, $action, $now) {
                        $scope->select(DB::raw(1))->from('clinical_access_scopes as cas')
                            ->where('cas.active', 1)
                            ->where(function ($owner) use ($user) {
                                $owner->where('cas.user_id', $user->id)
                                    ->orWhere('cas.role_id', $user->id_tipo_usuario);
                            })
                            ->where(function ($permission) use ($action) {
                                $permission->where('cas.action', $action)->orWhere('cas.action', '*');
                            })
                            ->whereRaw('(cas.encounter_id IS NULL OR cas.encounter_id = ce.id)')
                            ->whereRaw('(cas.care_setting IS NULL OR cas.care_setting = ce.care_setting)')
                            ->where(function ($validity) use ($now) {
                                $validity->whereNull('cas.valid_from')->orWhere('cas.valid_from', '<=', $now);
                            })
                            ->where(function ($validity) use ($now) {
                                $validity->whereNull('cas.valid_until')->orWhere('cas.valid_until', '>=', $now);
                            });
                    })
                    ->orWhereExists(function ($exception) use ($user, $action, $now) {
                        $exception->select(DB::raw(1))->from('clinical_break_glass_accesses as cbg')
                            ->whereRaw('cbg.encounter_id = ce.id')
                            ->where('cbg.user_id', $user->id)
                            ->where('cbg.status', 'ACTIVE')
                            ->where('cbg.expires_at', '>=', $now)
                            ->where(function ($permission) use ($action) {
                                $permission->where('cbg.action', $action)->orWhere('cbg.action', '*');
                            });
                    });
            });

        $patient = trim((string) $request->input('patient', ''));
        $setting = trim((string) $request->input('care_setting', ''));
        if ($patient !== '') {
            $query->where(function ($search) use ($patient) {
                $like = '%' . $patient . '%';
                $search->where('ce.id_paciente', 'like', $like)
                    ->orWhere('p.nombre1', 'like', $like)
                    ->orWhere('p.nombre2', 'like', $like)
                    ->orWhere('p.apellido1', 'like', $like)
                    ->orWhere('p.apellido2', 'like', $like);
            });
        }
        if (in_array($setting, array('AMBULATORY', 'EMERGENCY', 'INPATIENT'), true)) {
            $query->where('ce.care_setting', $setting);
        }

        return $query->select(
                'ce.id', 'ce.id_paciente', 'ce.care_setting', 'ce.started_at',
                'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2'
            )
            ->orderBy('ce.started_at', 'desc')
            ->paginate(20);
    }
}
