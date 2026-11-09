<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta entrega recepción de servicios de salud</title>
    <style>
        @page { size: 612pt 855pt; margin: 18pt 50pt 16pt 50pt; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 9pt; line-height: 1.18; }
        p { margin: 0; }
        .header { height: 60pt; }
        .header td { border: 0; padding: 0; text-align: center; vertical-align: middle; }
        .header-logo { height: 37pt; }
        .header img { max-width: 85pt; max-height: 33pt; }
        .company { height: 23pt; font-size: 12pt; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        .grid td { border: .75pt solid #000; height: 12pt; padding: 1.3pt 5.4pt; vertical-align: middle; }
        .document-title { height: 16pt !important; padding: 1pt 5.4pt !important; font-size: 12pt; font-weight: bold; text-align: center; }
        .split-left { width: 50%; }
        .label { font-weight: bold; }
        .patient-grid { margin-top: 12pt; }
        .patient-grid td { height: 15pt; }
        .section-title { margin: 2pt 0 6pt; font-size: 12pt; font-weight: bold; text-align: center; }
        .observation { height: 39pt !important; padding-top: 4pt !important; vertical-align: top !important; }
        .observation p:first-child { margin-bottom: 9pt; }
        .legal { margin: 2pt 8pt 5pt; font-size: 9pt; font-weight: bold; line-height: 1.18; text-align: justify; }
        .quote { margin-top: 2pt; border: .75pt solid #000; padding: 2pt 5pt; font-size: 9pt; font-weight: bold; line-height: 1.12; text-align: left; }
        .provider-signature { margin-top: 72pt; font-size: 10pt; font-weight: bold; }
        .signature-line { white-space: nowrap; }
        .provider-info { width: 255pt; }
        .provider-info td { border: 0; padding: 0; text-align: center; }
        .reception-title { margin-top: 18pt; font-size: 12pt; font-weight: bold; text-align: center; text-decoration: underline; }
        .date { margin-top: 14pt; font-size: 10pt; }
        .patient-signature { margin-top: 62pt; font-size: 10pt; font-weight: bold; }
        .representative-box { height: 84pt; margin-top: 16pt; overflow: hidden; border: .75pt solid #000; padding: 3pt 5pt; font-size: 9pt; line-height: 1.12; }
        .representative-line { margin-top: 6pt; }
        .representative-help { margin-top: 1pt; font-family: "Times New Roman", Times, serif; font-size: 8pt; font-weight: bold; }
        .certification { margin-top: 13pt; }
        .certification-title { margin-bottom: 2pt; font-size: 11pt; font-weight: bold; }
        .certification-text { font-size: 8pt; line-height: 1.15; text-align: justify; }
        .responsibility { margin-top: 8pt; font-size: 8pt; font-weight: bold; }
    </style>
</head>
<body>
    @php
        $upper = function ($value, $empty = '') {
            $value = trim((string) $value);
            return $value === '' ? $empty : mb_strtoupper($value, 'UTF-8');
        };
        $providerDisplay = $upper($providerName);
        $contactDisplay = trim((string)$providerContact) !== '' ? $providerContact : $providerName;
        $diagnosticDisplay = trim((string)$diagnosis) !== '' ? $diagnosis : '';
        $representativeDisplay = trim((string)$representativeName) !== '' ? $upper($representativeName) : '........................................................';
        $relationshipDisplay = trim((string)$representativeRelationship) !== '' ? $representativeRelationship : 'Padre/Madre';
    @endphp

    <table class="header">
        <tr><td class="header-logo">@if($logoData)<img src="{{ $logoData }}" alt="Logo de la empresa">@endif</td></tr>
        <tr><td class="company">{{ $providerDisplay }}</td></tr>
    </table>

    <table class="grid">
        <tr><td colspan="2" class="document-title">ACTA ENTREGA RECEPCIÓN DE SERVICIOS DE SALUD</td></tr>
        <tr><td colspan="2"><span class="label">PRESTADOR:</span> {{ $providerDisplay }}</td></tr>
        <tr><td colspan="2"><span class="label">PERSONA DE CONTACTO:</span> {{ $contactDisplay }}</td></tr>
        <tr>
            <td class="split-left"><span class="label">TELEFONO:</span> {{ $providerPhone }}</td>
            <td><span class="label">E-MAIL:</span> {{ $providerEmail }}</td>
        </tr>
        <tr>
            <td class="split-left"><span class="label">MES Y AÑO DE PRESTACION:</span> {{ ucfirst(strtolower($serviceMonth)) }} / {{ $serviceYear }}</td>
            <td><span class="label">CODIGO DE CIE 10:</span> {{ $diagnosticDisplay }}</td>
        </tr>
        <tr><td colspan="2"><span class="label">NUMERO DE CODIGO DE VALIDACION:</span> {{ $validationCode }}</td></tr>
        <tr><td colspan="2"><span class="label">NUMERO DE HISTORIA CLINICA:</span> {{ $historyNumber }}</td></tr>
        <tr><td colspan="2"><span class="label">SERVICIO ENTREGADO:</span> {{ $serviceType }}</td></tr>
    </table>

    <table class="grid patient-grid">
        <tr><td><span class="label">NOMBRE DEL PACIENTE (APELLIDOS Y NOMBRES):</span> {{ $upper($patientName) }}</td></tr>
        <tr><td><span class="label">N° DE CEDULA DE IDENTIDAD:</span> {{ $patient->id }}</td></tr>
    </table>

    <div class="section-title">ACUSE ENTREGA DEL SERVICIO</div>
    <table class="grid">
        <tr><td class="observation"><p class="label">OBSERVACIONES</p><p>(Si tuviera alguna observación llene este campo, caso contrario deje en blanco)</p></td></tr>
    </table>

    <div class="legal">
        Como prestador de la RPIS, conozco el cumplimiento obligatorio del TPSNS y sus procedimientos que están regulados
        en el presente Reglamento de relacionamiento. Además, tengo conocimiento del acápite que refiere a la Coordinación
        de pagos y tarifas que indica textualmente:
    </div>
    <div class="quote">
        "En caso de objeción o débito, el presentador no podrá requerir el pago al usuario/paciente, familiares o acompañante.
        Cualquier cobro en este sentido será motivo de la sanción que la Ley prevea"
    </div>

    <div class="provider-signature">
        <div class="signature-line">__________________________________ (firma)</div>
        <table class="provider-info"><tr><td>{{ $contactDisplay }}<br>C.I. {{ $providerDocument }}</td></tr></table>
    </div>

    <div class="reception-title">ACUSE RECEPCION DEL SERVICIO</div>
    <div class="date">Ciudad: {{ ucfirst(strtolower($city)) }}, a los ______ Días del mes de <strong>{{ ucfirst(strtolower($serviceMonth)) }}</strong> del año <strong>{{ $serviceYear }}</strong></div>

    <div class="patient-signature">
        <div class="signature-line">__________________________________(firma).</div>
        <div>{{ $upper($patientName) }}</div>
    </div>

    <div class="representative-box">
        <strong>Observaciones:</strong> Yo <strong>{{ $representativeDisplay }}</strong> en mi calidad de
        <strong>{{ $relationshipDisplay }}</strong> y/o representante o acompañante, del paciente
        <strong>{{ $upper($patientName) }}</strong> certifico que el mencionado paciente recibió el servicio registrado en el presente acta.
        <div class="representative-line">_________________________________________</div>
        <div>Firma {{ $representativeDisplay }}</div>
        <div class="representative-help">(utilizar este campo solo cuando el usuario/paciente no pueda registrar su firma)</div>
    </div>

    <div class="certification">
        <div class="certification-title">CERTIFICACION DE FIRMAS:</div>
        <div class="certification-text">
            En mi calidad de prestador de servicios, certifico que las firmas constantes en el presente documento,
            corresponden a la firma del paciente o representante de ser el caso, misma que fue receptada en este centro
            de atención, por lo tanto me responsabilizo por contenido de dicho certificado, asumiendo toda la responsabilidad
            tanto administrativa civil o penal por la veracidad de la información entregada.
        </div>
        <div class="responsibility">toda la responsabilidad tanto administrativa civil o penal por la veracidad de la información entregada</div>
    </div>
</body>
</html>
