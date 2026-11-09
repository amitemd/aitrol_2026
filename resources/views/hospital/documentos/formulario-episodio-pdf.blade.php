<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Formulario {{ $form['code'] }} · Episodio {{ $solicitud->id }}</title>
    <style>
        @page { margin: 7mm 8mm 13mm; }
        body { margin: 0; color: #111; font-family: DejaVu Sans, sans-serif; font-size: 6.5pt; }
        .form-page { page-break-before: always; }
        .form-page.is-first { page-break-before: auto; }
        .masthead { width: 100%; border-collapse: collapse; margin-bottom: 1.5mm; }
        .masthead td { padding: 0; border: 0; vertical-align: middle; }
        .masthead .title { width: 62%; }
        .masthead .branding { width: 25%; height: 14mm; padding: 0 1mm; overflow: hidden; text-align: center; }
        .masthead .branding-fallback { color: #254969; font-size: 6.5pt; font-weight: bold; letter-spacing: .4pt; }
        .masthead .code { text-align: right; font-size: 11pt; font-weight: bold; }
        h1 { margin: 0 0 .5mm; font-size: 10pt; }
        .notice { margin: 1mm 0 1.5mm; font-size: 5.5pt; color: #66521e; }
        table.record { width: 100%; table-layout: fixed; border-collapse: collapse; margin-bottom: 1.2mm; page-break-inside: avoid; }
        .record th, .record td { border: .6pt solid #555; padding: .7mm .9mm; vertical-align: top; }
        .record th { background: #cdffcd; font-size: 5.4pt; font-weight: normal; text-align: left; }
        .record .section-heading { background: #d0ccff; font-size: 6.5pt; font-weight: bold; padding: .6mm .9mm; }
        .record .identity { height: 4.5mm; }
        .cell-label { font-size: 5.4pt; color: #333; margin-bottom: .5mm; }
        .writing { font-size: 6.5pt; line-height: 7.5pt; overflow-wrap: break-word; word-wrap: break-word; }
        .continuation { font-size: 5.5pt; font-weight: bold; color: #254969; }
        .responsible { margin-top: 1.5mm; }
        .footer { position: fixed; bottom: -10mm; left: 0; right: 0; border-top: .5pt solid #666; padding-top: 1.5mm; font-size: 6.5pt; font-weight: bold; }
        .footer table { width: 100%; border-collapse: collapse; }
        .footer td { border: 0; padding: 0; vertical-align: bottom; }
        .footer .document-name { text-align: right; text-transform: uppercase; }
        .page-number:after { content: counter(page); }
        .annex h2 { margin: 5mm 0 2mm; font-size: 10pt; page-break-after: avoid; }
        .annex-line { font-size: 8pt; line-height: 11pt; white-space: pre-wrap; overflow-wrap: break-word; word-wrap: break-word; }
    </style>
</head>
<body>
@php
    $patient = $solicitud->paciente;
    $patientName = trim(($patient->apellido1 ?? '').' '.($patient->apellido2 ?? '').' '.($patient->nombre1 ?? '').' '.($patient->nombre2 ?? ''));
    $suggestions = $emergencySource['suggestions'] ?? [];
    $values = [];
    foreach ($fields as $key => $label) {
        $rawValue = $entry ? ($entry->payload[$key] ?? '') : ($suggestions[$key] ?? '');
        $values[$key] = is_scalar($rawValue) ? trim((string) $rawValue) : '';
    }
    $pages = \Sis_medico\Services\HospitalEpisodeFormLayout::pages($form['code']);
    $annex = [];
    $state = $entry ? 'BORRADOR SIN FIRMA' : (count($suggestions) ? 'VISTA PREVIA · DATOS POR REVISAR' : 'FORMATO SIN LLENAR');
    $institutionalCode = strpos($form['code'], 'ANEXO') === 0
        ? 'SNS-MSP / HCU-'.str_replace('ANEXO', 'ANEXO ', $form['code'])
        : 'SNS-MSP / HCU-form.'.$form['code'].' / '.$form['edition'];
@endphp
<div class="footer">
    <table><tr>
        <td>{{ $institutionalCode }}</td>
        <td class="document-name">{{ $form['name'] }} (<span class="page-number"></span>)</td>
    </tr></table>
</div>
@foreach($pages as $pageNumber => $sections)
    <div class="form-page {{ $loop->first ? 'is-first' : '' }}">
        <table class="masthead"><tr>
            <td class="title"><h1>{{ $form['name'] }}</h1>Plantilla de captura del episodio · {{ $state }}</td>
            <td class="branding">
                <!--AITROL_PDF_BRANDING_SLOT-->
                <!--AITROL_PDF_BRANDING_FALLBACK_START--><span class="branding-fallback">ESTABLECIMIENTO DE SALUD</span><!--AITROL_PDF_BRANDING_FALLBACK_END-->
            </td>
            <td class="code">{{ $form['code'] }}</td>
        </tr></table>
        <table class="record">
            <tr><th colspan="4" class="section-heading">A. DATOS DEL USUARIO / PACIENTE</th></tr>
            <tr><th>PRIMER APELLIDO</th><th>SEGUNDO APELLIDO</th><th>PRIMER NOMBRE</th><th>SEGUNDO NOMBRE</th></tr>
            <tr><td class="identity">{{ $patient->apellido1 ?? '' }}</td><td>{{ $patient->apellido2 ?? '' }}</td><td>{{ $patient->nombre1 ?? '' }}</td><td>{{ $patient->nombre2 ?? '' }}</td></tr>
            <tr><th colspan="2">IDENTIFICACIÓN / HISTORIA CLÍNICA</th><th>EPISODIO</th><th>REGISTRO</th></tr>
            <tr><td colspan="2">{{ $solicitud->id_paciente }}</td><td>{{ $solicitud->id }}</td><td>{{ $entry ? '#'.$entry->id : 'Sin guardar' }}</td></tr>
        </table>
        <p class="notice">{{ $entry ? 'Datos guardados en este episodio. Pendiente de revisión y firma clínica.' : (count($suggestions) ? 'Sugerencias procedentes de Emergencia; revise y guarde antes de incorporarlas al registro.' : 'Campos clínicos vacíos. Complete y guarde el formulario desde Documentación.') }}</p>
        @foreach($sections as $section)
            @foreach($section['rows'] as $rowNumber => $row)
                @php
                    $rowHeight = count($row) === 1 && in_array($row[0], ['fecha_hora', 'fecha_admision'], true) ? 6 : $section['height'] * .52;
                    $maxLines = max(1, (int) floor(($rowHeight * 2.8346 - 10) / 7.5));
                @endphp
                <table class="record">
                    @if($rowNumber === 0)<tr><th colspan="{{ count($row) }}" class="section-heading">{{ $section['title'] }}</th></tr>@endif
                    <tr>
                    @foreach($row as $key)
                        @php
                            $lines = \Sis_medico\Services\HospitalEpisodeFormLayout::writingLines($values[$key] ?? '', count($row) > 1 ? 44 : 94);
                            $continued = count($lines) > $maxLines;
                            if ($continued) $annex[$key] = $values[$key];
                            $visibleLines = $continued ? array_slice($lines, 0, max(0, $maxLines - 1)) : $lines;
                        @endphp
                        <td style="height: {{ $rowHeight }}mm; width: {{ 100 / count($row) }}%;">
                            <div class="cell-label">{{ $fields[$key] }}</div>
                            <div class="writing">@foreach($visibleLines as $line){{ $line }}@if(!$loop->last)<br>@endif @endforeach</div>
                            @if($continued)<div class="continuation">Texto completo en el anexo: {{ $fields[$key] }}.</div>@endif
                        </td>
                    @endforeach
                    </tr>
                </table>
            @endforeach
        @endforeach
        @if($loop->last)
            <table class="record responsible">
                <tr><th colspan="2" class="section-heading">DATOS DEL REGISTRO</th></tr>
                <tr><th>ÚLTIMA EDICIÓN</th><th>VALIDACIÓN Y FIRMA CLÍNICA</th></tr>
                <tr><td>{{ $entry ? $entry->updated_at.' · Usuario '.$entry->updated_by : 'Sin registro guardado' }}</td><td>Pendiente. Este borrador no acredita firma.</td></tr>
            </table>
        @endif
    </div>
@endforeach
@if(count($annex))
    <div class="form-page annex">
        <h1>Anexo · Formulario {{ $form['code'] }}</h1>
        <p>{{ $patientName }} · {{ $solicitud->id_paciente }} · Episodio {{ $solicitud->id }} · {{ $state }}</p>
        <p>Contenido completo de los campos que exceden el espacio del formato.</p>
        @foreach($annex as $key => $value)
            <h2>{{ $fields[$key] }}</h2>
            @foreach(\Sis_medico\Services\HospitalEpisodeFormLayout::writingLines($value, 94) as $line)<div class="annex-line">{{ $line === '' ? "\u{00a0}" : $line }}</div>@endforeach
        @endforeach
    </div>
@endif
@if(count($emergencySource['records'] ?? []))
    <div class="form-page annex">
        <h1>Registros originales de Emergencia</h1>
        <p>{{ $patientName }} · {{ $solicitud->id_paciente }} · Episodio {{ $solicitud->id }}</p>
        <p>Información de origen para consulta. No modifica el borrador guardado. No constituye un formulario validado ni firmado.</p>
        @foreach($emergencySource['records'] as $record)
            <h2>{{ $record['title'] }} · {{ $record['recorded_at'] }} · {{ $record['recorded_by'] }}</h2>
            @foreach(\Sis_medico\Services\HospitalEpisodeFormLayout::writingLines($record['content'], 94) as $line)<div class="annex-line">{{ $line === '' ? "\u{00a0}" : $line }}</div>@endforeach
        @endforeach
    </div>
@endif
</body>
</html>
