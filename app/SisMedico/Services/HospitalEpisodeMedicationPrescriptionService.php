<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Support\RequestSchema as Schema;

/**
 * Medication orders that may be transcribed into Form 022.
 *
 * Every query is constrained to the current encounter and patient. This keeps
 * an old prescription from another admission out of the nursing record.
 */
class HospitalEpisodeMedicationPrescriptionService
{
    public function forEpisode(Ho_Solicitud $solicitud)
    {
        $prescriptions = [];

        foreach ($this->legacyPrescriptions($solicitud) as $row) {
            $key = 'hc_receta_detalle:'.$row->detail_id;
            $prescriptions[$key] = [
                'prescripcion' => $key,
                'receta_id' => (string) $row->recipe_id,
                'medicamento' => trim((string) $row->medication_name),
                'dosis' => trim((string) $row->ordered_dose),
                'via' => trim((string) $row->ordered_route),
                'frecuencia' => trim((string) $row->ordered_frequency),
                'prescrita_por' => '',
                'fecha_prescripcion' => $this->dateLabel($row->ordered_at),
                'administraciones' => [],
                'eventos_emar' => [],
                '_prescriber_id' => trim((string) $row->prescriber_id),
            ];
        }

        foreach ($this->clinicalOrders($solicitud) as $row) {
            $sourceType = trim((string) $row->source_type);
            $sourceId = trim((string) $row->source_id);
            $key = $sourceType === 'hc_receta_detalle' && $sourceId !== ''
                ? 'hc_receta_detalle:'.$sourceId
                : 'clinical_medication_order:'.$row->id;

            if (isset($prescriptions[$key])) {
                if ($prescriptions[$key]['dosis'] === '') $prescriptions[$key]['dosis'] = trim((string) $row->ordered_dose);
                if ($prescriptions[$key]['via'] === '') $prescriptions[$key]['via'] = trim((string) $row->ordered_route);
                if ($prescriptions[$key]['fecha_prescripcion'] === '') $prescriptions[$key]['fecha_prescripcion'] = $this->dateLabel($row->ordered_at);
                if ($prescriptions[$key]['_prescriber_id'] === '') $prescriptions[$key]['_prescriber_id'] = trim((string) $row->ordered_by);
                continue;
            }

            $prescriptions[$key] = [
                'prescripcion' => $key,
                'receta_id' => '',
                'medicamento' => trim((string) $row->medication_name),
                'dosis' => trim((string) $row->ordered_dose),
                'via' => trim((string) $row->ordered_route),
                'frecuencia' => '',
                'prescrita_por' => '',
                'fecha_prescripcion' => $this->dateLabel($row->ordered_at),
                'administraciones' => [],
                'eventos_emar' => [],
                '_prescriber_id' => trim((string) $row->ordered_by),
            ];
        }

        $administrations = $this->clinicalAdministrations($solicitud);
        $userIds = array_merge(
            array_values(array_unique(array_filter(array_column($prescriptions, '_prescriber_id')))),
            array_values(array_unique(array_filter(array_column($administrations, 'administered_by'))))
        );
        $names = $this->userNames($userIds);
        foreach ($prescriptions as &$prescription) {
            $prescription['prescrita_por'] = $names[$prescription['_prescriber_id']] ?? '';
            unset($prescription['_prescriber_id']);
        }
        unset($prescription);

        foreach ($administrations as $administration) {
            $key = $administration['prescripcion'];
            if (!isset($prescriptions[$key])) continue;
            $event = [
                'emar_id' => (string) $administration['emar_id'],
                'fecha' => substr((string) $administration['administered_at'], 0, 10),
                'hora' => substr((string) $administration['administered_at'], 11, 5),
                'administered_at' => substr((string) $administration['administered_at'], 0, 16),
                'programada' => substr((string) $administration['scheduled_at'], 0, 16),
                'responsable' => $names[(string) $administration['administered_by']] ?? (string) $administration['administered_by'],
                'estado' => (string) $administration['status'],
                'motivo_omision' => trim((string) $administration['omission_reason']),
                'resultado' => trim((string) $administration['clinical_result']),
            ];
            $prescriptions[$key]['eventos_emar'][] = $event;
            if ($event['estado'] === 'ADMINISTERED') $prescriptions[$key]['administraciones'][] = $event;
        }

        return array_values(array_filter($prescriptions, function ($prescription) {
            return $prescription['medicamento'] !== '';
        }));
    }

    private function legacyPrescriptions(Ho_Solicitud $solicitud)
    {
        foreach (['historiaclinica', 'hc_receta', 'hc_receta_detalle'] as $table) {
            if (!Schema::hasTable($table)) return [];
        }
        if (!Schema::hasColumns('historiaclinica', ['hcid', 'id_paciente'])
            || !Schema::hasColumns('hc_receta', ['id', 'id_hc'])
            || !Schema::hasColumns('hc_receta_detalle', ['id', 'id_hc_receta', 'nombre', 'dosis'])) return [];

        $query = DB::table('hc_receta_detalle as detail')
            ->join('hc_receta as recipe', 'recipe.id', '=', 'detail.id_hc_receta')
            ->join('historiaclinica as history', 'history.hcid', '=', 'recipe.id_hc')
            ->where('history.id_paciente', $solicitud->id_paciente);

        if (!empty($solicitud->encounter_id) && Schema::hasColumn('historiaclinica', 'encounter_id')) {
            $query->where('history.encounter_id', $solicitud->encounter_id);
        } elseif (!empty($solicitud->id_agenda) && Schema::hasColumn('historiaclinica', 'id_agenda')) {
            $query->where('history.id_agenda', $solicitud->id_agenda);
        } else {
            return [];
        }

        $query->select([
            'detail.id as detail_id', 'recipe.id as recipe_id', 'detail.nombre as medication_name', 'detail.dosis as ordered_dose',
            DB::raw($this->columnExpression('detail', 'hc_receta_detalle', 'via', "''").' as ordered_route'),
            DB::raw($this->columnExpression('detail', 'hc_receta_detalle', 'frecuencia', "''").' as ordered_frequency'),
            DB::raw($this->columnExpression('detail', 'hc_receta_detalle', 'created_at',
                $this->columnExpression('recipe', 'hc_receta', 'created_at', 'NULL')).' as ordered_at'),
            DB::raw($this->prescriberExpression().' as prescriber_id'),
        ]);

        return $query->orderByDesc('detail.id')->get()->all();
    }

    private function clinicalOrders(Ho_Solicitud $solicitud)
    {
        if (!Schema::hasTable('clinical_medication_orders')
            || !Schema::hasColumns('clinical_medication_orders', [
                'id', 'encounter_id', 'patient_id', 'source_type', 'source_id', 'medication_name',
                'ordered_dose', 'ordered_route', 'status', 'ordered_by', 'ordered_at',
            ]) || empty($solicitud->encounter_id)) return [];

        return DB::table('clinical_medication_orders')
            ->where('encounter_id', $solicitud->encounter_id)
            ->where('patient_id', $solicitud->id_paciente)
            ->whereNotNull('ordered_by')
            ->where('source_type', '<>', 'EMERGENCY_PROTOCOL')
            ->where('status', '<>', 'CANCELLED')
            ->orderByDesc('id')
            ->get()->all();
    }

    private function clinicalAdministrations(Ho_Solicitud $solicitud)
    {
        if (empty($solicitud->encounter_id)
            || !Schema::hasTable('clinical_medication_orders')
            || !Schema::hasTable('clinical_medication_administrations')
            || !Schema::hasColumns('clinical_medication_administrations', [
                'id', 'encounter_id', 'medication_order_id', 'patient_id', 'scheduled_at',
                'administered_at', 'status', 'administered_by',
            ])) return [];

        $rows = DB::table('clinical_medication_administrations as administration')
            ->join('clinical_medication_orders as medication_order', 'medication_order.id', '=', 'administration.medication_order_id')
            ->where('administration.encounter_id', $solicitud->encounter_id)
            ->where('administration.patient_id', $solicitud->id_paciente)
            ->whereIn('administration.status', ['ADMINISTERED', 'OMITTED'])
            ->select([
                'administration.id as emar_id', 'administration.scheduled_at', 'administration.administered_at',
                'administration.status', 'administration.administered_by',
                'administration.omission_reason', 'administration.clinical_result',
                'medication_order.id as order_id', 'medication_order.source_type', 'medication_order.source_id',
            ])->orderBy('administration.administered_at')->orderBy('administration.id')->get();

        $regularized = [];
        if (Schema::hasTable('clinical_emergency_medication_exceptions')
            && Schema::hasColumns('clinical_emergency_medication_exceptions', ['administration_id', 'regularized_order_id', 'status'])) {
            $links = DB::table('clinical_emergency_medication_exceptions')
                ->whereIn('administration_id', $rows->pluck('emar_id'))
                ->where('status', 'REGULARIZED')->whereNotNull('regularized_order_id')->get();
            $regularizedOrders = DB::table('clinical_medication_orders')
                ->whereIn('id', $links->pluck('regularized_order_id'))->get()->keyBy('id');
            foreach ($links as $link) {
                if (isset($regularizedOrders[$link->regularized_order_id])) {
                    $regularized[(string) $link->administration_id] = $regularizedOrders[$link->regularized_order_id];
                }
            }
        }

        $result = [];
        foreach ($rows as $row) {
            $order = $row;
            if ((string) $row->source_type === 'EMERGENCY_PROTOCOL') {
                $order = $regularized[(string) $row->emar_id] ?? null;
                if (!$order) continue;
            }
            $sourceType = trim((string) $order->source_type);
            $sourceId = trim((string) $order->source_id);
            $key = $sourceType === 'hc_receta_detalle' && $sourceId !== ''
                ? 'hc_receta_detalle:'.$sourceId
                : 'clinical_medication_order:'.$order->id;
            $result[] = [
                'prescripcion' => $key,
                'emar_id' => $row->emar_id,
                'scheduled_at' => $row->scheduled_at,
                'administered_at' => $row->administered_at,
                'status' => $row->status,
                'administered_by' => $row->administered_by,
                'omission_reason' => $row->omission_reason,
                'clinical_result' => $row->clinical_result,
            ];
        }
        return $result;
    }

    private function prescriberExpression()
    {
        if (Schema::hasColumn('hc_receta', 'id_doctor_examinador')) {
            if (Schema::hasColumn('hc_receta', 'id_usuariocrea')) {
                return "COALESCE(NULLIF(recipe.id_doctor_examinador, ''), recipe.id_usuariocrea)";
            }
            return 'recipe.id_doctor_examinador';
        }
        return $this->columnExpression('recipe', 'hc_receta', 'id_usuariocrea', "''");
    }

    private function columnExpression($alias, $table, $column, $fallback)
    {
        return Schema::hasColumn($table, $column) ? $alias.'.'.$column : $fallback;
    }

    private function userNames(array $ids)
    {
        if (!$ids || !Schema::hasTable('users') || !Schema::hasColumn('users', 'id')) return [];

        $columns = ['id'];
        foreach (['apellido1', 'apellido2', 'nombre1', 'nombre2'] as $column) {
            if (Schema::hasColumn('users', $column)) $columns[] = $column;
        }
        $names = [];
        foreach (DB::table('users')->whereIn('id', $ids)->get($columns) as $user) {
            $parts = [];
            foreach (['apellido1', 'apellido2', 'nombre1', 'nombre2'] as $column) {
                if (!empty($user->{$column})) $parts[] = trim((string) $user->{$column});
            }
            $names[(string) $user->id] = trim(implode(' ', $parts));
        }
        return $names;
    }

    private function dateLabel($value)
    {
        return $value ? substr(trim((string) $value), 0, 16) : '';
    }
}
