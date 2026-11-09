@php
    $anesthesiaSections = [
        '018' => [
            'B. REGISTRO DE VALORACIÓN PREANESTÉSICA' => ['diagnosticos', 'procedimientos', 'prioridad', 'riesgo_quirurgico'],
            'C. ANAMNESIS' => ['antecedentes_personales', 'antecedentes_anestesicos', 'antecedentes_quirurgicos', 'alergias', 'transfusiones', 'habitos', 'antecedentes_familiares'],
            'D. EXAMEN FÍSICO' => ['signos_vitales', 'via_aerea', 'examen_fisico'],
            'E. RESULTADOS DE LABORATORIO, IMAGEN Y GABINETE' => ['resultados'],
            'F. ESCALAS E ÍNDICES' => ['asa', 'riesgos'],
            'G. AYUNO / H. INDICACIONES / I. PLAN ANESTÉSICO' => ['ayuno', 'indicaciones', 'plan_anestesico', 'observaciones', 'profesional'],
        ],
        '018A' => [
            'A. DATOS DEL USUARIO Y MEDIDAS' => ['medidas'],
            'B. SERVICIO Y PRIORIDAD' => ['diagnostico_pre', 'diagnostico_post', 'cirugia_propuesta', 'cirugia_realizada', 'servicio_prioridad', 'equipo_quirurgico'],
            'D. REGISTRO TRANSANESTÉSICO' => ['monitorizacion'],
            'E. DROGAS ADMINISTRADAS / F. TÉCNICA ANESTÉSICA' => ['drogas', 'tecnica_anestesica', 'via_aerea'],
            'G. ACCESOS / H. REPOSICIÓN DE VOLUMEN / I. PÉRDIDAS' => ['accesos', 'liquidos', 'perdidas'],
            'K. TIEMPOS / L. TÉCNICAS ESPECIALES / M. TEMPERATURA' => ['tiempos', 'tecnicas_especiales', 'temperatura'],
            'N. INCIDENTES / O. LABORATORIO / P. OBSERVACIONES' => ['incidentes', 'laboratorio', 'observaciones'],
            'R. EGRESO Y PROFESIONAL RESPONSABLE' => ['egreso', 'profesional'],
        ],
        '019' => [
            'B. PROCEDIMIENTO QUIRÚRGICO ANESTÉSICO' => ['especialidad', 'diagnostico', 'cirugia', 'tecnica_anestesica'],
            'C. ENTREGA - RECEPCIÓN DEL PACIENTE' => ['recepcion', 'escalas_ingreso'],
            'D. MONITORIZACIÓN' => ['monitorizacion'],
            'E. INGRESOS / F. ELIMINACIÓN' => ['ingresos', 'eliminacion'],
            'G. EVOLUCIÓN POSTANESTÉSICA' => ['evolucion', 'prescripcion', 'farmacos'],
            'H. CONDICIÓN DE EGRESO' => ['signos_egreso', 'escalas_egreso', 'destino'],
            'I. PROFESIONAL QUE AUTORIZA EL EGRESO' => ['profesional'],
        ],
    ];
    $shortAnesthesiaFields = ['prioridad', 'riesgo_quirurgico', 'asa', 'medidas', 'servicio_prioridad', 'tecnica_anestesica', 'tiempos', 'temperatura', 'especialidad', 'signos_egreso', 'escalas_egreso', 'profesional'];
    $legacyAnesthesiaFields = [
        '018' => ['examen_fisico' => 'evaluacion', 'riesgos' => 'riesgo', 'plan_anestesico' => 'plan'],
        '018A' => ['tecnica_anestesica' => 'tecnica', 'drogas' => 'medicamentos', 'monitorizacion' => 'monitorizacion', 'incidentes' => 'incidentes'],
        '019' => ['recepcion' => 'condicion', 'evolucion' => 'evolucion', 'prescripcion' => 'analgesia', 'destino' => 'destino'],
    ];
@endphp

<div class="form020-sheet">
    <p class="form-muted">El encabezado del PDF toma automáticamente la identidad, sexo, fecha de nacimiento y edad del paciente de este episodio. No escriba datos de otro paciente en los campos clínicos.</p>
    @foreach($anesthesiaSections[$form['code']] as $sectionTitle => $sectionFields)
        <div class="form020-band {{ $loop->even ? 'alt' : '' }}">{{ $sectionTitle }}</div>
        <div class="form020-grid">
            @foreach($sectionFields as $key)
                @php
                    $legacyKey = $legacyAnesthesiaFields[$form['code']][$key] ?? null;
                    $savedValue = $entry ? ($entry->payload[$key] ?? ($legacyKey ? ($entry->payload[$legacyKey] ?? '') : '')) : '';
                    $value = old('data.'.$key, $entry ? $savedValue : ($emergencySource['suggestions'][$key] ?? ''));
                    $isShort = in_array($key, $shortAnesthesiaFields, true);
                @endphp
                <div class="form-field {{ $isShort ? '' : 'span-2' }}">
                    <label for="field_{{ $key }}">{{ $fields[$key] }}</label>
                    @if($isShort)
                        <input id="field_{{ $key }}" type="text" name="data[{{ $key }}]" maxlength="10000" value="{{ $value }}" {{ $canWrite ? '' : 'disabled' }}>
                    @else
                        <textarea id="field_{{ $key }}" name="data[{{ $key }}]" maxlength="10000" rows="3" {{ $canWrite ? '' : 'disabled' }}>{{ $value }}</textarea>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
</div>
