@php
    $raw024 = [];
    foreach ($fields as $key024 => $label024) {
        $raw024[$key024] = old('data.'.$key024, $entry ? ($entry->payload[$key024] ?? '') : '');
    }
    if ($entry && isset($entry->payload['riesgos'])) $raw024['riesgos'] = $entry->payload['riesgos'];
    $document024 = \Sis_medico\Services\HospitalEpisodeForm024::forDisplay($raw024);
    $disabled024 = $canWrite ? '' : 'disabled';
    $savedProcedureCode024 = old('procedure_code', $entry ? ($entry->payload['_procedure_code'] ?? '') : '');
    $form024Images = $form024Images ?? [];
@endphp

<style>
    .form024-intro { padding: 12px 14px; margin-bottom: 16px; border: 1px solid #d8c4eb; border-left: 4px solid #8d56bc; border-radius: 7px; background: #faf5ff; color: #503b63; }
    .form024-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 13px; }
    .form024-grid .span-2 { grid-column: 1 / -1; }
    .form024-band { margin: 20px 0 12px; padding: 9px 11px; background: #b484d8; color: #1f1530; font-size: 13px; font-weight: 700; }
    .form024-help { display: block; margin-top: 5px; color: #6f7780; font-size: 12px; }
    .form024-images { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin: 10px 0; }
    .form024-image { overflow: hidden; padding: 8px; border: 1px solid #c9dce8; border-radius: 7px; background: #f7fbfd; text-align: center; }
    .form024-image img { display: block; width: 100%; height: 135px; margin-bottom: 6px; object-fit: contain; background: #fff; }
    .form024-image label { display: flex; justify-content: center; gap: 6px; align-items: center; font-weight: 400; }
    .form024-image input { width: auto; }
    @media (max-width: 720px) { .form024-grid { grid-template-columns: 1fr; } .form024-grid .span-2 { grid-column: auto; } }
</style>

<div id="form024-editor">
    <div class="form024-intro"><strong>Formulario 024 · Consentimiento informado.</strong><br>Seleccione el tipo de reporte y el procedimiento. Puede conservar el texto sugerido, ajustarlo al caso y adjuntar hasta tres imágenes explicativas antes de imprimir y firmar.</div>

    <div class="form024-band">A. DATOS DEL CONSENTIMIENTO</div>
    <div class="form024-grid">
        <div class="form-field span-2"><label for="field_tipo_consentimiento">Tipo de reporte / consentimiento</label><select id="field_tipo_consentimiento" name="data[tipo_consentimiento]" {{ $disabled024 }}>@foreach(\Sis_medico\Services\HospitalEpisodeForm024::types() as $value024 => $label024)<option value="{{ $value024 }}" {{ $document024['tipo_consentimiento'] === $value024 ? 'selected' : '' }}>{{ $label024 }}</option>@endforeach</select><small class="form024-help">Al cambiar el tipo se actualizan únicamente los textos que no hayan sido personalizados.</small></div>
        <div class="form-field"><label for="field_fecha">Fecha</label><input id="field_fecha" type="date" name="data[fecha]" value="{{ $document024['fecha'] }}" {{ $disabled024 }}></div>
        <div class="form-field"><label for="field_hora">Hora</label><input id="field_hora" type="time" name="data[hora]" value="{{ $document024['hora'] }}" {{ $disabled024 }}></div>
        <div class="form-field"><label for="field_tipo_atencion">Tipo de atención</label><select id="field_tipo_atencion" name="data[tipo_atencion]" {{ $disabled024 }}><option value=""></option>@foreach(['AMBULATORIA' => 'Ambulatoria', 'HOSPITALIZACION' => 'Hospitalización'] as $value024 => $label024)<option value="{{ $value024 }}" {{ $document024['tipo_atencion'] === $value024 ? 'selected' : '' }}>{{ $label024 }}</option>@endforeach</select></div>
        <div></div>
        <div class="form-field"><label for="field_diagnostico">Diagnóstico</label><input id="field_diagnostico" type="text" name="data[diagnostico]" value="{{ $document024['diagnostico'] }}" maxlength="10000" {{ $disabled024 }}></div>
        <div class="form-field"><label for="field_cie10">Código CIE-10</label><input id="field_cie10" type="text" name="data[cie10]" value="{{ $document024['cie10'] }}" maxlength="100" {{ $disabled024 }}></div>
    </div>

    <div class="form024-band">B. INFORMACIÓN SOBRE EL PROCEDIMIENTO</div>
    <div class="form024-grid">
        <div class="form-field span-2">
            <label for="field_procedure_code">Seleccionar procedimiento</label>
            <select id="field_procedure_code" class="js-remote-clinical-select" name="procedure_code" data-fill-target="#field_procedimiento" data-catalog-url="{{ route('hospital.surgery.catalog', ['type' => 'procedures', 'id_solicitud' => $solicitud->id]) }}" {{ $disabled024 }}>
                @if($savedProcedureCode024 !== '')<option value="{{ $savedProcedureCode024 }}" selected>{{ $document024['procedimiento'] ?: $savedProcedureCode024 }}</option>@endif
            </select>
            <small class="form024-help">Busque por código o nombre. El procedimiento seleccionado se guarda en este 024 y puede cambiarse después.</small>
        </div>
        @foreach([
            'procedimiento' => 'Procedimiento recomendado', 'consiste' => '¿En qué consiste?',
            'realiza' => '¿Cómo se realiza?', 'duracion_estimada' => 'Duración estimada',
            'beneficios' => 'Beneficios del procedimiento',
        ] as $key024 => $label024)
            <div class="form-field span-2"><label for="field_{{ $key024 }}">{{ $label024 }}</label>@if(in_array($key024, ['procedimiento', 'duracion_estimada'], true))<input id="field_{{ $key024 }}" type="text" name="data[{{ $key024 }}]" value="{{ $document024[$key024] }}" maxlength="10000" data-form024-preset="{{ $key024 }}" {{ $disabled024 }}>@else<textarea id="field_{{ $key024 }}" name="data[{{ $key024 }}]" maxlength="10000" data-form024-preset="{{ $key024 }}" {{ $disabled024 }}>{{ $document024[$key024] }}</textarea>@endif</div>
        @endforeach
    </div>

    <div class="form024-band">C. IMÁGENES EXPLICATIVAS</div>
    @if(count($form024Images))
        <div class="form024-images">
            @foreach($form024Images as $position024 => $image024)
                <div class="form024-image">
                    <img src="{{ route('hospital.documents.form024.image', ['id_solicitud' => $solicitud->id, 'entry' => $entry->id, 'position' => $position024]) }}" alt="Imagen explicativa {{ $position024 + 1 }}">
                    <small>{{ $image024['name'] ?? 'Imagen '.($position024 + 1) }}</small>
                    @if($canWrite)<label><input type="checkbox" name="procedure_image_remove[]" value="{{ $position024 }}"> Quitar al guardar</label>@endif
                </div>
            @endforeach
        </div>
    @endif
    @if($canWrite)
        <div class="form-field"><label for="field_procedure_images">Agregar imágenes</label><input id="field_procedure_images" type="file" name="procedure_images[]" accept="image/jpeg,image/png" multiple><small class="form024-help">Hasta tres imágenes JPG o PNG en total, máximo 4 MB cada una. En el PDF se distribuyen centradas y con el mismo espacio.</small></div>
    @endif

    <div class="form024-band">D. RIESGOS, ALTERNATIVAS Y CUIDADOS</div>
    @foreach([
        'riesgos_frecuentes' => 'Riesgos frecuentes (poco graves)',
        'riesgos_graves' => 'Riesgos poco frecuentes (graves)',
        'riesgos_especificos' => 'Riesgos específicos relacionados con este paciente',
        'alternativas' => 'Alternativas al procedimiento',
        'manejo_posterior' => 'Manejo posterior al procedimiento',
        'consecuencias' => 'Consecuencias posibles si no se realiza',
        'observaciones' => 'Observaciones de la conversación',
    ] as $key024 => $label024)
        <div class="form-field"><label for="field_{{ $key024 }}">{{ $label024 }}</label><textarea id="field_{{ $key024 }}" name="data[{{ $key024 }}]" maxlength="10000" @if(in_array($key024, ['riesgos_frecuentes', 'riesgos_graves', 'alternativas', 'manejo_posterior', 'consecuencias'], true)) data-form024-preset="{{ $key024 }}" @endif {{ $disabled024 }}>{{ $document024[$key024] }}</textarea></div>
    @endforeach
</div>

@if($canWrite)
<script>
(function () {
    var root = document.getElementById('form024-editor');
    var type = document.getElementById('field_tipo_consentimiento');
    if (!root || !type) return;
    var presets = @json(\Sis_medico\Services\HospitalEpisodeForm024::presets());
    var previous = type.value;
    type.addEventListener('change', function () {
        var next = type.value;
        root.querySelectorAll('[data-form024-preset]').forEach(function (field) {
            var key = field.getAttribute('data-form024-preset');
            var oldValue = presets[previous] && presets[previous][key] ? presets[previous][key] : '';
            var newValue = presets[next] && presets[next][key] ? presets[next][key] : '';
            if (!field.value.trim() || field.value.trim() === oldValue.trim()) field.value = newValue;
        });
        previous = next;
    });
})();
</script>
@endif
