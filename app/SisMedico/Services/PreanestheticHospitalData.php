<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sis_medico\Ho_Form008;
use Sis_medico\Ho_Solicitud;
use Sis_medico\HospitalEpisodeFormDraft;

/**
 * Reconciles the legacy preanesthetic assessment with data already captured
 * in the hospital record. Only empty fields are completed: a value entered by
 * the anesthesiologist is never replaced by an automatic import.
 */
class PreanestheticHospitalData
{
    public function sync($preAnesthetic, $userId = null, $ip = null)
    {
        $result = ['updated_fields' => [], 'sources' => [], 'replicated' => []];
        if (! $preAnesthetic || ! $preAnesthetic->anestesiologia) {
            return $result;
        }

        $history = $preAnesthetic->anestesiologia->historia;
        $patient = $history ? $history->paciente : null;
        if (! $history || ! $patient) {
            return $result;
        }

        $form008 = $this->form008ForHistory($history);
        $draft = $this->draftForHistory($history, $form008);
        $values = [];
        $fieldSources = [];

        if ($draft) {
            $draftPayload = (array) $draft->payload;
            $this->mergeOffers($values, $fieldSources, $this->valuesFromDraft($draftPayload), 'Formulario 018');
        }

        $antecedents = Form008PatientAntecedents::build(
            $patient,
            $patient->ho_datos_paciente,
            $patient->a_alergias
        );
        $legacyAntecedents = $this->legacyPatientAntecedents($patient->id);

        $this->offer($values, $fieldSources, 'quirurgicos', self::combine([
            isset($antecedents[6]) ? $antecedents[6] : '',
            isset($legacyAntecedents[3]) ? $legacyAntecedents[3] : '',
        ]), 'Historia clínica / Formulario 008');
        $this->offer($values, $fieldSources, 'alergicos', isset($antecedents[1]) ? $antecedents[1] : '', 'Historia clínica / Formulario 008');
        $this->offer($values, $fieldSources, 'habitos', isset($antecedents[8]) ? $antecedents[8] : '', 'Historia clínica / Formulario 008');
        $this->offer($values, $fieldSources, 'transfuciones', self::value($patient, 'transfusion'), 'Historia clínica');

        if ($form008) {
            $this->mergeOffers($values, $fieldSources, $this->physicalExamValues($form008), 'Formulario 008');
        }

        $this->mergeOffers($values, $fieldSources, $this->evolutionPhysicalExamValues($history), 'Evolución clínica');
        $this->mergeOffers($values, $fieldSources, $this->vitalValues($history, $form008), 'Signos vitales del episodio');

        $agenda = $history->agenda;
        if ($agenda && (string) self::value($agenda, 'est_amb_hos') === '2') {
            $this->offer($values, $fieldSources, 'tipo', 'EMERGENCIA', 'Atención hospitalaria');
        }

        foreach ($values as $field => $value) {
            if (! self::blank(self::value($preAnesthetic, $field)) || self::blank($value)) {
                continue;
            }
            if (! array_key_exists($field, $preAnesthetic->getAttributes())
                && (! Schema::hasTable('rc_pre_anestesica') || ! Schema::hasColumn('rc_pre_anestesica', $field))) {
                continue;
            }

            $preAnesthetic->{$field} = $value;
            $result['updated_fields'][] = $field;
            if (isset($fieldSources[$field])) {
                $result['sources'][$fieldSources[$field]] = true;
            }
        }

        if (count($result['updated_fields']) > 0) {
            if ($userId !== null) {
                $preAnesthetic->id_usuariomod = $userId;
            }
            if ($ip !== null) {
                $preAnesthetic->ip_modificacion = $ip;
            }
            $preAnesthetic->save();
        }

        $result['sources'] = array_keys($result['sources']);
        $result['replicated'] = $this->syncReadOnlySections($preAnesthetic, $userId, $ip);

        return $result;
    }

    /**
     * Copies the medical information required by the legacy Form 018 into its
     * own child tables. The operation is additive and idempotent: existing
     * rows are preserved and reopening the assessment never creates doubles.
     */
    public function syncReadOnlySections($preAnesthetic, $userId = null, $ip = null)
    {
        $summary = [
            'antecedentes' => ['inserted' => 0, 'existing' => 0],
            'indicaciones' => ['inserted' => 0, 'existing' => 0],
            'resultados' => ['inserted' => 0, 'existing' => 0, 'skipped' => 0],
        ];

        if (! $preAnesthetic || ! $preAnesthetic->id || ! $preAnesthetic->id_hc_anestesiologia
            || ! $this->hasTables(['rc_antecedentes', 'rc_indicaciones', 'rc_resultados'])) {
            return $summary;
        }

        $antecedents = collect($this->contextRows($preAnesthetic))->map(function ($row) {
            $label = self::clean(isset($row['label']) ? $row['label'] : '');
            $description = self::clean(isset($row['description']) ? $row['description'] : '');

            return [
                'label' => $label,
                'diagnostico' => $description,
            ];
        })->filter(function ($row) {
            return $row['diagnostico'] !== '' && strpos(self::normalized($row['label']), 'ALERG') === false;
        })->unique(function ($row) {
            return self::normalized($row['diagnostico']);
        })->values();

        $indications = $this->readOnlyIndications($preAnesthetic);
        $results = $this->readOnlyExamResults($preAnesthetic);
        $audit = $this->auditValues($userId, $ip);

        DB::transaction(function () use ($preAnesthetic, $antecedents, $indications, $results, $audit, &$summary) {
            $existingAntecedents = DB::table('rc_antecedentes')
                ->where('id_rc_pre_anestesica', $preAnesthetic->id)
                ->where('estado', 1)
                ->pluck('diagnostico')
                ->map(function ($value) {
                    return self::normalized($value);
                })->filter()->flip()->all();

            foreach ($antecedents as $row) {
                $key = self::normalized($row['diagnostico']);
                if (isset($existingAntecedents[$key])) {
                    $summary['antecedentes']['existing']++;

                    continue;
                }

                DB::table('rc_antecedentes')->insert(array_merge([
                    'id_rc_pre_anestesica' => $preAnesthetic->id,
                    'id_hc_anestesiologia' => $preAnesthetic->id_hc_anestesiologia,
                    'diagnostico' => $row['diagnostico'],
                    'tiempo_evolucion' => '',
                    'tratamiento' => '',
                    'estado' => 1,
                ], $audit));
                $existingAntecedents[$key] = true;
                $summary['antecedentes']['inserted']++;
            }

            $existingIndications = DB::table('rc_indicaciones')
                ->where('id_rc_pre_anestesica', $preAnesthetic->id)
                ->where('estado', 1)
                ->pluck('indicaciones')
                ->map(function ($value) {
                    return self::normalized($value);
                })->filter()->flip()->all();

            foreach ($indications as $row) {
                $description = self::clean(self::value($row, 'indicaciones'));
                $key = self::normalized($description);
                if ($key === '') {
                    continue;
                }
                if (isset($existingIndications[$key])) {
                    $summary['indicaciones']['existing']++;

                    continue;
                }

                DB::table('rc_indicaciones')->insert(array_merge([
                    'id_hc_anestesiologia' => $preAnesthetic->id_hc_anestesiologia,
                    'id_rc_pre_anestesica' => $preAnesthetic->id,
                    'estado' => 1,
                    'indicaciones' => $description,
                ], $audit));
                $existingIndications[$key] = true;
                $summary['indicaciones']['inserted']++;
            }

            $existingResults = DB::table('rc_resultados')
                ->where('id_rc_pre_anestesia', $preAnesthetic->id)
                ->where('estado', 1)
                ->get();

            foreach ($results as $row) {
                $examId = (int) self::value($row, 'id_examen');
                $parameterId = self::value($row, 'id_parametro');
                $orderId = self::value($row, 'id_orden');
                $name = self::clean(self::value($row, 'nombre'));
                $value = self::clean(self::value($row, 'resultado'));

                // rc_resultados.id_examen is mandatory. Free text that cannot
                // be matched to the exam catalog remains visible at its source
                // but is never stored with a fabricated foreign key.
                if ($examId <= 0 || ($value === '' && $name === '') || self::normalized($value) === 'PENDIENTE') {
                    $summary['resultados']['skipped']++;

                    continue;
                }

                $exists = $existingResults->contains(function ($existing) use ($examId, $parameterId, $orderId, $name, $value) {
                    if (! self::blank($orderId) && ! self::blank($parameterId)) {
                        return (string) self::value($existing, 'id_orden') === (string) $orderId
                            && (string) self::value($existing, 'id_parametro') === (string) $parameterId;
                    }

                    return (int) self::value($existing, 'id_examen') === $examId
                        && self::normalized(self::value($existing, 'nombre')) === self::normalized($name)
                        && self::normalized(self::value($existing, 'resultado')) === self::normalized($value);
                });

                if ($exists) {
                    $summary['resultados']['existing']++;

                    continue;
                }

                DB::table('rc_resultados')->insert(array_merge([
                    'id_examen' => $examId,
                    'id_rc_pre_anestesia' => $preAnesthetic->id,
                    'id_hc_anestesiologia' => $preAnesthetic->id_hc_anestesiologia,
                    'nombre' => $name,
                    'resultado' => $value,
                    'estado' => 1,
                    'id_parametro' => self::blank($parameterId) ? null : $parameterId,
                    'id_orden' => self::blank($orderId) ? null : $orderId,
                    'id_agrupador_publico' => self::value($row, 'id_agrupador_publico'),
                ], $audit));
                $existingResults->push((object) [
                    'id_examen' => $examId,
                    'id_parametro' => $parameterId,
                    'id_orden' => $orderId,
                    'nombre' => $name,
                    'resultado' => $value,
                ]);
                $summary['resultados']['inserted']++;
            }
        });

        return $summary;
    }

    public function persistedAntecedents($preAnesthetic)
    {
        if (! $preAnesthetic || ! $preAnesthetic->id || ! Schema::hasTable('rc_antecedentes')) {
            return collect();
        }

        return DB::table('rc_antecedentes')
            ->where('id_rc_pre_anestesica', $preAnesthetic->id)
            ->where('estado', 1)
            ->orderBy('id')
            ->get()
            ->map(function ($row) {
                $row->origen = 'Historia clínica';

                return $row;
            });
    }

    public function persistedIndications($preAnesthetic)
    {
        if (! $preAnesthetic || ! $preAnesthetic->id || ! Schema::hasTable('rc_indicaciones')) {
            return collect();
        }

        return DB::table('rc_indicaciones')
            ->where('id_rc_pre_anestesica', $preAnesthetic->id)
            ->where('estado', 1)
            ->orderBy('id')
            ->get()
            ->map(function ($row) {
                $row->origen = 'Historia médica';

                return $row;
            });
    }

    public function persistedExamResults($preAnesthetic)
    {
        if (! $preAnesthetic || ! $preAnesthetic->id || ! Schema::hasTable('rc_resultados')) {
            return collect();
        }

        return DB::table('rc_resultados')
            ->where('id_rc_pre_anestesia', $preAnesthetic->id)
            ->where('estado', 1)
            ->orderBy('id')
            ->get()
            ->map(function ($row) {
                $row->origen = self::blank(self::value($row, 'id_orden')) ? 'Historia médica' : 'Laboratorio';

                return $row;
            });
    }

    /**
     * Read-only clinical context shown above the editable assessment.
     */
    public function contextRows($preAnesthetic)
    {
        if (! $preAnesthetic || ! $preAnesthetic->anestesiologia || ! $preAnesthetic->anestesiologia->historia) {
            return [];
        }

        $history = $preAnesthetic->anestesiologia->historia;
        $patient = $history->paciente;
        if (! $patient) {
            return [];
        }

        $antecedents = Form008PatientAntecedents::build($patient, $patient->ho_datos_paciente, $patient->a_alergias);
        $rows = Form008PatientAntecedents::rows($antecedents);
        foreach ($rows as &$row) {
            $row['source'] = 'Historia clínica / F.008';
        }
        unset($row);

        $legacy = $this->legacyPatientAntecedents($patient->id);
        $labels = [1 => 'Familiares', 2 => 'Patológicos', 3 => 'Quirúrgicos'];
        foreach ($labels as $type => $label) {
            $description = isset($legacy[$type]) ? $legacy[$type] : '';
            if (! self::blank($description) && ! $this->rowAlreadyPresent($rows, $description)) {
                $rows[] = [
                    'number' => 'HC-'.$type,
                    'label' => $label,
                    'description' => $description,
                    'source' => 'Antecedentes del paciente',
                ];
            }
        }

        $draft = $this->draftForHistory($history, $this->form008ForHistory($history));
        $payload = $draft ? (array) $draft->payload : [];
        $draftRows = [
            'antecedentes_personales' => 'Patológicos personales',
            'antecedentes_familiares' => 'Familiares',
        ];
        foreach ($draftRows as $key => $label) {
            $description = isset($payload[$key]) ? self::clean($payload[$key]) : '';
            if ($description !== '' && ! $this->rowAlreadyPresent($rows, $description)) {
                $rows[] = [
                    'number' => '018',
                    'label' => $label,
                    'description' => $description,
                    'source' => 'Formulario 018',
                ];
            }
        }

        return $rows;
    }

    /** Medical indications available in the clinical episode. */
    public function readOnlyIndications($preAnesthetic)
    {
        $rows = collect();
        $history = $this->historyFor($preAnesthetic);
        if (! $history) {
            return $rows;
        }

        $draft = $this->draftForHistory($history, $this->form008ForHistory($history));
        $payload = $draft ? (array) $draft->payload : [];
        foreach ($this->textLines(self::payload($payload, ['indicaciones'])) as $line) {
            $rows->push((object) ['indicaciones' => $line, 'origen' => 'Formulario 018']);
        }

        if (Schema::hasTable('hc_evolucion') && Schema::hasTable('hc_evolucion_indicacion')) {
            $procedureId = self::value($preAnesthetic->anestesiologia, 'id_hc_procedimientos');
            $query = DB::table('hc_evolucion_indicacion as indication')
                ->join('hc_evolucion as evolution', 'evolution.id', '=', 'indication.id_evolucion')
                ->whereNotNull('indication.descripcion');
            if (! self::blank($procedureId)) {
                $query->where('evolution.hc_id_procedimiento', $procedureId);
            } else {
                $query->where('evolution.hcid', $history->hcid);
            }
            foreach ($query->orderBy('indication.secuencia')->orderBy('indication.id')->pluck('indication.descripcion') as $description) {
                foreach ($this->textLines($description) as $line) {
                    $rows->push((object) ['indicaciones' => $line, 'origen' => 'Evolución médica']);
                }
            }
        }

        return $rows->filter(function ($row) {
            return trim((string) $row->indicaciones) !== '';
        })->unique(function ($row) {
            return self::normalized($row->indicaciones);
        })->values();
    }

    /** Results available in medical Form 018 and the laboratory episode. */
    public function readOnlyExamResults($preAnesthetic)
    {
        $rows = collect();
        $history = $this->historyFor($preAnesthetic);
        if (! $history) {
            return $rows;
        }

        $draft = $this->draftForHistory($history, $this->form008ForHistory($history));
        $payload = $draft ? (array) $draft->payload : [];
        foreach ($this->textLines(self::payload($payload, ['resultados'])) as $line) {
            $parts = preg_split('/\s*[:=]\s*/u', $line, 2);
            $name = count($parts) === 2 ? $parts[0] : 'Resultado médico';
            $rows->push((object) [
                'id_examen' => $this->resolveExamId($name),
                'id_parametro' => null,
                'id_orden' => null,
                'id_agrupador_publico' => null,
                'nombre' => $name,
                'resultado' => count($parts) === 2 ? $parts[1] : $line,
                'origen' => 'Formulario 018',
            ]);
        }

        if ($this->hasTables(['examen_orden', 'examen_detalle', 'examen', 'examen_resultado', 'examen_parametro'])) {
            $orderQuery = DB::table('examen_orden')->whereIn('estado', [-1, 1]);
            if (! self::blank(self::value($history, 'hcid'))) {
                $orderQuery->where('hcid', $history->hcid);
            } elseif (! self::blank(self::value($history, 'id_agenda'))) {
                $orderQuery->where('id_agenda', $history->id_agenda);
            } else {
                $orderQuery->whereRaw('1 = 0');
            }
            $orderIds = $orderQuery->pluck('id');

            if ($orderIds->isNotEmpty()) {
                $details = DB::table('examen_detalle as detail')
                    ->join('examen as exam', 'exam.id', '=', 'detail.id_examen')
                    ->whereIn('detail.id_examen_orden', $orderIds)
                    ->where('detail.estado', 1)
                    ->select('detail.id_examen_orden as order_id', 'detail.id_examen as exam_id',
                        'exam.nombre', 'exam.id_agrupador as public_group_id')
                    ->get();
                $results = DB::table('examen_resultado as result')
                    ->join('examen_parametro as parameter', 'parameter.id', '=', 'result.id_parametro')
                    ->whereIn('result.id_orden', $orderIds)
                    ->select('result.id_orden as order_id', 'parameter.id_examen as exam_id', 'parameter.nombre as parameter_name',
                        'result.id_parametro as parameter_id', 'result.valor', 'parameter.unidad1', 'result.certificado')
                    ->orderBy('parameter.orden')->orderBy('result.id')->get()
                    ->groupBy(function ($result) {
                        return $result->order_id.'|'.$result->exam_id;
                    });

                foreach ($details as $detail) {
                    $key = $detail->order_id.'|'.$detail->exam_id;
                    $examResults = $results->get($key, collect());
                    if ($examResults->isEmpty()) {
                        $rows->push((object) [
                            'id_examen' => $detail->exam_id,
                            'id_parametro' => null,
                            'id_orden' => $detail->order_id,
                            'id_agrupador_publico' => $detail->public_group_id,
                            'nombre' => $detail->nombre,
                            'resultado' => 'Pendiente',
                            'origen' => 'Laboratorio',
                        ]);

                        continue;
                    }
                    foreach ($examResults as $result) {
                        $value = self::clean($result->valor);
                        $unit = self::clean($result->unidad1);
                        $rows->push((object) [
                            'id_examen' => $detail->exam_id,
                            'id_parametro' => $result->parameter_id,
                            'id_orden' => $result->order_id,
                            'id_agrupador_publico' => $detail->public_group_id,
                            'nombre' => trim($detail->nombre.' · '.$result->parameter_name, ' ·'),
                            'resultado' => trim($value.($unit !== '' ? ' '.$unit : '')),
                            'origen' => (string) $result->certificado === '1' ? 'Laboratorio certificado' : 'Laboratorio',
                        ]);
                    }
                }
            }
        }

        return $rows->filter(function ($row) {
            return trim((string) $row->nombre) !== '' || trim((string) $row->resultado) !== '';
        })->unique(function ($row) {
            return self::normalized($row->nombre.'|'.$row->resultado);
        })->values();
    }

    public function valuesFromDraft(array $payload)
    {
        $values = [
            'anestesico' => self::payload($payload, ['antecedentes_anestesicos']),
            'quirurgicos' => self::payload($payload, ['antecedentes_quirurgicos']),
            'alergicos' => self::payload($payload, ['alergias']),
            'transfuciones' => self::payload($payload, ['transfusiones']),
            'habitos' => self::payload($payload, ['habitos']),
            'riesgo_quirurgico' => self::choice(self::payload($payload, ['riesgo_quirurgico']), ['BAJO', 'MODERADO', 'ALTO']),
            'estado_fisico' => self::romanChoice(self::payload($payload, ['asa']), ['I', 'II', 'III', 'IV', 'V', 'VI']),
            'plan_anestesico' => self::payload($payload, ['plan_anestesico', 'plan']),
            'observaciones' => self::payload($payload, ['observaciones']),
        ];

        $priorityText = self::payload($payload, ['prioridad']);
        $priority = self::choice($priorityText, ['ELECTIVA', 'EMERGENCIA', 'URGENCIA']);
        if ($priority === '' && strpos(self::normalized($priorityText), 'URGENT') !== false) {
            $priority = 'URGENCIA';
        }
        if ($priority === '' && strpos(self::normalized($priorityText), 'EMERGENT') !== false) {
            $priority = 'EMERGENCIA';
        }
        if ($priority === '' && strpos(self::normalized($priorityText), 'PROGRAMAD') !== false) {
            $priority = 'ELECTIVA';
        }
        if ($priority !== '') {
            $values['tipo'] = $priority;
        }

        $values = array_merge($values, self::physicalExamFromText(self::payloadStructured($payload, ['examen_fisico', 'evaluacion'])));
        $values = array_merge($values, self::airwayFromText(self::payloadStructured($payload, ['via_aerea'])));
        $values = array_merge($values, self::vitalsFromText(self::payloadStructured($payload, ['signos_vitales'])));

        $riskText = self::payloadStructured($payload, ['riesgos', 'riesgo']);
        $values['riesgo_cardiaco'] = self::labeledValue($riskText, ['riesgo cardiaco', 'cardiaco', 'cardiovascular']);
        $values['riesgo_pulmonar'] = self::labeledValue($riskText, ['riesgo pulmonar', 'pulmonar', 'respiratorio']);
        $values['riesgo_tromboembolico'] = self::labeledValue($riskText, ['riesgo tromboembolico', 'tromboembolico', 'trombosis']);

        $fastingText = self::payloadStructured($payload, ['ayuno']);
        $values['liquidos_claros'] = self::labeledValue($fastingText, ['liquidos claros', 'liquidos']);
        $values['leche_materna'] = self::labeledValue($fastingText, ['leche materna']);
        $values['solidos'] = self::labeledValue($fastingText, ['solidos']);

        return array_filter($values, function ($value) {
            return ! self::blank($value);
        });
    }

    public static function physicalExamFromText($text)
    {
        $extremities = self::combine([
            self::labeledValue($text, ['extremidades']),
            self::labeledValue($text, ['miembros superiores']),
            self::labeledValue($text, ['miembros inferiores']),
            self::labeledValue($text, ['miembros']),
        ]);

        return array_filter([
            'torax' => self::labeledValue($text, ['torax']),
            'corazon' => self::labeledValue($text, ['corazon', 'cardiovascular']),
            'pulmones' => self::labeledValue($text, ['pulmones', 'pulmonar', 'respiratorio']),
            'abdomen' => self::labeledValue($text, ['abdomen']),
            'extremidades' => $extremities,
            'sistema_nervisoso' => self::labeledValue($text, ['sistema nervioso central', 'sistema nervioso', 'neurologico']),
            'equivalente_metabolico' => self::labeledValue($text, ['equivalente metabolico', 'mets', 'met']),
        ], function ($value) {
            return ! self::blank($value);
        });
    }

    public static function vitalsFromText($text)
    {
        return array_filter([
            'presion' => self::labeledValue($text, ['presion arterial', 'tension arterial', 'ta']),
            'pulso' => self::numeric(self::labeledValue($text, ['frecuencia cardiaca', 'pulso', 'fc'])),
            'fecuencia_respiratoria' => self::numeric(self::labeledValue($text, ['frecuencia respiratoria', 'fr'])),
            'temperatura' => self::numeric(self::labeledValue($text, ['temperatura'])),
            'o2' => self::labeledValue($text, ['saturacion de oxigeno', 'sato2', 'spo2']),
        ], function ($value) {
            return self::meaningful($value);
        });
    }

    public static function airwayFromText($text)
    {
        $mallampati = self::romanChoice(self::labeledValue($text, ['mallampati']), ['I', 'II', 'III', 'IV']);
        $history = self::choice(self::labeledValue($text, ['historia de intubacion dificil', 'intubacion dificil']), ['SI', 'NO']);
        $pathology = self::choice(self::labeledValue($text, ['patologia asociada a intubacion dificil', 'patologia asociada']), ['SI', 'NO']);

        return array_filter([
            'apertura_bucal' => self::comparison(self::labeledValue($text, ['apertura bucal']), ['<2', '2-2,5', '2.6-3', '>3']),
            'distancia_tiromentoneana' => self::comparison(self::labeledValue($text, ['distancia tiromentoniana', 'distancia tiromentoneana']), ['<6', '6 - 6.5', '>6.5']),
            'mallanpati' => $mallampati,
            'protusion_mandibular' => self::comparison(self::labeledValue($text, ['protrusion mandibular']), ['<0', '0', '>0']),
            'perimetro_cervical' => self::comparison(self::labeledValue($text, ['perimetro cervical']), ['<40', '>40']),
            'movilidad_cervical' => self::comparison(self::labeledValue($text, ['movilidad cervical']), ['<35', '>35']),
            'intubacion_dificil' => $history,
            'patologia' => $pathology,
        ], function ($value) {
            return ! self::blank($value);
        });
    }

    public static function labeledValue($text, array $aliases)
    {
        if (! is_scalar($text) || self::clean($text) === '') {
            return '';
        }

        $lines = preg_split('/[\r\n;|]+|\s+\/\s+/u', trim((string) $text));
        foreach ($lines as $line) {
            $parts = preg_split('/\s*[:=]\s*/u', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }
            $label = self::normalized($parts[0]);
            foreach ($aliases as $alias) {
                $needle = self::normalized($alias);
                if ($label === $needle || strpos($label, $needle) !== false) {
                    return self::clean($parts[1]);
                }
            }
        }

        return '';
    }

    private function form008ForHistory($history)
    {
        if (! Schema::hasTable('ho_form008')) {
            return null;
        }

        $query = Ho_Form008::query();
        if (Schema::hasColumn('ho_form008', 'encounter_id') && ! self::blank(self::value($history, 'encounter_id'))) {
            $query->where('encounter_id', $history->encounter_id);
        } elseif (! self::blank(self::value($history, 'id_agenda'))) {
            $query->where('id_agenda', $history->id_agenda);
        } else {
            return null;
        }

        return $query->orderByDesc('id')->first();
    }

    private function draftForHistory($history, $form008)
    {
        if (! Schema::hasTable('hospital_episode_form_drafts')) {
            return null;
        }

        $query = HospitalEpisodeFormDraft::where('form_code', '018');
        if (Schema::hasColumn('hospital_episode_form_drafts', 'encounter_id')
            && ! self::blank(self::value($history, 'encounter_id'))) {
            $query->where('encounter_id', $history->encounter_id);
        } elseif ($form008 && ! self::blank(self::value($form008, 'id_solicitud'))) {
            $query->where('solicitud_id', $form008->id_solicitud);
        } else {
            return null;
        }

        return $query->orderByDesc('updated_at')->orderByDesc('id')->first();
    }

    private function physicalExamValues($form008)
    {
        if (! Schema::hasTable('ho_form008_examen_fisico') || ! Schema::hasTable('ho_examen_fisico')) {
            return [];
        }

        $rows = DB::table('ho_form008_examen_fisico as detail')
            ->join('ho_examen_fisico as catalog', 'catalog.id', '=', 'detail.id_ho_examen_fisico')
            ->where('detail.id_ho_form008', $form008->id)
            ->where('detail.estado', 1)
            ->select('catalog.nombre', 'catalog.trans', 'detail.detalle')
            ->get();

        $mapped = [];
        foreach ($rows as $row) {
            $detail = self::clean(self::value($row, 'detalle'));
            if ($detail === '') {
                continue;
            }
            $label = self::normalized(self::value($row, 'nombre').' '.self::value($row, 'trans'));
            $field = '';
            if (strpos($label, 'TORAX') !== false) {
                $field = 'torax';
            } elseif (strpos($label, 'CORAZON') !== false || strpos($label, 'CARDIOVASC') !== false) {
                $field = 'corazon';
            } elseif (strpos($label, 'PULMON') !== false || strpos($label, 'RESPIRAT') !== false) {
                $field = 'pulmones';
            } elseif (strpos($label, 'ABDOMEN') !== false) {
                $field = 'abdomen';
            } elseif (strpos($label, 'EXTREM') !== false || strpos($label, 'MIEMBRO') !== false) {
                $field = 'extremidades';
            } elseif (strpos($label, 'NERVIOS') !== false || strpos($label, 'NEUROLOG') !== false) {
                $field = 'sistema_nervisoso';
            }

            if ($field !== '' && ! isset($mapped[$field])) {
                $mapped[$field] = $detail;
            }
        }

        $fromObservation = self::physicalExamFromText(self::value($form008, 'examen_fisico_obs'));
        foreach ($fromObservation as $field => $value) {
            if (! isset($mapped[$field])) {
                $mapped[$field] = $value;
            }
        }

        return $mapped;
    }

    private function evolutionPhysicalExamValues($history)
    {
        if (! Schema::hasTable('hc_evolucion') || ! Schema::hasTable('hc_child_pugh')) {
            return [];
        }

        $text = DB::table('hc_evolucion as evolution')
            ->join('hc_child_pugh as exam', 'exam.id_hc_evolucion', '=', 'evolution.id')
            ->where('evolution.hcid', $history->hcid)
            ->whereNotNull('exam.examen_fisico')
            ->orderByDesc('evolution.id')
            ->value('exam.examen_fisico');

        return self::physicalExamFromText($text);
    }

    private function vitalValues($history, $form008)
    {
        $vitals = [];
        if ($form008 && ! self::blank(self::value($form008, 'id_solicitud')) && Schema::hasTable('ho_solicitud')) {
            $solicitud = Ho_Solicitud::find($form008->id_solicitud);
            if ($solicitud) {
                $current = app(ClinicalEmergencyVitalSignsService::class)->values($solicitud, $form008, $history);
                $vitals = [
                    'presion' => isset($current['pressure']) ? $current['pressure'] : null,
                    'pulso' => isset($current['pulse']) ? self::numeric($current['pulse']) : null,
                    'fecuencia_respiratoria' => isset($current['respiratory']) ? self::numeric($current['respiratory']) : null,
                    'temperatura' => isset($current['temperature']) ? self::numeric($current['temperature']) : null,
                    'o2' => isset($current['oxygen']) ? $current['oxygen'] : null,
                ];
            }
        }

        $fallbacks = [
            'presion' => self::value($history, 'presion'),
            'pulso' => self::numeric(self::value($history, 'pulso')),
            'fecuencia_respiratoria' => self::numeric(self::value($history, 'frecuencia_respiratoria')),
            'temperatura' => self::numeric(self::value($history, 'temperatura')),
            'o2' => self::value($history, 'o2'),
        ];
        foreach ($fallbacks as $field => $value) {
            if (! isset($vitals[$field]) || ! self::meaningful($vitals[$field])) {
                $vitals[$field] = $value;
            }
        }

        return array_filter($vitals, function ($value) {
            return self::meaningful($value);
        });
    }

    private function legacyPatientAntecedents($patientId)
    {
        if (! Schema::hasTable('paciente_antecedente')) {
            return [];
        }

        $rows = DB::table('paciente_antecedente')
            ->where('id_paciente', $patientId)
            ->where('estado', 1)
            ->orderBy('id')
            ->get(['tipo', 'antecedente', 'otros']);
        $grouped = [];
        foreach ($rows as $row) {
            $type = (int) self::value($row, 'tipo');
            if (! in_array($type, [1, 2, 3], true)) {
                continue;
            }
            $grouped[$type] = self::combine([
                isset($grouped[$type]) ? $grouped[$type] : '',
                self::value($row, 'antecedente'),
                self::value($row, 'otros'),
            ]);
        }

        return $grouped;
    }

    private function historyFor($preAnesthetic)
    {
        return $preAnesthetic && $preAnesthetic->anestesiologia
            ? $preAnesthetic->anestesiologia->historia
            : null;
    }

    private function textLines($text)
    {
        if (self::blank($text)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/[\r\n;|]+/u', (string) $text)), function ($line) {
            return $line !== '';
        }));
    }

    private function hasTables(array $tables)
    {
        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    private function resolveExamId($name)
    {
        if (! Schema::hasTable('examen') || self::blank($name) || self::normalized($name) === 'RESULTADO MEDICO') {
            return null;
        }

        $exact = DB::table('examen')->where('nombre', self::clean($name))->value('id');
        if ($exact) {
            return $exact;
        }

        $needle = self::normalized($name);
        $match = DB::table('examen')->whereNotNull('nombre')->get(['id', 'nombre'])->first(function ($exam) use ($needle) {
            return self::normalized($exam->nombre) === $needle;
        });

        return $match ? $match->id : null;
    }

    private function auditValues($userId, $ip)
    {
        $values = [
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if (! self::blank($userId)) {
            $values['id_usuariocrea'] = $userId;
            $values['id_usuariomod'] = $userId;
        }
        if (! self::blank($ip)) {
            $values['ip_creacion'] = $ip;
            $values['ip_modificacion'] = $ip;
        }

        return $values;
    }

    private function offer(array &$values, array &$sources, $field, $value, $source)
    {
        $value = self::clean($value);
        if ($value !== '' && (! isset($values[$field]) || self::blank($values[$field]))) {
            $values[$field] = $value;
            $sources[$field] = $source;
        }
    }

    private function mergeOffers(array &$values, array &$sources, array $offers, $source)
    {
        foreach ($offers as $field => $value) {
            $this->offer($values, $sources, $field, $value, $source);
        }
    }

    private function rowAlreadyPresent(array $rows, $description)
    {
        $needle = self::normalized($description);
        foreach ($rows as $row) {
            if (self::normalized(isset($row['description']) ? $row['description'] : '') === $needle) {
                return true;
            }
        }

        return false;
    }

    private static function payload(array $payload, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($payload[$key]) && ! self::blank($payload[$key])) {
                return self::clean($payload[$key]);
            }
        }

        return '';
    }

    private static function payloadStructured(array $payload, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($payload[$key]) && is_scalar($payload[$key]) && self::clean($payload[$key]) !== '') {
                return trim((string) $payload[$key]);
            }
        }

        return '';
    }

    private static function choice($value, array $options)
    {
        $normalized = self::normalized($value);
        foreach ($options as $option) {
            if ($normalized === self::normalized($option) || preg_match('/\b'.preg_quote(self::normalized($option), '/').'\b/u', $normalized)) {
                return $option;
            }
        }

        return '';
    }

    private static function romanChoice($value, array $options)
    {
        $normalized = self::normalized($value);
        foreach (array_reverse($options) as $option) {
            if (preg_match('/\b'.preg_quote($option, '/').'\b/', $normalized)) {
                return $option;
            }
        }

        return '';
    }

    private static function comparison($value, array $options)
    {
        $normalized = str_replace([',', ' ', 'CM', '°'], ['.', '', '', ''], strtoupper(self::clean($value)));
        foreach ($options as $option) {
            $candidate = str_replace([',', ' ', 'CM', '°'], ['.', '', '', ''], strtoupper($option));
            if ($normalized === $candidate) {
                return $option;
            }
        }

        return '';
    }

    private static function combine(array $values)
    {
        $result = [];
        foreach ($values as $value) {
            $value = self::clean($value);
            if ($value === '') {
                continue;
            }
            $key = self::normalized($value);
            if (! isset($result[$key])) {
                $result[$key] = $value;
            }
        }

        return implode('; ', array_values($result));
    }

    private static function clean($value)
    {
        if (! is_scalar($value)) {
            return '';
        }
        $value = trim((string) preg_replace('/\s+/u', ' ', (string) $value));
        if (in_array(self::normalized($value), ['', '...', 'NO REGISTRADO', 'SIN REGISTRO', 'PENDIENTE DE REGISTRAR'], true)) {
            return '';
        }

        return $value;
    }

    private static function blank($value)
    {
        return self::clean($value) === '';
    }

    private static function meaningful($value)
    {
        if (self::blank($value)) {
            return false;
        }

        return ! preg_match('/^[\s0\.\,\/\-]+$/', trim((string) $value));
    }

    private static function numeric($value)
    {
        if (! is_scalar($value) || ! preg_match('/-?\d+(?:[\.,]\d+)?/', (string) $value, $match)) {
            return null;
        }

        return str_replace(',', '.', $match[0]);
    }

    private static function normalized($value)
    {
        $value = strtr(trim((string) $value), [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ñ' => 'N',
        ]);
        $value = strtoupper($value);

        return trim((string) preg_replace('/\s+/u', ' ', $value));
    }

    private static function value($source, $property)
    {
        if (is_array($source)) {
            return isset($source[$property]) ? $source[$property] : null;
        }
        if (is_object($source)) {
            return isset($source->{$property}) ? $source->{$property} : null;
        }

        return null;
    }
}
