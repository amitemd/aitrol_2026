@php
    $classificationLevels = \Sis_medico\Services\ClinicalEmergencyClassification::levels();
    $classificationComponents = \Sis_medico\Services\ClinicalEmergencyClassification::components();
    $componentNames = $classificationLegacy ? ['eye_response'=>'resp_ocular', 'verbal_response'=>'resp_verbal', 'motor_response'=>'resp_motora'] : [];
    $classificationValues = $classificationValues ?? [];
@endphp
<div class="ec-assessment" data-emergency-classification @if(!empty($classificationDemo)) data-gcs-demo="1" @endif>
    <input type="hidden" name="classification_method" value="CLINICIAN_MANCHESTER_V1">
    <section class="ec-section" aria-labelledby="{{ $classificationPrefix }}-glasgow-heading">
        <div class="ec-heading"><div><span class="ec-eyebrow">01 / VALORACIÓN NEUROLÓGICA</span><h3 id="{{ $classificationPrefix }}-glasgow-heading">Escala de Glasgow</h3></div><output class="ec-score" data-gcs-total aria-live="polite">Sin evaluar</output></div>
        <p class="ec-help">Seleccione la mejor respuesta observada. Use la escala pediátrica adaptada cuando corresponda; estos descriptores son de la escala estándar.</p>
        <div class="ec-components">
            @foreach($classificationComponents as $key => $component)
            <div><label for="{{ $classificationPrefix }}-{{ $key }}">{{ $component['label'] }}</label>
                <select class="form-control" id="{{ $classificationPrefix }}-{{ $key }}" name="{{ $componentNames[$key] ?? $key }}" data-gcs-component="{{ $key }}" data-gcs-code="{{ $component['code'] }}" data-gcs-max="{{ $component['max'] }}">
                    <option value="">Sin evaluar</option>
                    @foreach($component['options'] as $score => $description)<option value="{{ $score }}" @if((string)($classificationValues[$key] ?? '') === (string)$score) selected @endif>{{ $score }} · {{ $description }}</option>@endforeach
                    <option value="NT" @if(($classificationValues[$key] ?? '') === 'NT') selected @endif>NT · No evaluable</option>
                </select>
            </div>
            @endforeach
        </div>
        <div class="ec-gcs-detail" data-gcs-detail aria-live="polite">E — · V — · M —</div>
        <div class="ec-gcs-ranges" role="group" aria-label="Rangos del puntaje Glasgow, selección automática">
            @foreach(['3-8'=>'3–8 puntos', '9-12'=>'9–12 puntos', '13-15'=>'13–15 puntos'] as $range=>$label)<span data-gcs-range="{{ $range }}">{{ $label }}</span>@endforeach
        </div>
        <p class="ec-gcs-detail" data-gcs-range-label aria-live="polite">Rango pendiente: complete las tres respuestas evaluables.</p>
        <p class="ec-help">El rango de Glasgow se destaca automáticamente. La siguiente tabla propone un color editable; no sustituye los discriminadores Manchester.</p>
        @if(empty($classificationDemo))<p class="ec-rule-note"><strong>Regla de sugerencia solicitada:</strong> Glasgow 3–8 → rojo · 9–12 → naranja · 13–14 → amarillo · 15 → verde. Azul queda para selección profesional. Con un componente vacío o NT no se calcula el total ni se genera una nueva sugerencia.</p>@endif
        <p class="ec-demo-note" data-gcs-demo-note hidden><strong>Simulación con datos ficticios:</strong> al completar Glasgow se preselecciona un color de ejemplo (3–8 rojo, 9–12 naranja, 13–14 amarillo, 15 verde). Esta regla ilustrativa no es una clasificación Manchester y no guarda pacientes.</p>
        <div data-gcs-note-wrap hidden><label for="{{ $classificationPrefix }}-glasgow-note">Motivo de respuesta no evaluable *</label><input class="form-control" id="{{ $classificationPrefix }}-glasgow-note" name="glasgow_note" value="{{ $classificationValues['glasgow_note'] ?? '' }}" data-gcs-note minlength="3" maxlength="500" placeholder="Ej.: intubación, sedación o impedimento para evaluar"></div>
    </section>
    <section class="ec-section" aria-labelledby="{{ $classificationPrefix }}-manchester-heading">
        <div class="ec-heading"><div><span class="ec-eyebrow">02 / PRIORIDAD DE ATENCIÓN</span><h3 id="{{ $classificationPrefix }}-manchester-heading">{{ !empty($classificationDemo) ? 'Color de ejemplo' : 'Clasificación Manchester' }}</h3></div></div>
        <p class="ec-help">{{ !empty($classificationDemo) ? 'Pruebe las respuestas de Glasgow para cambiar la selección de ejemplo. El diagrama y discriminador se usan en el triaje clínico.' : 'Al completar Glasgow se marca el color sugerido por la tabla local. Revise el diagrama, el discriminador y al paciente; cambie el nivel si corresponde antes de confirmar.' }}</p>
        <div class="ec-criteria">
            <div><label for="{{ $classificationPrefix }}-flowchart">Diagrama de presentación *</label><input class="form-control" id="{{ $classificationPrefix }}-flowchart" name="manchester_flowchart" value="{{ $classificationValues['manchester_flowchart'] ?? '' }}" required minlength="3" maxlength="191" placeholder="Nombre del diagrama del protocolo"></div>
            <div><label for="{{ $classificationPrefix }}-discriminator">Discriminador determinante *</label><input class="form-control" id="{{ $classificationPrefix }}-discriminator" name="discriminator" value="{{ $classificationValues['discriminator'] ?? '' }}" required minlength="3" maxlength="191" placeholder="Hallazgo que determina la prioridad"></div>
        </div>
        <fieldset class="ec-priorities"><legend>{{ !empty($classificationDemo) ? 'Color simulado' : 'Nivel sugerido, editable por el profesional *' }}</legend>
            @foreach($classificationPriorities as $priority)
            @php($definition = $classificationLevels[(int) $priority->prioridad] ?? null)
            @if($definition)
            <label class="ec-priority ec-level-{{ (int) $priority->prioridad }}">
                <input type="radio" name="{{ $classificationLegacy ? 'prioridad' : 'priority_id' }}" value="{{ $priority->id }}" @if((string)($classificationValues['priority_id'] ?? '') === (string)$priority->id) checked @endif required data-clinical-priority data-level="{{ (int) $priority->prioridad }}" data-label="{{ $definition['name'] }}" data-time="{{ $definition['minutes'] }}">
                <span><strong>{{ $definition['name'] }}</strong><small>{{ $definition['minutes'] ? 'Objetivo: '.$definition['minutes'].' min' : 'Atención inmediata' }}</small></span>
            </label>
            @endif
            @endforeach
        </fieldset>
        <div class="ec-result" data-classification-result aria-live="polite"><strong data-classification-label>Pendiente de clasificar</strong><span data-classification-time>Seleccione un nivel después de valorar al paciente.</span></div>
        <label class="ec-confirm" for="{{ $classificationPrefix }}-confirm"><input id="{{ $classificationPrefix }}-confirm" type="checkbox" name="classification_confirmed" value="1" data-classification-confirm required><span>Confirmo la prioridad tras revisar al paciente y los discriminadores del protocolo.</span></label>
    </section>
</div>
