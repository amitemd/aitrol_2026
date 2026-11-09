<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Especialidad;
use Sis_medico\Procedimiento;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\v3\clinical\ScheduledCare;

class V3ScheduledCareService
{
    public function filters(Request $request)
    {
        $today = date('Y-m-d');
        $type = (string) $request->input('tipo', '2');
        $unit = (string) $request->input('unidad', 'x');
        $filters = array(
            'tipo' => in_array($type, array('0', '1', '2'), true) ? $type : '2',
            'unidad' => in_array($unit, array('x', '0', '2'), true) ? $unit : 'x',
            'desde' => $this->date($request->input('desde', $today), $today),
            'hasta' => $this->date($request->input('hasta', $today), $today),
            'cedula' => trim((string) $request->input('cedula', '')),
            'paciente' => trim((string) $request->input('paciente', '')),
            'doctor' => trim((string) $request->input('doctor', '')),
            'seguro' => trim((string) $request->input('seguro', '')),
            'especialidad' => trim((string) $request->input('especialidad', '')),
            'procedimiento' => trim((string) $request->input('procedimiento', '')),
            'estado' => in_array((string) $request->input('estado', ''), array('suspendidos', 'preadmision'), true)
                ? (string) $request->input('estado') : '',
            'seguro_tipo' => (string) $request->input('seguro_tipo', '') === 'publico' ? 'publico' : '',
        );
        if ($filters['desde'] > $filters['hasta']) {
            $start = $filters['hasta']; $filters['hasta'] = $filters['desde']; $filters['desde'] = $start;
        }
        return $filters;
    }

    public function paginate(array $filters, $companyId)
    {
        $query = $this->filteredQuery($filters, $companyId);
        $rows = $query->orderBy('a.fechaini', 'desc')->paginate(25);
        $this->attachRelated($rows->getCollection());
        return $rows;
    }

    public function all(array $filters, $companyId)
    {
        $rows = $this->filteredQuery($filters, $companyId)->orderBy('a.fechaini', 'desc')->get();
        $this->attachRelated($rows);
        return $rows;
    }

    public function summary(array $filters, $companyId)
    {
        $base = $this->filteredQuery($filters, $companyId);
        return array(
            'total' => (clone $base)->count('a.id'),
            'consultations' => (clone $base)->where('a.proc_consul', 0)->count('a.id'),
            'procedures' => (clone $base)->where('a.proc_consul', 1)->count('a.id'),
            'attended' => (clone $base)->whereIn('a.estado_cita', array(4, 5, 6))->count('a.id'),
        );
    }

    public function find($id, $companyId)
    {
        $row = $this->baseQuery($companyId)->where('a.id', $id)->first();
        if (!$row) abort(404, 'La atención solicitada no existe para la empresa activa.');
        $this->attachRelated(collect(array($row)));
        $row->history = DB::table('historiaclinica as hc')->leftJoin('seguros as hs', 'hs.id', '=', 'hc.id_seguro')
            ->leftJoin('subseguro as ss', 'ss.id', '=', 'hc.id_subseguro')->where('hc.id_agenda', $row->id)
            ->select('hc.*', 'hs.nombre as seguro_historia', 'ss.nombre as subseguro_nombre')->first();
        return $row;
    }

    public function selectedCatalogs(array $filters)
    {
        $selected = array('doctor' => null, 'seguro' => null, 'especialidad' => null, 'procedimiento' => null);
        if ($filters['doctor'] !== '') {
            $row = $this->activeDoctorQuery()->find($filters['doctor']);
            if ($row) $selected['doctor'] = array('id' => $row->id, 'text' => trim($row->apellido1.' '.$row->apellido2.' '.$row->nombre1.' '.$row->nombre2));
        }
        if ($filters['seguro'] !== '') {
            $row = Seguro::where('inactivo', '1')->find($filters['seguro']);
            if ($row) $selected['seguro'] = array('id' => $row->id, 'text' => $row->nombre);
        }
        if ($filters['especialidad'] !== '') {
            $row = Especialidad::where('estado', '1')->find($filters['especialidad']);
            if ($row) $selected['especialidad'] = array('id' => $row->id, 'text' => $row->nombre);
        }
        if ($filters['procedimiento'] !== '') {
            $row = Procedimiento::where('estado', '1')->find($filters['procedimiento']);
            if ($row) $selected['procedimiento'] = array('id' => $row->id, 'text' => $row->nombre);
        }
        return $selected;
    }

    public function searchCatalog($type, $term, $page = 1)
    {
        $term = trim((string) $term);
        $page = max(1, (int) $page);
        if (!in_array($type, array('doctor', 'seguro', 'especialidad', 'procedimiento'), true)) {
            return array('results' => array(), 'pagination' => array('more' => false));
        }
        $like = '%'.$this->escapeLike($term).'%';

        if ($type === 'doctor') {
            $query = $this->activeDoctorQuery();
            if ($term !== '') {
                foreach (preg_split('/\s+/', $term) as $doctorTerm) {
                    if ($doctorTerm === '') continue;
                    $doctorLike = '%'.$this->escapeLike($doctorTerm).'%';
                    $query->where(function ($builder) use ($doctorLike) {
                        $builder->where('id', 'like', $doctorLike)->orWhere('nombre1', 'like', $doctorLike)
                            ->orWhere('nombre2', 'like', $doctorLike)->orWhere('apellido1', 'like', $doctorLike)
                            ->orWhere('apellido2', 'like', $doctorLike);
                    });
                }
            }
            $query->orderBy('apellido1')->orderBy('nombre1');
        } elseif ($type === 'seguro') {
            $query = Seguro::where('inactivo', '1');
            if ($term !== '') $query->where('nombre', 'like', $like);
            $query->orderBy('nombre');
        } elseif ($type === 'especialidad') {
            $query = Especialidad::where('estado', '1');
            if ($term !== '') $query->where('nombre', 'like', $like);
            $query->orderBy('nombre');
        } else {
            $query = Procedimiento::where('estado', '1');
            if ($term !== '') $query->where('nombre', 'like', $like);
            $query->orderBy('nombre');
        }

        $rows = $query->simplePaginate(10, array('*'), 'page', $page);
        $results = array();
        foreach ($rows->items() as $row) {
            $text = $type === 'doctor'
                ? trim($row->apellido1.' '.$row->apellido2.' '.$row->nombre1.' '.$row->nombre2)
                : $row->nombre;
            $results[] = array('id' => (string) $row->id, 'text' => $text);
        }
        return array('results' => $results, 'pagination' => array('more' => $rows->hasMorePages()));
    }

    public function statusLabel($row)
    {
        if ((string) $row->omni === 'OM') {
            $labels = array('4' => 'Ingreso', '5' => 'Alta', '6' => 'Emergencia');
            if (isset($labels[(string) $row->estado_cita])) return $labels[(string) $row->estado_cita];
        }
        if ((string) $row->estado_cita === '4' && $row->pentax_estado !== null) {
            $pentax = array('-1' => 'Pre-admisión', '0' => 'En espera', '1' => 'Preparación', '2' => 'En procedimiento', '3' => 'Recuperación', '4' => 'Alta', '5' => 'Suspendido');
            if (isset($pentax[(string) $row->pentax_estado])) return $pentax[(string) $row->pentax_estado];
        }
        $labels = array('-1' => 'No asistió', '0' => 'Por confirmar', '1' => 'Confirmada', '2' => ((string) $row->estado === '1' ? 'Completar datos' : 'Reagendar'), '3' => 'Suspendida', '4' => 'Asistió', '5' => 'Alta', '6' => 'Emergencia');
        return isset($labels[(string) $row->estado_cita]) ? $labels[(string) $row->estado_cita] : 'Sin estado';
    }

    private function filteredQuery(array $filters, $companyId)
    {
        $query = $this->baseQuery($companyId)->whereBetween('a.fechaini', array($filters['desde'].' 00:00:00', $filters['hasta'].' 23:59:59'));
        if ($filters['tipo'] !== '2') $query->where('a.proc_consul', $filters['tipo']);
        if ($filters['tipo'] === '1' && $filters['unidad'] !== 'x') {
            if ($filters['unidad'] === '2') $query->where('h.id', 2);
            if ($filters['unidad'] === '0') $query->where(function ($q) { $q->where('h.id', '<>', 2)->orWhereNull('h.id'); });
        }
        if ($filters['cedula'] !== '') $query->where('a.id_paciente', 'like', '%'.$this->escapeLike($filters['cedula']).'%');
        if ($filters['doctor'] !== '') $query->where('a.id_doctor1', $filters['doctor']);
        if ($filters['seguro'] !== '') $query->where('a.id_seguro', $filters['seguro']);
        if ($filters['seguro_tipo'] === 'publico') $query->where('s.tipo', 0);
        if ($filters['especialidad'] !== '') $query->where('a.espid', $filters['especialidad']);
        if ($filters['procedimiento'] !== '') {
            $procedure = $filters['procedimiento'];
            $query->where(function ($q) use ($procedure) {
                $q->where('a.id_procedimiento', $procedure)->orWhereExists(function ($sub) use ($procedure) {
                    $sub->select(DB::raw(1))->from('agenda_procedimiento as ap')->whereRaw('ap.id_agenda = a.id')->where('ap.id_procedimiento', $procedure);
                });
            });
        }
        if ($filters['estado'] === 'suspendidos') {
            $query->where(function ($status) {
                $status->where('a.estado_cita', 3)->orWhereExists(function ($pentax) {
                    $pentax->select(DB::raw(1))->from('pentax as px_status')
                        ->whereRaw('px_status.id_agenda = a.id')->where('px_status.estado_pentax', 5);
                });
            });
        }
        if ($filters['estado'] === 'preadmision') {
            $query->whereExists(function ($pentax) {
                $pentax->select(DB::raw(1))->from('pentax as px_status')
                    ->whereRaw('px_status.id_agenda = a.id')->where('px_status.estado_pentax', -1);
            });
        }
        if ($filters['paciente'] !== '') {
            foreach (preg_split('/\s+/', $filters['paciente']) as $term) {
                if ($term === '') continue;
                $like = '%'.$this->escapeLike($term).'%';
                $query->where(function ($q) use ($like) {
                    $q->where('p.nombre1', 'like', $like)->orWhere('p.nombre2', 'like', $like)->orWhere('p.apellido1', 'like', $like)->orWhere('p.apellido2', 'like', $like);
                });
            }
        }
        return $query;
    }

    private function baseQuery($companyId)
    {
        return ScheduledCare::query()->from('agenda as a')->leftJoin('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->leftJoin('seguros as s', 's.id', '=', 'a.id_seguro')->leftJoin('users as d', 'd.id', '=', 'a.id_doctor1')
            ->leftJoin('users as um', 'um.id', '=', 'a.id_usuariomod')->leftJoin('sala as sa', 'sa.id', '=', 'a.id_sala')
            ->leftJoin('hospital as h', 'h.id', '=', 'sa.id_hospital')->leftJoin('procedimiento as pr', 'pr.id', '=', 'a.id_procedimiento')
            ->leftJoin('especialidad as e', 'e.id', '=', 'a.espid')->leftJoin('empresa as em', 'em.id', '=', 'a.id_empresa')
            ->where('a.id_empresa', $companyId)
            ->where(function ($q) { $q->where('a.proc_consul', '<', 2)->orWhere('a.omni', 'OM'); })
            ->select(array('a.*', 'p.nombre1 as paciente_nombre1', 'p.nombre2 as paciente_nombre2', 'p.apellido1 as paciente_apellido1', 'p.apellido2 as paciente_apellido2', 'p.fecha_nacimiento', 'p.alergias', 'p.antecedentes_pat', 'p.antecedentes_fam', 'p.antecedentes_quir', 'p.direccion', 'p.telefono1', 'p.telefono2', 's.nombre as seguro_nombre', 'd.nombre1 as doctor_nombre1', 'd.apellido1 as doctor_apellido1', 'd.apellido2 as doctor_apellido2', 'um.nombre1 as responsable_nombre1', 'um.apellido1 as responsable_apellido1', 'sa.nombre_sala as sala_nombre', 'h.nombre_hospital as hospital_nombre', 'pr.nombre as procedimiento_nombre', 'pr.observacion as procedimiento_observacion', 'e.nombre as especialidad_nombre', 'em.nombrecomercial as empresa_nombre', 'em.nombre_corto as empresa_corta'));
    }

    private function attachRelated($rows)
    {
        $ids = $rows->pluck('id')->filter()->all(); if (!$ids) return;
        $extra = DB::table('agenda_procedimiento as ap')->join('procedimiento as p', 'p.id', '=', 'ap.id_procedimiento')->whereIn('ap.id_agenda', $ids)->select('ap.id_agenda', 'p.id', 'p.nombre', 'p.observacion')->get()->groupBy('id_agenda');
        $pentaxRows = DB::table('pentax')->whereIn('id_agenda', $ids)->orderBy('id', 'desc')->select('id_agenda', 'estado_pentax')->get();
        $pentax = collect();
        foreach ($pentaxRows as $pentaxRow) if (!$pentax->has($pentaxRow->id_agenda)) $pentax->put($pentaxRow->id_agenda, $pentaxRow);
        $histories = DB::table('historiaclinica')->whereIn('id_agenda', $ids)->select('id_agenda', 'hcid')->get()->keyBy('id_agenda');
        $historyIds = $histories->pluck('hcid')->filter()->all();
        $documentCounts = $historyIds ? DB::table('archivo_historico')->whereIn('id_historia', $historyIds)->where('estado', 1)->select('id_historia', DB::raw('COUNT(*) as total'))->groupBy('id_historia')->get()->keyBy('id_historia') : collect();
        foreach ($rows as $row) {
            $additional = collect(); $seen = array();
            foreach ($extra->get($row->id, collect()) as $procedure) {
                if ((string) $procedure->id === (string) $row->id_procedimiento || isset($seen[$procedure->nombre])) continue;
                $seen[$procedure->nombre] = true; $additional->push($procedure);
            }
            $row->additional_procedures = $additional; $history = $histories->get($row->id);
            $pentaxState = $pentax->get($row->id); $row->pentax_estado = $pentaxState ? $pentaxState->estado_pentax : null;
            $row->history_id = $history ? $history->hcid : null; $count = $history ? $documentCounts->get($history->hcid) : null;
            $row->document_count = $count ? (int) $count->total : 0; $row->status_label = $this->statusLabel($row);
        }
    }

    private function activeDoctorQuery()
    {
        return User::query()->where('users.estado', 1)->whereExists(function ($role) {
            $role->select(DB::raw(1))->from('tipousuario as doctor_role')
                ->whereRaw('doctor_role.id = users.id_tipo_usuario')
                ->where('doctor_role.estado', 1)
                ->whereIn(DB::raw('UPPER(TRIM(doctor_role.nombre))'), array('DOCTOR', 'DOCTORES'));
        });
    }

    private function date($value, $default) { $value = str_replace('/', '-', trim((string) $value)); $date = \DateTime::createFromFormat('Y-m-d', $value); return $date && $date->format('Y-m-d') === $value ? $value : $default; }
    private function escapeLike($value) { return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $value); }
}
