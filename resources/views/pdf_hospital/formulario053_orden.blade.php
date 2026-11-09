<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Formulario 053</title>
    <style>
        @page { margin: 7mm 7mm 6mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: DejaVu Sans, sans-serif; font-size: 5.8px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td, th { border: .55px solid #555; padding: 1.3px 2px; vertical-align: middle; }
        .section { margin-top: 4px; background: #c8c5f2; font-size: 8.8px; font-weight: bold; }
        .label { background: #c8f5ca; font-size: 5.3px; font-weight: bold; text-align: center; }
        .value { height: 13px; text-align: center; }
        .left { text-align: left; }
        .center { text-align: center; }
        .box { display: inline-block; width: 12px; height: 10px; border: .6px solid #777; text-align: center; line-height: 9px; }
        .free { height: 45px; vertical-align: top; font-size: 7px; }
        .free-sm { height: 30px; vertical-align: top; font-size: 6.5px; }
        .line { height: 10px; }
        .diagnosis td { height: 11px; }
        .note { font-size: 4.5px; }
        .spacer { height: 4px; border: 0; }
        .footer { margin-top: 5px; font-size: 5.5px; font-weight: bold; }
    </style>
</head>
<body>
@php
    $birth = $patient && $patient->fecha_nacimiento ? new DateTime($patient->fecha_nacimiento) : null;
    $age = $birth ? (new DateTime())->diff($birth)->y : '';
    $orderDate = !empty($order->fecha_orden) ? strtotime($order->fecha_orden) : time();
    $summary = trim(strip_tags((string) $order->motivo_consulta));
    $clinical = trim(strip_tags((string) $order->resumen_clinico));
    if ($clinical !== '') $summary .= ($summary !== '' ? "\n\n" : '').$clinical;
    if ($procedures !== '') $summary .= ($summary !== '' ? "\n\n" : '').'PROCEDIMIENTOS SOLICITADOS: '.$procedures;
@endphp

<table>
    <tr><td colspan="24" class="section">A. DATOS DEL ESTABLECIMIENTO DE ORIGEN Y USUARIO / PACIENTE</td></tr>
    <tr>
        <td colspan="6" class="label">INSTITUCIÓN DEL SISTEMA</td><td colspan="2" class="label">UNICÓDIGO</td>
        <td colspan="6" class="label">ESTABLECIMIENTO DE SALUD</td><td colspan="2" class="label">TIPOLOGÍA</td>
        <td colspan="5" class="label">NÚMERO DE HISTORIA CLÍNICA ÚNICA</td><td colspan="3" class="label">NÚMERO DE ARCHIVO</td>
    </tr>
    <tr>
        <td colspan="6" class="value">{{optional($insurance)->nombre}}</td><td colspan="2" class="value">{{optional($company)->unicodigo}}</td>
        <td colspan="6" class="value">{{optional($company)->nombrecomercial ?: optional($company)->nombre_corto}}</td><td colspan="2"></td>
        <td colspan="5" class="value">{{$history_number}}</td><td colspan="3" class="value">{{optional($patient)->id}}</td>
    </tr>
    <tr>
        <td colspan="5" class="label">PRIMER APELLIDO</td><td colspan="4" class="label">SEGUNDO APELLIDO</td>
        <td colspan="4" class="label">PRIMER NOMBRE</td><td colspan="4" class="label">SEGUNDO NOMBRE</td>
        <td class="label">SEXO</td><td colspan="2" class="label">FECHA NACIMIENTO</td><td class="label">EDAD</td><td colspan="3" class="label">CONDICIÓN EDAD</td>
    </tr>
    <tr>
        <td colspan="5" class="value">{{optional($patient)->apellido1}}</td><td colspan="4" class="value">{{optional($patient)->apellido2}}</td>
        <td colspan="4" class="value">{{optional($patient)->nombre1}}</td><td colspan="4" class="value">{{optional($patient)->nombre2}}</td>
        <td class="value">@if(optional($patient)->sexo == 1) M @elseif(optional($patient)->sexo == 2) F @endif</td>
        <td colspan="2" class="value">{{$birth ? $birth->format('Y-m-d') : ''}}</td><td class="value">{{$age}}</td>
        <td class="center">H</td><td class="center">D/M</td><td class="center">A @if($birth) X @endif</td>
    </tr>
    <tr>
        <td colspan="8" class="label">No. TELÉFONO (CELULAR O CONVENCIONAL)</td>
        <td colspan="4" class="center"><b>REFERENCIA</b>&nbsp; <span class="box">X</span></td>
        <td colspan="12" rowspan="2" class="left"><b>DERIVACIÓN - MOTIVO</b><br>1. Accesibilidad geográfica &nbsp; 2. Problemas de abastecimiento &nbsp; 3. Falta de espacio físico<br>4. Insuficiencia de profesionales &nbsp; 5. Falta de equipamiento &nbsp; 6. Inadecuada capacidad resolutiva</td>
    </tr>
    <tr><td colspan="8" class="value">{{optional($patient)->telefono1 ?: optional($patient)->telefono2}}</td><td colspan="4"></td></tr>
    <tr><td colspan="4" class="label">PROVINCIA</td><td colspan="4" class="label">CANTÓN</td><td colspan="4" class="label">PARROQUIA</td><td colspan="12"></td></tr>
    <tr><td colspan="4"></td><td colspan="4" class="value">{{optional($patient)->ciudad}}</td><td colspan="4"></td><td colspan="12"></td></tr>

    <tr><td colspan="24" class="section">C. RESUMEN DEL CUADRO CLÍNICO</td></tr>
    <tr><td colspan="24" class="free">{!! nl2br(e($summary)) !!}</td></tr>

    <tr><td colspan="24" class="section">D. HALLAZGOS RELEVANTES DE EXÁMENES Y PROCEDIMIENTOS DIAGNÓSTICOS</td></tr>
    <tr><td colspan="24" class="free-sm">{{trim(strip_tags((string) $order->observacion_medica))}}</td></tr>

    <tr><td colspan="24" class="section">E. DIAGNÓSTICO <span class="note">&nbsp;&nbsp; PRE = PRESUNTIVO &nbsp;&nbsp; DEF = DEFINITIVO</span></td></tr>
    @for($row = 0; $row < 3; $row++)
        @php $leftDiagnosis = isset($diagnoses[$row]) ? $diagnoses[$row] : null; $rightDiagnosis = isset($diagnoses[$row + 3]) ? $diagnoses[$row + 3] : null; @endphp
        <tr class="diagnosis">
            <td class="label">{{$row + 1}}</td><td colspan="8">{{$leftDiagnosis ? $leftDiagnosis['description'] : ''}}</td><td colspan="2" class="center">{{$leftDiagnosis ? $leftDiagnosis['code'] : ''}}</td><td class="center">{{$leftDiagnosis && $leftDiagnosis['type'] === 'PRESUNTIVO' ? 'X' : ''}}</td><td class="center">{{$leftDiagnosis && $leftDiagnosis['type'] === 'DEFINITIVO' ? 'X' : ''}}</td>
            <td class="label">{{$row + 4}}</td><td colspan="7">{{$rightDiagnosis ? $rightDiagnosis['description'] : ''}}</td><td class="center">{{$rightDiagnosis ? $rightDiagnosis['code'] : ''}}</td><td class="center">{{$rightDiagnosis && $rightDiagnosis['type'] === 'PRESUNTIVO' ? 'X' : ''}}</td><td class="center">{{$rightDiagnosis && $rightDiagnosis['type'] === 'DEFINITIVO' ? 'X' : ''}}</td>
        </tr>
    @endfor

    <tr><td colspan="24" class="section">F. DATOS DEL PROFESIONAL RESPONSABLE</td></tr>
    <tr><td colspan="3" class="label">FECHA</td><td colspan="2" class="label">HORA</td><td colspan="7" class="label">PRIMER NOMBRE</td><td colspan="7" class="label">PRIMER APELLIDO</td><td colspan="5" class="label">SEGUNDO APELLIDO</td></tr>
    <tr><td colspan="3" class="value">{{date('Y-m-d', $orderDate)}}</td><td colspan="2" class="value">{{date('H:i', $orderDate)}}</td><td colspan="7" class="value">{{optional($doctor)->nombre1}}</td><td colspan="7" class="value">{{optional($doctor)->apellido1}}</td><td colspan="5" class="value">{{optional($doctor)->apellido2}}</td></tr>
    <tr><td colspan="6" class="label">NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</td><td colspan="9" class="label">FIRMA</td><td colspan="9" class="label">SELLO</td></tr>
    <tr><td colspan="6" class="value">{{optional($doctor)->id}}</td><td colspan="9" class="line"></td><td colspan="9"></td></tr>

    <tr><td colspan="24" class="section">G. EVALUACIÓN DE LA REFERENCIA / DERIVACIÓN</td></tr>
    <tr><td colspan="9" class="label left">REFERENCIA JUSTIFICADA</td><td colspan="3" class="center">SÍ&nbsp; <span class="box"></span></td><td colspan="9" class="label left">DERIVACIÓN JUSTIFICADA</td><td colspan="3" class="center">SÍ&nbsp; <span class="box"></span></td></tr>
    <tr><td colspan="12" class="center"><b>CONTRARREFERENCIA</b>&nbsp; <span class="box"></span></td><td colspan="12" class="center"><b>REFERENCIA INVERSA</b>&nbsp; <span class="box"></span></td></tr>

    <tr><td colspan="24" class="section">A. DATOS DEL ESTABLECIMIENTO QUE CONTRARREFIERE O REALIZA LA REFERENCIA INVERSA</td></tr>
    <tr><td colspan="5" class="label">INSTITUCIÓN DEL SISTEMA</td><td colspan="3" class="label">UNICÓDIGO</td><td colspan="6" class="label">ESTABLECIMIENTO DE SALUD</td><td colspan="3" class="label">TIPOLOGÍA</td><td colspan="4" class="label">HISTORIA CLÍNICA</td><td colspan="3" class="label">NÚMERO DE ARCHIVO</td></tr>
    <tr><td colspan="5" class="line"></td><td colspan="3"></td><td colspan="6"></td><td colspan="3"></td><td colspan="4"></td><td colspan="3"></td></tr>

    <tr><td colspan="24" class="section">B. DATOS DEL ESTABLECIMIENTO AL CUAL SE CONTRARREFIERE O SE REALIZA REFERENCIA INVERSA</td></tr>
    <tr><td colspan="6" class="label">INSTITUCIÓN DEL SISTEMA</td><td colspan="7" class="label">ESTABLECIMIENTO DE SALUD</td><td colspan="4" class="label">DISTRITO</td><td colspan="7" class="label">FECHA</td></tr>
    <tr><td colspan="6" class="line"></td><td colspan="7"></td><td colspan="4"></td><td colspan="7"></td></tr>

    <tr><td colspan="24" class="section">C. RESUMEN DEL CUADRO CLÍNICO</td></tr><tr><td colspan="24" class="free-sm"></td></tr>
    <tr><td colspan="24" class="section">D. HALLAZGOS RELEVANTES DE EXÁMENES Y PROCEDIMIENTOS DIAGNÓSTICOS</td></tr><tr><td colspan="24" class="free-sm"></td></tr>
    <tr><td colspan="24" class="section">E. TRATAMIENTOS Y PROCEDIMIENTOS TERAPÉUTICOS REALIZADOS</td></tr><tr><td colspan="24" class="free-sm"></td></tr>
    <tr><td colspan="24" class="section">F. DIAGNÓSTICO</td></tr>
    @for($row = 1; $row <= 3; $row++)<tr class="diagnosis"><td class="label">{{$row}}</td><td colspan="10"></td><td colspan="2"></td><td class="label">{{$row + 3}}</td><td colspan="7"></td><td></td><td></td><td></td></tr>@endfor
    <tr><td colspan="24" class="section">G. TRATAMIENTO RECOMENDADO A SEGUIR EN ESTABLECIMIENTO DE SALUD AL QUE SE CONTRARREFIERE</td></tr><tr><td colspan="24" class="free-sm"></td></tr>
    <tr><td colspan="24" class="section">H. DATOS DEL PROFESIONAL RESPONSABLE</td></tr>
    <tr><td colspan="3" class="label">FECHA</td><td colspan="2" class="label">HORA</td><td colspan="7" class="label">PRIMER NOMBRE</td><td colspan="7" class="label">PRIMER APELLIDO</td><td colspan="5" class="label">SEGUNDO APELLIDO</td></tr>
    <tr><td colspan="3" class="line"></td><td colspan="2"></td><td colspan="7"></td><td colspan="7"></td><td colspan="5"></td></tr>
    <tr><td colspan="6" class="label">NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</td><td colspan="9" class="label">FIRMA</td><td colspan="9" class="label">SELLO</td></tr>
    <tr><td colspan="6" class="line"></td><td colspan="9"></td><td colspan="9"></td></tr>
</table>

<div class="footer">SNS-MSP/HCU-form.053/2021</div>
</body>
</html>
