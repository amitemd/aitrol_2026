<!DOCTYPE html>
<html>
<?php
$fecha_actual=date('Y-m-d');
$hora_actual = date('H:i:s');

$usuarioLogueado = auth()->user();
$rd = rand();
$logoPath = public_path('assets/images/logo_certificado_borja.png');
$logoSrc = is_file($logoPath) && is_readable($logoPath)
    ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
    : '';
$fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$diaNacimiento = $fechaNacimiento->format('d');

$mesNacimiento = $fechaNacimiento->format('m');
$pdfPage = $pdfPage ?? 'all';
$pdfPageNumber = $pdfPageNumber ?? 1;
?>

<head>
    <meta charset="utf-8">
    <title>formulario 009</title>
    <style>
        @page {
            margin: 0;
        }

        /* Define now the real margins of every page in the PDF */
        body {
            margin-top: 5px;
            margin-left: 5px;
            margin-right: 5px;
            margin-bottom: 5px;
            font-size: 10px;
        }

        /* Define the header rules */
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1cm;
        }

        footer {
            display: table;
            position: fixed;
            right: 0;
            bottom: 30px;
            left: 0;
            width: 100%;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            border-top: 1px solid #999;
            font-size: 10px;
            font-weight: 700;
            line-height: 1;
            padding-top: 8px;
            table-layout: fixed;
        }

        footer > span { display: table-cell; width: 50%; }
        footer .document-name { text-align: right; }
        .page-number:after { content: counter(page); }

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

        .td-not-border-bottom {
            border-top: 1px solid;
            border-left: 1px solid;
            border-right: 1px solid;
        }

        .td-not-border-top {
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

        .text-left-top {
            text-align: left;
            vertical-align: text-top;
        }

        .width5 {
            width: 20px;
        }

        .width20 {
            width: 20px;
        }

        .width100 {
            width: 100px;
        }
        .page-break-before {
            page-break-before: always;
        }

        .page-break-after {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <footer>
        <span>SNS-MSP / HCU-form.019 / 2021</span>
        <span class="document-name">CUIDADOS POST ANESTÉSICO ({{$pdfPageNumber}})</span>
    </footer>
    <header>

    </header>
    @if($pdfPage !== 'second')
    <main>
        <table border="1" style="width: 99%">
            <tr>
                <td colspan="12">
                    @if($logoSrc !== '')
                    <img src="{{ $logoSrc }}" alt="Clínica Borja" style="width:750px;height: 80px;">
                    @endif
                </td>
            </tr>
        </table>
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="14" class="bg-blue"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width=118 colspan="3" class="bg-green text-center" valign=top style='width:105pt;'>
                        <p class=MsoNormal align=center style='text-align:center;line-height:normal;'><b><span>INSTITUCIÓN DEL SISTEMA </span></b></p>
                    </td>
                    <td colspan="3" class="bg-green text-center"><b>ESTABLECIMIENTO DE SALUD</b></td>
                    <td colspan="3" class="bg-green text-center"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                    <td colspan="5" class="bg-green text-center"><b>NÚMERO DE ARCHIVO</b></td>
                </tr>
                <tr>
                    <td colspan="3" valign=top style='width:88.3pt;'>
                        <p class=MsoNormal align=center style='text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$historia->seguro->nombre}} </span></p>
                    </td>
                    <td colspan="3">
                        <p class=MsoNormal align=center style='text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$empresa->nombrecomercial}} </span></p>
                    </td>
                    <td colspan="3">
                        <p class=MsoNormal align=center style='text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$paciente->id}}</span></p>
                    </td>
                    <td colspan="5">&nbsp;</td>
                </tr>
                <tr> 
                    <td rowspan="2" colspan="3" class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>SEGUNDO NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center">
                        <p align=center style='text-align:center;line-height:normal;'><b><span style='font-size:10.0pt;'>SEXO</span></b></p>
                    </td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>FECHA DE NACIMIENTO</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>EDAD</b></td>
                    <td colspan="4" class="bg-green text-center"><b>CONDICIÓN EDAD (MARCAR)</b></td>
                </tr>
                <tr>
                    <td class="bg-green text-center">H</td>
                    <td class="bg-green text-center">D</td>
                    <td class="bg-green text-center">M</td>
                    <td class="bg-green text-center">A</td>
                </tr>
                <tr>
                    <td colspan="3">
                        <p class=MsoNormal align=center style='text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$paciente->apellido1}}</span></p>
                    </td>
                    <td colspan="2">
                        <p class=MsoNormal align=center style='text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$paciente->apellido2}}</span></p>
                    </td>
                    <td colspan="1">
                        <p class=MsoNormal align=center style='text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$paciente->nombre1}}</span></p>
                    </td>
                    <td colspan="1">
                        <p class=MsoNormal align=center style='text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$paciente->nombre2}}</span></p>
                    </td>
                    <td colspan="1" valign=top style='width:56.25pt;padding:0cm 5.4pt 0cm 5.4pt'>
                        <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
                    text-align:center;line-height:normal'><span style='font-size:9.0pt'>@if($paciente->sexo=='1')MASCULINO @elseif($paciente->sexo=='2')FEMENINO @endif</span></p>
                    </td>
                    <td class="text-center" style="font-size: 11px;" colspan="1">{{$paciente->fecha_nacimiento}}</td>
                    <td colspan="1" class="text-center">{{$edad}}</td>
                    <td colspan="1"></td>
                    <td colspan="1" class="text-center">{{$diaNacimiento}}</td>
                    <td colspan="1" class="text-center">{{$mesNacimiento}}</td>
                    <td colspan="1" class="text-center">{{date('Y', strtotime($paciente->fecha_nacimiento))}}</td>
                </tr>
            </tbody>
        </table>
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="9" class="bg-blue"><b>B. PROCEDIMIENTO QUIRÚRGICO ANESTÉSICO</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 10%;" class="bg-green text-center"><b>ESPECIALIDAD</b></td>
                    <td style="width: 40%;">{{$especialidad->nombre}}</td>
                    <td style="width: 10%;" class="bg-green text-center"><b>DIAGNÓSTICO</b></td>
                    <td colspan="6" style="width: 40%;">&nbsp;</td>
                </tr>
                <tr>
                    <td rowspan="2" style="width: 10%;" class="bg-green text-center"><b>CIRUGÍA REALIZADA</b></td>
                    <td rowspan="2" style="width: 40%;">{{$cirugia}}</td>
                    <td rowspan="2" style="width: 10%;" class="bg-green text-center"><b>TÉCNICA ANESTÉSICA</b></td>
                    <td style="width: 10%;">GENERAL</td>
                    <td style="width: 3%;" class="text-center">@if($evolucion_anestesiologo->tecnica_anestesica == '0') X @else &nbsp; @endif</td>
                    <td style="width: 10%;">LOCAL</td>
                    <td style="width: 3%;" class="text-center">@if($evolucion_anestesiologo->tecnica_anestesica == '1') X @else &nbsp; @endif</td>
                    <td style="width: 10%;">TRONCULAR</td>
                    <td style="width: 4%;" class="text-center">@if($evolucion_anestesiologo->tecnica_anestesica == '2') X @else &nbsp; @endif</td>
                </tr>
                <tr>
                    <td style="width: 10%;">NEUROAXIAL</td>
                    <td style="width: 3%;" class="text-center">@if($evolucion_anestesiologo->tecnica_anestesica == '3') X @else &nbsp; @endif</td>
                    <td style="width: 10%;">INTRAVENOSA</td>
                    <td style="width: 3%;" class="text-center">@if($evolucion_anestesiologo->tecnica_anestesica == '4') X @else &nbsp; @endif</td>
                    <td style="width: 10%;">SEDACIÓN</td>
                    <td style="width: 4%;" class="text-center">@if($evolucion_anestesiologo->tecnica_anestesica == '5') X @else &nbsp; @endif</td>
                </tr>
            </tbody>
        </table>
         <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="40" class="bg-blue"><b>C. ENTREGA - RECEPCIÓN DEL PACIENTE</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="3" class="text-center width100">INGRESO A UNIDAD CUIDADOS POSTANESTÉSICOS</td>
                    <td rowspan="2" colspan="37" style="width: 60%;" class="text-center">ESCALAS / ÍNDICES / PUNTAJES</td>
                    <td colspan="2" class="text-center width100"><b>RESPONSABLES</b></td>
                </tr>
                <tr>
                    <td class="text-center" colspan="2"><b>ENTREGA</b></td>
                </tr>
                <tr>
                    <td colspan="8" class="bg-green text-center"><b>ALDRETE MODIFICADO</b></td>
                    <td colspan="12" class="bg-green text-center"><b>BROMAGE</b></td>
                    <td colspan="14" class="bg-green text-center"><b>RAMSAY</b></td>
                    <td rowspan="2" colspan="3" class="bg-green text-center"></td>
                    <td>NOMBRE</td>
                    <td class="width100"></td>
                </tr>
                <tr>
                    <td rowspan="2" style="width: 20%;" class="text-left">FECHA: <?php $fecha_recepcion = date('Y-m-d', strtotime($agenda->fechaini)); ?>{{$fecha_recepcion}}</td>
                    <td class="text-center width20">7</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->aldrete_modificado == '1') X @else &nbsp; @endif</td>
                    <td class="text-center width20">8</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->aldrete_modificado == '2') X @else &nbsp; @endif</td>
                    <td class="text-center width20">9</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->aldrete_modificado == '3') X @else &nbsp; @endif</td>
                    <td class="text-center width20">10</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->aldrete_modificado == '4') X @else &nbsp; @endif</td>
                    <td colspan="2" >100%</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->bromage == '1') X @else &nbsp; @endif</td>
                    <td colspan="2" >66%</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->bromage == '2') X @else &nbsp; @endif</td>
                    <td colspan="2" >33%</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->bromage == '3') X @else &nbsp; @endif</td>
                    <td colspan="2">0%</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->bromage == '4') X @else &nbsp; @endif</td>
                    <td class="text-center width20">0</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->ramsay == '1') X @else &nbsp; @endif</td>
                    <td class="text-center width20">1</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->ramsay == '2') X @else &nbsp; @endif</td>
                    <td class="text-center width20">2</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->ramsay == '3') X @else &nbsp; @endif</td>
                    <td class="text-center width20">3</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->ramsay == '4') X @else &nbsp; @endif</td>
                    <td class="text-center width20">4</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->ramsay == '5') X @else &nbsp; @endif</td>
                    <td class="text-center width20">5</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->ramsay == '6') X @else &nbsp; @endif</td>
                    <td class="text-center width20">6</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->ramsay == '7') X @else &nbsp; @endif</td>
                    <td>FIRMA</td>
                    <td class="width100"></td>
                </tr>
                <tr>
                    <td class="bg-green text-center" colspan="22">EVA</td>
                    <td class="bg-green text-center" colspan="15">STEWARD</td>
                    <td class="text-center" colspan="2"><b>RECIBE</b></td>
                </tr>
                <tr>
                    <td rowspan="2" style="width: 20%;" class="text-left">HORA: <?php $hora_recepcion = date('H:i', strtotime($agenda->fechaini)); ?>{{$hora_recepcion}}</td>
                    <td class="text-center width20">0</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->eva == '1') X @else &nbsp; @endif</td>
                    <td class="text-center width20">1</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->eva == '2') X @else &nbsp; @endif</td>
                    <td class="text-center width20">2</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->eva == '3') X @else &nbsp; @endif</td>
                    <td class="text-center width20">3</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->eva == '4') X @else &nbsp; @endif</td>
                    <td class="text-center width20">4</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->eva == '5') X @else &nbsp; @endif</td>
                    <td class="text-center width20">5</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->eva == '6') X @else &nbsp; @endif</td>
                    <td class="text-center width20">6</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->eva == '7') X @else &nbsp; @endif</td>
                    <td class="text-center width20">7</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->eva == '8') X @else &nbsp; @endif</td>
                    <td class="text-center width20">8</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->eva == '9') X @else &nbsp; @endif</td>
                    <td class="text-center width20">9</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->eva == '10') X @else &nbsp; @endif</td>
                    <td class="text-center width20">10</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->eva == '11') X @else &nbsp; @endif</td>
                    <td class="text-center width20">0</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->steward == '1') X @else &nbsp; @endif</td>
                    <td class="text-center width20">1</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->steward == '2') X @else &nbsp; @endif</td>
                    <td class="text-center width20">2</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->steward == '3') X @else &nbsp; @endif</td>
                    <td class="text-center width20">3</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->steward == '4') X @else &nbsp; @endif</td>
                    <td class="text-center width20">4</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->steward == '5') X @else &nbsp; @endif</td>
                    <td class="text-center width20">5</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->steward == '6') X @else &nbsp; @endif</td>
                    <td class="text-center width20" colspan="2">6</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->steward == '7') X @else &nbsp; @endif</td>
                    <td>NOMBRE</td>
                    <td class="width100"></td>
                </tr>
                <tr>
                    <td class="bg-green text-left" colspan="2">OTRO</td>
                    <td class="text-center" colspan="35"></td>
                    <td class="text-left">FIRMA</td>
                    <td class="width100"></td>
                </tr>
            </tbody>
    </table>

        
        <table border="1" style="width: 99%">
            <thead>
                <tr>
                    <td colspan="7" class="bg-blue"><b>D. MONITORIZACIÓN</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7"  class="text-center">
                        @if(!empty($postanestesicoImage))
                        <img style="width: 1100px; height: 250px;" src="{{$postanestesicoImage}}" style="background-size: cover !important;">
                        @elseif(is_null($anestesiologia->post_anastesico_path))
                        <img style="width: 1100px; height: 250px;" src="{{base_path().'/public/assets/images/record_postanestesico.png'}}" style="background-size: cover !important;">
                        @else
                        <img style="width: 1100px; height: 250px;" src="{{base_path().'/storage/app/hc_ima/trans/'.$anestesiologia->post_anastesico_path}}" style="background-size: cover !important;">
                        @endif
                    </td>
                </tr>
                 <tr>
                    <td style="width: 16%;" class="bg-green text-left">ALDRETE MODIFICADO</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_aldrete1}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_aldrete2}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_aldrete3}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_aldrete4}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_aldrete5}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_aldrete6}}</td>
                </tr>
                <tr>
                    <td style="width: 16%;" class="bg-green text-left">BROMAGE</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_bromage1}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_bromage2}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_bromage3}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_bromage4}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_bromage5}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_bromage6}}</td>
                </tr>
                <tr>
                    <td style="width: 16%;" class="bg-green text-left">RAMSAY</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_ramsay1}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_ramsay2}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_ramsay3}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_ramsay4}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_ramsay5}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_ramsay6}}</td>
                </tr>
                <tr>
                    <td style="width: 16%;" class="bg-green text-left">EVA</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_eva1}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_eva2}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_eva3}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_eva4}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_eva5}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_eva6}}</td>
                </tr>
                <tr>
                    <td style="width: 16%;" class="bg-green text-left">STEWARD</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_steward1}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_steward2}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_steward3}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_steward4}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_steward5}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_steward6}}</td>
                </tr>
                <tr>
                    <td style="width: 16%;" class="bg-green text-left">OTRO</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_otro1}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_otro2}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_otro3}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_otro4}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_otro5}}</td>
                    <td class="text-center" style="width: 14%;">{{$evolucion_anestesiologo->valor_otro6}}</td>
                </tr>
            </tbody>
        </table>

    </main>
    @endif
    
    @if($pdfPage !== 'first')
    <main class="{{$pdfPage === 'all' ? 'page-break-before' : ''}}">

    <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="2" style="width: 50%;" class="bg-blue text-left"><b>E. INGRESOS</b></td>
                    <td colspan="2" style="width: 50%;" class="bg-blue text-left"><b>F. ELIMINACIÓN</b></td>
                </tr>
            </thead>
            <tbody>
                
                <tr>
                    <td style="width: 25%;" class="text-left">ORAL</td>
                    <td style="width: 25%;">{{$evolucion_anestesiologo->oral}}</td>
                    <td style="width: 25%;">DIURESIS O SONDA VESICAL</td>
                    <td style="width: 25%;">{{$evolucion_anestesiologo->diuresis_sonda_vesical}}</td>
                </tr>
                <tr>
                    <td style="width: 25%;" class="text-left">INFUSIONES</td>
                    <td style="width: 25%;">{{$evolucion_anestesiologo->infusion}}</td>
                    <td style="width: 25%;">VÓMITO O SONDA NASOGÁSTRICO</td>
                    <td style="width: 25%;">{{$evolucion_anestesiologo->vomito}}</td>
                </tr>
                <tr>
                    <td style="width: 25%;" class="text-left">SANGRE Y DERIVADOS</td>
                    <td style="width: 25%;">{{$evolucion_anestesiologo->sangre_derivados}}</td>
                    <td style="width: 25%;">TORÁCICO</td>
                    <td style="width: 25%;">{{$evolucion_anestesiologo->toracico}}</td>
                </tr>
                <tr>
                    <td style="width: 25%;" class="text-left">OTROS</td>
                    <td style="width: 25%;">{{$evolucion_anestesiologo->vomito}}</td>
                    <td style="width: 25%;">DRENAJES</td>
                    <td style="width: 25%;">{{$evolucion_anestesiologo->drenajes}}</td>
                </tr>
                <tr>
                    <td rowspan="2" style="width: 25%;" class="text-left"><b>TOTAL</b></td>
                    <td rowspan="2" style="width: 25%;"></td>
                    <td style="width: 25%;">OTRAS</td>
                    <td style="width: 25%;">{{$evolucion_anestesiologo->eliminacion_otras}}</td>
                </tr>
                <tr>
                    <td style="width: 25%;" class="text-left"><b>TOTAL</b></td>
                    <td style="width: 25%;"></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="5" class="bg-blue"><b>G. EVOLUCIÓN POSTANESTÉSICA</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="bg-green text-center">FECHA</td>
                    <td class="bg-green text-center">HORA</td>
                    <td class="bg-green text-center"><b>EVOLUCIÓN</b></td>
                    <td class="bg-green text-center"><b>PRESCRIPCIÓN</b></td>
                    <td class="bg-green text-center">ADMINISTRACIÓN FÁRMACOS</td>
                </tr>
                <tr>
                    <td class="text-center"><?php $fecha = date('Y-m-d', strtotime($evolucion_anestesiologo->created_at)); ?>{{$fecha}}</td>
                    <td class="text-center"><?php $hora = date('H:i', strtotime($evolucion_anestesiologo->created_at)); ?>{{$hora}}</td>
                    <td class="text-center">{{$evolucion_anestesiologo->evolcuion}}</td>
                    <td class="text-center">{{$evolucion_anestesiologo->prescripcion}}</td>
                    <td class="text-center">{{$evolucion_anestesiologo->administracion_farmacos}}</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="6" class="bg-blue"><b>H. CONDICIÓN DE EGRESO</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 50px;"><b>CONSTANTES VITALES DE ENTREGA</b></td>
                    <td class="text-center">TA @if($anestesiologia->constantes_vitales == 1) X @else &nbsp; @endif</td>
                    <td class="text-center">FC @if($anestesiologia->constantes_vitales == 2) X @else &nbsp; @endif</td>
                    <td class="text-center">FR @if($anestesiologia->constantes_vitales == 3) X @else &nbsp; @endif</td>
                    <td class="text-center">SAT. O2 @if($anestesiologia->constantes_vitales == 4) X @else &nbsp; @endif</td>
                    <td class="text-center">T° @if($anestesiologia->constantes_vitales == 5) X @else &nbsp; @endif</td>
                </tr>
                <tr>
                    <td style="width: 50px;" rowspan="3"><b>EGRESO DE LA UNIDAD CUIDADOS POSTANESTÉSICOS</b></td>
                    <td class="text-center"><b>ESCALA / ÍNDICE</b></td>
                    <td class="text-center"><b>PUNTAJE</b></td>
                    <td colspan="3" class="text-center"><b>RESPONSABLES</b></td>
                </tr>
                <tr>
                    <td class="text-right">ALDRETE MODIFICADO</td>
                    <td class="text-center">{{$evolucion_anestesiologo->valor_aldrete_egreso}}</td>
                    <td colspan="3" class="text-center"><b>ENTREGA</b></td>
                </tr>
                <tr>
                    <td class="text-right">BROMAGE</td>
                    <td class="text-center">{{$evolucion_anestesiologo->valor_bromage_egreso}}</td>
                    <td class="text-left"><b>NOMBRE</b></td>
                    <td colspan="2" class="text-center"></td>
                </tr>
                <tr>
                    <td class="text-left" rowspan="2"><b>FECHA:</b> <?php $fecha_egreso = date('Y-m-d', strtotime($evolucion_anestesiologo->created_at)); ?>{{$fecha_egreso}}</td>
                    <td class="text-right">RAMSAY</td>
                    <td class="text-center">{{$evolucion_anestesiologo->valor_ramsay_egreso}}</td>
                    <td class="text-left"><b>FIRMA</b></td>
                    <td colspan="2" class="text-center"></td>
                </tr>
                <tr>
                    <td class="text-right">EVA</td>
                    <td class="text-center">{{$evolucion_anestesiologo->valor_eva_egreso}}</td>
                    <td colspan="3" class="text-center"><b>RECIBE</b></td>
                </tr>
                <tr>
                    <td class="text-left" rowspan="2"><b>HORA:</b> <?php $hora_egreso = date('H:i', strtotime($evolucion_anestesiologo->created_at)); ?>{{$hora_egreso}}</td>
                    <td class="text-right">STEWARD</td>
                    <td class="text-center">{{$evolucion_anestesiologo->valor_steward_egreso}}</td>
                    <td class="text-left"><b>NOMBRE</b></td>
                    <td colspan="2" class="text-center"></td>
                </tr>
                <tr>
                    <td class="text-right">OTRO:</td>
                    <td class="text-center">{{$evolucion_anestesiologo->valor_otro_egreso}}</td>
                    <td class="text-left"><b>FIRMA</b></td>
                    <td colspan="2" class="text-center"></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="5" class="bg-blue"><b>I. DATOS DEL PROFESIONAL QUE AUTORIZA EL EGRESO</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 15%" class="bg-green text-center"><b>FECHA <br> (aaaa-mm-dd)</b></td>
                    <td style="width: 10%" class="bg-green text-center"><b>HORA <br> (hh:mm)</b></td>
                    <td style="width: 25%" class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                    <td style="width: 25%" class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                    <td style="width: 25%" class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
                </tr>
                <tr>
                    <td class="text-center">{{$fecha_actual}}</td>
                    <td class="text-center">{{$hora_actual}}</td>
                    <td class="text-center">{{$doctor->nombre1}}</td>
                    <td class="text-center">{{$doctor->apellido1}}</td>
                    <td class="text-center">{{$doctor->apellido2}}</td>
                </tr>
                <tr>
                    <td colspan="2" class="bg-green text-center">NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</td>
                    <td class="bg-green text-center">FIRMA</td>
                    <td colspan="2" class="bg-green text-center">SELLO</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-left-top">{{$doctor->id}}</td>
                    <td style="height: 100px;">
                        @if(!is_null($firma))
    					   <img width=170 height=80 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center hspace=12><br>
                        @endif
                    </td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </tbody>
        </table>
       


        <table border="1" style="width: 100%">
            <tr>
                <td rowspan="2" style="width: min-content;"><b>CONDICIONES AL SALIR</b></td>
                <td style="width: min-content;">EXTUBADO</td>
                <td class="text-center">@if($anestesiologia->condiciones_al_salir == '1') X @else &nbsp; @endif</td>
                <td style="width: min-content;" rowspan="2"><b>CONDUCIDO A:</b></td>
                <td style="width: min-content;" rowspan="2">HOSPITALIZACIÓN</td>
                <td class="text-center" rowspan="2">@if($anestesiologia->conducido == '1') X @else &nbsp; @endif</td>
                <td style="width: min-content;" rowspan="2">UNIDAD CUIDADOS INTENSIVOS</td>
                <td class="text-center" rowspan="2">@if($anestesiologia->conducido == '2') X @else &nbsp; @endif</td>
                <td style="width: min-content;" rowspan="2">CRÍTICOS DE EMERGENCIA</td>
                <td class="text-center" rowspan="2">@if($anestesiologia->conducido == '3') X @else &nbsp; @endif</td>
                <td style="width: min-content;" rowspan="2">DOMICILIO</td>
                <td class="text-center" rowspan="2">@if($anestesiologia->conducido == '4') X @else &nbsp; @endif</td>
                <td style="width: min-content;" rowspan="2">MORGUE</td>
                <td class="text-center" rowspan="2">@if($anestesiologia->conducido == '5') X @else &nbsp; @endif</td>
            </tr>
            <tr>
                <td style="width: min-content;">INTUBADO</td>
                <td class="text-center">@if($anestesiologia->condiciones_al_salir == '2') X @else &nbsp; @endif</td>
            </tr>
        </table>

    </main>
    @endif
</body>

</html>
