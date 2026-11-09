<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class V3FallRiskService
{
    public function filters(Request $request)
    {
        return array('hospital' => (int) $request->input('hospital', $request->input('hospital_tipo', 2)), 'buscar' => trim((string) $request->input('buscar', '')));
    }

    public function beds(array $filters)
    {
        $query = DB::table('camilla as c')->leftJoin('hospital as h', 'h.id', '=', 'c.id_hospital')->where('c.estado', 1)
            ->select('c.*', 'h.nombre_hospital as hospital_name');
        if ($filters['hospital']) $query->where('c.id_hospital', $filters['hospital']);
        if ($filters['buscar'] !== '') $query->where('c.nombre_camilla', 'like', '%'.$this->escapeLike($filters['buscar']).'%');
        $beds = $query->orderBy('c.nombre_camilla')->paginate(12);
        $this->attachState($beds->getCollection());
        return $beds;
    }

    public function hospitals() { return DB::table('hospital')->orderBy('nombre_hospital')->get(); }

    public function recordFilters(Request $request)
    {
        $today = date('Y-m-d');
        $from = $this->date($request->input('desde'), $today);
        $to = $this->date($request->input('hasta'), $today);
        if ($from > $to) {
            $swap = $from;
            $from = $to;
            $to = $swap;
        }

        return array(
            'desde' => $from,
            'hasta' => $to,
            'buscar' => trim((string) $request->input('buscar', '')),
        );
    }

    public function records(array $filters)
    {
        $query = DB::table('camilla_gestion as cg')
            ->join('paciente as p', 'p.id', '=', 'cg.id_paciente')
            ->join('camilla as c', 'c.id', '=', 'cg.camilla')
            ->leftJoin('hospital as h', 'h.id', '=', 'c.id_hospital')
            ->where('cg.estado', 0)
            ->whereRaw(
                'COALESCE(cg.fecha_cambio, DATE(cg.created_at)) BETWEEN ? AND ?',
                array($filters['desde'], $filters['hasta'])
            )
            ->whereExists(function ($subquery) {
                $subquery->select(DB::raw(1))
                    ->from('log_camilla as lc')
                    ->whereRaw('lc.id_camagestion = cg.id');
            })
            ->select(
                'cg.id', 'cg.id_agenda', 'cg.id_paciente', 'cg.created_at as ingreso_at',
                'cg.updated_at as salida_at', 'cg.fecha_cambio', 'cg.estado', 'cg.nivel_riesgo',
                'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento',
                'c.nombre_camilla', 'h.nombre_hospital'
            );

        if ($filters['buscar'] !== '') {
            $term = '%'.$this->escapeLike($filters['buscar']).'%';
            $query->where(function ($search) use ($term) {
                $search->where('p.id', 'like', $term)
                    ->orWhere('p.nombre1', 'like', $term)
                    ->orWhere('p.nombre2', 'like', $term)
                    ->orWhere('p.apellido1', 'like', $term)
                    ->orWhere('p.apellido2', 'like', $term)
                    ->orWhere('c.nombre_camilla', 'like', $term)
                    ->orWhere('h.nombre_hospital', 'like', $term);
            });
        }

        $records = $query->orderBy('cg.created_at', 'desc')->orderBy('cg.id', 'desc')->paginate(12);
        foreach ($records as $record) {
            $record->patient_name = trim($record->apellido1.' '.$record->apellido2.' '.$record->nombre1.' '.$record->nombre2);
            $record->patient_age = $record->fecha_nacimiento ? $this->age($record->fecha_nacimiento) : null;
        }

        return $records;
    }

    public function summary($beds)
    {
        $summary = array('total' => $beds->total(), 'free' => 0, 'occupied' => 0, 'preparation' => 0, 'disinfection' => 0);
        foreach ($beds as $bed) {
            if ($bed->state_code === 2) $summary['occupied']++;
            elseif ($bed->state_code === 3) $summary['preparation']++;
            elseif ($bed->state_code === 4) $summary['disinfection']++;
            else $summary['free']++;
        }
        return $summary;
    }

    private function attachState($beds)
    {
        $ids = $beds->pluck('id')->all(); if (!$ids) return;
        $states = DB::table('camilla_gestion')->whereIn('camilla', $ids)->where('num_atencion', 1)->orderBy('id', 'desc')->get();
        $latest = collect(); foreach ($states as $state) if (!$latest->has($state->camilla)) $latest->put($state->camilla, $state);
        $patientIds = $latest->pluck('id_paciente')->filter()->unique()->all();
        $patients = DB::table('paciente')->whereIn('id', $patientIds);
        app(PatientTenantService::class)->constrain($patients, 'paciente.id');
        $patients = $patientIds ? $patients->get()->keyBy('id') : collect();
        $labels = array(1 => 'Libre', 2 => 'Ocupada', 3 => 'Preparación', 4 => 'Desinfección');
        foreach ($beds as $bed) {
            $state = $latest->get($bed->id); $active = $state && (string) $state->estado === '1';
            $bed->management = $active ? $state : null; $bed->state_code = $active && $state->estado_uso ? (int) $state->estado_uso : 1;
            $bed->state_label = isset($labels[$bed->state_code]) ? $labels[$bed->state_code] : 'Libre';
            $bed->patient = $active ? $patients->get($state->id_paciente) : null;
            $bed->patient_age = $bed->patient && $bed->patient->fecha_nacimiento ? $this->age($bed->patient->fecha_nacimiento) : null;
        }
    }

    private function age($date) { try { return (new \DateTime($date))->diff(new \DateTime('today'))->y; } catch (\Exception $e) { return null; } }
    private function date($value, $fallback)
    {
        $date = \DateTime::createFromFormat('Y-m-d', (string) $value);
        return $date && $date->format('Y-m-d') === (string) $value ? $date->format('Y-m-d') : $fallback;
    }
    private function escapeLike($value) { return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $value); }
}
