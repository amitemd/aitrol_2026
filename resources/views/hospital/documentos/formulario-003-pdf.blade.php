<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Formulario 003 · Episodio {{ $solicitud->id }}</title>
    <style>
        @page { size: letter portrait; margin: 0; }
        body { margin: 0; color: #111; font-family: DejaVu Sans, sans-serif; }
        .sheet { position: relative; width: 215.9mm; height: 250mm; overflow: visible; page-break-after: always; }
        .sheet:last-child { page-break-after: auto; }
        .template { position: absolute; top: 0; left: 0; width: 215.9mm; height: 279.4mm; }
        .value { position: absolute; overflow: hidden; white-space: pre-line; overflow-wrap: break-word; font-size: 7pt; line-height: 1.18; }
        .value.small { font-size: 6pt; }
        .value.name { font-size: 8pt; text-transform: uppercase; }
        .exam-check { position: absolute; text-align: center; font-size: 6pt; line-height: 6pt; font-weight: bold; color: #111; }
        .draft-stamp { position: absolute; top: 268mm; left: 8mm; font-size: 7pt; color: #6b551c; }
        .annex { margin: 15mm; font-size: 9pt; line-height: 1.4; }
        .annex h1 { margin: 0 0 8mm; font-size: 14pt; }
        .annex h2 { margin: 6mm 0 2mm; font-size: 10pt; color: #194961; }
        .annex p { margin: 0 0 3mm; white-space: pre-wrap; overflow-wrap: break-word; }
    </style>
</head>
<body>
@php
    $patient = $solicitud->paciente;
    $payload = $entry ? ($entry->payload ?? []) : [];
    $suggestions = $emergencySource['suggestions'] ?? [];
    $values = [];
    foreach ($fields as $key => $label) {
        $values[$key] = trim((string) ($entry ? ($payload[$key] ?? '') : ($suggestions[$key] ?? '')));
    }
    $marks = \Sis_medico\Services\HospitalEpisodeBodyDiagram::forDisplay($values['body_marks'] ?? '');
    $examChecks = \Sis_medico\Services\HospitalEpisodePhysicalExam::effectiveChecks($values['examen_fisico_marcas'] ?? '', $marks);
    $examBoxes = \Sis_medico\Services\HospitalEpisodePhysicalExam::pdfBoxes();
    $layout = json_decode(file_get_contents(public_path('assets/hospital/form003/pdf-003-layout.json')), true);
    $pageImage = [
        'first' => base64_encode(file_get_contents(public_path('assets/hospital/form003/pdf-003-anamnesis.png'))),
        'second' => base64_encode(\Sis_medico\Services\HospitalEpisodeBodyDiagram::renderMarkedForm003Page($marks)),
    ];
    $position = function ($box) {
        return sprintf('left:%.3fmm;top:%.3fmm;width:%.3fmm;height:%.3fmm;',
            $box[0] * 215.9 / 1700, $box[1] * 279.4 / 2200,
            $box[2] * 215.9 / 1700, $box[3] * 279.4 / 2200);
    };
    $limits = [
        'motivo_consulta' => 170, 'antecedentes_personales' => 500,
        'antecedentes_familiares' => 250, 'enfermedad_actual' => 950,
        'revision_sistemas' => 320, 'signos_vitales' => 130,
        'examen_fisico' => 300, 'analisis' => 330,
        'diagnostico' => 170, 'plan_tratamiento' => 850,
    ];
    $overflow = [];
    foreach ($limits as $key => $limit) {
        if (mb_strlen($values[$key] ?? '', 'UTF-8') > $limit) {
            $overflow[$key] = $values[$key];
        }
    }
    $displayValue = function ($key) use ($values, $limits) {
        $value = $values[$key] ?? '';
        $limit = $limits[$key] ?? 10000;
        return mb_strlen($value, 'UTF-8') > $limit
            ? mb_substr($value, 0, $limit - 24, 'UTF-8').'… (continúa en anexo)'
            : $value;
    };
    $names = [
        'apellido1' => $patient->apellido1 ?? '', 'apellido2' => $patient->apellido2 ?? '',
        'nombre1' => $patient->nombre1 ?? '', 'nombre2' => $patient->nombre2 ?? '',
    ];
@endphp
@foreach(['first', 'second'] as $page)
    <div class="sheet">
        <img class="template" src="data:image/png;base64,{{ $pageImage[$page] }}" alt="Plantilla 003, {{ $page === 'first' ? 'anamnesis' : 'examen físico y prescripciones' }}">
        @foreach($layout[$page] as $key => $box)
            @if(isset($names[$key]))
                <div class="value name" style="{{ $position($box) }}">{{ $names[$key] }}</div>
            @elseif(isset($limits[$key]))
                <div class="value {{ in_array($key, ['signos_vitales', 'diagnostico'], true) ? 'small' : '' }}" style="{{ $position($box) }}">{{ $displayValue($key) }}</div>
            @endif
        @endforeach
        @if($page === 'second')
            @foreach($examChecks as $examCode)
                @php
                    $examBox = $examBoxes[$examCode];
                    // Center the fixed 6pt text line, accounting for DejaVu's baseline in Dompdf.
                    $examBox[1] += $examBox[3] / 2 - 12.75;
                    $examBox[3] = 25.5;
                @endphp
                <div class="exam-check" data-exam-code="{{ $examCode }}" style="{{ $position($examBox) }}">X</div>
            @endforeach
        @endif
        @if($page === 'first')<div class="draft-stamp">BORRADOR SIN FIRMA · Episodio {{ $solicitud->id }}@if($entry) · Registro #{{ $entry->id }}@endif</div>@endif
    </div>
@endforeach
@if(count($overflow) || count($marks))
    <div class="annex">
        <h1>Formulario 003 · Detalle complementario del borrador</h1>
        <p>Episodio {{ $solicitud->id }} · {{ trim(($names['apellido1'] ?? '').' '.($names['apellido2'] ?? '').' '.($names['nombre1'] ?? '').' '.($names['nombre2'] ?? '')) }} · Sin firma clínica</p>
        @foreach($overflow as $key => $fullText)
            <h2>{{ $fields[$key] }}</h2><p>{{ $fullText }}</p>
        @endforeach
        @if(count($marks))
            <h2>Marcas del esquema corporal</h2>
            @foreach($marks as $index => $mark)
                <p><strong>{{ $index + 1 }}. {{ \Sis_medico\Services\HospitalEpisodeBodyDiagram::views()[$mark['view']]['label'] }}:</strong> {{ $mark['note'] }}</p>
            @endforeach
        @endif
    </div>
@endif
</body>
</html>
