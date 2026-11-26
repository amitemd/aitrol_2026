<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Formulario 017 · Protocolo quirúrgico</title>
    <style>
        @page { margin: 5mm 8mm 10mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: DejaVu Sans, sans-serif; font-size: 6.3pt; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: .55pt solid #4a4a4a; padding: .55mm .75mm; vertical-align: top; overflow-wrap: break-word; word-wrap: break-word; }
        .page { page-break-before: always; }
        .page.first { page-break-before: auto; }
        .masthead { margin-bottom: .6mm; }
        .masthead td { border: 0; padding: 0; vertical-align: middle; }
        .masthead .title { width: 58%; padding-left: 2mm; font-size: 10pt; font-weight: bold; }
        .masthead .sub { display: block; margin-top: .6mm; font-size: 5.4pt; font-weight: normal; }
        .masthead .brand { width: 27%; height: 16mm; padding: 0 1mm; text-align: left; overflow: hidden; }
        .masthead .brand-fallback { color: #254969; font-weight: bold; letter-spacing: .5pt; }
        .masthead .code { width: 15%; text-align: right; font-size: 11pt; font-weight: bold; }
        .section { background: #ccccff; font-size: 6.8pt; font-weight: bold; text-align: left; }
        .label { background: #ccffcc; font-size: 5.2pt; font-weight: normal; }
        .label.center, .center { text-align: center; }
        .value { font-size: 6.2pt; white-space: pre-wrap; line-height: 7.2pt; }
        .identity-value { height: 5mm; text-align: center; vertical-align: middle; }
        .gap { height: .55mm; border: 0; padding: 0; }
        .choice { width: 3.5%; text-align: center; vertical-align: middle; font-weight: bold; }
        .choice-label { text-align: center; vertical-align: middle; }
        .h-6 { height: 6mm; }
        .h-8 { height: 8mm; }
        .h-11 { height: 8mm; }
        .h-14 { height: 10mm; }
        .h-17 { height: 12mm; }
        .h-22 { height: 16mm; }
        .h-28 { height: 20mm; }
        .h-40 { height: 26mm; }
        .h-80 { height: 38mm; }
        .compact td, .compact th { padding-top: .4mm; padding-bottom: .4mm; }
        .notice { margin: .8mm 0 1mm; color: #66521e; font-size: 5.1pt; }
        .footer { position: fixed; bottom: -8.8mm; left: 0; right: 0; border-top: .4pt solid #777; padding-top: 1mm; font-size: 6pt; font-weight: bold; }
        .footer .right { float: right; }
        .page-number:after { content: counter(page); }
        .signature-box { height: 19mm; text-align: center; vertical-align: middle; }
        .diagram-gallery { width: 100%; text-align: center; white-space: nowrap; }
        .diagram-image { display: inline-block; margin: .5mm; text-align: center; vertical-align: middle; }
        .diagram-image img { max-width: 100%; width: auto; height: auto; }
        .diagram-gallery.images-1 .diagram-image { width: 96%; height: 48mm; }
        .diagram-gallery.images-1 .diagram-image img { max-height: 47mm; }
        .diagram-gallery.images-2 .diagram-image { width: 47.5%; height: 40mm; }
        .diagram-gallery.images-2 .diagram-image img { max-height: 39mm; }
        .diagram-gallery.images-3 .diagram-image { width: 31.2%; height: 34mm; }
        .diagram-gallery.images-3 .diagram-image img { max-height: 33mm; }
    </style>
</head>
<body>
@php
    $patient = $solicitud->paciente;
    $payload = $entry ? (array) $entry->payload : [];
    $suggestions = $emergencySource['suggestions'] ?? [];
    $values = [];
    foreach ($fields as $key => $label) {
        $raw = array_key_exists($key, $payload) ? $payload[$key] : ($suggestions[$key] ?? '');
        $values[$key] = is_scalar($raw) ? trim((string) $raw) : '';
    }
    $value = function ($key, $fallback = '') use ($values) {
        return $values[$key] !== '' ? $values[$key] : $fallback;
    };
    // Los borradores antiguos guardaban "CÓDIGO - DESCRIPCIÓN" en la columna
    // diagnóstica. Se elimina el prefijo al presentar el PDF; el código continúa
    // mostrándose exclusivamente en la columna CIE.
    $diagnosisDescription = function ($key) use ($value) {
        return preg_replace('/^\s*[A-Z][A-Z0-9.\-]*\d[A-Z0-9.\-]*\s+-\s+/imu', '', $value($key));
    };
    $normal = function ($text) {
        $text = mb_strtoupper(trim((string) $text), 'UTF-8');
        return strtr($text, ['Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U']);
    };
    $mark = function ($key, $expected) use ($value, $normal) {
        $selected = $normal($value($key));
        foreach ((array) $expected as $option) {
            if ($selected === $normal($option) || strpos($selected, $normal($option)) !== false) return 'X';
        }
        return '';
    };
    $dateValue = $value('fecha_operacion');
    $dateParts = ['', '', ''];
    if ($dateValue !== '') {
        try {
            $date = \Carbon\Carbon::parse($dateValue);
            $dateParts = [$date->format('d'), $date->format('m'), $date->format('Y')];
        } catch (\Throwable $exception) {
            $dateParts = [$dateValue, '', ''];
        }
    }
    $age = '';
    if (!empty($patient->fecha_nacimiento)) {
        try { $age = \Carbon\Carbon::parse($patient->fecha_nacimiento)->age; } catch (\Throwable $exception) { $age = ''; }
    }
    $sex = $patient->sexo ?? '';
    $sex = in_array((string) $sex, ['1', 'M', 'm', 'Masculino', 'MASCULINO'], true) ? 'M' : (in_array((string) $sex, ['2', 'F', 'f', 'Femenino', 'FEMENINO'], true) ? 'F' : $sex);
    $institution = $empresa->nombrecomercial ?? $empresa->razonsocial ?? 'RED PRIVADA COMPLEMENTARIA';
    $unicodigo = $empresa->unicodigo ?? $empresa->codigo ?? '';
    $state = !empty($entrySigned) ? 'FIRMADO' : ($entry ? 'BORRADOR SIN FIRMA' : 'FORMATO SIN LLENAR');
@endphp

<div class="footer">
    SNS-MSP/HCU-form. 017/2021
    <span class="right">PROTOCOLO QUIRÚRGICO (<span class="page-number"></span>)</span>
</div>

<section class="page first">
    <table class="masthead"><tr>
        <td class="brand"><!--AITROL_PDF_BRANDING_SLOT--><!--AITROL_PDF_BRANDING_FALLBACK_START--><span class="brand-fallback">{{ $institution }}</span><!--AITROL_PDF_BRANDING_FALLBACK_END--></td>
        <td class="title"></td>
        <td class="code">017<span class="sub">{{ $state }}</span></td>
    </tr></table>

    <table class="compact">
        <tr><th colspan="24" class="section">A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</th></tr>
        <tr>
            <th colspan="5" class="label center">INSTITUCIÓN DEL SISTEMA</th>
            <th colspan="3" class="label center">UNICÓDIGO</th>
            <th colspan="6" class="label center">ESTABLECIMIENTO DE SALUD</th>
            <th colspan="6" class="label center">NÚMERO DE HISTORIA CLÍNICA ÚNICA</th>
            <th colspan="4" class="label center">NÚMERO DE ARCHIVO</th>
        </tr>
        <tr>
            <td colspan="5" class="identity-value">RED PRIVADA COMPLEMENTARIA</td>
            <td colspan="3" class="identity-value">{{ $unicodigo }}</td>
            <td colspan="6" class="identity-value">{{ $institution }}</td>
            <td colspan="6" class="identity-value">{{ $solicitud->id_paciente }}</td>
            <td colspan="4" class="identity-value">{{ $solicitud->id }}</td>
        </tr>
        <tr>
            <th colspan="5" class="label center">PRIMER APELLIDO</th><th colspan="4" class="label center">SEGUNDO APELLIDO</th>
            <th colspan="4" class="label center">PRIMER NOMBRE</th><th colspan="4" class="label center">SEGUNDO NOMBRE</th>
            <th colspan="2" class="label center">SEXO</th><th colspan="2" class="label center">EDAD</th><th colspan="3" class="label center">CONDICIÓN EDAD</th>
        </tr>
        <tr>
            <td colspan="5" class="identity-value">{{ $patient->apellido1 ?? '' }}</td><td colspan="4" class="identity-value">{{ $patient->apellido2 ?? '' }}</td>
            <td colspan="4" class="identity-value">{{ $patient->nombre1 ?? '' }}</td><td colspan="4" class="identity-value">{{ $patient->nombre2 ?? '' }}</td>
            <td colspan="2" class="identity-value">{{ $sex }}</td><td colspan="2" class="identity-value">{{ $age }}</td><td colspan="3" class="identity-value">{{ $age !== '' ? 'A' : '' }}</td>
        </tr>
    </table>
    <div class="notice">{{ !empty($entrySigned) ? 'Documento clínico firmado. La evidencia aparece en la sección J.' : ($entry ? 'Datos guardados en el episodio. El borrador no acredita firma clínica.' : 'Campos clínicos vacíos. Complete y guarde el formulario desde Documentación.') }}</div>

    <table>
        <tr><th colspan="20" class="section">B. DIAGNÓSTICOS</th><th colspan="4" class="section center">CIE</th></tr>
        <tr><th colspan="4" class="label">Preoperatorio:</th><td colspan="16" class="value h-11">{{ $diagnosisDescription('diagnosticos_preoperatorios') }}</td><td colspan="4" class="value center">{{ $value('cie_preoperatorio') }}</td></tr>
        <tr><th colspan="4" class="label">Postoperatorio:</th><td colspan="16" class="value h-11">{{ $diagnosisDescription('diagnosticos_postoperatorios') }}</td><td colspan="4" class="value center">{{ $value('cie_postoperatorio') }}</td></tr>
    </table>
    <div class="gap"></div>

    <table>
        <tr>
            <th colspan="12" class="section">C. PROCEDIMIENTO</th>
            <th colspan="3" class="label choice-label">Electiva</th><td class="choice">{{ $mark('tipo_procedimiento', 'ELECTIVA') }}</td>
            <th colspan="3" class="label choice-label">Emergencia</th><td class="choice">{{ $mark('tipo_procedimiento', 'EMERGENCIA') }}</td>
            <th colspan="3" class="label choice-label">Urgencia</th><td class="choice">{{ $mark('tipo_procedimiento', 'URGENCIA') }}</td>
        </tr>
        <tr><th colspan="4" class="label">Proyectado:</th><td colspan="20" class="value h-8">{{ $value('procedimiento') }}</td></tr>
        <tr><th colspan="4" class="label">Realizado:</th><td colspan="20" class="value h-8">{{ $value('indicacion') }}</td></tr>
    </table>
    <div class="gap"></div>

    <table class="compact">
        <tr><th colspan="24" class="section">D. INTEGRANTES DEL EQUIPO QUIRÚRGICO</th></tr>
        <tr><th colspan="4" class="label">Cirujano 1:</th><td colspan="8" class="value">{{ $value('cirujano_1') }}</td><th colspan="4" class="label">Instrumentista:</th><td colspan="8" class="value">{{ $value('instrumentista') }}</td></tr>
        <tr><th colspan="4" class="label">Cirujano 2:</th><td colspan="8" class="value">{{ $value('cirujano_2') }}</td><th colspan="4" class="label">Circulante:</th><td colspan="8" class="value">{{ $value('circulante') }}</td></tr>
        <tr><th colspan="4" class="label">Primer ayudante:</th><td colspan="8" class="value">{{ $value('primer_ayudante') }}</td><th colspan="4" class="label">Anestesiólogo/a:</th><td colspan="8" class="value">{{ $value('anestesiologo') }}</td></tr>
        <tr><th colspan="4" class="label">Segundo ayudante:</th><td colspan="8" class="value">{{ $value('segundo_ayudante') }}</td><th colspan="4" class="label">Ayudante anestesia:</th><td colspan="8" class="value">{{ $value('ayudante_anestesia') }}</td></tr>
        <tr><th colspan="4" class="label">Tercer ayudante:</th><td colspan="8" class="value">{{ $value('tercer_ayudante') }}</td><th colspan="4" class="label">Otros:</th><td colspan="8" class="value">{{ $value('otros_equipo') }}</td></tr>
    </table>
    <div class="gap"></div>

    <table class="compact"><tr>
        <th colspan="12" class="section">E. TIPO ANESTESIA</th>
        <th colspan="2" class="label choice-label">General</th><td class="choice">{{ $mark('tipo_anestesia', 'GENERAL') }}</td>
        <th colspan="2" class="label choice-label">Regional</th><td class="choice">{{ $mark('tipo_anestesia', 'REGIONAL') }}</td>
        <th colspan="2" class="label choice-label">Sedación</th><td class="choice">{{ $mark('tipo_anestesia', ['SEDACION', 'SEDACIÓN']) }}</td>
        <th colspan="2" class="label choice-label">Otros</th><td class="choice">{{ $mark('tipo_anestesia', 'OTROS') }}</td>
    </tr></table>
    <div class="gap"></div>

    <table>
        <tr><th colspan="24" class="section">F. TIEMPOS QUIRÚRGICOS</th></tr>
        <tr><th colspan="6" class="label center">FECHA DE OPERACIÓN</th><th colspan="3" class="label center">DÍA</th><th colspan="3" class="label center">MES</th><th colspan="3" class="label center">AÑO</th><th colspan="4" class="label center">HORA DE INICIO</th><th colspan="5" class="label center">HORA DE TERMINACIÓN</th></tr>
        <tr><td colspan="6" class="value center">{{ $dateValue }}</td><td colspan="3" class="value center">{{ $dateParts[0] }}</td><td colspan="3" class="value center">{{ $dateParts[1] }}</td><td colspan="3" class="value center">{{ $dateParts[2] }}</td><td colspan="4" class="value center">{{ $value('hora_inicio') }}</td><td colspan="5" class="value center">{{ $value('hora_terminacion') }}</td></tr>
        <tr><th colspan="5" class="label">Diéresis:</th><td colspan="19" class="value h-6">{{ $value('dieresis') }}</td></tr>
        <tr><th colspan="5" class="label">Exposición y exploración:</th><td colspan="19" class="value h-8">{{ $value('exposicion_exploracion') }}</td></tr>
        <tr><th colspan="5" class="label">Hallazgos quirúrgicos:</th><td colspan="19" class="value h-14">{{ $value('hallazgos') }}</td></tr>
        <tr><th colspan="5" class="label">Procedimiento quirúrgico:</th><td colspan="19" class="value h-80">{{ $value('tecnica') }}</td></tr>
    </table>
</section>

<section>
    <table>
        <tr><th colspan="24" class="section">G. COMPLICACIONES DEL PROCEDIMIENTO QUIRÚRGICO</th></tr>
        <tr><td colspan="24" class="value h-17">{{ $value('complicaciones') }}</td></tr>
        <tr>
            <th colspan="5" class="label">Pérdida sanguínea total:</th><td colspan="2" class="value center">{{ $value('perdida_sanguinea_total') }}</td><td class="center">ml</td>
            <th colspan="4" class="label">Sangrado aproximado:</th><td colspan="2" class="value center">{{ $value('sangrado_aproximado') }}</td><td class="center">ml</td>
            <th colspan="5" class="label">Uso de material protésico:</th><th class="label center">Sí</th><td class="choice">{{ $mark('material_protesico', ['SI', 'SÍ']) }}</td><th class="label center">No</th><td class="choice">{{ $mark('material_protesico', 'NO') }}</td>
        </tr>
        <tr><th colspan="5" class="label">Descripción:</th><td colspan="19" class="value h-11">{{ $value('conclusion') }}</td></tr>
    </table>
    <div class="gap"></div>

    <table>
        <tr><th colspan="24" class="section">H. EXÁMENES HISTOPATOLÓGICOS</th></tr>
        <tr><th colspan="5" class="label">Transquirúrgico:</th><td colspan="19" class="value h-8">{{ $value('transquirurgico') }}</td></tr>
        <tr>
            <th colspan="6" class="label">Biopsia por congelación:</th><th colspan="2" class="label center">Sí</th><td class="choice">{{ $mark('biopsia_congelacion', ['SI', 'SÍ']) }}</td><th colspan="2" class="label center">No</th><td class="choice">{{ $mark('biopsia_congelacion', 'NO') }}</td>
            <th colspan="4" class="label">Resultado:</th><td colspan="8" class="value">{{ $value('resultado_biopsia') }}</td>
        </tr>
        <tr><th colspan="6" class="label">Patólogo que reporta:</th><td colspan="18" class="value h-6">{{ $value('patologo_reporta') }}</td></tr>
        <tr>
            <th colspan="5" class="label">Histopatológico:</th><th colspan="2" class="label center">Sí</th><td class="choice">{{ $mark('histopatologico', ['SI', 'SÍ']) }}</td><th colspan="2" class="label center">No</th><td class="choice">{{ $mark('histopatologico', 'NO') }}</td>
            <th colspan="4" class="label">Muestra:</th><td colspan="9" class="value">{{ $value('muestra') }}</td>
        </tr>
    </table>
    <div class="gap"></div>

    <table>
        <tr><th class="section">I. DIAGRAMA DEL PROCEDIMIENTO</th></tr>
        <tr><td class="value">
            @if(!empty($form017DiagramImages))
                <div class="diagram-gallery images-{{ count($form017DiagramImages) }}">
                    @foreach($form017DiagramImages as $diagramImage)
                        <span class="diagram-image"><img src="{{ $diagramImage }}" alt="Diagrama del procedimiento"></span>
                    @endforeach
                </div>
            @endif
            @if($value('diagrama') !== '')<div>{{ $value('diagrama') }}</div>@endif
            @if(empty($form017DiagramImages) && $value('diagrama') === '')<div class="h-40"></div>@endif
        </td></tr>
    </table>
    <div class="gap"></div>

    @php
        $professionalBlockTitle = 'J. DATOS DEL PROFESIONAL RESPONSABLE';
    @endphp
    @include('hospital.documentos._professional-block-pdf')
</section>
</body>
</html>
