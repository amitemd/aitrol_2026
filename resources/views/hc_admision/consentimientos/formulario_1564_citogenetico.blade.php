<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario 1564 - Orden de Examen Citogenetico</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 28pt 38pt 22pt 38pt;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #202020;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.12;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: middle;
        }

        .header-logo {
            width: 18%;
            text-align: center;
        }

        .header-logo img {
            display: block;
            width: 72pt;
            height: auto;
            margin: 0 auto;
        }

        .header-title {
            width: 90%;
            text-align: center;
        }

        .form-number {
            margin-bottom: 3pt;
            font-size: 16pt;
            font-weight: bold;
        }

        .form-title {
            font-size: 15pt;
            font-weight: bold;
        }

        .instruction {
            margin-top: 2pt;
            font-size: 7pt;
        }

        .revision {
            margin-top: 5pt;
            font-size: 7pt;
            text-align: right;
        }

        .header-rule {
            margin: 5pt 0 0 0;
            border-top: 1pt solid #000000;
        }

        .top-data {
            margin-bottom: 10pt;
        }

        .label {
            font-weight: bold;
        }

        .line-value {
            color: #0876b9;
            display: inline-block;
            min-height: 12pt;
            padding: 0 3pt 1pt 3pt;
            border-bottom: .7pt solid #000000;
            font-weight: bold;
        }

        .section-title {
            margin: 7pt 0 3pt 0;
            font-size: 9.5pt;
            font-weight: bold;
        }

        .patient-table td {
            height: 15pt;
            padding: 1.5pt 4pt;
            border: .6pt solid #000000;
            font-size: 8.5pt;
        }

        .patient-label {
            width: 32%;
            font-weight: bold;
        }

        .patient-value {
            font-weight: bold;
            color: #0876b9;
        }

        .question-row {
            margin: 4pt 0;
            font-size: 9.5pt;
            white-space: nowrap;
        }

        .circle {
            display: inline;
            margin: 0 2pt;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 11pt;
            font-weight: normal;
            line-height: normal;
            vertical-align: baseline;
        }

        .right-answer {
            margin: 8pt 0 9pt 55%;
            font-size: 8.5pt;
        }

        .sample-table td {
            height: 15pt;
            padding: 0;
            line-height: 12pt;
            vertical-align: middle;
        }

        .sample-table .sample-label {
            width: 43%;
        }

        .sample-table .sample-mark {
            width: 7%;
            padding-right: 12pt;
            text-align: right;
            white-space: nowrap;
        }

        .sample-table .sample-line {
            padding-top: 4pt;
            vertical-align: middle;
        }

        .blank-line {
            display: inline-block;
            width: 100pt;
            min-width: 100pt;
            height: 8pt;
            border-bottom: .7pt solid #000000;
        }

        .wide-line {
            display: block;
            width: 100%;
            height: 18pt;
            border-bottom: .7pt solid #000000;
        }

        .text-area {
            margin-bottom: 8pt;
        }

        .signature-table {
            margin-top: 34pt;
        }

        .signature-table td {
            width: 50%;
            padding-right: 34pt;
            vertical-align: top;
        }

        .signature-line {
            height: 8pt;
            margin-bottom: 0;
            border-top: .8pt solid #000000;
        }

        .signature-name {
            min-height: 14pt;
            font-weight: bold;
        }

        .version {
            margin-top: 19pt;
            font-size: 8.5pt;
            text-align: right;
        }
    </style>
</head>
@php
    $persona = $paciente->paciente;
    $nombres = '';
    $apellidos = '';
    $fechaNacimiento = '';
    $edad = '';
    $sexo = '';
    $ciudad = '';
    $telefono = '';

    if (!is_null($persona)) {
        $nombres = trim(($persona->nombre1 ?? '') . ' ' . ($persona->nombre2 ?? ''));
        $apellidos = trim(($persona->apellido1 ?? '') . ' ' . ($persona->apellido2 ?? ''));
        $ciudad = $persona->ciudad ?? '';
        $telefono = $persona->telefono1 ?? '';
        if ($telefono == '') {
            $telefono = $persona->telefono2 ?? '';
        }

        if (!empty($persona->fecha_nacimiento)) {
            try {
                $nacimiento = \Carbon\Carbon::parse($persona->fecha_nacimiento);
                $fechaNacimiento = $nacimiento->format('d/m/Y');
                $edad = $nacimiento->age;
            } catch (\Exception $e) {
                $fechaNacimiento = '';
                $edad = '';
            }
        }

        if ((string) $persona->sexo === '1') {
            $sexo = 'Masculino';
        } elseif ((string) $persona->sexo === '2') {
            $sexo = 'Femenino';
        }
    }

    $fechaOrden = $date;
    if (!empty($paciente->fecha_orden)) {
        try {
            $fechaOrden = \Carbon\Carbon::parse($paciente->fecha_orden)->format('d/m/Y');
        } catch (\Exception $e) {
            $fechaOrden = date('d/m/Y');
        }
    } else {
        $fechaOrden = date('d/m/Y');
    }

    $nombreProfesional = '';
    if (!is_null($usuario)) {
        $nombreProfesional = trim(
            ($usuario->nombre1 ?? '') . ' ' .
            ($usuario->nombre2 ?? '') . ' ' .
            ($usuario->apellido1 ?? '') . ' ' .
            ($usuario->apellido2 ?? '')
        );
    }
@endphp
<body>
    <table>
        <tr>
            <td class="header-logo">
                <img src="{{base_path().'/storage/app/logo/logo0993075000001.jpg'}}" style="width:180px;height: 100px;" alt="Interlab">
                {{-- <img src="{{base_path().'/public/imagenes/login2.png'}}" style="width:150px;height: 80px;"> --}}
            </td>
            <td class="header-title">
                <br><br>
                <div class="form-number">FORMULARIO 1564</div>
                <div class="form-title">ORDEN DE EXAMEN CITOGEN&Eacute;TICO</div>
                <div class="instruction">(Llenar con letra IMPRENTA)</div>
                <div class="header-rule"></div> <br>
                <div class="revision">&Uacute;ltima Actualizaci&oacute;n: 24/04/26</div>
            </td>
        </tr>
    </table>

    <table class="top-data">
        <tr>
            <td style="width: 55%;"><span class="label">Fecha:</span> <span class="line-value" style="width: 150pt;">{{ $fechaOrden }}</span></td>
            <td style="width: 45%; text-align: left;"><span class="label">ORDEN #:</span> <span class="" style="width: 115pt; text-align: center;"></span></td>
        </tr>
    </table>

    <div class="section-title">INFORMACI&Oacute;N DEL PACIENTE</div>
    <table class="patient-table">
        <tr>
            <td class="patient-label">Nombres:</td>
            <td class="patient-value" colspan="5">{{ $nombres }}</td>
        </tr>
        <tr>
            <td class="patient-label">Apellidos:</td>
            <td class="patient-value" colspan="5">{{ $apellidos }}</td>
        </tr>
        <tr>
            <td class="patient-label">Fecha de Nacimiento (dd/mm/aaaa):</td>
            <td class="patient-value" style="width: 25%;">{{ $fechaNacimiento }}</td>
            <td class="label" style="width: 8%;">Edad:</td>
            <td class="patient-value" style="width: 9%; text-align: center;">{{ $edad }}</td>
            <td class="label" style="width: 8%;">Sexo:</td>
            <td class="patient-value">{{ $sexo }}</td>
        </tr>
        <tr>
            <td class="patient-label">Tel&eacute;fono:</td>
            <td class="patient-value" colspan="3" >{{ $telefono }}</td>
            <td class="patient-label" style="width: 8%;">Ciudad:</td>
            <td class="patient-value">{{ $ciudad }}</td>
        </tr>
    </table>

    <div style="height: 9pt;"></div>
    <div class="question-row"><span class="label">Consumo de Alcohol:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Menos de 72 hrs.<span class="circle">&#9711;</span>&nbsp;&nbsp;&nbsp;&nbsp; Mas de 72 hrs.<span class="circle">&#9711;</span></div>
    <div class="question-row"><span class="label">Cuadro Infeccioso Febril Actual:</span>&nbsp; Si<span class="circle">&#9711;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No<span class="circle">&#9711;</span></div>
    <div class="question-row"><span class="label">En tratamiento con antibi&oacute;ticos:</span>&nbsp; Completado hace menos de 8 d&iacute;as<span class="circle">&#9711;</span> Completado hace m&aacute;s de 8 d&iacute;as<span class="circle">&#9711;</span> Actual<span class="circle">&#9711;</span></div>

    <div class="right-answer">
        <div>Apto para Toma de Muestra&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Si<span class="circle">&#9711;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No<span class="circle">&#9711;</span></div>
        <div style="margin-top: 5pt;">Se debe posponer <span class="blank-line line-value" style="width: 120pt; min-width: 120pt;"></span> d&iacute;as</div>
    </div>

    <div class="section-title">INFORMACI&Oacute;N DE LA MADRE:</div>
    <div>Embarazo de Alto Riesgo:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Si<span class="circle">&#9711;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No<span class="circle">&#9711;</span></div>
    <div style="margin-top: 7pt;">Semanas de Gestaci&oacute;n: <span class="blank-line" style="width: 135pt; min-width: 135pt;"></span></div>

    <div class="section-title" style="margin-top: 12pt;">INFORMACI&Oacute;N DE LA MUESTRA:</div>
    <table class="sample-table">
        <tr>
            <td class="sample-label">Sangre Periferica:</td>
            <td class="sample-mark"><span class="circle">&#9711;</span></td>
            <td class="sample-label">M&eacute;dula &Oacute;sea</td>
            <td class="sample-mark"><span class="circle">&#9711;</span></td>
        </tr>
        <tr>
            <td class="sample-label">L&iacute;quido Amni&oacute;tico:</td>
            <td class="sample-mark"><span class="circle">&#9711;</span></td>
            <td class="sample-label">Restos Corioplacentarios:</td>
            <td class="sample-mark"><span class="circle">&#9711;</span></td>
        </tr>
        <tr>
            <td class="sample-line" colspan="2">Tumor: <span class="blank-line line-value" style="width: 180pt; min-width: 180pt;"></span></td>
            <td class="sample-line" colspan="2">Otros: <span class="blank-line line-value" style="width: 185pt; min-width: 185pt;"></span></td>
        </tr>
    </table>

    <div style="margin-top: 11pt;"><span class="section-title">FECHA DE TOMA DE MUESTRA:</span> <span class="blank-line" style="width: 245pt; min-width: 245pt;"></span> <span style="font-size: 8.5pt;">(dd/mm/aaaa)</span></div>

    <div class="section-title" style="margin-top: 12pt;">INFORMACI&Oacute;N CL&Iacute;NICA DEL CASO:</div>
    <div class="text-area">
        <span class="wide-line"></span>
        <span class="wide-line"></span>
        <span class="wide-line"></span>
    </div>

    <div class="section-title">OTROS DATOS DE EX&Aacute;MENES COMPLEMENTARIOS:</div>
    <div class="text-area">
        <span class="wide-line"></span>
        <span class="wide-line"></span>
        <span class="wide-line"></span>
    </div>

    <div style="margin-top: 14pt;"><span class="section-title">FECHA DE ENV&Iacute;O DE LA MUESTRA:</span> <span class="blank-line" style="width: 260pt; min-width: 260pt;"></span> <span style="font-size: 8.5pt;">(dd/mm/aaaa)</span></div>

    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-line"></div>
                <div>Firma del Profesional que Entrevisto al Paciente</div>
                <div>Nombre: <span class="signature-name"></span></div>
            </td>
            <td>
                <div class="signature-line"></div>
                <div>Responsable de Env&iacute;o de Muestra</div>
                <div>Nombre:</div>
            </td>
        </tr>
    </table>

    <div class="version">Version 03</div>
</body>
</html>
