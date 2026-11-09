<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Certificado médico</title>
    <style>
        @page { size: letter portrait; margin: 28px 40px 60px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; line-height: 1.25; }
        .certificate { width: 100%; }
        .header { width: 100%; border-collapse: collapse; margin-bottom: 3px; }
        .header td { vertical-align: bottom; }
        .brand-cell { width: 48%; height: 88px; }
        .brand-logo { display: block; width: 245px; max-height: 82px; object-fit: contain; object-position: left center; }
        .issue-cell { width: 42%; padding: 0 8px 4px; text-align: right; font-weight: 700; font-size: 10px; }
        .iess-cell { width: 10%; text-align: center; }
        .iess-logo { display: block; width: 58px; height: 72px; margin: 0 auto; object-fit: contain; }
        .title { margin: 0 0 5px; text-align: center; font-size: 11px; font-weight: 700; }
        table.form-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .form-table th, .form-table td { border: 1px solid #111; height: 20px; padding: 3px 5px; vertical-align: middle; }
        .form-table th { background: #d9d9d9; font-weight: 400; text-align: center; }
        .form-table td { font-size: 9px; }
        .form-table .value { text-align: center; font-weight: 700; text-transform: uppercase; }
        .form-table .left { text-align: left; }
        .form-table .tall { height: 25px; vertical-align: top; }
        .line-data { margin: 6px 3px 0; font-size: 9px; font-weight: 700; }
        .line-data .answer { font-weight: 400; }
        .checks { letter-spacing: .05px; }
        .section-gap { height: 5px; }
        .diagnosis-cell { min-height: 21px; }
        .signature { margin: 4px 4px 0; font-size: 9px; line-height: 1.08; }
        .signature .physician-data { margin: 0; padding: 0; font-weight: 700; }
        .signature-space { height: 58px; }
        .many-diagnoses .procedure-spacer { height: 12px; }
        .many-diagnoses .signature-space { height: 34px; }
        .many-diagnoses .signature { line-height: 1.08; }
        .signature-image { display: block; width: 210px; height: 78px; object-fit: contain; object-position: left bottom; margin: 0 0 -2px; }
        .footer { position: fixed; right: 40px; bottom: 2px; left: 40px; padding-top: 3px; border-top: 1px solid #777; color: #333; font-size: 6.3px; line-height: 1; text-align: center; white-space: nowrap; }
        @media screen {
            body { background: #eef3f5; padding: 24px; }
            .certificate { width: 816px; min-height: 1056px; margin: 0 auto; padding: 38px 52px; background: #fff; box-shadow: 0 10px 35px rgba(20, 45, 58, .16); }
        }
        @media print { body { background: #fff; } }
    </style>
</head>
<body>
@php
    $clean = function ($value) {
        $value = trim((string) $value);
        return $value === '(N/A)' ? '' : $value;
    };
    $upper = function ($value) {
        return mb_strtoupper(trim((string) $value), 'UTF-8');
    };
    $formatDate = function ($value) {
        return $value ? date('d/m/Y', strtotime($value)) : '';
    };
    $months = array(1 => 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
    $dateTimestamp = strtotime($data['date']);
    $companyName = $clean($company->nombrecomercial) ?: $clean($company->razonsocial);
    $city = $clean($company->ciudad) ?: 'GUAYAQUIL';
    $firstNames = trim($clean($patient->nombre1).' '.$clean($patient->nombre2));
    $lastNames = trim($clean($patient->apellido1).' '.$clean($patient->apellido2));
    $patientPhone = $clean($patient->telefono1) ?: $clean($patient->telefono2);
    $patientAddress = $clean($patient->direccion);
    $patientJob = $clean($patient->ocupacion);
    $historyNumber = isset($patient->hcid) && $patient->hcid ? $patient->hcid : $patient->id;
    $procedureCode = isset($agenda->id_procedimiento) && $agenda->id_procedimiento ? $agenda->id_procedimiento : '';
    $physicianName = trim($clean($physician->apellido1).' '.$clean($physician->apellido2).' '.$clean($physician->nombre1).' '.$clean($physician->nombre2));
    $logoPath = public_path('assets/images/logo_certificado_borja.png');
    $logoSource = is_file($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : '';
    $iessLogoPath = public_path('assets/images/logo_iess.png');
    $iessLogoSource = is_file($iessLogoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($iessLogoPath)) : '';
    $signaturePath = $signature && !empty($signature->nombre)
        ? app(\Sis_medico\Services\DoctorSignatureService::class)->pathForFilename($signature->nombre) : '';
    $signatureSource = $signaturePath && is_file($signaturePath) ? 'data:image/'.pathinfo($signaturePath, PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($signaturePath)) : '';
    $diagnosisLines = preg_split('/\r\n|\r|\n/', trim($data['diagnosis']));
    $diagnosisCodeLines = preg_split('/\r\n|\r|\n/', trim(isset($data['diagnosisCode']) ? $data['diagnosisCode'] : ''));
    $diagnoses = array();
    $diagnosisCount = max(count($diagnosisLines), count($diagnosisCodeLines));
    for ($diagnosisIndex = 0; $diagnosisIndex < $diagnosisCount; $diagnosisIndex++) {
        $line = isset($diagnosisLines[$diagnosisIndex]) ? trim($diagnosisLines[$diagnosisIndex]) : '';
        $code = isset($diagnosisCodeLines[$diagnosisIndex]) ? trim($diagnosisCodeLines[$diagnosisIndex]) : '';
        $line = trim($line);
        if ($line === '' && $code === '') continue;
        $diagnoses[] = array('code' => $code, 'description' => $line);
    }
    if (!$diagnoses) $diagnoses[] = array('code' => '', 'description' => '');
    $restTypes = isset($data['restTypes']) ? (array)$data['restTypes'] : array();
    $causes = isset($data['causes']) ? (array)$data['causes'] : array();
    $hasMedicalRest = !empty($data['days']);
    $companyPhones = array_filter(array($clean(isset($company->telefono1) ? $company->telefono1 : ''), $clean(isset($company->telefono2) ? $company->telefono2 : '')));
    $companyPhoneText = implode(' / ', $companyPhones);
    $companyEmail = $clean(isset($company->email) ? $company->email : '');
    $companyAddress = $clean(isset($company->direccion) ? $company->direccion : '');
@endphp

<main class="certificate{{count($diagnoses)>2?' many-diagnoses':''}}">
    <table class="header">
        <tr>
            <td class="brand-cell">
                @if($logoSource)<img class="brand-logo" src="{{$logoSource}}" alt="Borja Clinic">@else<strong>BORJA CLINIC</strong>@endif
            </td>
            <td class="issue-cell">
                {{$upper($city)}}, {{date('d', $dateTimestamp)}} DE {{$months[(int) date('n', $dateTimestamp)]}} DEL {{date('Y', $dateTimestamp)}}
            </td>
            <td class="iess-cell">
                @if($iessLogoSource)<img class="iess-logo" src="{{$iessLogoSource}}" alt="IESS">@else<strong>IESS</strong>@endif
            </td>
        </tr>
    </table>

    <h1 class="title">CERTIFICADO MEDICO</h1>

    <table class="form-table">
        <colgroup><col style="width:33%"><col style="width:34%"><col style="width:33%"></colgroup>
        <tr><th>UNIDAD OPERATIVA</th><th>CIUDAD</th><th>TIPO DE AFILIADO</th></tr>
        <tr><td class="value">{{$companyName}}</td><td class="value">{{$city}}</td><td class="value">IESS</td></tr>
    </table>

    <table class="form-table">
        <colgroup><col style="width:28%"><col style="width:28%"><col style="width:20%"><col style="width:24%"></colgroup>
        <tr><th>NOMBRES</th><th>APELLIDOS</th><th>HISTORIA CLINICA</th><th>NUMERO DE CEDULA</th></tr>
        <tr><td class="value">{{$firstNames}}</td><td class="value">{{$lastNames}}</td><td class="value">{{$historyNumber}}</td><td class="value">{{$patient->id}}</td></tr>
    </table>

    <table class="form-table">
        <colgroup><col style="width:81%"><col style="width:19%"></colgroup>
        <tr><th class="left">PROCEDIMIENTO REALIZADO</th><th>CODIGO</th></tr>
        <tr><td class="left tall">{{$upper($data['observation'])}}</td><td class="value tall">{{$procedureCode}}</td></tr>
        <tr><td class="tall procedure-spacer"></td><td class="tall procedure-spacer"></td></tr>
        <tr><td class="tall procedure-spacer"></td><td class="tall procedure-spacer"></td></tr>
    </table>

    <div class="line-data">PUESTO DE TRABAJO: <span class="answer">{{$upper($patientJob)}}</span></div>
    <div class="line-data">INSTITUCION/ EMPRESA: <span class="answer">{{$upper($data['institution'])}}</span></div>
    <div class="line-data">DIRECCION: <span class="answer">{{$upper($patientAddress)}}</span></div>
    <div class="line-data">NUMERO DE CELULAR: <span class="answer">{{$patientPhone}}</span></div>
    <div class="line-data checks">PRESENTA SINTOMAS: SI ({{$data['symptoms']==='si'?'X':' '}}) NO ({{$data['symptoms']==='no'?'X':' '}})</div>
    <div class="line-data checks">AISLAMIENTO ({{in_array('aislamiento',$restTypes,true)?'X':' '}}), REPOSO ABSOLUTO ({{in_array('reposo_absoluto',$restTypes,true)?'X':' '}}), TELETRABAJO ({{in_array('teletrabajo',$restTypes,true)?'X':' '}}).</div>
    <div class="line-data checks">CAUSA: ENFERMEDAD GENERAL ({{in_array('enfermedad_general',$causes,true)?'X':' '}}), ACCIDENTE TRABAJO ({{in_array('accidente_trabajo',$causes,true)?'X':' '}}), ACCIDENTE DE TRANSITO ({{in_array('accidente_transito',$causes,true)?'X':' '}}), MATERNIDAD ({{in_array('maternidad',$causes,true)?'X':' '}}), ENF. CATASTROFICA ({{in_array('e_catastrofica',$causes,true)?'X':' '}}).</div>

    <div class="section-gap"></div>
    <table class="form-table">
        <tr><th class="left">ANAMNESIS</th></tr>
        <tr><td class="left tall">{{$data['disease']}}@if($data['symptoms']==='si' && $data['symptomsDetail']) - {{$data['symptomsDetail']}}@endif</td></tr>
    </table>

    <table class="form-table">
        <colgroup><col style="width:80%"><col style="width:20%"></colgroup>
        <tr><th class="left">DIAGNOSTICO MEDICO</th><th>CIE 10</th></tr>
        <?php foreach ($diagnoses as $diagnosis): ?>
            <tr><td class="left diagnosis-cell">{{$upper($diagnosis['description'])}}</td><td class="value diagnosis-cell">{{$diagnosis['code']}}</td></tr>
        <?php endforeach; ?>
    </table>

    <table class="form-table">
        <colgroup><col style="width:35%"><col style="width:37%"><col style="width:28%"></colgroup>
        <tr><th>FECHA ATENCION POR EMERGENCIA</th><th>FECHA INTERVENCION QUIRURGICA</th><th>FECHA ALTA HOSPITALARIA</th></tr>
        <tr>
            <td class="value">{{(int)$agenda->proc_consul===1?'':$formatDate($data['date'])}}</td>
            <td class="value">{{(int)$agenda->proc_consul===1?$formatDate($data['date']):''}}</td>
            <td class="value"></td>
        </tr>
    </table>

    <table class="form-table">
        <colgroup><col style="width:22%"><col style="width:30%"><col style="width:48%"></colgroup>
        <tr><th>DESCANSO MEDICO</th><th>DIAS DE REPOSO</th><th>FECHA</th></tr>
    </table>
    <table class="form-table">
        <colgroup><col style="width:11%"><col style="width:11%"><col style="width:15%"><col style="width:15%"><col style="width:24%"><col style="width:24%"></colgroup>
        <tr><th>SI</th><th>NO</th><th>NUMEROS</th><th>LETRAS</th><th>DESDE</th><th>HASTA</th></tr>
        <tr><td class="value">{{$hasMedicalRest?'X':''}}</td><td class="value">{{$hasMedicalRest?'':'X'}}</td><td class="value">{{$data['days']?:''}}</td><td class="value">{{$data['days']?$upper($data['daysInWords']):''}}</td><td class="value">{{$data['days']?$formatDate($data['date']):''}}</td><td class="value">{{$data['days']?$formatDate($data['endDate']):''}}</td></tr>
    </table>

    <div class="signature">
        @if($signatureSource)<img class="signature-image" src="{{$signatureSource}}" alt="Firma del profesional">@else<div class="signature-space"></div>@endif
        <div class="physician-data">DR(A). {{$upper($physicianName)}}</div>
        <div class="physician-data">CEDULA: {{$physician->id}}</div>
        <div class="physician-data">{{$upper($specialty ? $specialty->nombre : 'ESPECIALIDAD')}}</div>
    </div>
</main>
<footer class="footer">
    {{$upper($companyName)}}@if($companyAddress) - {{$companyAddress}}@endif @if($companyPhoneText) | TELEFONOS: {{$companyPhoneText}}@endif @if($companyEmail) | CORREO: {{$companyEmail}}@endif
</footer>
</body>
</html>
