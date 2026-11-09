@extends('layouts.hospital-embedded')
@section('title', $form['code'].' · '.$form['name'])
@section('styles')
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 20px; background: #f5faff; color: #27475f; font: 14px/1.5 Arial, sans-serif; }
        .form-page { max-width: 1000px; margin: 0 auto; }
        .form-header { padding: 20px 22px; border-radius: 10px; background: #104c70; color: #fff; }
        .form-header small { color: #b9e7f2; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .form-header h1 { margin: 4px 0 7px; font-size: 22px; }
        .form-header p { margin: 0; color: #d9edf6; }
        .form-note { padding: 11px 14px; margin: 14px 0; border: 1px solid #f1d99a; border-radius: 7px; background: #fff9e9; color: #75551a; }
        .form-note-info { border-color: #bdddeb; background: #eef8fc; color: #28566f; }
        .form-note .btn { margin-top: 9px; }
        .form-success { padding: 11px 14px; margin: 14px 0; border: 1px solid #a8dfbd; border-radius: 7px; background: #ebf9f0; color: #176b3f; }
        .form-error { padding: 11px 14px; margin: 14px 0; border: 1px solid #edb2b2; border-radius: 7px; background: #fff1f1; color: #a33232; }
        .form-layout { display: block; }
        .form-card { padding: 17px; border: 1px solid #d8e7f1; border-radius: 9px; background: #fff; }
        .form-card h2 { margin: 0 0 12px; color: #123b5d; font-size: 16px; }
        .entry-toolbar { margin-bottom: 14px; }
        .entry-toolbar__header { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .entry-toolbar__header h2 { margin: 0; }
        .entry-toolbar__empty { margin: 12px 0 0; }
        .form-section-title { margin: 22px 0 14px; padding: 9px 10px; border-left: 3px solid #0b6f9e; background: #edf7fb; color: #123b5d; font-size: 14px; }
        .entry-list { display: flex; flex-wrap: wrap; gap: 7px; margin: 12px 0 0; padding: 0; list-style: none; }
        .entry-list li { flex: 1 1 190px; margin: 0; }
        .entry-list a { display: block; padding: 9px 10px; border: 1px solid #dce9f1; border-radius: 6px; color: #1b567b; text-decoration: none; }
        .entry-list a:hover, .entry-list a.is-active { border-color: #6ab4d4; background: #e9f7fd; }
        .entry-list small { display: block; color: #678093; }
        .form-field { margin-bottom: 15px; }
        .form-field label { display: block; margin-bottom: 5px; color: #27475f; font-weight: 700; }
        .form-field input, .form-field textarea, .form-field select { display: block; width: 100%; padding: 9px 10px; border: 1px solid #bcd5e5; border-radius: 6px; color: #183d57; background: #fff; font: inherit; }
        .form-field textarea { min-height: 82px; resize: vertical; }
        .form-field input:focus, .form-field textarea:focus { outline: 2px solid #80c5e7; border-color: #1686bb; }
        .form-field input:disabled, .form-field textarea:disabled { background: #f5f8fa; }
        .form003-field { padding: 0 0 15px; border-bottom: 1px solid #d8e7f1; }
        .form003-field > label { padding: 8px 10px; border-left: 3px solid #0a7d9b; background: #edf7fb; color: #123b5d; }
        .exam-checks fieldset { min-width: 0; margin: 12px 0; padding: 10px; border: 1px solid #c7dce8; border-radius: 6px; }
        .exam-checks legend { padding: 0 5px; font-weight: 700; }
        .exam-options { display: grid; grid-template-columns: repeat(auto-fit, minmax(185px, 1fr)); gap: 6px; }
        .exam-options label { display: flex; gap: 8px; align-items: center; margin: 0; padding: 7px; border: 1px solid #d8e7f1; border-radius: 5px; font-weight: 400; cursor: pointer; }
        .exam-options label:has(input:checked) { background: #e6f4fc; border-color: #1686bb; }
        .exam-options input[type="checkbox"] { flex: 0 0 18px; width: 18px; height: 18px; margin: 0; padding: 0; accent-color: #0b6f9e; }
        .body-view-switch { display: flex; flex-wrap: wrap; gap: 7px; margin: 12px 0 8px; }
        .body-view-switch button[aria-pressed="true"] { background: #0b6f9e; border-color: #0b6f9e; color: #fff; }
        .body-stage { position: relative; overflow: hidden; min-height: 355px; padding: 14px; border: 1px solid #b7d3e5; border-radius: 12px; background: radial-gradient(ellipse 48% 85% at 50% 42%, #ffffff 0, #ffffff 48%, #e8f5fc 85%, #cce4f2 100%); perspective: 850px; }
        .body-stage:after { position: absolute; right: 12%; bottom: 11px; left: 12%; height: 18px; border-radius: 50%; background: rgba(27, 79, 111, .12); filter: blur(8px); content: ""; }
        .body-grid { position: relative; z-index: 1; margin: 0 auto; max-width: 400px; }
        .body-panel { display: none; min-height: 325px; padding: 8px; text-align: center; transform-origin: 50% 60%; }
        .body-panel.is-active { display: block; animation: body-turn .28s ease-out; }
        .body-panel strong { display: block; margin-bottom: 7px; color: #124d72; font-size: 15px; }
        @keyframes body-turn { from { opacity: .35; transform: rotateY(-32deg) scale(.94); } to { opacity: 1; transform: rotateY(0) scale(1); } }
        .body-canvas { position: relative; margin: 0 auto; cursor: crosshair; touch-action: manipulation; }
        .body-canvas.is-readonly { cursor: default; }
        .body-canvas img { display: block; width: 100%; height: 100%; user-select: none; mix-blend-mode: multiply; filter: contrast(1.12); }
        .body-mark-layer { position: absolute; inset: 0; pointer-events: none; }
        .body-mark { position: absolute; width: 21px; height: 21px; margin: -10px 0 0 -10px; border-radius: 50%; border: 2px solid #fff; background: #c63232; color: #fff; font: bold 11px/17px Arial, sans-serif; box-shadow: 0 0 0 1px #8f2525; text-align: center; pointer-events: auto; cursor: pointer; }
        .body-mark-list { margin-top: 10px; }
        .body-mark-row { display: grid; grid-template-columns: 110px minmax(0, 1fr) auto; align-items: center; gap: 7px; margin: 7px 0; }
        .body-mark-row input { margin: 0; }
        .body-mark-region { grid-column: 1 / -1; }
        .body-mark-region label { font-size: 12px; }
        .body-mark-row button { white-space: nowrap; }
        .form-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
        .btn { display: inline-block; padding: 8px 12px; border: 1px solid #bcd5e5; border-radius: 6px; background: #fff; color: #17547a; font: inherit; font-weight: 700; text-decoration: none; cursor: pointer; }
        .btn-primary { border-color: #0b6f9e; background: #0b6f9e; color: #fff; }
        .form-muted { color: #698197; font-size: 12px; }
        .official-list { margin: 14px 0; padding: 13px 17px; border: 1px solid #bdddeb; border-radius: 8px; background: #fff; }
        .official-list h2 { margin: 0 0 8px; color: #123b5d; font-size: 15px; }
        .official-list summary { cursor: pointer; color: #123b5d; font-size: 15px; font-weight: 700; }
        .official-list summary:focus-visible { outline: 2px solid #1686bb; outline-offset: 3px; }
        .official-list ul { margin: 0; padding-left: 18px; }
        .official-list li { margin: 5px 0; }
        .form020-sheet { color: #17384f; }
        .form020-band { margin: 18px 0 0; padding: 8px 10px; border: 1px solid #4497b7; background: #d2ccff; color: #123b5d; font-size: 14px; font-weight: 700; }
        .form020-band.alt { background: #8dd8df; }
        .form020-table-wrap { overflow-x: auto; border: 1px solid #b6d3df; border-top: 0; }
        .form020-table { width: 100%; min-width: 860px; border-collapse: collapse; table-layout: fixed; }
        .form020-table th, .form020-table td { border: 1px solid #b6d3df; padding: 4px; vertical-align: middle; }
        .form020-table th { background: #e8f7f8; color: #17465c; font-size: 11px; line-height: 1.2; text-align: center; }
        .form020-table input, .form020-table select { width: 100%; min-width: 58px; padding: 6px 5px; border: 1px solid #b8cfd9; border-radius: 3px; background: #fff; color: #183d57; font: 12px Arial, sans-serif; }
        .form020-grid { display: grid; grid-template-columns: repeat(4, minmax(130px, 1fr)); gap: 9px; padding: 12px; border: 1px solid #b6d3df; border-top: 0; }
        .form020-grid .form-field { margin: 0; }
        .form020-grid .span-2 { grid-column: span 2; }
        .form020-grid .span-4 { grid-column: 1 / -1; }
        .form020-remove { padding: 5px 8px; border: 1px solid #e1b0b0; border-radius: 4px; background: #fff4f4; color: #9a3030; cursor: pointer; }
        .form020-toolbar { display: flex; justify-content: space-between; gap: 8px; align-items: center; padding: 8px 10px; border: 1px solid #b6d3df; border-top: 0; background: #f5fbfd; }
        .form020-total { font-weight: 700; }
        .form020-legacy { margin: 8px 0 0; padding: 9px; border: 1px solid #efcf8b; background: #fff9e9; white-space: pre-wrap; }
        .form022-sheet { color: #17384f; }
        .form022-band { margin: 18px 0 0; padding: 8px 10px; border: 1px solid #7c7c90; background: #cfccff; color: #132b45; font-size: 14px; font-weight: 700; }
        .form022-allergy { display: grid; grid-template-columns: auto auto minmax(220px, 1fr); gap: 12px; align-items: end; padding: 12px; border: 1px solid #b6d3df; border-top: 0; background: #f6fff6; }
        .form022-allergy label { font-weight: 700; }
        .form022-allergy input[type="radio"] { width: auto; margin-right: 5px; }
        .form022-medical-orders { padding: 12px; border: 1px solid #b6d3df; border-top: 0; background: #f4fbff; }
        .form022-medical-orders__intro { display: flex; flex-direction: column; margin-bottom: 10px; }
        .form022-medical-orders__intro span { color: #5d7689; font-size: 12px; }
        .form022-order-grid { display: grid; grid-template-columns: repeat(2, minmax(220px, 1fr)); gap: 8px; }
        .form022-order-option { display: flex; gap: 9px; align-items: flex-start; padding: 9px; border: 1px solid #c7dce8; border-radius: 6px; background: #fff; cursor: pointer; }
        .form022-order-option:has(input:checked) { border-color: #1686bb; background: #e7f6fc; }
        .form022-order-option input { flex: 0 0 18px; width: 18px; height: 18px; margin: 2px 0 0; accent-color: #0b6f9e; }
        .form022-order-option small { color: #668094; }
        .form022-dates { display: grid; grid-template-columns: repeat(4, minmax(125px, 1fr)); gap: 8px; padding: 12px; border: 1px solid #b6d3df; border-top: 0; background: #f6fff6; }
        .form022-card { margin-top: 12px; border: 1px solid #95aab5; border-radius: 6px; overflow: hidden; }
        .form022-card-head { display: flex; justify-content: space-between; align-items: center; gap: 8px; padding: 8px 10px; background: #e9f8e9; }
        .form022-card-head strong { color: #163c53; }
        .form022-card-head small { display: block; color: #5d7689; }
        .form022-order-badge { display: inline-block; margin-left: 5px; padding: 2px 6px; border-radius: 10px; background: #0b6f9e; color: #fff; font-size: 10px; font-weight: 700; }
        .form022-order-badge[hidden] { display: none; }
        .form022-prescription { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 8px; padding: 10px; }
        .form022-prescription input[readonly] { border-color: #9fc2d5; background: #eef6fa; color: #17384f; }
        .form022-admin { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .form022-admin th, .form022-admin td { border: 1px solid #c4d6de; padding: 5px; }
        .form022-admin th { background: #d7f4d7; color: #17384f; font-size: 11px; }
        .form022-admin input, .form022-admin select { width: 100%; padding: 6px; border: 1px solid #b8cfd9; border-radius: 3px; }
        .form022-card-actions { display: flex; justify-content: flex-end; padding: 8px 10px; background: #f7fafb; }
        @media (max-width: 700px) { body { padding: 10px; } .entry-toolbar__header { align-items: flex-start; flex-direction: column; } .body-mark-row { grid-template-columns: 1fr auto; } .body-mark-row strong { grid-column: 1 / -1; } .form020-grid { grid-template-columns: 1fr 1fr; } .form020-grid .span-4 { grid-column: 1 / -1; } .form022-allergy, .form022-dates, .form022-prescription, .form022-order-grid { grid-template-columns: 1fr; } }
        @media (prefers-reduced-motion: reduce) { .body-panel.is-active { animation: none; } }
        @media print { body { padding: 0; background: #fff; } .entry-sidebar, .form-actions, .form-success, .form-error { display: none !important; } .form-layout { display: block; } .form-card { border: 0; } .form-header { color: #000; background: #fff; border-bottom: 2px solid #000; } .form-header p, .form-header small { color: #000; } .form-note { border: 2px solid #000; color: #000; font-weight: 700; } .form-field input, .form-field textarea { border: 0; border-bottom: 1px solid #999; resize: none; } }
    </style>
@endsection
@section('content')
@php
    $emergencySource = $emergencySource ?? ['suggestions' => [], 'records' => []];
    $form003Sections = ['motivo_consulta' => 'B', 'antecedentes_personales' => 'C', 'antecedentes_familiares' => 'D', 'enfermedad_actual' => 'E', 'revision_sistemas' => 'F', 'signos_vitales' => 'G', 'examen_fisico_marcas' => 'H', 'examen_fisico' => 'H', 'body_marks' => 'H', 'analisis' => 'I', 'diagnostico' => 'J', 'plan_tratamiento' => 'K'];
    $fieldSections = \Sis_medico\Services\HospitalEpisodeFormLayout::fieldSections($form['code']);
    $previousSection = null;
    $embeddedQuery = Request::boolean('embedded') ? ['embedded' => 1] : [];
    $pdfUrl = route('hospital.documents.form.pdf', ['code' => $form['code'], 'id_solicitud' => $solicitud->id] + ($entry ? ['entry' => $entry->id] : ['template' => 1]));
@endphp
<div class="form-page">
    <header class="form-header">
        <small>Formulario {{ $form['code'] }} · Episodio {{ $solicitud->id }}</small>
        <h1>{{ $form['name'] }}</h1>
        <p>{{ mb_strtoupper(trim((optional($solicitud->paciente)->apellido1 ?? '').' '.(optional($solicitud->paciente)->apellido2 ?? '').' '.(optional($solicitud->paciente)->nombre1 ?? '').' '.(optional($solicitud->paciente)->nombre2 ?? '')), 'UTF-8') }} · ID {{ $solicitud->id_paciente }}</p>
    </header>
    <div class="form-note">Este registro es un borrador del episodio. No sustituye el formulario oficial, la validación clínica ni la firma correspondiente.</div>
    @if(in_array($form['code'], ['010A', '010B'], true))
        <div class="form-note form-note-info"><strong>Laboratorio se registra en la orden.</strong> Este borrador manual no solicita exámenes ni registra resultados de laboratorio.
            <a class="btn btn-primary" href="{{ route('hospital.laboratory.index', ['id_solicitud' => $solicitud->id, 'form' => $form['code']]) }}">{{ $form['code'] === '010A' ? 'Solicitar laboratorio' : 'Ingresar resultados' }}</a>
        </div>
    @endif
    <div class="form-note form-note-info">
        <strong>Completar y ver el formulario {{ $form['code'] }}.</strong>
        {{ \Sis_medico\Services\HospitalEpisodeFormLayout::guidance($form['code']) }}
        @if($canWrite) Complete los campos y guarde el borrador. Después podrá verlo e imprimirlo en su formato desde aquí o desde Documentación.@endif
        <br><a class="btn btn-primary" href="{{ $pdfUrl }}" target="_blank" rel="noopener">{{ $entry ? 'Ver borrador en formato '.$form['code'] : 'Ver formato '.$form['code'] }}</a>
        @if($entry)<div class="form-muted">La vista PDF muestra la última versión guardada. Guarde los cambios antes de abrirla.</div>@endif
    </div>
    @if($form['code'] === '003')
        <div class="form-note form-note-info">
            <strong>¿Dónde y cuándo se llena?</strong> Aquí, desde Documentación → «Completar 003». Puede iniciarlo después de crear el episodio de Emergencia, antes o después de asignar la cama. Sistemas puede preparar o corregir el borrador; el médico debe revisar y validar la valoración de ingreso a Hospitalización.<br>
            <strong>¿De dónde salen los datos?</strong> El triaje y las notas de Emergencia de este episodio sugieren motivo, antecedentes, enfermedad actual, signos vitales y examen físico cuando existen. Revisión por sistemas, análisis, diagnóstico y prescripciones se escriben y verifican aquí; no se inventan ni se copian automáticamente.
            @if($canWrite)<br><a class="btn" href="#campos-003">Ir a los campos del 003</a>@endif
        </div>
        @if(!$canWrite)
            <div class="form-note" role="status"><strong>003 disponible solo para consulta.</strong> Para escribir y guardar esta valoración, abra este mismo episodio con una cuenta de médico o Sistemas autorizada mientras el episodio esté abierto.</div>
        @endif
    @endif
    @if($officialConsultations->isNotEmpty())
        <div class="official-list">
            <h2>Interconsultas registradas en el módulo clínico</h2>
            <ul>@foreach($officialConsultations as $consultation)
                <li>{{ $consultation->fecha }} · {{ $consultation->especialidad ?: 'Interconsulta #'.$consultation->id }} — <a href="{{ route($form['code'] === '007' ? 'formulario.007' : 'informe.007', ['id' => $consultation->id]) }}" target="_blank" rel="noopener">Ver {{ $form['code'] }} oficial</a></li>
            @endforeach</ul>
        </div>
    @endif
    @if(count($emergencySource['records']))
        <details class="official-list">
            <summary>Consultar datos registrados en Emergencia ({{ count($emergencySource['records']) }})</summary>
            <p class="form-muted">Se muestran desde este episodio. Revise las sugerencias antes de guardar el borrador; no se asignan diagnósticos ni prescripciones automáticamente.</p>
            <ul>@foreach($emergencySource['records'] as $sourceRecord)
                <li><strong>{{ $sourceRecord['title'] }}</strong> · {{ $sourceRecord['recorded_at'] }} · {{ $sourceRecord['recorded_by'] }}<br>{{ $sourceRecord['content'] }}</li>
            @endforeach</ul>
        </details>
    @endif
    @if(session('status'))<div class="form-success" role="status">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="form-error" role="alert">{{ $errors->first() }}</div>@endif
    <div class="form-layout">
        <section class="form-card entry-sidebar entry-toolbar">
            <div class="entry-toolbar__header">
                <h2>Registros de este formulario</h2>
                @if($canWrite)<a class="btn" href="{{ route('hospital.documents.form.show', ['code' => $form['code'], 'id_solicitud' => $solicitud->id, 'new' => 1] + $embeddedQuery) }}">Nuevo registro</a>@endif
            </div>
            @if($entries->isEmpty())
                <p class="form-muted entry-toolbar__empty">Aún no hay borradores guardados.</p>
            @else
                <ul class="entry-list">
                    @foreach($entries as $item)
                        <li><a class="{{ $entry && $entry->id === $item->id ? 'is-active' : '' }}" href="{{ route('hospital.documents.form.show', ['code' => $form['code'], 'id_solicitud' => $solicitud->id, 'entry' => $item->id] + $embeddedQuery) }}">Registro #{{ $item->id }}<small>{{ $item->updated_at }} · {{ $item->updated_by }}</small></a></li>
                    @endforeach
                </ul>
            @endif
        </section>
        <main class="form-card" @if($form['code'] === '003') id="campos-003" @endif>
            <h2>{{ $entry ? 'Borrador #'.$entry->id : 'Nuevo borrador' }}</h2>
            @if(!$canWrite)<p class="form-muted">El episodio está en modo de lectura o su usuario no tiene permiso para editar borradores clínicos.@if($form['code'] === '003') Para completar el 003 se requiere un médico o usuario de Sistemas autorizado.@endif</p>@endif
            <form method="POST" action="{{ route('hospital.documents.form.store', ['code' => $form['code'], 'id_solicitud' => $solicitud->id] + $embeddedQuery) }}">
                {{ csrf_field() }}
                @if($entry)<input type="hidden" name="entry_id" value="{{ $entry->id }}">@endif
                @if($form['code'] === '020')
                    @include('hospital.documentos.formulario-020-editor')
                @elseif($form['code'] === '022')
                    @include('hospital.documentos.formulario-022-editor')
                @elseif($form['code'] === '024')
                    @include('hospital.documentos.formulario-024-editor')
                @elseif(in_array($form['code'], ['018', '018A', '019'], true))
                    @include('hospital.documentos.formulario-anestesia-editor')
                @else
                @foreach($fields as $key => $label)
                    @php $value = old('data.'.$key, $entry ? ($entry->payload[$key] ?? '') : ($emergencySource['suggestions'][$key] ?? '')); @endphp
                    @if(isset($fieldSections[$key]) && $fieldSections[$key] !== $previousSection)
                        <h3 class="form-section-title">{{ $fieldSections[$key] }}</h3>
                        @php $previousSection = $fieldSections[$key]; @endphp
                    @endif
                    <div class="form-field {{ $form['code'] === '003' ? 'form003-field' : '' }}">
                        <label id="label_{{ $key }}" for="field_{{ $key }}">@if($form['code'] === '003'){{ $form003Sections[$key] ?? '' }}. @endif{{ $label }}</label>
                        @if($form['code'] === '003' && $key === 'examen_fisico_marcas')
                            @php
                                $selectedExam = \Sis_medico\Services\HospitalEpisodePhysicalExam::forDisplay($value);
                                $savedBodyMarks = old('data.body_marks', $entry ? ($entry->payload['body_marks'] ?? '') : '');
                                $bodyChecks = \Sis_medico\Services\HospitalEpisodePhysicalExam::bodyChecks(\Sis_medico\Services\HospitalEpisodeBodyDiagram::forDisplay($savedBodyMarks));
                            @endphp
                            <p id="exam-instructions">Marque las regiones o sistemas que presentan patología y describa los hallazgos abajo. Las marcas del cuerpo también activan su casilla regional: puede corregir la región debajo de cada marca. Todas aparecerán con una X en el literal H del PDF.</p>
                            <input type="hidden" name="data[{{ $key }}]" value="" {{ $canWrite ? '' : 'disabled' }}>
                            <div id="body-exam-manual-inputs">
                                @if($canWrite)
                                    @foreach(array_intersect($selectedExam, $bodyChecks) as $manualCode)
                                        <input type="hidden" name="data[{{ $key }}][]" value="{{ $manualCode }}">
                                    @endforeach
                                @endif
                            </div>
                            <div class="exam-checks" id="field_{{ $key }}" role="group" aria-labelledby="label_{{ $key }}" aria-describedby="exam-instructions">
                                @foreach(\Sis_medico\Services\HospitalEpisodePhysicalExam::groups() as $group => $options)
                                    <fieldset><legend>{{ $group }}</legend><div class="exam-options">
                                        @foreach($options as $optionCode => $optionLabel)
                                            <label for="exam_{{ $optionCode }}"><input type="checkbox" id="exam_{{ $optionCode }}" name="data[{{ $key }}][]" value="{{ $optionCode }}" data-manual-checked="{{ in_array($optionCode, $selectedExam, true) ? 'true' : 'false' }}" {{ in_array($optionCode, $selectedExam, true) || in_array($optionCode, $bodyChecks, true) ? 'checked' : '' }} {{ $canWrite && !in_array($optionCode, $bodyChecks, true) ? '' : 'disabled' }}><span>{{ $optionCode }}. {{ $optionLabel }}</span></label>
                                        @endforeach
                                    </div></fieldset>
                                @endforeach
                            </div>
                        @elseif($form['code'] === '003' && $key === 'body_marks')
                            @php $bodyViews = \Sis_medico\Services\HospitalEpisodeBodyDiagram::views(); @endphp
                            <p>El esquema ubica hallazgos del examen físico, como dolor o lesiones; no es un diagnóstico por sí solo. Gire entre las cuatro vistas, toque la zona y describa la marca. Se conservará en la misma vista del formulario 003. Si no hay hallazgos que ubicar, déjelo sin marcas.</p>
                            <div class="body-view-switch" id="body-view-switch" role="group" aria-label="Vista del cuerpo">
                                @foreach($bodyViews as $viewKey => $bodyView)
                                    <button class="btn" type="button" data-select-view="{{ $viewKey }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}">{{ $bodyView['label'] }}</button>
                                @endforeach
                            </div>
                            <div class="body-stage"><div class="body-grid" id="body-grid">
                                @foreach($bodyViews as $viewKey => $bodyView)
                                    <div class="body-panel {{ $loop->first ? 'is-active' : '' }}" data-panel-view="{{ $viewKey }}">
                                        <strong>{{ $bodyView['label'] }}</strong>
                                        <div class="body-canvas {{ $canWrite ? '' : 'is-readonly' }}" data-view="{{ $viewKey }}" style="width:{{ $bodyView['width'] }}px;height:{{ $bodyView['height'] }}px" aria-label="Esquema corporal: {{ $bodyView['label'] }}">
                                            <img src="{{ asset($bodyView['image']) }}" alt="Vista {{ mb_strtolower($bodyView['label'], 'UTF-8') }} del cuerpo" draggable="false">
                                            <div class="body-mark-layer"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div></div>
                            <input id="field_{{ $key }}" type="hidden" name="data[{{ $key }}]" value="{{ $value }}" {{ $canWrite ? '' : 'disabled' }}>
                            <div class="body-mark-list" id="body-mark-list" aria-live="polite"></div>
                        @elseif(in_array($key, ['fecha_hora', 'fecha_admision'], true))
                            <input id="field_{{ $key }}" type="datetime-local" name="data[{{ $key }}]" value="{{ $value }}" {{ $canWrite ? '' : 'disabled' }}>
                        @elseif(in_array($key, ['especialidad_solicitada', 'especialidad', 'estudio', 'muestra', 'sitio_anatomico', 'establecimiento_destino', 'prioridad', 'destino', 'procedimiento'], true))
                            <input id="field_{{ $key }}" type="text" name="data[{{ $key }}]" value="{{ $value }}" maxlength="10000" {{ $canWrite ? '' : 'disabled' }}>
                        @else
                            <textarea id="field_{{ $key }}" name="data[{{ $key }}]" maxlength="10000" {{ $canWrite ? '' : 'disabled' }}>{{ $value }}</textarea>
                        @endif
                        @if($form['code'] === '003' && !$entry && !empty($emergencySource['suggestions'][$key]))
                            <small class="form-muted">Sugerido desde Emergencia; revise antes de guardar.</small>
                        @endif
                    </div>
                @endforeach
                @endif
                <div class="form-actions">
                    @if($canWrite)<button class="btn btn-primary" type="submit">{{ $entry ? 'Actualizar borrador' : 'Guardar borrador' }}</button>@endif
                    @if($entry)<a class="btn" href="{{ $pdfUrl }}" target="_blank" rel="noopener">Ver / imprimir formato {{ $form['code'] }}</a>@endif
                    <a class="btn" href="{{ route('hospital.documents.index', ['id_solicitud' => $solicitud->id]) }}" target="_top">Volver al episodio</a>
                </div>
            </form>
        </main>
    </div>
</div>
@if($form['code'] === '003')
<script>
(function () {
    var input = document.getElementById('field_body_marks');
    var grid = document.getElementById('body-grid');
    var list = document.getElementById('body-mark-list');
    var switcher = document.getElementById('body-view-switch');
    if (!input || !grid || !list) return;
    var views = {front: 'Frente', back: 'Espalda', profile_1: 'Perfil 1', profile_2: 'Perfil 2'};
    var regionZones = @json(\Sis_medico\Services\HospitalEpisodeBodyDiagram::regionZones());
    var regionOptions = @json(\Sis_medico\Services\HospitalEpisodePhysicalExam::groups()['Regional']);
    var examCheckboxes = document.querySelectorAll('#field_examen_fisico_marcas input[type="checkbox"]');
    var manualInputs = document.getElementById('body-exam-manual-inputs');
    function regionAt(view, x, y) {
        var zones = regionZones[view] || [];
        for (var z = 0; z < zones.length; z++) {
            var points = zones[z][1], inside = false;
            for (var i = 0, j = points.length - 1; i < points.length; j = i++) {
                var xi = points[i][0], yi = points[i][1], xj = points[j][0], yj = points[j][1];
                if ((yi > y) !== (yj > y) && x < (xj - xi) * (y - yi) / (yj - yi) + xi) inside = !inside;
            }
            if (inside) return zones[z][0];
        }
        return '';
    }
    var marks = [];
    try {
        var saved = JSON.parse(input.value || '[]');
        if (Array.isArray(saved)) marks = saved.filter(function (mark) {
            return mark && Object.prototype.hasOwnProperty.call(views, mark.view)
                && Number.isInteger(mark.x) && Number.isInteger(mark.y)
                && mark.x >= 0 && mark.x <= 1000 && mark.y >= 0 && mark.y <= 1000
                && typeof mark.note === 'string';
        });
    } catch (error) { marks = []; }

    marks.forEach(function (mark) {
        if (!Object.prototype.hasOwnProperty.call(mark, 'region')) mark.region = regionAt(mark.view, mark.x, mark.y);
    });
    function syncExamChecks() {
        if (manualInputs) manualInputs.textContent = '';
        examCheckboxes.forEach(function (checkbox) {
            var manual = checkbox.dataset.manualChecked === 'true';
            var automatic = marks.some(function (mark) { return mark.region === checkbox.value; });
            checkbox.checked = manual || automatic;
            checkbox.disabled = input.disabled || automatic;
            checkbox.title = automatic ? 'Marcada desde el cuerpo. Para cambiarla, corrija la región o quite la marca corporal.' : '';
            if (manual && automatic && !input.disabled && manualInputs) {
                var hidden = document.createElement('input');
                hidden.type = 'hidden'; hidden.name = checkbox.name; hidden.value = checkbox.value;
                manualInputs.appendChild(hidden);
            }
        });
    }
    examCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            checkbox.dataset.manualChecked = checkbox.checked ? 'true' : 'false';
            syncExamChecks();
        });
    });
    function sync() { input.value = marks.length ? JSON.stringify(marks) : ''; syncExamChecks(); }
    function selectView(view) {
        grid.querySelectorAll('[data-panel-view]').forEach(function (panel) {
            panel.classList.toggle('is-active', panel.dataset.panelView === view);
        });
        switcher.querySelectorAll('[data-select-view]').forEach(function (button) {
            button.setAttribute('aria-pressed', button.dataset.selectView === view ? 'true' : 'false');
        });
    }
    switcher.querySelectorAll('[data-select-view]').forEach(function (button) {
        button.addEventListener('click', function () { selectView(button.dataset.selectView); });
    });
    function render() {
        grid.querySelectorAll('.body-mark-layer').forEach(function (layer) { layer.textContent = ''; });
        list.textContent = '';
        if (!marks.length) {
            var empty = document.createElement('p');
            empty.className = 'form-muted';
            empty.textContent = 'Aún no hay marcas en el esquema corporal.';
            list.appendChild(empty);
        }
        marks.forEach(function (mark, index) {
            var canvas = grid.querySelector('[data-view="' + mark.view + '"]');
            if (!canvas) return;
            var dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'body-mark';
            dot.textContent = String(index + 1);
            dot.title = 'Editar la descripción de la marca ' + (index + 1);
            dot.style.left = (mark.x / 10) + '%';
            dot.style.top = (mark.y / 10) + '%';
            dot.addEventListener('click', function (event) {
                event.stopPropagation();
                selectView(mark.view);
                var notes = list.querySelectorAll('input');
                if (notes[index]) notes[index].focus();
            });
            canvas.querySelector('.body-mark-layer').appendChild(dot);

            var row = document.createElement('div');
            row.className = 'body-mark-row';
            var title = document.createElement('strong');
            title.textContent = (index + 1) + '. ' + views[mark.view];
            title.addEventListener('click', function () { selectView(mark.view); });
            row.appendChild(title);
            var note = document.createElement('input');
            note.type = 'text';
            note.maxLength = 250;
            note.placeholder = 'Describa el hallazgo';
            note.setAttribute('aria-label', 'Descripción de la marca ' + (index + 1));
            note.value = mark.note;
            note.disabled = input.disabled;
            note.required = !input.disabled;
            note.addEventListener('input', function () { mark.note = note.value; sync(); });
            row.appendChild(note);
            if (!input.disabled) {
                var remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'btn';
                remove.textContent = 'Quitar';
                remove.setAttribute('aria-label', 'Quitar marca ' + (index + 1));
                remove.addEventListener('click', function () { marks.splice(index, 1); sync(); render(); });
                row.appendChild(remove);
            }
            var regionField = document.createElement('div');
            regionField.className = 'body-mark-region';
            var regionLabel = document.createElement('label');
            regionLabel.htmlFor = 'body-region-' + index;
            regionLabel.textContent = 'Casilla regional de la marca ' + (index + 1);
            regionField.appendChild(regionLabel);
            var region = document.createElement('select');
            region.id = regionLabel.htmlFor;
            region.disabled = input.disabled;
            var noRegion = document.createElement('option');
            noRegion.value = ''; noRegion.textContent = 'Sin casilla regional'; region.appendChild(noRegion);
            Object.keys(regionOptions).forEach(function (code) {
                var option = document.createElement('option');
                option.value = code; option.textContent = code + '. ' + regionOptions[code];
                region.appendChild(option);
            });
            region.value = mark.region || '';
            region.addEventListener('change', function () { mark.region = region.value; sync(); });
            regionField.appendChild(region); row.appendChild(regionField);
            list.appendChild(row);
        });
    }
    if (!input.disabled) grid.querySelectorAll('.body-canvas').forEach(function (canvas) {
        canvas.addEventListener('click', function (event) {
            if (marks.length >= 40) { alert('El esquema admite hasta 40 marcas.'); return; }
            var bounds = canvas.getBoundingClientRect();
            var mark = {
                view: canvas.dataset.view,
                x: Math.max(0, Math.min(1000, Math.round((event.clientX - bounds.left) / bounds.width * 1000))),
                y: Math.max(0, Math.min(1000, Math.round((event.clientY - bounds.top) / bounds.height * 1000))),
                note: ''
            };
            mark.region = regionAt(mark.view, mark.x, mark.y);
            marks.push(mark);
            sync(); render();
            var notes = list.querySelectorAll('input');
            if (notes.length) notes[notes.length - 1].focus();
        });
    });
    sync(); render();
})();
</script>
@endif
@endsection
