<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Formulario 022 · Episodio {{ $solicitud->id }}</title>
    <style>
        @page { margin: 7mm 7mm 11mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: DejaVu Sans, sans-serif; font-size: 6pt; }
        .form-page { page-break-before: always; }
        .form-page.is-first { page-break-before: auto; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: .5pt solid #666; padding: .65mm; vertical-align: middle; overflow-wrap: break-word; }
        .masthead { margin-bottom: 2.5mm; }
        .masthead td { border: 0; }
        .masthead .branding { width: 25%; height: 20mm; overflow: hidden; text-align: center; }
        .masthead .branding-fallback { color: #0a345b; font-size: 12pt; font-weight: bold; letter-spacing: 2pt; }
        .masthead .title { font-size: 10pt; font-weight: bold; text-align: center; }
        .masthead .code { width: 25%; font-size: 7pt; text-align: right; }
        .section { background: #ccccff; color: #111b36; font-size: 8pt; font-weight: bold; text-align: left; }
        .label { background: #ccffcc; font-size: 5.4pt; font-weight: bold; text-align: center; }
        .identity { height: 5.5mm; font-size: 6.2pt; }
        .allergy { margin-top: 2.5mm; }
        .allergy td { height: 5.5mm; }
        .medication { margin-top: 2.5mm; }
        .medication .group { background: #ccccff; font-size: 7.3pt; font-weight: bold; text-align: center; }
        .medication .date-head { background: #ccffcc; font-weight: bold; text-align: center; }
        .medication .admin-row td { height: 2mm; padding: 0 .35mm; font-size: 4pt; line-height: 4.2pt; }
        .prescription { padding: 1.2mm; font-size: 5.5pt; line-height: 1.25; vertical-align: top; }
        .prescription strong { font-size: 6pt; }
        .center { text-align: center; }
        .legacy { margin-top: 1.5mm; padding: 1.2mm; border: .5pt solid #b89445; background: #fff9e7; white-space: pre-wrap; }
        .footer { position: fixed; right: 0; bottom: -7.5mm; left: 0; border-top: .4pt solid #666; padding-top: 1mm; font-size: 5pt; font-weight: bold; }
        .footer table { width: 100%; border-collapse: collapse; }
        .footer td { border: 0; padding: 0; }
        .footer .document-name { text-align: right; }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
@php
    $patient = $solicitud->paciente;
    $payload022 = [];
    foreach (['alergias', 'medicamentos'] as $key022) {
        $payload022[$key022] = $form022Payload[$key022] ?? ($entry ? ($entry->payload[$key022] ?? '') : (($emergencySource['suggestions'][$key022] ?? '')));
    }
    $document022 = \Sis_medico\Services\HospitalEpisodeForm022::forDisplay($payload022);
    $records022 = array_slice($document022['medicamentos']['registros'], 0, 6);
    while (count($records022) < 6) $records022[] = ['prescripcion' => '', 'medicamento' => '', 'dosis' => '', 'via' => '', 'frecuencia' => '', 'prescrita_por' => '', 'fecha_prescripcion' => '', 'administraciones' => []];
    $state022 = $entry ? 'BORRADOR SIN FIRMA' : (count($document022['medicamentos']['registros']) ? 'DATOS eMAR DEL EPISODIO' : 'FORMATO SIN LLENAR');
@endphp

<div class="footer"><table><tr>
    <td>SNS-MSP / HCU-form.022 / 2021</td>
    <td class="document-name">ADMINISTRACIÓN DE MEDICAMENTOS (<span class="page-number"></span>)</td>
</tr></table></div>
<div class="form-page is-first">
    <table class="masthead"><tr>
        <td class="branding">
            <!--AITROL_PDF_BRANDING_SLOT-->
            <!--AITROL_PDF_BRANDING_FALLBACK_START--><span class="branding-fallback">AITROL</span><!--AITROL_PDF_BRANDING_FALLBACK_END-->
        </td>
        <td class="title">ADMINISTRACIÓN DE MEDICAMENTOS</td>
        <td class="code">FORMULARIO 022<br>{{ $state022 }}</td>
    </tr></table>
    <table>
        <tr><th colspan="5" class="section">A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</th></tr>
        <tr><td class="label">PRIMER APELLIDO</td><td class="label">PRIMER NOMBRE</td><td class="label" style="width:8%">EDAD</td><td class="label">NÚMERO DE HISTORIA CLÍNICA ÚNICA</td><td class="label">NÚMERO DE ARCHIVO</td></tr>
        <tr><td class="identity">{{ $patient->apellido1 ?? '' }}</td><td>{{ $patient->nombre1 ?? '' }}</td><td class="center">{{ $patient->edad ?? '' }}</td><td>{{ $solicitud->id_paciente }}</td><td>{{ $solicitud->id }}</td></tr>
    </table>

    <table class="allergy">
        <tr><td class="label" style="width:16%">ALERGIA A MEDICAMENTOS</td><td class="label" style="width:5%">SÍ</td><td class="center" style="width:5%">{{ $document022['alergias']['estado'] === 'SI' ? 'X' : '' }}</td><td class="label" style="width:5%">NO</td><td class="center" style="width:5%">{{ $document022['alergias']['estado'] === 'NO' ? 'X' : '' }}</td><td class="label" style="width:12%">DESCRIBA:</td><td>{{ $document022['alergias']['descripcion'] }}</td></tr>
    </table>

    <table class="medication">
        <tr><th colspan="10" class="section">B. ADMINISTRACIÓN DE MEDICAMENTOS PRESCRITOS</th></tr>
        <tr><th colspan="2" class="group">1. MEDICAMENTO</th><th colspan="8" class="group">2. ADMINISTRACIÓN</th></tr>
        <tr><th colspan="2" class="date-head">FECHA</th>@for($dateIndex022 = 0; $dateIndex022 < 4; $dateIndex022++)<th colspan="2" class="date-head">{{ $document022['medicamentos']['fechas'][$dateIndex022] }}</th>@endfor</tr>
        <tr><th colspan="2" class="date-head">DOSIS, VÍA, FRECUENCIA</th>@for($dateIndex022 = 0; $dateIndex022 < 4; $dateIndex022++)<th class="date-head">HORA</th><th class="date-head">RESPONSABLE</th>@endfor</tr>
        @foreach($records022 as $record022)
            @php
                $byDate022 = [[], [], [], []];
                foreach (($record022['administraciones'] ?? []) as $administration022) {
                    $dateIndex022 = (int) ($administration022['fecha'] ?? -1);
                    if ($dateIndex022 >= 0 && $dateIndex022 <= 3 && count($byDate022[$dateIndex022]) < 12) $byDate022[$dateIndex022][] = $administration022;
                }
            @endphp
            @for($adminRow022 = 0; $adminRow022 < 12; $adminRow022++)
                <tr class="admin-row">
                    @if($adminRow022 === 0)
                        <td rowspan="12" colspan="2" class="prescription">
                            <strong>{{ $record022['medicamento'] }}</strong>
                            @if($record022['dosis'] !== '')<br><b>Dosis:</b> {{ $record022['dosis'] }}@endif
                            @if($record022['via'] !== '')<br><b>Vía:</b> {{ $record022['via'] }}@endif
                            @if($record022['frecuencia'] !== '')<br><b>Frecuencia:</b> {{ $record022['frecuencia'] }}@endif
                            @if(($record022['prescripcion'] ?? '') !== '')
                                <br><b>Orden médica:</b> {{ ($record022['prescrita_por'] ?? '') !== '' ? $record022['prescrita_por'] : 'vinculada al episodio' }}
                                @if(($record022['fecha_prescripcion'] ?? '') !== '') · {{ $record022['fecha_prescripcion'] }} @endif
                            @endif
                        </td>
                    @endif
                    @for($dateIndex022 = 0; $dateIndex022 < 4; $dateIndex022++)
                        @php $administration022 = $byDate022[$dateIndex022][$adminRow022] ?? []; @endphp
                        <td class="center">{{ $administration022['hora'] ?? '' }}</td><td>{{ $administration022['responsable'] ?? '' }}</td>
                    @endfor
                </tr>
            @endfor
        @endforeach
    </table>

    @if($document022['alergias']['nota_legacy'] !== '')<div class="legacy"><strong>Alergias · texto de borrador anterior:</strong> {{ $document022['alergias']['nota_legacy'] }}</div>@endif
    @if($document022['medicamentos']['nota_legacy'] !== '')<div class="legacy"><strong>Medicamentos · texto de borrador anterior:</strong> {{ $document022['medicamentos']['nota_legacy'] }}</div>@endif
</div>
</body>
</html>
