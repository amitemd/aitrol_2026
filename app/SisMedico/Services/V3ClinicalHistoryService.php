<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class V3ClinicalHistoryService
{
    public function filters(Request $request, $laboratoryOnly = false)
    {
        $today = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-90 days'));
        $filters = array(
            'desde' => $this->date($request->input('desde', $request->input('fecha', $from)), $from),
            'hasta' => $this->date($request->input('hasta', $request->input('fecha_hasta', $today)), $today),
            'paciente' => trim((string) $request->input('paciente', $request->input('nombres', ''))),
            'cedula' => trim((string) $request->input('cedula', '')),
            'estado' => trim((string) $request->input('estado', '')),
            'categoria' => $laboratoryOnly ? 'LABORATORY' : trim((string) $request->input('categoria', '')),
        );
        if ($filters['desde'] > $filters['hasta']) {
            $copy = $filters['desde']; $filters['desde'] = $filters['hasta']; $filters['hasta'] = $copy;
        }
        $statuses = array('DRAFT', 'ORDERED', 'ACKNOWLEDGED', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED');
        if ($filters['estado'] !== '' && !in_array($filters['estado'], $statuses, true)) $filters['estado'] = '';
        return $filters;
    }

    public function paginate(array $filters, $companyId)
    {
        $orders = $this->query($filters, $companyId)->orderBy('co.ordered_at', 'desc')->orderBy('co.id', 'desc')->paginate(25);
        $this->attachResults($orders->getCollection());
        return $orders;
    }

    public function summary(array $filters, $companyId)
    {
        $query = $this->query($filters, $companyId);
        return array(
            'total' => (clone $query)->count('co.id'),
            'completed' => (clone $query)->where('co.status', 'COMPLETED')->count('co.id'),
            'pending' => (clone $query)->whereIn('co.status', array('DRAFT', 'ORDERED', 'ACKNOWLEDGED', 'IN_PROGRESS'))->count('co.id'),
            'critical' => (clone $query)->whereExists(function ($result) {
                $result->select(DB::raw(1))->from('clinical_results as cr')->whereRaw('cr.clinical_order_id = co.id')->where('cr.is_critical', 1);
            })->count('co.id'),
        );
    }

    private function query(array $filters, $companyId)
    {
        $query = DB::table('clinical_orders as co')->join('clinical_encounters as ce', 'ce.id', '=', 'co.encounter_id')
            ->join('paciente as p', 'p.id', '=', 'co.patient_id')->leftJoin('users as u', 'u.id', '=', 'co.ordered_by')
            ->where('ce.id_empresa', $companyId)
            ->whereBetween(DB::raw('COALESCE(co.ordered_at, co.created_at)'), array($filters['desde'].' 00:00:00', $filters['hasta'].' 23:59:59'))
            ->select(array('co.*', 'ce.care_setting', 'ce.status as encounter_status',
                'p.nombre1 as patient_name1', 'p.nombre2 as patient_name2', 'p.apellido1 as patient_lastname1', 'p.apellido2 as patient_lastname2',
                'u.nombre1 as doctor_name1', 'u.nombre2 as doctor_name2', 'u.apellido1 as doctor_lastname1', 'u.apellido2 as doctor_lastname2'));
        $this->applyClinicalAccess($query, 'ce', 'ORDER_VIEW');
        if ($filters['categoria'] !== '') $query->where('co.category', $filters['categoria']);
        if ($filters['estado'] !== '') $query->where('co.status', $filters['estado']);
        if ($filters['cedula'] !== '') $query->where('co.patient_id', 'like', '%'.$this->escapeLike($filters['cedula']).'%');
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

    private function attachResults($orders)
    {
        $ids = $orders->pluck('id')->filter()->all();
        if (!$ids) return;
        $results = DB::table('clinical_results')->whereIn('clinical_order_id', $ids)
            ->orderBy('occurred_at', 'desc')->orderBy('id', 'desc')->get()->groupBy('clinical_order_id');
        $legacyIds = array();
        foreach ($orders as $order) if ($order->source_type === 'examen_orden' && ctype_digit((string) $order->source_id)) $legacyIds[] = (int) $order->source_id;
        $tests = $legacyIds ? DB::table('examen_detalle')->whereIn('id_examen_orden', array_unique($legacyIds))
            ->select('id_examen_orden', DB::raw('COUNT(*) as total'))->groupBy('id_examen_orden')->get()->keyBy('id_examen_orden') : collect();
        foreach ($orders as $order) {
            $group = $results->get($order->id, collect());
            $order->result_count = $group->count();
            $order->latest_result = $group->first();
            $test = $order->source_type === 'examen_orden' ? $tests->get((int) $order->source_id) : null;
            $order->test_count = $test ? (int) $test->total : null;
        }
    }

    private function date($value, $default)
    {
        $value = str_replace('/', '-', trim((string) $value));
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $value : $default;
    }

    private function escapeLike($value) { return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $value); }
}
