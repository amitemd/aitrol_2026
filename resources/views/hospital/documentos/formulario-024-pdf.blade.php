<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Formulario 024 · Episodio {{ $solicitud->id }}</title>
    <style>
        @page { margin: 8mm 10mm 9mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: DejaVu Sans, sans-serif; font-size: 7pt; line-height: 1.28; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td, th { vertical-align: middle; }
        .page { page-break-before: always; }
        .page.first { page-break-before: auto; }
        .masthead { margin-bottom: 2mm; border: .7pt solid #111; }
        .masthead td { border-right: .7pt solid #111; }
        .masthead td:last-child { border-right: 0; }
        .brand { width: 22%; height: 16mm; text-align: center; color: #69205f; font-size: 11pt; font-weight: bold; letter-spacing: .8pt; }
        .title { font-size: 14pt; font-weight: bold; text-align: center; }
        .code { width: 18%; font-size: 7pt; }
        .code div { padding: 1.8mm 2mm; border-bottom: .6pt solid #111; }
        .code div:last-child { border-bottom: 0; }
        .box { border: .7pt solid #111; }
        .box td, .box th { padding: 1.25mm; }
        .band { background: #b887da; color: #17101f; font-weight: bold; }
        .band-dark { background: #454545; color: #fff; font-weight: bold; text-align: center; }
        .line { display: inline-block; min-height: 4mm; border-bottom: .55pt solid #111; }
        .identity th { border: .55pt solid #111; }
        .identity td { height: 8mm; border: .55pt solid #111; }
        .content { border: .7pt solid #111; border-top: 0; }
        .content td { padding: 1.25mm; border-bottom: .45pt solid #555; }
        .content tr:last-child td { border-bottom: 0; }
        .label { width: 30%; }
        .value { min-height: 5mm; }
        .diagram-title { padding: 1.2mm; border-right: .7pt solid #111; border-left: .7pt solid #111; font-weight: bold; }
        .diagram { height: 55mm; border: .7pt solid #111; text-align: center; }
        .diagram img { max-width: 94%; max-height: 52mm; }
        .section-table { margin-top: 2mm; border: .7pt solid #111; }
        .section-table td { padding: 1.4mm; border-bottom: .45pt solid #555; }
        .section-table tr:last-child td { border-bottom: 0; }
        .section-table .label { background: #b887da; }
        .patient-risk { height: 9mm; vertical-align: top; }
        .consent-box { margin-bottom: 2.2mm; border: .8pt solid #111; }
        .consent-box td { padding: 1.5mm 1.8mm; }
        .consent-title { display: inline-block; padding: .8mm 1.2mm; background: #b887da; font-weight: bold; }
        .consent-text { height: 34mm; text-align: justify; vertical-align: top; font-size: 7.3pt; line-height: 1.42; }
        .negative .consent-text { height: 22mm; }
        .revocation .consent-text { height: 19mm; }
        .signatures { table-layout: fixed; }
        .signatures td { padding: 0 3mm 1.2mm; text-align: center; font-size: 6pt; }
        .signature-line { height: 10mm; border-bottom: .55pt solid #111; }
        .compact-line { height: 7mm; border-bottom: .55pt solid #111; }
        .representative { padding-top: 1mm; font-size: 6.5pt; font-weight: bold; }
        .footer { position: fixed; right: 0; bottom: -5.5mm; left: 0; font-size: 5.5pt; }
        .footer .right { float: right; }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
@php
    $patient024 = $solicitud->paciente;
    $payload024 = [];
    foreach (\Sis_medico\Services\HospitalEpisodeFormCatalog::fields('024') as $key024 => $label024) {
        $payload024[$key024] = $entry ? ($entry->payload[$key024] ?? '') : ($emergencySource['suggestions'][$key024] ?? '');
    }
    if ($entry && isset($entry->payload['riesgos'])) $payload024['riesgos'] = $entry->payload['riesgos'];
    $document024 = \Sis_medico\Services\HospitalEpisodeForm024::forDisplay($payload024);
    $typeLabel024 = \Sis_medico\Services\HospitalEpisodeForm024::types()[$document024['tipo_consentimiento']];
    $imageFile024 = public_path(\Sis_medico\Services\HospitalEpisodeForm024::imageFor($document024['tipo_consentimiento']));
    $image024 = is_file($imageFile024) ? 'data:image/png;base64,'.base64_encode(file_get_contents($imageFile024)) : null;
    $imageName024 = basename($imageFile024);
    $state024 = $entry ? 'BORRADOR SIN FIRMA' : 'FORMATO SIN LLENAR';
    $age024 = '';
    if (!empty($patient024->fecha_nacimiento)) {
        try { $age024 = (new \DateTime($patient024->fecha_nacimiento))->diff(new \DateTime())->y; } catch (\Exception $e) { $age024 = ''; }
    }
    $fullName024 = trim(($patient024->apellido1 ?? '').' '.($patient024->apellido2 ?? '').' '.($patient024->nombre1 ?? '').' '.($patient024->nombre2 ?? ''));
@endphp

<div class="footer"><b>SNS-MSP / HCU-form.024 / 2021</b><span class="right"><b>CONSENTIMIENTO INFORMADO (<span class="page-number"></span>)</b></span></div>
<section class="page first">
    <table class="masthead"><tr>
        <td class="brand"><!--AITROL_PDF_BRANDING_SLOT--><!--AITROL_PDF_BRANDING_FALLBACK_START-->BORJA<br>CLINIC<!--AITROL_PDF_BRANDING_FALLBACK_END--></td>
        <td class="title">CONSENTIMIENTO INFORMADO</td>
        <td class="code"><div><b>Código:</b> FDT-23</div><div><b>Versión:</b> 1</div></td>
    </tr></table>

    <table class="box">
        <tr><td colspan="12" class="band">CONSENTIMIENTO INFORMADO PARA: &nbsp; {{ mb_strtoupper($typeLabel024, 'UTF-8') }}</td></tr>
        <tr><td colspan="6">Establecimiento de salud: <span class="line" style="width:58%">BORJA CLINIC</span></td><td colspan="3">Fecha: <span class="line" style="width:64%">{{ $document024['fecha'] }}</span></td><td colspan="3">Hora: <span class="line" style="width:64%">{{ $document024['hora'] }}</span></td></tr>
        <tr><td colspan="6">Área/Servicio: <span class="line" style="width:64%;text-align:center">ANESTESIOLOGÍA</span></td><td colspan="6">Cédula de ciudadanía: <span class="line" style="width:55%">{{ $solicitud->id_paciente }}</span></td></tr>
    </table>
    <table class="identity">
        <tr><th class="band-dark">Apellido paterno</th><th class="band-dark">Apellido materno</th><th colspan="2" class="band-dark">Nombres</th><th style="width:10%" class="band-dark">Edad</th></tr>
        <tr><td>{{ $patient024->apellido1 ?? '' }}</td><td>{{ $patient024->apellido2 ?? '' }}</td><td colspan="2">{{ trim(($patient024->nombre1 ?? '').' '.($patient024->nombre2 ?? '')) }}</td><td style="text-align:center">{{ $age024 }}</td></tr>
    </table>
    <table class="content">
        <tr><td colspan="6">Tipo de atención: &nbsp; Ambulatoria [{{ $document024['tipo_atencion'] === 'AMBULATORIA' ? 'X' : ' ' }}]</td><td colspan="6">Hospitalización [{{ $document024['tipo_atencion'] === 'HOSPITALIZACION' ? 'X' : ' ' }}]</td></tr>
        <tr><td colspan="9">Diagnóstico (codificación CIE-10): {{ $document024['diagnostico'] }}</td><td colspan="3">{{ $document024['cie10'] }}</td></tr>
        <tr><td class="label">Procedimiento recomendado:</td><td colspan="11" class="band" style="text-align:center">{{ $document024['procedimiento'] }}</td></tr>
        <tr><td class="label">¿En qué consiste?</td><td colspan="11" class="value">{{ $document024['consiste'] }}</td></tr>
        <tr><td class="label">¿Cómo se realiza?</td><td colspan="11" class="value">{{ $document024['realiza'] }}</td></tr>
    </table>
    <div class="diagram-title">Gráfico de la intervención (facilita la comprensión del paciente):</div>
    <div class="diagram">@if($image024)<img src="{{ $image024 }}" data-source="{{ $imageName024 }}" alt="Esquema de {{ mb_strtolower($typeLabel024, 'UTF-8') }}">@endif</div>
    <table class="section-table">
        <tr><td class="label">Duración estimada de la intervención:</td><td>{{ $document024['duracion_estimada'] }}</td></tr>
        <tr><td class="label">Beneficios del procedimiento:</td><td>{{ $document024['beneficios'] }}</td></tr>
        <tr><td class="label">Riesgos frecuentes (poco graves):</td><td>{{ $document024['riesgos_frecuentes'] }}</td></tr>
        <tr><td class="label">Riesgos poco frecuentes (graves):</td><td>{{ $document024['riesgos_graves'] }}</td></tr>
        <tr><td class="label">Riesgos específicos relacionados con el paciente:</td><td class="patient-risk">{{ $document024['riesgos_especificos'] }}</td></tr>
        <tr><td class="label">Alternativas al procedimiento:</td><td>{{ $document024['alternativas'] }}</td></tr>
        <tr><td class="label">Manejo posterior al procedimiento:</td><td>{{ $document024['manejo_posterior'] }}</td></tr>
        <tr><td class="label">Consecuencias si no se realiza:</td><td>{{ $document024['consecuencias'] }}</td></tr>
    </table>
</section>

<section class="page">
    <table class="masthead"><tr>
        <td class="brand"><!--AITROL_PDF_BRANDING_SLOT--><!--AITROL_PDF_BRANDING_FALLBACK_START-->BORJA<br>CLINIC<!--AITROL_PDF_BRANDING_FALLBACK_END--></td>
        <td class="title">CONSENTIMIENTO INFORMADO</td>
        <td class="code"><div><b>Código:</b> FDT-23</div><div><b>Versión:</b> 1</div></td>
    </tr></table>

    <table class="consent-box"><tr><td><span class="consent-title">DECLARACIÓN DE CONSENTIMIENTO INFORMADO</span></td><td style="width:24%">Fecha: {{ $document024['fecha'] }}</td><td style="width:19%">Hora: {{ $document024['hora'] }}</td></tr>
        <tr><td colspan="3" class="consent-text">He facilitado la información completa que conozco y me ha sido solicitada sobre mis antecedentes personales, familiares y mi estado de salud. Soy consciente de que omitir estos datos puede afectar los resultados del tratamiento. Estoy de acuerdo con el procedimiento propuesto; he sido informado de sus ventajas e inconvenientes y se me ha explicado de forma clara en qué consiste, sus beneficios y posibles riesgos. He escuchado, leído y comprendido la información recibida y he tenido oportunidad de realizar preguntas. Autorizo consciente y libremente el procedimiento y, si el profesional lo considera necesario para mi beneficio, las actuaciones adicionales justificadas. Conozco que puedo retirar este consentimiento cuando lo estime oportuno.@if($document024['observaciones'])<br><b>Observaciones:</b> {{ $document024['observaciones'] }}@endif</td></tr>
        <tr><td colspan="3"><table class="signatures"><tr><td><div class="signature-line">{{ $fullName024 }}</div>Nombre completo del paciente</td><td><div class="signature-line">{{ $solicitud->id_paciente }}</div>Cédula de ciudadanía</td><td><div class="signature-line"></div>Firma del paciente o huella</td></tr><tr><td colspan="2"><div class="signature-line"></div>Nombre del profesional que realiza el procedimiento</td><td><div class="signature-line"></div>Firma, sello y código profesional</td></tr></table><div class="representative">Si el paciente no está en capacidad para firmar:</div><table class="signatures"><tr><td><div class="compact-line"></div>Nombre del representante legal</td><td><div class="compact-line"></div>Cédula de ciudadanía</td><td><div class="compact-line"></div>Firma del representante legal</td></tr><tr><td colspan="2" style="text-align:left">Parentesco: ______________________________</td><td></td></tr></table></td></tr>
    </table>

    <table class="consent-box negative"><tr><td><span class="consent-title">NEGATIVA DEL CONSENTIMIENTO INFORMADO</span></td></tr><tr><td class="consent-text">Una vez que he entendido claramente el procedimiento propuesto y las posibles consecuencias si no se realiza, no autorizo y me niego a que se me practique. Desvinculo de responsabilidades futuras al establecimiento de salud y al profesional sanitario que me atiende por no realizar la intervención sugerida.</td></tr><tr><td><table class="signatures"><tr><td><div class="compact-line"></div>Nombre completo del paciente</td><td><div class="compact-line"></div>Cédula de ciudadanía</td><td><div class="compact-line"></div>Firma del paciente o huella</td></tr><tr><td colspan="2"><div class="compact-line"></div>Nombre del profesional que realiza el procedimiento</td><td><div class="compact-line"></div>Firma, sello y código profesional</td></tr></table><div class="representative">Si el paciente no está en capacidad para firmar:</div><table class="signatures"><tr><td><div class="compact-line"></div>Nombre del representante legal</td><td><div class="compact-line"></div>Cédula de ciudadanía</td><td><div class="compact-line"></div>Firma del representante legal</td></tr></table></td></tr></table>

    <table class="consent-box revocation"><tr><td><span class="consent-title">REVOCATORIA DE CONSENTIMIENTO INFORMADO</span></td></tr><tr><td class="consent-text">De forma libre y voluntaria, revoco el consentimiento realizado en fecha: __________________ y manifiesto expresamente mi deseo de no continuar con el procedimiento médico, que doy por finalizado en esta fecha: __________________. Declaro haber sido informado de las consecuencias de esta decisión.</td></tr><tr><td><table class="signatures"><tr><td><div class="compact-line"></div>Nombre completo del paciente</td><td><div class="compact-line"></div>Cédula de ciudadanía</td><td><div class="compact-line"></div>Firma del paciente o huella</td></tr></table><div class="representative">Si el paciente no está en capacidad para firmar:</div><table class="signatures"><tr><td><div class="compact-line"></div>Nombre del representante legal</td><td><div class="compact-line"></div>Cédula de ciudadanía</td><td><div class="compact-line"></div>Firma del representante legal</td></tr></table></td></tr></table>
</section>
</body>
</html>
