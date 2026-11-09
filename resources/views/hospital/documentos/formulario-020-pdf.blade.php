<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Formulario 020 · Episodio {{ $solicitud->id }}</title>
    <style>
        @page { margin: 7mm 7mm 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: DejaVu Sans, sans-serif; font-size: 6.2pt; }
        .form-page { page-break-before: always; }
        .form-page.is-first { page-break-before: auto; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: .45pt solid #4b5a62; padding: .8mm; vertical-align: middle; overflow-wrap: break-word; }
        .no-border, .no-border td { border: 0; }
        .brand { height: 20mm; overflow: hidden; color: #0a345b; font-size: 13pt; font-weight: bold; letter-spacing: 2.5pt; text-align: center; }
        .title { font-size: 10pt; font-weight: bold; text-align: center; }
        .code { font-size: 7pt; text-align: right; }
        .section { background: #d0ccff; color: #112d47; font-size: 7.5pt; font-weight: bold; text-align: left; }
        .section.alt { background: #8dd8df; }
        .label { background: #d8f3f3; font-size: 5.5pt; font-weight: bold; }
        .identity { height: 5.5mm; font-size: 6.3pt; }
        .tiny { font-size: 4.7pt; line-height: 1.1; }
        .center { text-align: center; }
        .left { text-align: left; }
        .h5 { height: 5mm; }
        .h6 { height: 6mm; }
        .h8 { height: 8mm; }
        .h12 { height: 12mm; }
        .slot { padding: .45mm .2mm; text-align: center; font-size: 4.6pt; }
        .row-label { width: 19%; background: #d8f3f3; font-weight: bold; }
        .graph-row td { height: 5.4mm; }
        .critical td { border-top: 1.4pt dashed #8a2470; }
        .subhead { background: #e9f8f8; font-weight: bold; text-align: center; }
        .fluid td { height: 6.2mm; }
        .fluid .amount { text-align: right; }
        .summary td { height: 6mm; }
        .note { color: #694f1b; font-size: 5.4pt; }
        .legacy { margin-top: 1.5mm; padding: 1.5mm; border: .5pt solid #b89445; background: #fff9e7; white-space: pre-wrap; }
        .footer { position: fixed; right: 0; bottom: -8mm; left: 0; border-top: .4pt solid #666; padding-top: 1mm; font-size: 5pt; font-weight: bold; color: #111; }
        .footer table { width: 100%; border-collapse: collapse; }
        .footer td { border: 0; padding: 0; }
        .footer .document-name { text-align: right; }
        .page-number:after { content: counter(page); }
        .spacer { height: 1.3mm; }
    </style>
</head>
<body>
@php
    $patient = $solicitud->paciente;
    $payload020 = [];
    foreach (['fecha_hora', 'constantes', 'ingresos', 'egresos', 'balance'] as $key020) {
        $payload020[$key020] = $entry ? ($entry->payload[$key020] ?? '') : (($emergencySource['suggestions'][$key020] ?? ''));
    }
    $document020 = \Sis_medico\Services\HospitalEpisodeForm020::forDisplay($payload020);
    $totals020 = \Sis_medico\Services\HospitalEpisodeForm020::totals($document020);
    $vitals020 = array_slice($document020['constantes']['registros'], 0, 21);
    while (count($vitals020) < 21) $vitals020[] = [];
    $intakes020 = array_slice($document020['ingresos']['registros'], 0, 24);
    $outputs020 = array_slice($document020['egresos']['registros'], 0, 24);
    $fluidRowCount020 = max(12, min(24, max(count($intakes020), count($outputs020))));
    $state020 = $entry ? 'BORRADOR SIN FIRMA' : 'FORMATO SIN LLENAR';
    $fullName020 = trim(($patient->apellido1 ?? '').' '.($patient->apellido2 ?? '').' '.($patient->nombre1 ?? '').' '.($patient->nombre2 ?? ''));
    $formatNumber020 = function ($value) {
        return abs($value - round($value)) < 0.00001 ? number_format($value, 0, '.', '') : number_format($value, 2, '.', '');
    };
@endphp

<div class="footer"><table><tr>
    <td>SNS-MSP / HCU-form.020 / 2021</td>
    <td class="document-name">CONSTANTES VITALES / BALANCE HÍDRICO (<span class="page-number"></span>)</td>
</tr></table></div>

<div class="form-page is-first">
    <table class="no-border"><tr>
        <td class="brand" style="width:25%">
            <!--AITROL_PDF_BRANDING_SLOT-->
            <!--AITROL_PDF_BRANDING_FALLBACK_START-->AITROL<!--AITROL_PDF_BRANDING_FALLBACK_END-->
        </td>
        <td class="title">CONSTANTES VITALES / BALANCE HÍDRICO</td>
        <td class="code" style="width:25%">FORMULARIO 020<br>{{ $state020 }}</td>
    </tr></table>
    <div class="spacer"></div>
    <table>
        <tr><th colspan="8" class="section">A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</th></tr>
        <tr><td class="label" colspan="2">INSTITUCIÓN DEL SISTEMA</td><td class="label">UNICÓDIGO</td><td class="label" colspan="2">ESTABLECIMIENTO DE SALUD</td><td class="label">HISTORIA CLÍNICA</td><td class="label">ARCHIVO</td><td class="label">HOJA</td></tr>
        <tr><td class="identity" colspan="2">BORJA CLINIC</td><td></td><td colspan="2">BORJA CLINIC</td><td>{{ $solicitud->id_paciente }}</td><td>{{ $solicitud->id }}</td><td>1/2</td></tr>
        <tr><td class="label">PRIMER APELLIDO</td><td class="label">SEGUNDO APELLIDO</td><td class="label">PRIMER NOMBRE</td><td class="label">SEGUNDO NOMBRE</td><td class="label">SEXO</td><td class="label">EDAD</td><td class="label" colspan="2">FECHA Y HORA DEL REGISTRO</td></tr>
        <tr><td class="identity">{{ $patient->apellido1 ?? '' }}</td><td>{{ $patient->apellido2 ?? '' }}</td><td>{{ $patient->nombre1 ?? '' }}</td><td>{{ $patient->nombre2 ?? '' }}</td><td>{{ $patient->sexo ?? '' }}</td><td>{{ $patient->edad ?? '' }}</td><td colspan="2">{{ $payload020['fecha_hora'] }}</td></tr>
    </table>
    <div class="spacer"></div>

    <table class="tiny">
        <tr><th colspan="22" class="section">B. CONSTANTES VITALES</th></tr>
        @foreach([
            'fecha' => 'FECHA', 'dia_internacion' => 'DÍA DE INTERNACIÓN', 'dia_postquirurgico' => 'DÍA POST QUIRÚRGICO',
            'turno' => 'TURNO', 'hora' => 'HORA', 'pulso' => 'PULSO (lpm)', 'temperatura' => 'TEMPERATURA (°C)',
            'frecuencia_respiratoria' => 'FRECUENCIA RESPIRATORIA (rpm)', 'saturacion' => 'PULSIOXIMETRÍA (%)',
            'presion_sistolica' => 'PRESIÓN SISTÓLICA (mmHg)', 'presion_diastolica' => 'PRESIÓN DIASTÓLICA (mmHg)',
            'responsable' => 'RESPONSABLE',
        ] as $key020 => $label020)
            <tr class="graph-row {{ $key020 === 'pulso' ? 'critical' : '' }}"><td class="row-label">{{ $label020 }}</td>@foreach($vitals020 as $vital020)<td class="slot">{{ $vital020[$key020] ?? '' }}</td>@endforeach</tr>
        @endforeach
    </table>
    <div class="spacer"></div>

    <table>
        <tr><th colspan="5" class="section alt">C. MEDIDAS ANTROPOMÉTRICAS</th></tr>
        <tr>@foreach(['peso' => 'PESO (kg)', 'talla' => 'TALLA (cm)', 'perimetro_cefalico' => 'PERÍMETRO CEFÁLICO (cm)', 'perimetro_abdominal' => 'PERÍMETRO ABDOMINAL (cm)', 'otro' => 'OTRA MEDIDA'] as $key020 => $label020)<td class="label center">{{ $label020 }}</td>@endforeach</tr>
        <tr class="h6">@foreach(['peso', 'talla', 'perimetro_cefalico', 'perimetro_abdominal', 'otro'] as $key020)<td class="center">{{ $document020['constantes']['antropometria'][$key020] }}</td>@endforeach</tr>
    </table>
    <div class="spacer"></div>

    <table>
        <tr><th colspan="9" class="section">D. INGESTA - ELIMINACIÓN / BALANCE HÍDRICO</th></tr>
        <tr><td class="label" colspan="2">INGRESOS</td><td class="label">TOTAL ML</td><td class="label" colspan="2">ELIMINACIONES</td><td class="label">TOTAL ML</td><td class="label" colspan="2">BALANCE CALCULADO</td><td class="label">ML</td></tr>
        <tr class="h8"><td colspan="2">Enteral · Parenteral · Vía oral · Otros</td><td class="center">{{ $formatNumber020($totals020['ingresos']) }}</td><td colspan="2">Orina · Drenaje · Vómito · Diarrea · Heces · Otros</td><td class="center">{{ $formatNumber020($totals020['egresos']) }}</td><td colspan="2">Ingresos − eliminaciones</td><td class="center"><strong>{{ $formatNumber020($totals020['balance']) }}</strong></td></tr>
        <tr>@foreach(['dieta' => 'DIETA PRESCRITA', 'comidas' => 'N.º COMIDAS', 'micciones' => 'N.º MICCIONES', 'deposiciones' => 'N.º DEPOSICIONES'] as $key020 => $label020)<td class="label center" colspan="{{ $loop->first ? 3 : 2 }}">{{ $label020 }}</td>@endforeach</tr>
        <tr class="h6">@foreach(['dieta', 'comidas', 'micciones', 'deposiciones'] as $key020)<td class="center" colspan="{{ $loop->first ? 3 : 2 }}">{{ $document020['balance']['alimentacion'][$key020] }}</td>@endforeach</tr>
    </table>
    <div class="spacer"></div>

    <table>
        <tr><th colspan="5" class="section alt">E. CUIDADOS GENERALES</th></tr>
        <tr>@foreach(['aseo' => 'ASEO', 'bano' => 'BAÑO', 'reposo' => 'REPOSO · ESPECIFIQUE', 'posicion' => 'POSICIÓN · ESPECIFIQUE', 'otros' => 'OTROS'] as $key020 => $label020)<td class="label center">{{ $label020 }}</td>@endforeach</tr>
        <tr class="h8">@foreach(['aseo', 'bano', 'reposo', 'posicion', 'otros'] as $key020)<td>{{ $document020['balance']['cuidados'][$key020] }}</td>@endforeach</tr>
    </table>
    <div class="spacer"></div>

    <table>
        <tr><th colspan="6" class="section">F. FECHA DE COLOCACIÓN DE DISPOSITIVOS MÉDICOS</th></tr>
        <tr>@foreach(['via_central' => 'VÍA CENTRAL', 'via_periferica' => 'VÍA PERIFÉRICA', 'sonda_nasogastrica' => 'SONDA NASOGÁSTRICA', 'sonda_vesical' => 'SONDA VESICAL', 'otros' => 'OTROS', 'responsable' => 'RESPONSABLE'] as $key020 => $label020)<td class="label center">{{ $label020 }}</td>@endforeach</tr>
        <tr class="h8">@foreach(['via_central', 'via_periferica', 'sonda_nasogastrica', 'sonda_vesical', 'otros', 'responsable'] as $key020)<td>{{ $document020['balance']['dispositivos'][$key020] }}</td>@endforeach</tr>
    </table>
    @if($document020['constantes']['nota_legacy'] !== '')<div class="legacy"><strong>Constantes vitales · texto de borrador anterior:</strong> {{ $document020['constantes']['nota_legacy'] }}</div>@endif
</div>

<div class="form-page">
    <table class="no-border"><tr>
        <td class="brand" style="width:25%">
            <!--AITROL_PDF_BRANDING_SLOT-->
            <!--AITROL_PDF_BRANDING_FALLBACK_START-->AITROL<!--AITROL_PDF_BRANDING_FALLBACK_END-->
        </td>
        <td class="title">BALANCE HÍDRICO · REGISTRO DETALLADO</td>
        <td class="code" style="width:25%">FORMULARIO 020<br>HOJA 2/2</td>
    </tr></table>
    <div class="spacer"></div>
    <table>
        <tr><th colspan="6" class="section alt">A. IDENTIFICACIÓN DEL PACIENTE</th></tr>
        <tr><td class="label">APELLIDOS Y NOMBRES</td><td colspan="2">{{ $fullName020 }}</td><td class="label">IDENTIFICACIÓN / HC</td><td>{{ $solicitud->id_paciente }}</td><td class="label">EPISODIO {{ $solicitud->id }}</td></tr>
    </table>
    <div class="spacer"></div>
    <table class="fluid">
        <tr><th colspan="8" class="section">D. BALANCE HÍDRICO DETALLADO</th></tr>
        <tr><th colspan="4" class="subhead">INGRESOS EN ML</th><th colspan="4" class="subhead">ELIMINACIONES EN ML</th></tr>
        <tr><th>Fecha / turno</th><th>Hora</th><th>Vía · tipo</th><th>Cantidad</th><th>Fecha / turno</th><th>Hora</th><th>Tipo · detalle</th><th>Cantidad</th></tr>
        @for($index020 = 0; $index020 < $fluidRowCount020; $index020++)
            @php $in020 = $intakes020[$index020] ?? []; $out020 = $outputs020[$index020] ?? []; @endphp
            <tr><td>{{ trim(($in020['fecha'] ?? '').' '.($in020['turno'] ?? '')) }}</td><td class="center">{{ $in020['hora'] ?? '' }}</td><td>{{ trim(($in020['categoria'] ?? '').(empty($in020['tipo']) ? '' : ' · '.$in020['tipo'])) }}</td><td class="amount">{{ $in020['cantidad'] ?? '' }}</td><td>{{ trim(($out020['fecha'] ?? '').' '.($out020['turno'] ?? '')) }}</td><td class="center">{{ $out020['hora'] ?? '' }}</td><td>{{ trim(($out020['categoria'] ?? '').(empty($out020['tipo']) ? '' : ' · '.$out020['tipo'])) }}</td><td class="amount">{{ $out020['cantidad'] ?? '' }}</td></tr>
        @endfor
        <tr><th colspan="3" class="left">TOTAL INGRESOS</th><th class="amount">{{ $formatNumber020($totals020['ingresos']) }}</th><th colspan="3" class="left">TOTAL ELIMINACIONES</th><th class="amount">{{ $formatNumber020($totals020['egresos']) }}</th></tr>
    </table>
    <div class="spacer"></div>

    <table class="summary">
        <tr><th colspan="6" class="section alt">RESUMEN DE TURNOS Y 24 HORAS</th></tr>
        <tr><td class="label">1.er TURNO · INGRESOS</td><td class="label">1.er TURNO · EGRESOS</td><td class="label">BALANCE</td><td class="label">2.º TURNO · INGRESOS</td><td class="label">2.º TURNO · EGRESOS</td><td class="label">BALANCE</td></tr>
        <tr>@foreach(['primer_ingresos', 'primer_egresos', 'primer_total', 'segundo_ingresos', 'segundo_egresos', 'segundo_total'] as $key020)<td class="center">{{ $document020['balance']['resumen'][$key020] }}</td>@endforeach</tr>
        <tr><td class="label">INGRESOS 24 H</td><td class="label">EGRESOS 24 H</td><td class="label">BALANCE 24 H</td><td class="label">DIURESIS HORARIA</td><td class="label">GASTO URINARIO</td><td class="label">GASTO FECAL</td></tr>
        <tr>@foreach(['total_ingresos', 'total_egresos', 'balance_24h', 'diuresis_horaria', 'gasto_urinario', 'gasto_fecal'] as $key020)<td class="center">{{ $document020['balance']['resumen'][$key020] }}</td>@endforeach</tr>
        <tr><td class="label">PÉRDIDAS INSENSIBLES</td><td colspan="5">{{ $document020['balance']['resumen']['perdidas_insensibles'] }}</td></tr>
    </table>
    <div class="spacer"></div>

    <table>
        <tr><th colspan="6" class="section">OBSERVACIONES Y APOYO DE CÁLCULO</th></tr>
        <tr><td colspan="4" rowspan="5" class="h12">{{ $document020['balance']['observaciones'] }}</td><td class="label center">TEMPERATURA</td><td class="label center">FACTOR</td></tr>
        <tr><td class="center">37 °C</td><td class="center">0,5</td></tr>
        <tr><td class="center">38 °C</td><td class="center">0,6</td></tr>
        <tr><td class="center">39 °C</td><td class="center">0,7</td></tr>
        <tr><td class="center">40 °C</td><td class="center">0,8</td></tr>
        <tr><td colspan="6" class="note">Referencia de cálculo del formato: pérdidas insensibles = peso × horas × factor de temperatura; diuresis horaria = diuresis total ÷ horas; gasto urinario = diuresis ÷ peso ÷ horas. El profesional debe verificar los resultados antes de validarlos.</td></tr>
    </table>
    @foreach(['ingresos' => 'Ingresos', 'egresos' => 'Egresos', 'balance' => 'Balance'] as $section020 => $label020)
        @if($document020[$section020]['nota_legacy'] !== '')<div class="legacy"><strong>{{ $label020 }} · texto de borrador anterior:</strong> {{ $document020[$section020]['nota_legacy'] }}</div>@endif
    @endforeach
    <div class="spacer"></div>
    <table><tr><td class="label" style="width:35%">ÚLTIMA EDICIÓN</td><td>{{ $entry ? $entry->updated_at.' · Usuario '.$entry->updated_by : 'Sin registro guardado' }}</td><td class="label" style="width:30%">VALIDACIÓN / FIRMA</td><td>Pendiente</td></tr></table>
</div>
</body>
</html>
