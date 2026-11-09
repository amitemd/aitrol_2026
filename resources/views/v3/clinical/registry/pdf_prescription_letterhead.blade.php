<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Receta médica</title>
    <style>
        @page { margin: 42mm 29mm 23mm 29mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #000; font-family: DejaVu Sans, sans-serif; font-size: 9px; line-height: 1.35; }
        #header { position: fixed; top: -38mm; left: 0; right: 0; height: 35mm; }
        #footer { position: fixed; bottom: -18mm; left: 0; right: 0; height: 14mm; color: #000; font-size: 7.3px; }
        .header-rule, .footer-rule { border-top: 1.4px solid #000; height: 1px; }
        .header-table, .footer-table { width: 100%; border-collapse: collapse; }
        .header-logo { width: 56px; max-height: 35px; object-fit: contain; }
        .header-brand { padding-left: 10px; vertical-align: middle; }
        .header-brand strong { display: block; font-size: 12px; letter-spacing: .35px; text-transform: uppercase; }
        .header-brand span { display: block; font-size: 7px; margin-top: 2px; }
        .header-doctor { width: 38%; text-align: right; vertical-align: middle; }
        .header-doctor strong { display: block; font-size: 9px; text-transform: uppercase; }
        .header-doctor span { display: block; font-size: 7px; margin-top: 2px; }
        .watermark { position: fixed; z-index: -1; top: 41%; left: 17%; width: 66%; text-align: center; opacity: .055; }
        .watermark img { max-width: 100%; max-height: 230px; }
        .watermark-text { font-size: 48px; font-weight: bold; letter-spacing: 6px; color: #000; transform: rotate(-20deg); }
        .patient-box { border: 1px solid #000; padding: 8px 10px 7px; margin-bottom: 13px; }
        .patient-title { font-size: 8px; font-weight: bold; letter-spacing: 1px; margin-bottom: 6px; text-transform: uppercase; }
        .patient-table { width: 100%; border-collapse: collapse; }
        .patient-table td { vertical-align: top; padding-right: 12px; }
        .label { display: block; font-size: 6.7px; font-weight: bold; letter-spacing: .55px; text-transform: uppercase; }
        .value { display: block; font-size: 9px; font-weight: bold; margin-top: 2px; text-transform: uppercase; }
        .rx-title { font-family: DejaVu Sans, sans-serif; font-size: 20px; font-weight: bold; margin: 0 0 11px; }
        .rx-title small { font-size: 8px; font-weight: normal; margin-left: 8px; letter-spacing: .7px; vertical-align: middle; }
        .medicines { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .medicines th { border-bottom: 1px solid #000; font-size: 7px; letter-spacing: .65px; padding: 0 0 5px; text-align: left; text-transform: uppercase; }
        .medicines td { border-bottom: .4px solid #aaa; padding: 7px 4px 7px 0; vertical-align: top; }
        .medicines td:first-child { width: 54%; font-weight: bold; }
        .notes { margin-top: 10px; min-height: 80px; }
        .notes-title { font-size: 7px; font-weight: bold; letter-spacing: .6px; margin-bottom: 5px; text-transform: uppercase; }
        .notes-content { font-size: 10px; line-height: 1.45; }
        .signature { margin-top: 17px; text-align: center; width: 400px; margin-left: auto; page-break-inside: avoid; }
        .signature-image { width: 400px; height: 150px; text-align: center; }
        .signature-line { border-top: 1px solid #000; margin: 0 auto; width: 400px; }
        .signature strong { display: block; font-size: 8px; margin-top: 4px; text-transform: uppercase; }
        .signature span { display: block; font-size: 7px; margin-top: 2px; }
        .footer-table td { vertical-align: middle; }
        .footer-left { width: 50%; }
        .footer-right { text-align: right; }
        .footer-title { display: block; font-size: 7.5px; font-weight: bold; text-transform: uppercase; }
        .footer-muted { display: block; margin-top: 1px; }
        .empty-row { color: #777; font-style: italic; }
    </style>
</head>
<body>
@php
    $patientName = trim(implode(' ', array_filter(array($paciente->nombre1, $paciente->nombre2, $paciente->apellido1, $paciente->apellido2), function ($part) {
        return !empty($part) && $part !== '(N/A)';
    })));
    $doctorName = $doctor ? trim(implode(' ', array_filter(array($doctor->nombre1, $doctor->nombre2, $doctor->apellido1, $doctor->apellido2)))) : '';
    $companyName = !empty($logo_emp) && !empty($logo_emp->nombrecomercial) ? $logo_emp->nombrecomercial : (!empty($logo_emp) && !empty($logo_emp->razonsocial) ? $logo_emp->razonsocial : 'AITROL');
    $companyAddress = !empty($logo_emp) && !empty($logo_emp->direccion) ? $logo_emp->direccion : '';
    $companyPhone = !empty($logo_emp) && !empty($logo_emp->telefono1) ? $logo_emp->telefono1 : (!empty($logo_emp) && !empty($logo_emp->telefono) ? $logo_emp->telefono : '');
    $issueDate = !empty($receta->created_at) ? \Carbon\Carbon::parse($receta->created_at)->format('d/m/Y') : date('d/m/Y');
    $signaturePath = !empty($firma) && !empty($firma->nombre)
        ? app(\Sis_medico\Services\DoctorSignatureService::class)->pathForFilename($firma->nombre) : null;
    $signatureWidth = 0;
    $signatureHeight = 0;
    if ($signaturePath && ($signatureSize = @getimagesize($signaturePath)) && $signatureSize[0] && $signatureSize[1]) {
        $signatureScale = min(400 / $signatureSize[0], 150 / $signatureSize[1]);
        $signatureWidth = (int) round($signatureSize[0] * $signatureScale);
        $signatureHeight = (int) round($signatureSize[1] * $signatureScale);
    }
@endphp

<div id="header">
    <div style="height:20mm;line-height:0;text-align:right">
        <!--AITROL_PDF_BRANDING_SLOT-->
        <!--AITROL_PDF_BRANDING_FALLBACK_START-->
        @if($logoData)<img style="max-width:38mm;max-height:20mm" src="{{$logoData}}" alt="{{$companyName}}">@endif
        <!--AITROL_PDF_BRANDING_FALLBACK_END-->
    </div>
    <div class="header-rule"></div>
    <table class="header-table">
        <tr>
            <td class="header-brand">
                <strong>{{$companyName}}</strong>
                @if($companyAddress)<span>{{$companyAddress}}</span>@endif
            </td>
            <td class="header-doctor">
                @if($doctorName)<strong>Dr(a). {{$doctorName}}</strong>@endif
                <span>RECETA MÉDICA · {{$issueDate}}</span>
            </td>
        </tr>
    </table>
</div>

<div id="footer">
    <div class="footer-rule"></div>
    <table class="footer-table">
        <tr>
            <td class="footer-left"><span class="footer-title">{{$companyName}}</span><span class="footer-muted">{{$companyAddress}}</span></td>
            <td class="footer-right"><span class="footer-title">Receta médica</span><span class="footer-muted">@if($companyPhone)Tel. {{$companyPhone}} · @endif Emitida el {{$issueDate}}</span></td>
        </tr>
    </table>
</div>

<div class="watermark">
    <!--AITROL_PDF_BRANDING_WATERMARK-->
    <!--AITROL_PDF_BRANDING_FALLBACK_START-->
    @if($logoData)<img src="{{$logoData}}" alt="">@else<div class="watermark-text">{{$companyName}}</div>@endif
    <!--AITROL_PDF_BRANDING_FALLBACK_END-->
</div>

<main>
    <section class="patient-box">
        <div class="patient-title">Datos del paciente</div>
        <table class="patient-table">
            <tr>
                <td style="width:46%"><span class="label">Paciente</span><span class="value">{{$patientName ?: 'Paciente no registrado'}}</span></td>
                <td style="width:18%"><span class="label">Identificación</span><span class="value">{{$paciente->id}}</span></td>
                <td style="width:12%"><span class="label">Edad</span><span class="value">{{$edad}} años</span></td>
                <td><span class="label">Alergias</span><span class="value">{{$principioActivo}}</span></td>
            </tr>
        </table>
    </section>

    <h1 class="rx-title">℞ <small>INDICACIONES MÉDICAS</small></h1>

    <table class="medicines">
        <thead><tr><th>Medicamento</th><th>Indicación / dosis</th></tr></thead>
        <tbody>
        @forelse($detalles as $detalle)
            @php
                $medicine = $detalle->medicina ?: $medicinas->where('id', $detalle->id_medicina)->first();
                $genericNames = $medicine && $medicine->genericos ? $medicine->genericos->map(function ($generic) { return optional($generic->generico)->nombre; })->filter()->implode(', ') : '';
            @endphp
            <tr>
                <td>{{optional($medicine)->nombre ?: $detalle->nombre ?: 'Medicamento'}}@if($genericNames)<br><span style="font-size:7px;font-weight:normal">{{$genericNames}}</span>@endif @if($detalle->cantidad)<br><span style="font-size:8px;font-weight:normal">Cantidad: {{$detalle->cantidad}} {{$detalle->detalle_cantidad}}</span>@endif</td>
                <td>{{$detalle->dosis}}</td>
            </tr>
        @empty
            <tr><td colspan="2" class="empty-row">No se registraron medicamentos en esta receta.</td></tr>
        @endforelse
        </tbody>
    </table>

    @if(trim(strip_tags((string)$receta->rp)) !== '' || trim(strip_tags((string)$receta->prescripcion)) !== '')
        <section class="notes">
            <div class="notes-title">Indicaciones adicionales</div>
            <div class="notes-content">{!! $receta->rp !!}{!! $receta->prescripcion ? '<br>' . $receta->prescripcion : '' !!}</div>
        </section>
    @endif

    <section class="signature">
        <div class="signature-image">@if($signaturePath && is_file($signaturePath))<img src="{{$signaturePath}}" alt="Firma del médico" width="{{$signatureWidth}}" height="{{$signatureHeight}}">@endif</div>
        <div class="signature-line"></div>
        @if($doctorName)<strong>Dr(a). {{$doctorName}}</strong>@endif
        <span>Firma y sello del profesional</span>
    </section>
</main>
</body>
</html>
