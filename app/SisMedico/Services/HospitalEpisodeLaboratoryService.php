<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Sis_medico\ClinicalEncounter;
use Sis_medico\Examen_Orden;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Support\RequestSchema;

/** Read the laboratory's existing order/result records; never copy them into drafts. */
class HospitalEpisodeLaboratoryService
{
    public function orders(Ho_Solicitud $episode, ?ClinicalEncounter $encounter = null)
    {
        if (!$this->hasTables(['examen_orden', 'ho_orden_examenes'])) return collect();
        $encounter = $encounter ?: ClinicalEncounter::find($episode->encounter_id);
        if (!$encounter || !$encounter->id_empresa) return collect();

        return Examen_Orden::query()->where('id_paciente', $episode->id_paciente)
            ->where('id_empresa', $encounter->id_empresa)->whereIn('estado', [-1, 1])
            ->whereIn('id', function ($query) use ($episode) {
                $query->select('id_examen_orden')->from('ho_orden_examenes')
                    ->where('id_ho_solicitud', $episode->id);
            })->orderByDesc('id')->get();
    }

    public function order(Ho_Solicitud $episode, $orderId)
    {
        $order = $this->orders($episode)->firstWhere('id', $orderId);
        abort_unless($order, 404, 'La orden de laboratorio no pertenece a este episodio.');
        return $order;
    }

    public function report(Ho_Solicitud $episode, Examen_Orden $order)
    {
        $exams = $this->hasTables(['examen_detalle', 'examen'])
            ? DB::table('examen')->whereIn('id', function ($query) use ($order) {
                $query->select('id_examen')->from('examen_detalle')->where('id_examen_orden', $order->id);
            })->orderBy('nombre')->get() : collect();
        $parameters = RequestSchema::hasTable('examen_parametro')
            ? DB::table('examen_parametro')->whereIn('id_examen', $exams->pluck('id'))->get()
                ->sortBy(function ($row) { return $row->orden ?? $row->id; }) : collect();
        $results = RequestSchema::hasTable('examen_resultado')
            ? DB::table('examen_resultado')->where('id_orden', $order->id)
                ->whereIn('id_parametro', $parameters->pluck('id'))->orderByDesc('id')->get()
                ->unique('id_parametro')->keyBy('id_parametro') : collect();
        // Use the same historical reference selection as the result-entry module.
        $references = RequestSchema::hasTable('examen_parametro_referencia_anterior') && $order->fecha_orden
            ? DB::table('examen_parametro_referencia_anterior')->whereIn('id_parametro', $parameters->pluck('id'))
                ->where('fecha_valida', '>', $order->fecha_orden)->orderBy('fecha_valida')->get()->groupBy('id_parametro') : collect();
        $details = RequestSchema::hasTable('examen_sub_resultado')
            ? DB::table('examen_sub_resultado')->where('id_orden', $order->id)
                ->whereIn('id_examen', $exams->pluck('id'))->where('estado', 1)->orderBy('id')->get() : collect();
        $rows = [];
        $expected = $recorded = $certified = 0;
        $sex = $episode->paciente->sexo ?? null;
        foreach ($exams as $exam) {
            $examParameters = $parameters->where('id_examen', $exam->id)->filter(function ($parameter) use ($exam, $sex, $results) {
                if ((int) ($exam->no_resultado ?? 0) === 1 && !isset($results[$parameter->id])) return false;
                if (isset($parameter->estado) && (int) $parameter->estado !== 1 && !isset($results[$parameter->id])) return false;
                $expectedSex = (int) ($exam->sexo_n_s ?? 0) === 0 ? 3 : $sex;
                return !isset($parameter->sexo) || (string) $parameter->sexo === (string) $expectedSex;
            });
            if ($examParameters->isEmpty()) {
                $requiresResult = (int) ($exam->no_resultado ?? 0) !== 1;
                if ($requiresResult) $expected++;
                $rows[] = ['exam' => $exam->nombre, 'parameter' => '', 'value' => '', 'unit' => '', 'reference' => '',
                    'state' => $requiresResult ? 'Pendiente' : 'No requiere resultado', 'recorded_at' => '', 'recorded_by' => ''];
            }
            foreach ($examParameters as $parameter) {
                $result = $results[$parameter->id] ?? null;
                $value = $result ? self::plain($result->valor) : '';
                $filled = $result && $value !== '';
                $isCertified = $filled && (string) ($result->certificado ?? '') === '1';
                $expected++;
                if ($filled) $recorded++;
                if ($isCertified) $certified++;
                $reference = isset($references[$parameter->id]) ? $references[$parameter->id]->first() : $parameter;
                $referenceText = self::plain($reference->texto_referencia ?? '');
                if ($referenceText === '') {
                    $range = array_filter([$reference->valor1 ?? null, $reference->valor1g ?? null], function ($v) {
                        return $v !== null && trim((string) $v) !== '';
                    });
                    $referenceText = implode(' - ', $range);
                }
                $rows[] = ['exam' => $exam->nombre, 'parameter' => $parameter->nombre, 'value' => $value,
                    'unit' => self::plain($reference->unidad1 ?? ''), 'reference' => $referenceText,
                    'state' => !$filled ? 'Pendiente' : ($isCertified ? 'Certificado' : 'Por certificar'),
                    'recorded_at' => $result->updated_at ?? '', 'recorded_by' => $result->id_usuariomod ?? ''];
            }
        }
        $complete = $expected > 0 && $recorded === $expected && $certified === $expected && (int) $order->estado === 1;
        return compact('order', 'exams', 'rows', 'details', 'expected', 'recorded', 'certified', 'complete');
    }

    public function links(Ho_Solicitud $episode, ?ClinicalEncounter $encounter = null)
    {
        $links = ['010A' => [], '010B' => []];
        if (!Route::has('hospital.laboratory.pdf')) return $links;
        foreach ($this->orders($episode, $encounter) as $order) {
            $report = $this->report($episode, $order);
            if ($report['exams']->isEmpty()) continue;
            $links['010A'][] = $this->pdfLink($episode->id, $order->id, '010A', (int) $order->estado === 1,
                (int) $order->estado === 1 ? 'Solicitud registrada' : 'Solicitud pendiente');
            if ($report['recorded'] || $report['details']->isNotEmpty()) {
                $links['010B'][] = $this->pdfLink($episode->id, $order->id, '010B', $report['complete'],
                    $report['complete'] ? 'Informe certificado' : 'Resultados por completar / certificar');
            }
        }
        return $links;
    }

    private function pdfLink($episodeId, $orderId, $code, $official, $status)
    {
        return ['url' => route('hospital.laboratory.pdf', ['id_solicitud' => $episodeId, 'order' => $orderId, 'code' => $code]),
            'label' => 'Orden #'.$orderId, 'official' => $official, 'status' => $status,
            'preview_label' => $code === '010A' ? 'Ver solicitud' : 'Ver resultados'];
    }

    public static function plain($value)
    {
        $value = preg_replace('/<br\s*\/?\s*>|<\/(?:p|div|li|tr)>/i', "\n", (string) $value);
        return trim(html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private function hasTables(array $tables)
    {
        foreach ($tables as $table) if (!RequestSchema::hasTable($table)) return false;
        return true;
    }
}
