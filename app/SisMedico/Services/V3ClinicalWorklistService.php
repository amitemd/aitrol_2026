<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Especialidad;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\v3\clinical\ScheduledCare;

class V3ClinicalWorklistService
{
    public function nursingFilters(Request $request)
    {
        $today = date('Y-m-d');
        return array(
            'desde' => $this->date($request->input('desde', $request->input('fecha', $today)), $today),
            'hasta' => $this->date($request->input('hasta', $request->input('fechafin', $today)), $today),
            'paciente' => trim((string) $request->input('paciente', $request->input('nombres', ''))),
            'cedula' => trim((string) $request->input('cedula', '')),
        );
    }

    public function nursing(array $filters, $companyId)
    {
        $query = $this->baseAgenda($companyId)->where('a.estado_cita', 4)->whereIn('a.proc_consul', array(0, 1, 4));
        $this->dateAndPatientFilters($query, $filters);
        $rows = $query->orderBy('a.fechaini', 'desc')->paginate(25);
        $this->attachAnesthesiaProcedures($rows->getCollection());
        return $rows;
    }

    public function dailyFilters(Request $request)
    {
        $today = date('Y-m-d');
        return array(
            'desde' => $this->date($request->input('desde', $request->input('fecha', $today)), $today),
            'hasta' => $this->date($request->input('hasta', $request->input('fecha_hasta', $today)), $today),
            'paciente' => trim((string) $request->input('paciente', $request->input('nombres', ''))),
            'cedula' => trim((string) $request->input('cedula', '')),
            'especialidad' => trim((string) $request->input('especialidad', $request->input('espid', ''))),
            'doctor' => trim((string) $request->input('doctor', $request->input('id_doctor1', ''))),
            'seguro' => trim((string) $request->input('seguro', $request->input('id_seguro', ''))),
        );
    }

    public function daily(array $filters, $companyId)
    {
        $query = $this->baseAgenda($companyId)->whereIn('a.estado', array(1, 4));
        $this->dateAndPatientFilters($query, $filters);
        if ($filters['especialidad'] !== '') $query->where('a.espid', $filters['especialidad']);
        if ($filters['doctor'] !== '') $query->where('a.id_doctor1', $filters['doctor']);
        if ($filters['seguro'] !== '') $query->where('a.id_seguro', $filters['seguro']);
        $rows = $query->orderBy('a.fechaini', 'desc')->paginate(25);
        $this->attachClinicalState($rows->getCollection());
        return $rows;
    }

    public function catalogs()
    {
        return app(V3CatalogCacheService::class)->remember('clinical-worklist', function () {
            return array(
                'doctors' => DoctorUserType::constrain(User::query()->from('users as doctor'), 'doctor', 'doctor_type')
                    ->where('doctor.estado', 1)->orderBy('doctor.apellido1')->select('doctor.*')->get(),
                'specialties' => Especialidad::where('estado', 1)->orderBy('nombre')->get(),
                'insurances' => Seguro::orderBy('nombre')->get(),
            );
        });
    }

    private function baseAgenda($companyId)
    {
        return ScheduledCare::query()->from('agenda as a')->join('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->leftJoin('seguros as s', 's.id', '=', 'a.id_seguro')->leftJoin('users as d', 'd.id', '=', 'a.id_doctor1')
            ->leftJoin('especialidad as e', 'e.id', '=', 'a.espid')->leftJoin('sala as sa', 'sa.id', '=', 'a.id_sala')
            ->leftJoin('procedimiento as pr', 'pr.id', '=', 'a.id_procedimiento')->where('a.id_empresa', $companyId)
            ->select(array('a.*', 'p.nombre1 as patient_name1', 'p.nombre2 as patient_name2', 'p.apellido1 as patient_lastname1', 'p.apellido2 as patient_lastname2', 'p.fecha_nacimiento', 's.nombre as insurance_name', 'd.nombre1 as doctor_name', 'd.apellido1 as doctor_lastname', 'e.nombre as specialty_name', 'sa.nombre_sala as room_name', 'pr.nombre as procedure_name'));
    }

    private function dateAndPatientFilters($query, array $filters)
    {
        $desde = $filters['desde']; $hasta = $filters['hasta'];
        if ($desde > $hasta) { $copy = $desde; $desde = $hasta; $hasta = $copy; }
        $query->whereBetween('a.fechaini', array($desde.' 00:00:00', $hasta.' 23:59:59'));
        if (isset($filters['cedula']) && $filters['cedula'] !== '') $query->where('a.id_paciente', 'like', '%'.$this->escapeLike($filters['cedula']).'%');
        if ($filters['paciente'] !== '') {
            foreach (preg_split('/\s+/', $filters['paciente']) as $term) {
                if ($term === '') continue; $like = '%'.$this->escapeLike($term).'%';
                $query->where(function ($part) use ($like) { $part->where('p.nombre1', 'like', $like)->orWhere('p.nombre2', 'like', $like)->orWhere('p.apellido1', 'like', $like)->orWhere('p.apellido2', 'like', $like); });
            }
        }
    }

    private function attachClinicalState($rows)
    {
        $agendaIds = $rows->pluck('id')->filter()->all(); if (!$agendaIds) return;
        $histories = DB::table('historiaclinica')->whereIn('id_agenda', $agendaIds)->orderBy('hcid', 'desc')->select('id_agenda', 'hcid')->get();
        $historyByAgenda = collect(); foreach ($histories as $history) if (!$historyByAgenda->has($history->id_agenda)) $historyByAgenda->put($history->id_agenda, $history);
        $historyIds = $historyByAgenda->pluck('hcid')->filter()->all();
        $diagnoses = $historyIds ? DB::table('hc_cie10')->whereIn('hcid', $historyIds)->select('hcid', DB::raw('COUNT(*) as total'))->groupBy('hcid')->get()->keyBy('hcid') : collect();
        $extra = DB::table('agenda_procedimiento as ap')->join('procedimiento as p', 'p.id', '=', 'ap.id_procedimiento')->whereIn('ap.id_agenda', $agendaIds)->select('ap.id_agenda', 'p.nombre')->get()->groupBy('id_agenda');
        foreach ($rows as $row) {
            $history = $historyByAgenda->get($row->id); $row->history_id = $history ? $history->hcid : null;
            $count = $history ? $diagnoses->get($history->hcid) : null; $row->diagnosis_count = $count ? (int) $count->total : 0;
            $row->additional_procedures = $extra->get($row->id, collect());
        }
    }

    private function attachAnesthesiaProcedures($rows)
    {
        $agendaIds = $rows->filter(function ($row) {
            return in_array((int) $row->proc_consul, array(1, 4), true);
        })->pluck('id')->filter()->all();
        $proceduresByAgenda = $agendaIds ? DB::table('historiaclinica as hc')
            ->join('hc_procedimientos as hp', 'hp.id_hc', '=', 'hc.hcid')
            ->whereIn('hc.id_agenda', $agendaIds)
            ->orderBy('hp.id')
            ->select('hc.id_agenda', 'hp.id')
            ->get()
            ->groupBy('id_agenda') : collect();

        foreach ($rows as $row) {
            $row->anesthesia_procedures = $proceduresByAgenda->get($row->id, collect());
        }
    }

    private function date($value, $default) { $value = str_replace('/', '-', trim((string) $value)); $date = \DateTime::createFromFormat('Y-m-d', $value); return $date && $date->format('Y-m-d') === $value ? $value : $default; }
    private function escapeLike($value) { return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $value); }
}
