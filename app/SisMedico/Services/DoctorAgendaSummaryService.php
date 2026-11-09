<?php

namespace Sis_medico\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Procedimiento;
use Sis_medico\Sala;

class DoctorAgendaSummaryService
{
    /** Datos complementarios en lotes; la vista no debe consultar por cada fila. */
    public function load(Collection $appointments, Collection $meetings)
    {
        $historyIds = $appointments->pluck('hcid')->filter()->unique()->values();
        $diagnoses = $historyIds->isEmpty() ? collect() : DB::table('hc_cie10')
            ->whereIn('hcid', $historyIds->all())
            ->select('hcid', DB::raw('COUNT(*) as total'))
            ->groupBy('hcid')->pluck('total', 'hcid');

        $roomIds = $meetings->pluck('id_sala')->filter()->unique()->values();
        $rooms = $roomIds->isEmpty() ? collect() : Sala::with('hospital')
            ->whereIn('id', $roomIds->all())->get()->keyBy('id');

        return [
            'diagnosticos_por_hcid' => $diagnoses,
            'salas_reuniones' => $rooms,
            'ultimos_seguros' => $this->latestInsurance($appointments),
            'agendas_proc' => $this->procedureNames($appointments->where('proc_consul', '1')),
        ];
    }

    private function latestInsurance(Collection $appointments)
    {
        $patientIds = $appointments->pluck('id')->filter()->unique()->values();
        if ($patientIds->isEmpty()) {
            return collect();
        }

        // Conserva el convenio de la última historia válida que mostraba la
        // pantalla, incluyendo sus joins y la exclusión de especialidad 10.
        $histories = DB::table('agenda as a')
            ->whereIn('a.id_paciente', $patientIds->all())
            ->where('a.espid', '<>', '10')
            ->join('historiaclinica as h', 'h.id_agenda', '=', 'a.id')
            ->join('seguros as s', 's.id', '=', 'h.id_seguro')
            ->join('empresa as em', 'em.id', '=', 'a.id_empresa');

        $latestDates = (clone $histories)->select('a.id_paciente')
            ->selectRaw('MAX(a.fechaini) as latest_date')->groupBy('a.id_paciente');

        return $histories->joinSub($latestDates, 'latest', function ($join) {
                $join->on('latest.id_paciente', '=', 'a.id_paciente')
                    ->on('latest.latest_date', '=', 'a.fechaini');
            })
            ->select('a.id_paciente', 's.nombre', 'em.nombre_corto')
            ->orderBy('a.id', 'desc')->orderBy('h.hcid', 'desc')
            ->get()->unique('id_paciente')->keyBy('id_paciente');
    }

    private function procedureNames(Collection $appointments)
    {
        $appointments = $appointments->unique('id_agenda')->keyBy('id_agenda');
        if ($appointments->isEmpty()) {
            return [];
        }

        $pentaxes = Pentax::whereIn('id_agenda', $appointments->keys()->all())
            ->orderBy('id')->get()->unique('id_agenda')->keyBy('id_agenda');
        $pentaxDetails = $pentaxes->isEmpty() ? collect() : PentaxProc::whereIn('id_pentax', $pentaxes->pluck('id')->all())
            ->orderBy('id')->get();
        $fallbackIds = $appointments->keys()->diff($pentaxes->keys());
        $additional = $fallbackIds->isEmpty() ? collect() : AgendaProcedimiento::whereIn('id_agenda', $fallbackIds->all())
            ->orderBy('id')->get();

        $procedureIds = $appointments->only($fallbackIds->all())->pluck('id_procedimiento')
            ->merge($additional->pluck('id_procedimiento'))
            ->merge($pentaxDetails->pluck('id_procedimiento'))->filter()->unique();
        $procedures = $procedureIds->isEmpty() ? collect() : Procedimiento::whereIn('id', $procedureIds->all())
            ->get()->keyBy('id');
        $pentaxDetails = $pentaxDetails->groupBy('id_pentax');
        $additional = $additional->groupBy('id_agenda');

        $names = [];
        foreach ($appointments as $agendaId => $appointment) {
            $pentax = $pentaxes->get($agendaId);
            if ($pentax) {
                $labels = [];
                foreach ($pentaxDetails->get($pentax->id, collect()) as $detail) {
                    if ($procedure = $procedures->get($detail->id_procedimiento)) {
                        $labels[] = $procedure->nombre;
                    }
                }
                $names[$agendaId] = [implode('+', $labels)];
            } elseif ($procedure = $procedures->get($appointment->id_procedimiento)) {
                $label = $procedure->nombre;
                foreach ($additional->get($agendaId, collect()) as $detail) {
                    if ($extra = $procedures->get($detail->id_procedimiento)) {
                        $label .= ' + '.$extra->nombre;
                    }
                }
                $names[$agendaId] = [$label];
            }
        }

        return $names;
    }
}
