<!DOCTYPE html>
<html>
<?php
ob_start(function ($html) {
    return preg_replace('/(>\s*)N\/A(\s*<)/i', '$1$2', $html);
});
?>
@php
$fecha = substr($historia_clinica->agenda->fechaini,0,10);
$hora = substr($historia_clinica->agenda->fechaini,11);
$especialidad = Sis_medico\Especialidad::find( $historia_clinica->agenda->espid);
@endphp
<?php
$fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');

// Conservar el texto y las negritas, sin las dimensiones pegadas desde el editor.
$cuadroClinicoHtml = (string) ($historia_clinica->anamnesis->problema_actual ?? '');
$cuadroClinico = '';
if (trim($cuadroClinicoHtml) !== '') {
    $documentoClinico = new \DOMDocument('1.0', 'UTF-8');
    $erroresLibxml = libxml_use_internal_errors(true);
    $documentoClinico->loadHTML('<?xml encoding="UTF-8"><html><body>' . $cuadroClinicoHtml . '</body></html>', LIBXML_NONET);
    libxml_clear_errors();
    libxml_use_internal_errors($erroresLibxml);

    $normalizarClinico = function ($nodo) use (&$normalizarClinico) {
        if ($nodo->nodeType === XML_TEXT_NODE) {
            $texto = str_replace(["\xC2\xA0", "\xE2\x80\xAF"], ' ', $nodo->nodeValue);
            return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }
        if ($nodo->nodeType !== XML_ELEMENT_NODE) {
            return '';
        }
        $etiqueta = strtolower($nodo->nodeName);
        if (in_array($etiqueta, ['script', 'style'], true)) {
            return '';
        }
        if ($etiqueta === 'br') {
            return "\n";
        }
        $texto = '';
        foreach ($nodo->childNodes as $hijo) {
            $texto .= $normalizarClinico($hijo);
        }
        if (in_array($etiqueta, ['b', 'strong'], true)
            || preg_match('/font-weight\s*:\s*(?:bold|[7-9]00)\b/i', $nodo->getAttribute('style'))) {
            $texto = '<strong>' . $texto . '</strong>';
        }
        if (in_array($etiqueta, ['p', 'div', 'li', 'tr', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) {
            return "\n" . $texto . "\n";
        }
        return $texto . (in_array($etiqueta, ['td', 'th'], true) ? ' ' : '');
    };
    $cuadroClinico = $normalizarClinico($documentoClinico->getElementsByTagName('body')->item(0));
    $cuadroClinico = preg_replace('/[\t ]+/u', ' ', str_replace(["\r\n", "\r"], "\n", $cuadroClinico));
    $cuadroClinico = preg_replace('/ *\n */u', "\n", $cuadroClinico);
    $cuadroClinico = nl2br(trim(preg_replace('/\n{2,}/', "\n", $cuadroClinico)), false);
}
?>

<head>
    <meta charset="utf-8">
    <title>HCU_007</title>
    <style>
        @page {
            margin: 8mm 7mm;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin: 0;
            font-size: 9.5px;
            line-height: 1.1;
        }

        /** Define the header rules **/
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1cm;
        }

        /** Define the footer rules **/
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
        }

        table {
            width: 100px;
            border-collapse: collapse;
        }

        .table2d {
            border: none;
            text-align: center;
        }

        .titulo {
            background-color: #C7C3C2;
        }

        p {
            padding-top: 0;
            padding-bottom: 0;
            margin: 0;
        }

        div {
            padding-top: 0;
            padding-bottom: 0;
        }

        .txt_vertical {
            writing-mode: vertical-lr;
            transform: rotate(-90deg);
            letter-spacing: 2px;
            word-wrap: break-word;
        }

        .td-border {
            border: 1px solid;
        }

        .td-not-border-top {
            border-top: 1px solid;
            border-left: 1px solid;
            border-right: 1px solid;
        }

        .td-not-border-bottom {
            border-bottom: 1px solid;
            border-left: 1px solid;
            border-right: 1px solid;
        }

        .td-not-border-right {
            border-bottom: 1px solid;
            border-left: 1px solid;
            border-top: 1px solid;
        }

        .td-not-border-left {
            border-bottom: 1px solid;
            border-top: 1px solid;
            border-right: 1px solid;
        }

        .td-border-top {
            border-top: 1px solid;
        }

        .td-border-bottom {
            border-bottom: 1px solid;
        }

        .td-border-left {
            border-left: 1px solid;
        }

        .td-border-right {
            border-right: 1px solid;
        }

        .bg-blue {
            background-color: #DDEBF7;
        }

        .bg-green {
            background-color: #BADAC8;
        }

        .bg-yellow {
            background-color: #F1FD77;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-right {
            text-align: right;
        }

        .page-break-before {
            page-break-before: always;
        }

        .page-break-after {
            page-break-after: always;
        }

        .section-gap {
            height: 5px;
            line-height: 0;
        }

        .cuadro-clinico {
            border: 1px solid;
            border-top: 0;
            padding: 3px 4px;
            min-height: 50px;
            font-size: 9.5px;
            line-height: 1.1;
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .diagnosticos {
            width: 100%;
            table-layout: fixed;
        }

        .diagnosticos td {
            word-wrap: break-word;
        }

        .diagnostico-contador {
            width: 3%;
            text-align: center;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <header>
    </header>
    <main>
        @php
        $empresa = $historia_clinica->agenda->empresa;
        @endphp

        <table style="width: 99%">
            <tr>
                <td colspan="12" style="text-align: center">
                    @if(!is_null($empresa))
                    <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:140px;height: 45px">
                    @endif
                </td>
            </tr>
        </table>
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="12" class="bg-blue"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" class="bg-green text-center"><b>INSTITUCIÓN DEL SISTEMA</b></td>
                    <td colspan="2" class="bg-green text-center"><b>UNICÓDIGO</b></td>
                    <td colspan="2" class="bg-green text-center"><b>ESTABLECIMIENTO DE SALUD</b></td>
                    <td colspan="2" class="bg-green text-center"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                    <td colspan="2" class="bg-green text-center"><b>NÚMERO DE ARCHIVO</b></td>
                    <td colspan="2" class="bg-green text-center"><b>No. HOJA</b></td>
                </tr>
                <tr>
                    <td class="text-center" colspan="2">{{$historia_clinica->seguro->nombre}}</td>
                    <td class="text-center" colspan="2">{{$historia_clinica->agenda->empresa->unicodigo}}</td>
                    <td class="text-center" colspan="2">{{$empresa->nombrecomercial}}</td>
                    <td class="text-center" colspan="2">{{$paciente->id}}</td>
                    <td class="text-center" colspan="2"></td>
                    <td class="text-center" colspan="2"></td>
                </tr>
                <tr>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>SEGUNDO NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>SEXO</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>EDAD</b></td>
                    <td colspan="4" class="bg-green text-center"><b>CONDICIÓN EDAD (MARCAR)</b></td>
                </tr>
                <tr>
                    <td style="height: 15px;" class="bg-green text-center">D</td>
                    <td class="bg-green text-center">H</td>
                    <td class="bg-green text-center">M</td>
                    <td class="bg-green text-center">A</td>
                </tr>
                <tr>
                    <td class="text-center" style="height: 15px;" colspan="2">{{$paciente->apellido1}}</td>
                    <td class="text-center" colspan="1">{{$paciente->apellido2}}</td>
                    <td class="text-center" colspan="2">{{$paciente->nombre1}}</td>
                    <td class="text-center" colspan="1">{{$paciente->nombre2}}</td>
                    <td class="text-center" colspan="1">@if($paciente->sexo == 1) Masculino @endif @if($paciente->sexo == 2) Femenino @endif</td>
                    <td class="text-center" colspan="1">{{$edad}}</td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1">X</td>
                </tr>
            </tbody>
        </table>
        <div class="section-gap"></div>
        <table cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td colspan="13" style="font-size: 11px;" class="bg-blue td-border"><b>B. CARACTERÍSTICA DE LA SOLICITUD, MOTIVO Y PRIORIDAD DE ATENCIÓN</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center bg-green td-border" style="width: 20%;" colspan="6">SERVICIO</td>
                    <td class="text-center bg-green td-border">ESPECIALIDAD</td>
                    <td class="text-center bg-green td-border">No.CAMA</td>
                    <td class="text-center bg-green td-border">No.SALA</td>
                    <td class="text-center bg-green td-border" colspan="4">URGENTE</td>
                </tr>
                <tr style="text-align: center;">
                    <td class="td-border bg-green">EMERGENCIA</td>
                    <td class="td-border"></td>
                    <td class="td-border bg-green">CONSULTA EXTERNA</td>
                    <td class="td-border">X</td>
                    <td class="td-border bg-green">HOSPITALIZACIÓN</td>
                    <td class="td-border"></td>
                    <td class="td-border">{{$especialidad->nombre}}</td>
                    <td class="td-border"></td>
                    <td class="td-border"></td>
                    <td class="td-border bg-green">SI</td>
                    <td class="td-border"></td>
                    <td class="td-border bg-green">NO</td>
                    <td class="td-border">X</td>
                </tr>
                <tr>
                    <td colspan="4" class="td-border bg-green" style="width:12%; height: 18px;">ESPECIALIDAD CONSULTADA</td>
                    <td colspan="9" class="td-border">{{$especialidad->nombre}}</td>
                </tr>
                <tr>
                    <td colspan="4" class="td-border bg-green" style="width:12%; height: 18px;">DESCRIPCIÓN DEL MOTIVO</td>
                    <td colspan="9" class="td-border"></td>
                </tr>
            </tbody>
        </table>
        <div class="section-gap"></div>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 11px;" class="bg-blue"><b>C. CUADRO CLÍNICO ACTUAL </b></td>
                </tr>
            </thead>
        </table>
        <div class="cuadro-clinico">{!! $cuadroClinico !!}</div>
        <div class="section-gap"></div>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 11px;" class="bg-blue"><b>D. RESULTADOS DE EXÁMENES Y PROCEDIMIENTOS DIAGNÓSTICOS RELEVANTES </b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border" style="height: 32px;">
                        <span></span>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="section-gap"></div>
        <table class="diagnosticos">
            <colgroup>
                <col style="width: 3%;">
                <col style="width: 77%;">
                <col style="width: 8%;">
                <col style="width: 6%;">
                <col style="width: 6%;">
            </colgroup>
            <thead>
            <tr>
                <td colspan="2" style="font-size: 11px;" class="bg-blue td-border"><b>E. DIAGNOSTICOS</b></td>
                <td class="td-border" style="width: 8%;"><b>CIE-10</b></td>
                <td class="td-border" style="width: 6%;"><b>PRE</b></td>
                <td class="td-border" style="width: 6%;"><b>DEF</b></td>
            </tr>
            </thead>
            <tbody>
                @php $contador = 1; @endphp
                @foreach ($cie10 as $key => $val)
                @php
                $c10 = Sis_medico\Cie_10_3::find($val->cie10);
                if (is_null($c10)) {
                $c10 = Sis_medico\Cie_10_4::find($val->cie10);
                }
                @endphp

                <tr>

                    <td class="td-border bg-green diagnostico-contador"><b>{{ $contador }}</b></td>
                    <td class="td-border" style="width: 77%;">{{ $c10->descripcion }}</td>
                    <td class="text-center td-border" style="width: 8%;">{{ $c10->id }}</td>
                    <td class="text-center td-border" style="width: 6%;">@if ($val->presuntivo_definitivo == 'PRESUNTIVO') X @endif</td>
                    <td class="text-center td-border" style="width: 6%;">@if ($val->presuntivo_definitivo == 'DEFINITIVO') X @endif</td>
                    @php $contador++; @endphp

                </tr>

                @endforeach
            </tbody>


        </table>
        <div class="section-gap"></div>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 11px;" class="bg-blue"><b>F. PLAN TERAPEÚTICO REALIZADO</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border" style="height: 28px;"></td>
                </tr>
            </tbody>
        </table>
        <div class="section-gap"></div>
       @if(isset($empresa) && isset($plano_cabecera) && $empresa->id == '1307189140001' && $plano_cabecera->id_seguro == 2)
         <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td colspan="5" style="font-size: 11px;" class="bg-blue"><b>G. DATOS DEL PROFESIONAL RESPONSABLE</b></td>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td style="width:14%; height: 10px;" class="bg-green text-center"><b>FECHA</b></td>
                <td style="width:8%; height: 10px;" class="bg-green text-center"><b>HORA</b></td>
                <td style="width:20%" class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                <td style="width:20%" class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                <td style="width:15%" class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
            </tr>
            <tr>
            <td class="text-center" style="width:5%; height: 18px;">{{$fecha}}</td>
            <td class="text-center" style="width:5%; height: 18px;">@if($historia_clinica->seguro->id == 2) @else {{$hora}} @endif</td>
                <td class="text-center" style="width:14%; height: 18px;">{{$crm->nombre1}}</td>
                <td class="text-center" style="width:14%; height: 18px;">{{$crm->apellido1}}</td>
                <td class="text-center" style="width:14%; height: 18px;">{{$crm->apellido2}}</td>
            </tr>
            <tr>
                <td colspan="2" class="td-border bg-green text-center"><b>NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
                <td colspan="2" class="td-border bg-green text-center"><b>FIRMA</b></td>
                <td class="td-border bg-green text-center"><b>SELLO</b></td>
            </tr>
            <tr>

                <td colspan="2" class="text-center" style="width:12%; height:60px; font-size:15px">{{$crm->id}}</td>
                <td colspan="2" class="text-center" style="width:25%; height:60px">
                    @php
                    $firma = null;
                   
                    $firma = Sis_medico\Firma_Usuario::where('id_usuario',$crm->id)->first();

                    
                    @endphp
                    @if(!is_null($firma))
                        @if(!is_null($firma->firma))
                            <img width=130 height=45 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" style="" align=center hspace=12><br>
                        @else
                            <br><br><br>
                            <b style="font-size: 10px;">@if($firma->sexo == '2') Dra. @else Dr. @endif  {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}}</b><br>
                        @endif
                    @endif
                </td>
                <td class="text-center" style="width:25%; height:60px;">
                @if(!is_null($firma))
                    @if(!is_null($firma->doctor)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @if($firma->doctor->nombre2!='N/A'){{$firma->doctor->nombre2}}@endif @endif<br>
                    {{$historia_clinica->agenda->especialidad->nombre}} <br>
                    @if(!is_null($firma->doctor)) Cedula No. {{$firma->doctor->id}} @endif
                @endif
                </td>
            </tr>
            <tr>
                <td colspan="2"><b>SNS-MSP/HCU-form.007/2021</b></td>
                <td colspan="3" class="text-right"><b>INTERCONSULTA - SOLICITUD (1)</b></td>
            </tr>
            </tbody>
        </table>
        @else
         <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td colspan="5" style="font-size: 11px;" class="bg-blue"><b>G. DATOS DEL PROFESIONAL RESPONSABLE</b></td>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td style="width:14%; height: 10px;" class="bg-green text-center"><b>FECHA</b></td>
                <td style="width:8%; height: 10px;" class="bg-green text-center"><b>HORA</b></td>
                <td style="width:20%" class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                <td style="width:20%" class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                <td style="width:15%" class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
            </tr>
            <tr>
                <td class="text-center" style="width:5%; height: 18px;">{{$fecha}}</td>
                <td class="text-center" style="width:5%; height: 18px;">@if($historia_clinica->seguro->id == 2) @else {{$hora}} @endif</td>
                <td class="text-center" style="width:14%; height: 18px;">{{$doctor->nombre1}}</td>
                <td class="text-center" style="width:14%; height: 18px;">{{$doctor->apellido1}}</td>
                <td class="text-center" style="width:14%; height: 18px;">{{$doctor->apellido2}}</td>
            </tr>
            <tr>
                <td colspan="2" class="td-border bg-green text-center"><b>NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
                <td colspan="2" class="td-border bg-green text-center"><b>FIRMA</b></td>
                <td class="td-border bg-green text-center"><b>SELLO</b></td>
            </tr>
            <tr>

                <td colspan="2" class="text-center" style="width:12%; height:60px; font-size:15px">{{$doctor->id}}</td>
                <td colspan="2" class="text-center" style="width:25%; height:60px">
                    @php
                    $firma = null;

                    $firma = Sis_medico\Firma_Usuario::where('id_usuario',$doctor->id)->first();


                    @endphp
                    @if(!is_null($firma))
                    @if(!is_null($firma->firma))
                    <img width=130 height=45 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" style="" align=center hspace=12><br>
                    @else
                    <br><br><br>
                    <b style="font-size: 10px;">@if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}}</b><br>
                    @endif
                    @endif
                </td>
                <td class="text-center" style="width:25%; height:60px;">
                    @if(!is_null($firma))
                    @if(!is_null($firma->doctor)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @if($firma->doctor->nombre2!='N/A'){{$firma->doctor->nombre2}}@endif @endif<br>
                    {{$historia_clinica->agenda->especialidad->nombre}} <br>
                    @if(!is_null($firma->doctor)) Cedula No. {{$firma->doctor->id}} @endif
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2"><b>SNS-MSP/HCU-form.007/2021</b></td>
                <td colspan="3" class="text-right"><b>INTERCONSULTA - SOLICITUD (2)</b></td>
            </tr>
            </tbody>
        </table>
        @endif
        <div class="page-break-before"></div>

        <table style="width: 99%">
            <tr>
                <td colspan="12" style="text-align: center">
                    @if(!is_null($empresa))
                    <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:140px;height: 45px">
                    @endif
                </td>
            </tr>
        </table>
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="12" class="bg-blue"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" class="bg-green text-center"><b>INSTITUCIÓN DEL SISTEMA</b></td>
                    <td colspan="2" class="bg-green text-center"><b>UNICÓDIGO</b></td>
                    <td colspan="2" class="bg-green text-center"><b>ESTABLECIMIENTO DE SALUD</b></td>
                    <td colspan="2" class="bg-green text-center"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                    <td colspan="2" class="bg-green text-center"><b>NÚMERO DE ARCHIVO</b></td>
                    <td colspan="2" class="bg-green text-center"><b>No. HOJA</b></td>
                </tr>
                <tr>
                    <td class="text-center" colspan="2">{{$historia_clinica->seguro->nombre}}</td>
                    <td class="text-center" colspan="2">{{$historia_clinica->agenda->empresa->unicodigo}}</td>
                    <td class="text-center" colspan="2">{{$empresa->nombrecomercial}}</td>
                    <td class="text-center" colspan="2">{{$paciente->id}}</td>
                    <td class="text-center" colspan="2"></td>
                    <td class="text-center" colspan="2"></td>
                </tr>
                <tr>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>SEGUNDO NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>SEXO</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>EDAD</b></td>
                    <td colspan="4" class="bg-green text-center"><b>CONDICIÓN EDAD (MARCAR)</b></td>
                </tr>
                <tr>
                    <td style="height: 15px;" class="bg-green text-center">D</td>
                    <td class="bg-green text-center">H</td>
                    <td class="bg-green text-center">M</td>
                    <td class="bg-green text-center">A</td>
                </tr>
                <tr>
                    <td class="text-center" style="height: 15px;" colspan="2">{{$paciente->apellido1}}</td>
                    <td class="text-center" colspan="1">{{$paciente->apellido2}}</td>
                    <td class="text-center" colspan="2">{{$paciente->nombre1}}</td>
                    <td class="text-center" colspan="1">{{$paciente->nombre2}}</td>
                    <td class="text-center" colspan="1">@if($paciente->sexo == 1) Masculino @endif @if($paciente->sexo == 2) Femenino @endif</td>
                    <td class="text-center" colspan="1">{{$edad}}</td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1">X</td>
                </tr>
            </tbody>
        </table>
        <div class="section-gap"></div>

        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 11px;" class="bg-blue"><b>B. CUADRO CLÍNICO DE INTERCONSULTA</b></td>
                </tr>
            </thead>
        </table>
        <div class="cuadro-clinico">{!! $cuadroClinico !!}</div>
        <div class="section-gap"></div>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 11px;" class="bg-blue"><b>C. RESUMEN DEL CRITERIO CLÍNICO </b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border" style="height: 28px;"></td>


                </tr>
            </tbody>
        </table>
        <div class="section-gap"></div>
        <table class="diagnosticos">
            <colgroup>
                <col style="width: 3%;">
                <col style="width: 77%;">
                <col style="width: 8%;">
                <col style="width: 6%;">
                <col style="width: 6%;">
            </colgroup>
            <thead>
            <tr>
                <td colspan="2" style="font-size: 11px;" class="bg-blue td-border"><b>E. DIAGNOSTICOS</b></td>
                <td class="td-border" style="width: 8%;"><b>CIE-10</b></td>
                <td class="td-border" style="width: 6%;"><b>PRE</b></td>
                <td class="td-border" style="width: 6%;"><b>DEF</b></td>
            </tr>
            </thead>
            <tbody>
                @php $contador = 1; @endphp
                @foreach ($cie10 as $key => $val)
                @php
                $c10 = Sis_medico\Cie_10_3::find($val->cie10);
                if (is_null($c10)) {
                $c10 = Sis_medico\Cie_10_4::find($val->cie10);
                }
                @endphp

                <tr>

                    <td class="td-border bg-green diagnostico-contador"><b>{{ $contador }}</b></td>
                    <td class="td-border" style="width: 77%;">{{ $c10->descripcion }}</td>
                    <td class="text-center td-border" style="width: 8%;">{{ $c10->id }}</td>
                    <td class="text-center td-border" style="width: 6%;">@if ($val->presuntivo_definitivo == 'PRESUNTIVO') X @endif</td>
                    <td class="text-center td-border" style="width: 6%;">@if ($val->presuntivo_definitivo == 'DEFINITIVO') X @endif</td>
                    @php $contador++; @endphp

                </tr>

                @endforeach
            </tbody>


        </table>
        <div class="section-gap"></div>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 11px;" class="bg-blue"><b>E. PLAN DE DIAGNÓSTICO PROPUESTO </b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border" style="height: 28px;"></td>
                </tr>
            </tbody>
        </table>
        <div class="section-gap"></div>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 11px;" class="bg-blue"><b>F. PLAN TERAPEÚTICO PROPUESTO </b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border" style="height: 28px;"></td>
                </tr>
            </tbody>
        </table>
        <div class="section-gap"></div>
        @if(isset($empresa) && isset($plano_cabecera) && $empresa->id == '1307189140001' && $plano_cabecera->id_seguro == 2)
         <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td colspan="5" style="font-size: 11px;" class="bg-blue"><b>G. DATOS DEL PROFESIONAL RESPONSABLE</b></td>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td style="width:14%; height: 10px;" class="bg-green text-center"><b>FECHA</b></td>
                <td style="width:8%; height: 10px;" class="bg-green text-center"><b>HORA</b></td>
                <td style="width:20%" class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                <td style="width:20%" class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                <td style="width:15%" class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
            </tr>
            <tr>
            <td class="text-center" style="width:5%; height: 18px;">{{$fecha}}</td>
            <td class="text-center" style="width:5%; height: 18px;">@if($historia_clinica->seguro->id == 2) @else {{$hora}} @endif</td>
                <td class="text-center" style="width:14%; height: 18px;">{{$crm->nombre1}}</td>
                <td class="text-center" style="width:14%; height: 18px;">{{$crm->apellido1}}</td>
                <td class="text-center" style="width:14%; height: 18px;">{{$crm->apellido2}}</td>
            </tr>
            <tr>
                <td colspan="2" class="td-border bg-green text-center"><b>NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
                <td colspan="2" class="td-border bg-green text-center"><b>FIRMA</b></td>
                <td class="td-border bg-green text-center"><b>SELLO</b></td>
            </tr>
            <tr>

                <td colspan="2" class="text-center" style="width:12%; height:60px; font-size:15px">{{$crm->id}}</td>
                <td colspan="2" class="text-center" style="width:25%; height:60px">
                    @php
                    $firma = null;
                   
                    $firma = Sis_medico\Firma_Usuario::where('id_usuario',$crm->id)->first();

                    
                    @endphp
                    @if(!is_null($firma))
                        @if(!is_null($firma->firma))
                            <img width=130 height=45 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" style="" align=center hspace=12><br>
                        @else
                            <br><br><br>
                            <b style="font-size: 10px;">@if($firma->sexo == '2') Dra. @else Dr. @endif  {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}}</b><br>
                        @endif
                    @endif
                </td>
                <td class="text-center" style="width:25%; height:60px;">
                @if(!is_null($firma))
                    @if(!is_null($firma->doctor)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @if($firma->doctor->nombre2!='N/A'){{$firma->doctor->nombre2}}@endif @endif<br>
                    {{$historia_clinica->agenda->especialidad->nombre}} <br>
                    @if(!is_null($firma->doctor)) Cedula No. {{$firma->doctor->id}} @endif
                @endif
                </td>
            </tr>
            <tr>
                <td colspan="2"><b>SNS-MSP/HCU-form.007/2021</b></td>
                <td colspan="3" class="text-right"><b>INTERCONSULTA - SOLICITUD (3)</b></td>
            </tr>
            </tbody>
        </table>
        @else
         <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td colspan="5" style="font-size: 11px;" class="bg-blue"><b>G. DATOS DEL PROFESIONAL RESPONSABLE</b></td>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td style="width:14%; height: 10px;" class="bg-green text-center"><b>FECHA</b></td>
                <td style="width:8%; height: 10px;" class="bg-green text-center"><b>HORA</b></td>
                <td style="width:20%" class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                <td style="width:20%" class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                <td style="width:15%" class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
            </tr>
            <tr>
                <td class="text-center" style="width:5%; height: 18px;">{{$fecha}}</td>
                <td class="text-center" style="width:5%; height: 18px;">@if($historia_clinica->seguro->id == 2) @else {{$hora}} @endif</td>
                <td class="text-center" style="width:14%; height: 18px;">{{$doctor->nombre1}}</td>
                <td class="text-center" style="width:14%; height: 18px;">{{$doctor->apellido1}}</td>
                <td class="text-center" style="width:14%; height: 18px;">{{$doctor->apellido2}}</td>
            </tr>
            <tr>
                <td colspan="2" class="td-border bg-green text-center"><b>NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
                <td colspan="2" class="td-border bg-green text-center"><b>FIRMA</b></td>
                <td class="td-border bg-green text-center"><b>SELLO</b></td>
            </tr>
            <tr>

                <td colspan="2" class="text-center" style="width:12%; height:60px; font-size:15px">{{$doctor->id}}</td>
                <td colspan="2" class="text-center" style="width:25%; height:60px">
                    @php
                    $firma = null;

                    $firma = Sis_medico\Firma_Usuario::where('id_usuario',$doctor->id)->first();


                    @endphp
                    @if(!is_null($firma))
                    @if(!is_null($firma->firma))
                    <img width=130 height=45 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" style="" align=center hspace=12><br>
                    @else
                    <br><br><br>
                    <b style="font-size: 10px;">@if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}}</b><br>
                    @endif
                    @endif
                </td>
                <td class="text-center" style="width:25%; height:60px;">
                    @if(!is_null($firma))
                    @if(!is_null($firma->doctor)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @if($firma->doctor->nombre2!='N/A'){{$firma->doctor->nombre2}}@endif @endif<br>
                    {{$historia_clinica->agenda->especialidad->nombre}} <br>
                    @if(!is_null($firma->doctor)) Cedula No. {{$firma->doctor->id}} @endif
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2"><b>SNS-MSP/HCU-form.007/2021</b></td>
                <td colspan="3" class="text-right"><b>INTERCONSULTA - SOLICITUD (4)</b></td>
            </tr>
            </tbody>
        </table>
        @endif

    </main>
</body>
</html>
