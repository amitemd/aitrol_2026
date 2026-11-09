<!DOCTYPE html>
<html>

@php
$fecha=date('Y-m-d');
$hora = date('H:i:s');
$logoPath = public_path('assets/images/logo_certificado_borja.png');
$logoSrc = is_file($logoPath) && is_readable($logoPath)
    ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
    : '';
$isLandscapeFirstPage = ($pdfPage ?? 'all') === 'first';
$firstPageCanvasHeight = (int) ($firstPageCanvasHeight ?? 280);
$firstPageFontSize = (float) ($firstPageFontSize ?? 5);
$firstPageLineHeight = (float) ($firstPageLineHeight ?? 0.88);
$firstPageLogoHeight = (int) ($firstPageLogoHeight ?? 52);
@endphp


<?php

$fechaNacimiento = new DateTime($record->historia->paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($record->historia->paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($record->historia->paciente->fecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');

$nombre = '';
$id_anestesiologo = null;
if (isset($record->anestesiologo)) {
    $nombre = $record->anestesiologo->nombre1 . ' ' . $record->anestesiologo->apellido1 . ' ' . $record->anestesiologo->apellido2;
    $id_anestesiologo = $record->anestesiologo->id;
}

?>



<head>
    <meta charset="utf-8">
    <title>Formulario018A</title>
    <style>
        @page {
            margin: 0;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin-top: 2px;
            margin-left: 2px;
            margin-right: 2px;
            margin-bottom: 2px;
            font-size: 6px;
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
            display: table;
            position: fixed;
            bottom: 30px;
            left: 0cm;
            right: 0cm;
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
            width: 100%;
            border-collapse: collapse;
        }

        .table2d {
            border: none;
            text-align: center;
        }

        .titulo {
            background-color: #C7C3C2;
            font-size: 6px; 
        }

        p {
            padding-top: 0;
            padding-bottom: 0;
            margin: 0;
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
            border-left: 1px solid;
            border-right: 1px solid;
            border-bottom: 1px solid;
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
            font-size: 6px; 
        }

        .bg-green {
            background-color: #BADAC8;
            font-size: 6px; 
        }

        .bg-yellow {
            background-color: #F1FD77;
            font-size: 6px; 
        }

        .bg-purple {
            background-color: #CCCCFF;
            font-size: 6px;
        }

        .bg-turquoise {
            background-color: #40E0D0;
        }

        .bg-celeste {
            background-color: #00BFFF;
        }

        .text-center {
            text-align: center;
            font-size: 6px; 
        }


        .texto-izquierda-arriba {
            text-align: left;
            vertical-align: top;
        }

        .justify-text {
            text-align: justify;
        }

        .align-left {
            text-align: left;
            font-size: 6px; 
        }

        .align-right {
            text-align: right;
            font-size: 6px; 
        }
        td.contenedor-texto {
            padding: 20px;
            text-align: left;
        }

        .page-break-before {
            page-break-before: always;
        }



        ol {
            list-style-type: decimal;
            margin-left: 2px;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            width: 100%;
            border: 1px solid black;
        }
        .header {
            grid-column: span 6;
            background-color: #d3d3f9;
            font-size: 6px;
            font-weight: bold;
            padding: 5px;
            text-align: center;
        }
        .cell {
            border: 1px solid black;
            text-align: center;
            padding: 2px;
            box-sizing: border-box;
        }

        /* A-J debe permanecer completo en una sola hoja A4 horizontal. */
        body.pdf-page-first {
            font-size: {{ $firstPageFontSize }}px;
            line-height: {{ $firstPageLineHeight }};
        }
        body.pdf-page-first table {
            line-height: {{ $firstPageLineHeight }};
        }
        body.pdf-page-first td {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        body.pdf-page-first .bg-blue,
        body.pdf-page-first .bg-green,
        body.pdf-page-first .bg-yellow,
        body.pdf-page-first .bg-purple,
        body.pdf-page-first .text-center,
        body.pdf-page-first .align-left,
        body.pdf-page-first .align-right {
            font-size: {{ $firstPageFontSize }}px !important;
        }
        body.pdf-page-first footer {
            bottom: 2px;
            font-size: 7px;
            padding-top: 2px;
        }
    </style>
</head>
@php $empresa = $hc_procedimiento->empresa; @endphp 
<body class="{{ $isLandscapeFirstPage ? 'pdf-page-first' : 'pdf-page-'.($pdfPage ?? 'all') }}">
    <footer><span>SNS-MSP / HCU-form.018A / 2021</span><span class="document-name">TRANSANESTÉSICO ({{ $pdfPageNumber ?? 1 }})</span></footer>
    <main>
        @if(($pdfPage ?? 'all') !== 'second')
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tr>
                <td style="height: {{ $isLandscapeFirstPage ? '38px' : '50px' }};">
                    @if($logoSrc !== '')
                    <img src="{{ $logoSrc }}" alt="Clínica Borja" style="width:750px;height: {{ $isLandscapeFirstPage ? $firstPageLogoHeight.'px' : '80px' }};">
                    @endif
                </td>
            </tr>         
        </table>
      
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="14" class="bg-purple"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="bg-green text-center" style="width: 20%;"><b>INSTITUCION DEL SISTEMA</b></td>
                    <td colspan="4" class="bg-green text-center" style="width: 22%;"><b>ESTABLECIMIENTO DE SALUD</b></td>
                    <td colspan="4" class="bg-green text-center"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                    <td colspan="5" class="bg-green text-center"><b>NÚMERO DE ARCHIVO</b></td>
                </tr>
                <tr>
                    <td class="text-center">{{$record->historia->seguro->nombre}}</td>
                    <td colspan="4" class="text-center">{{$record->historia->agenda->empresa->nombrecomercial}}</td>
                    <td class="text-center" colspan="4">{{$record->historia->paciente->id}}</td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center" style="width: 15%;"><b>SEGUNDO NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>SEXO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>EDAD</b></td>
                    <td colspan="4" class="bg-green text-center"><b>CONDICIÓN EDAD (MARCAR)</b></td>
                </tr>
                <tr>
                    <td class="bg-green text-center">H</td>
                    <td class="bg-green text-center">D</td>
                    <td class="bg-green text-center">M</td>
                    <td class="bg-green text-center" style="width: 5%;">A</td>
                </tr>
                <tr>
                    <td class="text-center" colspan="2">{{$record->historia->paciente->apellido1}}</td>
                    <td class="text-center" colspan="2">{{$record->historia->paciente->apellido2}}</td>
                    <td class="text-center" colspan="2">{{$record->historia->paciente->nombre1}}</td>
                    <td class="text-center" colspan="1">{{$record->historia->paciente->nombre2}}</td>
                    <td class="text-center" colspan="1">@if($record->historia->paciente->sexo == 1) Masculino @else Femenino @endif</td>
                    <td class="text-center" colspan="2">{{$edad}}</td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1">{{$diaNacimiento}}</td>
                    <td class="text-center" colspan="1">{{$mesNacimiento}}</td>
                    <td class="text-center" colspan="1">{{ date('Y', strtotime($record->historia->paciente->fecha_nacimiento))}}</td>
                </tr>
            </tbody>
        </table>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td class="bg-green text-center" style="width: 5%;">FECHA</td>
                    <td style="width: 7%;"></td>
                    <td class="bg-green text-center" style="width: 5%;">TALLA</td>
                    <td style="width: 10%;"></td>
                    <td class="bg-green text-center" style="width: 7%;">PESO KG</td>
                    <td style="width: 12%;"></td>
                    <td class="bg-green text-center" style="width: 5%;">IMC</td>
                    <td style="width: 8%;"></td>
                    <td class="bg-green text-center" style="width: 12%;">GRUPO Y FACTOR</td>
                    <td style="width: 11%;"></td>
                    <td class="bg-green text-center" style="width: 5%;">SI</td>
                    <td></td>
                    <td class="bg-green text-center" style="width: 5%;">NO</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        @php 
            $cont = 0;
            $nombre = '';
            $nombrePre='';
            $ciePre='';
            $nombrePost='';
            $ciePost='';
            $id_anestesiologo = null;
            if(isset($record->anestesiologo)) {
                $nombre = $record->anestesiologo->nombre1 . ' ' . $record->anestesiologo->apellido1 . ' ' . $record->anestesiologo->apellido2;
                $id_anestesiologo = $record->anestesiologo->id;
            }
            $nombreAyudante = isset($record->ayudante) ? $record->ayudante->nombre1.' '.$record->ayudante->apellido1.' '.$record->ayudante->apellido2 : '';
            $nombreInstrumentista = isset($record->instrumentista) ? $record->instrumentista->nombre1.' '.$record->instrumentista->apellido1.' '.$record->instrumentista->apellido2 : '';
        @endphp
        @foreach ($cie10 as $key=>$val)
            @php
                $c3 = Sis_medico\Cie_10_3::find($val->cie10);
                if(!is_null($c3)){
                    if($nombrePre==''){
                        $nombrePre = $c3->descripcion;
                        $ciePre = $val->cie10;
                    }
                    else{
                        $nombrePost=$c3->descripcion;
                        $ciePost = $val->cie10;
                    }
                }
                else{
                    $c4 = Sis_medico\Cie_10_4::find($val->cie10);
                    if($nombrePre==''){
                        $nombrePre = $c3->descripcion;
                        $ciePre = $val->cie10;
                    }
                    else{
                        $nombrePost=$c3->descripcion;
                        $ciePost = $val->cie10;
                    }
                }
            @endphp
        @endforeach
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="12" class="bg-purple"><b>B. SERVICIO Y PRIORIDAD DE ATENCION</b></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center bg-green" style="width: 200px;">DIAGNÓSTICO PREOPERATORIO</td>
                    <td>{{$nombrePre}}</td>
                    <td class="text-center bg-green" >CIE</td>
                    <td>{{$ciePre}}</td>
                    <td class="text-center bg-green" style="width: 15%;">CIRUGÍA PROPUESTA</td>
                    <td colspan="2"></td>
                    <td class="text-center bg-green" style="width: 10%;">ESPECIALDIDAD</td>
                    <td rowspan="4" class="text-center bg-green" style="width: 5%;">PRIORIDAD</td>
                    <td style="width: min-content;">EMERGENTE</td>
                    <td style="width: 2%;"></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center bg-green" style="width: 200px;">DIAGNÓSTICO POSTOPERATORIO</td>
                    <td>{{$nombrePost}}</td>
                    <td class="text-center bg-green" >CIE</td>
                    <td>{{$ciePost}}</td>
                    <td class="text-center bg-green" style="width: 15%;">CIRUGÍA REALIZADA</td>
                    <td colspan="2"></td>
                    <td class="text-center" style="width: 10%;"></td>
                    <td style="width: min-content;">URGENTE</td>
                    <td style="width: 2%;">X</td>
                </tr>
                <tr>
                    <td class="text-center bg-green" style="width: 20%;">ANESTESIÓLOGO</td>
                    <td colspan="2">{{$nombre}}</td>
                    <td class="text-center bg-green" style="width: 10%;">AYUDANTE (S)</td>
                    <td>{{$nombreAyudante}}</td>
                    <td class="text-center bg-green" style="width: 15%;">INSTRUMENTISTA</td>
                    <td colspan="2">{{$nombreInstrumentista}}</td>
                    <td class="text-center bg-green" style="width: 10%;">QUIRÓFANO</td>
                    <td style="width: min-content;">ELECTIVO</td>
                    <td style="width: 2%;"></td>
                </tr>
                <tr>
                    <td class="text-center bg-green" style="width: 20%;">CIRUJANO</td>
                    <td colspan="2"></td>
                    <td class="text-center bg-green" style="width: 10%;">AYUDANTE (S)</td>
                    <td></td>
                    <td class="text-center bg-green" style="width: 15%;">CIRCULANTE</td>
                    <td colspan="2"></td>
                    <td class="text-center" style="width: 10%;"></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <tr>
                    <td colspan="12" class="text-center">OTROS</td>
                </tr>
            </tbody>
        </table>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="20" class="bg-purple"><b>C. SERVICIO Y PRIORIDAD DE ATENCION</b></td>
                </tr>
                <tr>
                    <td>CABEZA</td>
                    <td style="width: 3%;" class="text-center">@if($record->cabeza == '1') X </td> @endif
                    <td>ÓRGANOS</td>
                    <td style="width: 2%;" class="text-center">@if($record->organos == '1') X </td> @endif
                    <td>CUELLO</td>
                    <td style="width: 2%;" class="text-center">@if($record->cuello == '1') X </td> @endif
                    <td>COLUMNA</td>
                    <td style="width: 2%;" class="text-center">@if($record->columna == '1') X </td> @endif
                    <td>TÓRAX</td>
                    <td style="width: 2%;" class="text-center">@if($record->torax == '1') X </td> @endif
                    <td>ABDÓMEN</td>
                    <td style="width: 2%;" class="text-center">@if($record->abdomen == '1') X </td> @endif
                    <td>PELVIS</td>
                    <td style="width: 2%;" class="text-center">@if($record->pelvis == '1') X </td> @endif
                    <td>EXT.SUPERIORES</td>
                    <td style="width: 2%;" class="text-center">@if($record->e_superiores == '1') X </td> @endif
                    <td>EXT.INFERIORES</td>
                    <td style="width: 2%;" class="text-center">@if($record->e_inferiores == '1') X </td> @endif
                    <td>PERINEAL</td>
                    <td style="width: 2%;" class="text-center">@if($record->perineal == '1') X </td> @endif
                </tr>
            </tbody>
        </table>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td class="bg-purple"><b>D. REGISTRO TRANSANESTÉSICO</b></td>
                </tr>
                <tr>
                    <td class="text-center">
                    @if(!empty($transanestesicoImage))
                        <img style="width: 1100px;height: {{ $isLandscapeFirstPage ? $firstPageCanvasHeight.'px' : '380px' }};" src="{{$transanestesicoImage}}">
                    @elseif(!empty($record->tran_anastesico_path))
                        <img style="width: 1100px;height: {{ $isLandscapeFirstPage ? $firstPageCanvasHeight.'px' : '380px' }};" src="{{base_path().'/storage/app/hc_ima/trans/'.$record->tran_anastesico_path}}">
                    @elseif(!empty($record->url_imagen))
                        <img style="width: 1100px;height: {{ $isLandscapeFirstPage ? $firstPageCanvasHeight.'px' : '380px' }};" src="{{base_path().'/storage/app/hc_ima/'.$record->url_imagen}}">
                    @else
                        <img width="1000" height="{{ $isLandscapeFirstPage ? $firstPageCanvasHeight : '380' }}" src="{{base_path().'/public/assets/images/record_transanestesico.png'}}">
                    @endif
                    </td>
                </tr>
            </tbody>
        </table>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="8" class="bg-purple"><b>E. DROGAS ADMINISTRADAS</b></td>
                </tr>
                @php
                    $contador = 0;
                    $col= 4;
                    $row = 4;
                @endphp
                @for($j = 0;$j < $row;$j++)
                    <tr>
                        @for($i = 0;$i < $col;$i++)
                            @php $contador++; @endphp
                            <td style="width: 2%;" class="text-center">{{ $contador }}</td>
                            <td class="text-center">@if(isset($drogas[$contador-1])) {{ $drogas[$contador-1]->drogas }} @else &nbsp; @endif</td>
                        @endfor
                    </tr>
                @endfor
            </tbody>
        </table>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="36" class="bg-purple"><b>F. TÉCNICA ANESTÉSICA</b></td>
                </tr>
                <tr>
                    <td class="text-center bg-green" colspan="14"><b>GENERAL</b></td>
                    <td class="text-center bg-green" colspan="10" style="width: 32%;"><b>REGIONAL</b></td>
                    <td colspan="12" class="text-center bg-green">SEDO  - ANALGESIA</td>
                </tr>
                <tr>
                    <td class="text-center bg-green" colspan="8"><b>SISTEMA</b></td>
                    <td class="text-center bg-green" colspan="6"><b>APARATO</b></td>
                    <td class="bg-green" colspan="3">ASEPSIA CON</td>
                    <td colspan="2" style="width: 10%;">{{$record->asepsia}}</td>
                    <td class="bg-green" colspan="2">HABÓN CON</td>
                    <td colspan="3" style="width: 10%;">{{$record->habon}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td style="width: 5%;">ABIERTO</td>
                    <td class="text-center">@if($record->sistema == 'abierto') X @endif</td>
                    <td colspan="2" style="width: 5%;">SEMICERRADO</td>
                    <td class="text-center">@if($record->sistema == 'semicerrado') X @endif</td>
                    <td colspan="2" style="width: 5%;">CERRADO</td>
                    <td class="text-center">@if($record->sistema == 'cerrado') X @endif</td>
                    <td colspan="2" style="width: 5%;">CIRCUITO CIRCULAR</td>
                    <td class="text-center">@if($record->aparato == 'circuito_circular') X @endif</td>
                    <td colspan="2" style="width: 5%;">UNIDIRECCIONAL</td>
                    <td class="text-center">@if($record->aparato == 'unidireccional') X @endif</td>
                    <td colspan="3" class="bg-green">LOCAL ASISTIDA</td>
                    <td colspan="2">{{$record->local}}</td>
                    <td colspan="2" class="bg-green">INTRAVENOSA</td>
                    <td colspan="3">{{$record->intravenosa}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td class="text-center bg-green" colspan="14"><b>MANEJO DE VÍA AÉREA</b></td>
                    <td class="text-center bg-green" colspan="10"><b>TRONCULAR</b></td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-center" style="font-size:5;"><b>MÁSCARA FACIAL</b></td>
                    <td>@if($record->manejo == 'mascara') X @endif</td>
                    <td colspan="4" class="text-center" style="font-size:5;"><b>SUPRAGLÓTICA</b></td>
                    <td>@if($record->manejo == 'supraglotica') X @endif</td>
                    <td colspan="3" class="text-center" style="font-size:5;"><b>TRAQUEÓTOMO</b></td>
                    <td>@if($record->manejo == 'traqueotomo') X @endif</td>
                    <td colspan="3" style="font-size:5;" class="bg-green">BLOQUEO DE NERVIO</td>
                    <td colspan="2">{{$record->bloqueo_de_nervio}}</td>
                    <td colspan="2" style="font-size:5;" class="bg-green">NÚMERO DE INTENTOS</td>
                    <td colspan="3">{{$record->numero_intentos1}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td class="text-center bg-green" colspan="14"><b>INTUBACIÓN</b></td>
                    <td class="bg-green" colspan="3">BLOQUEO DE PLEXO</td>
                    <td colspan="2">{{$record->bloqueo_plexo}}</td>
                    <td class="bg-green" colspan="2">NÚMERO DE INTENTOS</td>
                    <td colspan="3">{{$record->numero_intentos2}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td style="width: 5%;" class="text-center">NASAL</td>
                    <td style="width: 1;">@if($record->intubacion == 'nasal') X @endif</td>
                    <td style="width: 5%;" class="text-center">ORAL</td>
                    <td style="width: 1;">@if($record->intubacion == 'oral') X @endif</td>
                    <td colspan="3" style="width: 5%;" class="text-center">SUBMENTONEANA</td>
                    <td style="width: 1;">@if($record->intubacion == 'submentoneana') X @endif</td>
                    <td colspan="2" style="width: 5%;" class="text-center">VISIÓN DIRECTA</td>
                    <td style="width: 1;">@if($record->intubacion == 'vision') X @endif</td>
                    <td colspan="2" style="width: 5%;" class="text-center">A CIEGAS</td>
                    <td style="width: 1;">@if($record->intubacion == 'a_ciegas') X @endif</td>
                    <td class="bg-green" colspan="3">ANESTÉSICO LOCAL</td>
                    <td colspan="2">{{$record->anestesico}}</td>
                    <td class="bg-green" colspan="2">COADYUVANTE</td>
                    <td colspan="3">{{$record->coadyuvante}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td class="text-center bg-green" style="width: 40%;" colspan="14"><b>TIPO DE TUBO</b></td>
                    <td style=" width: 10%;" class="bg-green" colspan="3">TIPO DE AGUJA</td>
                    <td colspan="2">{{$record->tipo_aguja}}</td>
                    <td style=" width: 11%;" class="bg-green" colspan="2">EQUIPO</td>
                    <td colspan="3">{{$record->equipo}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td colspan="2" class="align-left">CONVENCIONAL</td>
                    <td>@if($record->tipo_tubo == 'convencional') X @endif</td>
                    <td colspan="3" class="align-left">PREFORMADO ORAL</td>
                    <td>@if($record->tipo_tubo == 'p_oral') X @endif</td>
                    <td colspan="3" class="align-left">PREFORMADO NASAL</td>
                    <td>@if($record->tipo_tubo == 'p_nasal') X @endif</td>
                    <td colspan="2" class="align-left">REFORZADO</td>
                    <td>@if($record->tipo_tubo == 'reforsado') X @endif</td>
                    <td class="text-center bg-green" colspan="10"><b>NEUROAXIAL</b></td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td class="align-left">DOBLE LÚMEN</td>
                    <td class="text-center">@if($record->tipo_tubo == 'doble') X @endif</td>
                    <td class="align-left">DIÁMETRO</td>
                    <td class="text-center">@if($record->tipo_tubo == 'diametro') X @endif</td>
                    <td class="align-left">BALÓN</td>
                    <td class="align-left">SI</td>
                    <td class="text-center">@if($record->balon == 'SI') X @endif</td>
                    <td style="width: 2%;">NO</td>
                    <td class="text-center">@if($record->balon == 'NO') X @endif</td>
                    <td class="align-left">TAPONAMIENTO</td>
                    <td class="align-left">SI</td>
                    <td class="text-center">@if($record->taponamiento == 'SI') X @endif</td>
                    <td class="align-left">NO</td>
                    <td class="text-center">@if($record->taponamiento == 'NO') X @endif</td>

                    <td class="align-left">RAQUIDEA</td>
                    <td style="width: 1;">{{$record->raquidea}}</td>
                    <td colspan="3" class="text-center">EPIDURAL</td>
                    <td>{{$record->epidural}}</td>
                    <td class="align-left">CAUDAL</td>
                    <td>{{$record->caudal}}</td>
                    <td class="align-left">CATETER</td>
                    <td>{{$record->cateter}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td class="text-center bg-green" colspan="14"><b>EQUIPO DE INTUBACION</b></td>
                    <td colspan="2" class="align-left">TIPO DE AGUA</td>
                    <td>{{$record->aguja_neu}}</td>
                    <td colspan="2" class="align-left">NUMERO DE AGUJA</td>
                    <td>{{$record->num_aguja}}</td>
                    <td colspan="3" class="align-left">N° DE INTENTOS</td>
                    <td>{{$record->numero_intentos3}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td rowspan="2" colspan="2" class="text-center">CORMACK</td>
                    <td rowspan="2" class="text-center">I</td>
                    <td rowspan="2" class="text-center" class="text-center">@if($record->cormack == '1') X @endif</td>
                    <td rowspan="2" class="text-center">II</td>
                    <td rowspan="2" class="text-center">@if($record->cormack == '2') X @endif</td>
                    <td rowspan="2" class="text-center">III</td>
                    <td rowspan="2" class="text-center">@if($record->cormack == '3') X @endif</td>
                    <td rowspan="2" class="text-center">IV</td>
                    <td rowspan="2" class="text-center">@if($record->cormack == '4') X @endif</td>
                    <td rowspan="2" colspan="3" class="text-center">NUMERO INTENTOS</td>
                    <td rowspan="2">{{$record->intentos}}</td>
                    <td rowspan="2" class="text-center">BORBOTAJE</td>
                    <td rowspan="2" class="text-center">SI</td>
                    <td rowspan="2" class="text-center">@if($record->borbotaje == 'SI') X @endif</td>
                    <td rowspan="2" class="text-center">NO</td>
                    <td rowspan="2" class="text-center">@if($record->borbotaje == 'NO') X @endif</td>
                    <td rowspan="2" class="text-center">ACCESO</td>
                    <td class="text-center">MEDIAL</td>
                    <td class="text-center">LATERAL</td>
                    <td rowspan="2" class="text-center">SITIO DE PUNCIÓN</td>
                    <td rowspan="2">{{$record->puncion}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td class="text-center">@if($record->acceso == 'medial') X @endif</td>
                    <td class="text-center">@if($record->acceso == 'lateral') X @endif</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td class="text-center bg-green" colspan="6"><b>INDUCCION</b></td>
                    <td class="text-center bg-green" colspan="8"><b>MANTENIMIENTO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green">DERMATOMA</td>
                    <td rowspan="2" colspan="2">{{$record->dermatoma}}</td>
                    <td rowspan="2" colspan="2" class="bg-green">POSICIÓN</td>
                    <td rowspan="2" colspan="4">{{$record->posicion}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td class="align-left">INHALATORIA</td>
                    <td class="text-center">@if($record->induccion === 'inhalatoria') X @endif</td>
                    <td colspan="2" class="align-left">INTRAVENOSA</td>
                    <td class="text-center">@if($record->induccion === 'intravenosa') X @endif</td>
                    <td colspan="3" class="align-left">INHALATORIA</td>
                    <td class="text-center">@if($record->induccion === 'inhalatoria') X @endif</td>
                    <td class="align-left">INTRAVENOSA</td>
                    <td></td>
                    <td colspan="2" class="align-left">BALANCEADA</td>
                    <td></td>
                    <td>1</td>
                    <td>@if($record->escala == '1') X @endif</td>
                    <td>2</td>
                    <td>@if($record->escala == '2') X @endif</td>
                    <td>3</td>
                    <td>@if($record->escala == '3') X @endif</td>
                    <td>4</td>
                    <td>@if($record->escala == '4') X @endif</td>
                    <td>5</td>
                    <td>@if($record->escala == '5') X @endif</td>
                    <td>6</td>
                    <td>@if($record->escala == '6') X @endif</td>
                </tr>
            </tbody>
        </table>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="3" class="bg-purple" style="font-size: 8;"><b>G. ACCESOS VASCULARES</b></td>
                    <td colspan="4" class="bg-purple" style="font-size: 8;"><b>H. REPOSICÍON VOLÉMICA</b></td>
                    <td colspan="2" class="bg-purple" style="font-size: 8;"><b>I. PÉRDIDAS</b></td>
                </tr>
                <tr>
                    <td class="text-center bg-green">TIPO</td>
                    <td class="text-center bg-green" style="width: 7%;">CALIBRE</td>
                    <td class="text-center bg-green">SITIO</td>
                    <td class="text-center bg-green">DESTROSA 5%</td>
                    <td>{{$record->dextrosa}}</td>
                    <td class="text-center bg-green">SANGRE</td>
                    <td>{{$record->sangre_derivados}}</td>
                    <td class="text-center bg-green">SANGRADO</td>
                    <td>{{$record->sangrado}}</td>
                </tr>
                <tr>
                    <td class="text-center">IV PERIFÉRICO 1</td>
                    <td>{{$record->c1}}</td>
                    <td>{{$record->s1}}</td>
                    <td class="text-center bg-green">DESTROSA 10%</td>
                    <td>{{$record->destrosa10}}</td>
                    <td class="text-center bg-green">PLASMA</td>
                    <td>{{$record->plasma}}</td>
                    <td class="text-center bg-green">DIURESIS</td>
                    <td>{{$record->diuresis}}</td>
                </tr>
                <tr>
                    <td class="text-center">IV PERIFÉRICO 2</td>
                    <td>{{$record->c2}}</td>
                    <td>{{$record->s2}}</td>
                    <td class="text-center bg-green">DESTROSA 50%</td>
                    <td>{{$record->destrosa5}}</td>
                    <td class="text-center bg-green">PLAQUETAS</td>
                    <td>{{$record->plaquetas}}</td>
                    <td class="text-center bg-green">OTROS</td>
                    <td>{{$record->otros_perdidas}}</td>
                </tr>
                <tr>
                    <td class="text-center">IV PERIFÉRICO 3</td>
                    <td>{{$record->c3}}</td>
                    <td>{{$record->s3}}</td>
                    <td class="text-center bg-green">DESTROSA EN SS</td>
                    <td>{{$record->destrosass}}</td>
                    <td class="text-center bg-green">CRIOPRECIPITADOS</td>
                    <td>{{$record->crioprecipitados}}</td>
                    <td rowspan="3" class="text-center bg-green">TOTAL</td>
                    <td rowspan="3">{{$record->total_perdidas}}</td>
                </tr>
                <tr>
                    <td class="text-center">IV CENTRAL</td>
                    <td>{{$record->c4}}</td>
                    <td>{{$record->s4}}</td>
                    <td class="text-center bg-green">SS 0.9%</td>
                    <td>{{$record->ss_9}}</td>
                    <td rowspan="2" class="text-center bg-green">OTROS</td>
                    <td rowspan="2">{{$record->otros_reposicion}}</td>
                </tr>
                <tr>
                    <td class="text-center">INTRA ALTERIAL</td>
                    <td>{{$record->c5}}</td>
                    <td>{{$record->s5}}</td>
                    <td class="text-center bg-green">LACTO RINGER</td>
                    <td>{{$record->lactato_ringer}}</td>
                </tr>
                <tr>
                    <td class="text-center">OTRO</td>
                    <td>{{$record->c6}}</td>
                    <td>{{$record->s6}}</td>
                    <td class="text-center bg-green">EXPANSORES</td>
                    <td>{{$record->expansores}}</td>
                    <td class="text-center bg-green">TOTAL</td>
                    <td>{{$record->total_reposicion}}</td>
                    <td class="text-center bg-green">BALANCE</td>
                    <td>{{$record->balance}}</td>
                </tr>
            </tbody>
        </table>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="9" class="bg-purple" style="font-size: 8;"><b>J. DATOS DEL RECIENTE NACIDO</b></td>
                </tr>
                <tr>
                    <td class="text-center bg-green"><b>APGAR</b></td>
                    <td class="text-center">FETO MUERTO</td>
                    <td class="text-center" style="width: 3%;">@if($record->apgar == 'feto') X @endif</td>
                    <td class="text-center">1 MINUTO</td>
                    <td class="text-center" style="width: 3%;">@if($record->apgar == '1minuto') X @endif</td>
                    <td class="text-center">5 MINUTOS</td>
                    <td class="text-center" style="width: 3%;">@if($record->apgar == '5minuto') X @endif</td>
                    <td class="text-center">10 MINUTOS</td>
                    <td class="text-center" style="width: 3%;">@if($record->apgar == '10minuto') X @endif</td>
                </tr>
            </tbody>
        </table>
        <div>
            <p>@for($count=0; $count<=8;$count++) &nbsp; @endfor  @for($count=0; $count<=54;$count++) &nbsp; <b> @endfor  </b></p>
        </div>
        @endif

        @if(($pdfPage ?? 'all') !== 'first')
        <table border="1" style="width: 100%" class="{{ ($pdfPage ?? 'all') === 'all' ? 'page-break-before' : '' }}">
            <tbody>
                <tr>
                    <td colspan="4" class="bg-purple" style="font-size: 8;"><b>K. TIEMPOS TRANSCURRIDOS</b></td>
                </tr>
                <tr>
                    <td class="text-center bg-green">DURACION ANESTESIA</td>
                    <td class="text-center">{{$record->duracion_anestesia}}</td>
                    <td class="text-center bg-green">CIRCULACIÓN EXTRACORPÓREA</td>
                    <td class="text-center">{{$record->duracion_operacion}}</td>
                </tr>
            </tbody>
        </table>

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="10" class="bg-purple" style="font-size: 8;"><b>L. TECNICAS ESPECIALES</b></td>
                </tr>
                <tr>
                    <td class="text-center">HEMODILUCIÓN</td>
                    <td class="text-center" style="width: 3%;">@if($record->tecnicas_especiales == 'hemodilucion') X @endif</td>
                    <td class="text-center">AUTOTRANSFUSIÓN</td>
                    <td class="text-center" style="width: 3%;">@if($record->tecnicas_especiales == 'autotransfusion') X @endif</td>
                    <td class="text-center">HIPOTENSÍON</td>
                    <td class="text-center" style="width: 3%;">@if($record->tecnicas_especiales == 'hipotension') X @endif</td>
                    <td class="text-center">HIPOTERMIA</td>
                    <td class="text-center" style="width: 3%;">@if($record->tecnicas_especiales == 'hipotermia') X @endif</td>
                    <td class="text-center">CIRCULACIÓN EXTRACOPÓREA</td>
                    <td class="text-center" style="width: 3%;">@if($record->tecnicas_especiales == 'circulacion_extracoperea') X @endif</td>
                </tr>
            </tbody>
        </table>

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="6" class="bg-purple" style="font-size: 8;"><b>M. MANTENIMIENTO TEMPERATURA CORPORAL</b></td>
                </tr>
                <tr>
                    <td style="width: 15%;">MANTA TERMICA</td>
                    <td class="text-center" style="width: 3%;">@if($record->mantenimiento_temperatura == '1') X @endif</td>
                    <td style="width: 15%;">CALENTAMIENTO DE FLUIDOS</td>
                    <td class="text-center" style="width: 3%;">@if($record->mantenimiento_temperatura == '2') X @endif</td>
                    <td class="text-center" style="width: 3%;">OTROS</td>
                    <td style="width: 30%;">{{$record->otros_temperatura}}</td>
                </tr>
            </tbody>
        </table>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="8" class="bg-purple" style="font-size: 8;"><b>N. INCIDENTES</b></td>
                </tr>
                <tr>
                    <td>ACTIVIDAD ELÉCTRICA SIN PULSO</td>
                    <td style="width: 3%;" class="text-center">@if($record->indice == '1') X @endif</td>
                    <td style="width: 15%;">ARRITMIA</td>
                    <td style="width: 3%;" class="text-center">@if($record->indice == '2') X @endif</td>
                    <td>ASISTOLIA</td>
                    <td style="width: 3%;" class="text-center">@if($record->indice == '3') X @endif</td>
                    <td>TRMBOEMBOLIA</td>
                    <td style="width: 3%;" class="text-center">@if($record->indice == '4') X @endif</td>
                </tr>
                <tr>
                    <td>HIPERTERMIA MALIGNA</td>
                    <td style="width: 3%;" class="text-center">@if($record->indice == '5') X @endif</td>
                    <td style="width: 15%;">ANAFILAXIA</td>
                    <td style="width: 3%;" class="text-center">@if($record->indice == '6') X @endif</td>
                    <td>ISQUEMIA MIOCÁRDICA</td>
                    <td style="width: 3%;" class="text-center">@if($record->indice == '7') X @endif</td>
                    <td>NEUMOTÓRAX</td>
                    <td style="width: 3%;" class="text-center">@if($record->indice == '8') X @endif</td>
                </tr>
                <tr>
                    <td>BRONCOESPASMO</td>
                    <td style="width: 3%;" class="text-center">@if($record->indice == '9') X @endif</td>
                    <td style="width: 15%;">DESPERTAR PROLONGADO</td>
                    <td style="width: 3%;" class="text-center">@if($record->indice == '10') X @endif</td>
                    <td>EMBOLIA AÉREA VENOSA</td>
                    <td style="width: 3%;" class="text-center">@if($record->indice == '11') X @endif</td>
                    <td>LARINGOESPASMO</td>
                    <td style="width: 3%;" class="text-center">@if($record->indice == '12') X @endif</td>
                </tr>
                <tr>
                    <td>DIFICULTAD DE LA TÉCNICA</td>
                    <td style="width: 3%;" class="text-center">@if($record->indice == '13') X @endif</td>
                    <td style="width: 15%;">OTROS</td>
                    <td colspan="5">{{$record->otros_indice}}</td>
                </tr>
            </tbody>
        </table>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="4" style="font-size: 12px;" class="bg-purple"><b> O. RESULTADO DE EXÁMENES DE LABORATORIO</b></td>
                </tr>
                <tr>
                    <td class="text-center bg-green" colspan="2">EXAMEN</td>
                    <td class="text-center bg-green">AGRUPADOR</td>
                    <td class="text-center bg-green">RESULTADO</td>
                </tr>

                @php
                $contador = 1;
                @endphp
                @foreach ($examen as $exa)
                <tr>
                    <td class="text-center bg-green" style="width: 1%;">{{ $contador++ }}</td>
                    <td style="height: 18px;" class="text-center">{{$exa->nombre}}</td>
                    <td style="height: 18px;" class="text-center">{{$exa->examen->agrupador_018->nombre}}</td>
                    <td> &nbsp; &nbsp;{{ $exa->resultado }}</td>
                </tr>
                @endforeach

            </tbody>
        </table>

        <!--p class="page-break-before"-->

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td class="bg-purple" style="font-size: 8;"><b>P. OBSERVACIONES</b></td>
                </tr>
                <tr>
                    <td style="height: 30px;">{{$record->observaciones}}</td>
                </tr>
            </tbody>
        </table>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="12" class="bg-purple"><b>R. CONDICIONES DE EGRESO</b></td>
                </tr>
                <tr>
                    <td class="text-center" style="width: 18%;" rowspan="2">CONDICIONES AL SALIR</td>
                    <td class="text-center">EXTUBADO</td>
                    <td class="text-center" style="width: 5%;">@if($record->condiciones_al_salir == '1') X @endif</td>
                    <td class="text-center" rowspan="2">CONDUCIDO A</td>
                    <td class="text-center" rowspan="2">UNIDAD DE CUIDADOS POST ANESTÉSICO</td>
                    <td class="text-center" rowspan="2">@if($record->conducido == '1') X @endif</td>
                    <td class="text-center" rowspan="2">UNIDAD DE CUIDADOS INTENSIVOS</td>
                    <td class="text-center" rowspan="2">@if($record->conducido == '2') X @endif</td>
                    <td class="text-center" rowspan="2">CRÍTICOS DE EMERGENCIA</td>
                    <td class="text-center" rowspan="2">@if($record->conducido == '3') X @endif</td>
                    <td class="text-center" rowspan="2">MORGUE</td>
                    <td class="text-center" rowspan="2">@if($record->conducido == '4') X @endif</td>
                </tr>
                <tr>
                    <td class="text-center">INTUBADO</td>
                    <td>@if($record->condiciones_al_salir == '2') X @endif</td>
                </tr>
                <tr>
                    <td>CONSTANTES VITALES DE ENTREGA</td>
                    <td> <b>TA</b></td>
                    <td class="text-center">{{$record->ta}}</td>
                    <td> <b>FC</b></td>
                    <td class="text-center">{{$record->fc}}</td>
                    <td> <b>FR</b></td>
                    <td class="text-center">{{$record->fr}}</td>
                    <td colspan="2"> <b>SAT.O2</b></td>
                    <td class="text-center">{{$record->sat}}</td>
                    <td> <b>T°</b></td>
                    <td class="text-center">{{$record->t}}</td>
                </tr>
            </tbody>
        </table>
        @php
        $fecha = $record->fecha;
        $hora = date("H:i:s", strtotime($fecha));
        @endphp



        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="8" class="bg-purple"><b>R. CONDICIONES DE EGRESO</b></td>
                </tr>
                <tr>
                    <td class="text-center bg-green" style="height: 45px; width: 6%;">HORA</td>
                    <td class="text-center" style="width: 8%;">{{$hora}}</td>
                    <td class="text-center bg-green" style="width: 15%;">NOMBRE Y APELLIDO DEL PROFESIONAL</td>
                    <td class="text-center" style="width: 25%;">{{$nombre}}</td>
                    <td class="text-center bg-green" style="width: 6%;">FIRMA</td>
                    <td class="text-center">
                        @php 
                            $ruta_firma = "";
                            if(!is_null($id_anestesiologo)){
                                $firma = \Sis_medico\Firma_Usuario::where('id_usuario',$id_anestesiologo)->first();
                                if(!is_null($firma)){
                                    $ruta_firma = base_path().'/storage/app/logo/' . $firma->nombre;
                                }
                            }
                        @endphp
                        @if($ruta_firma!="")
                            <img src="{{$ruta_firma}}" style="width: 50%;"><br>
                        @endif
                    </td>
                    <td class="text-center bg-green" style="width: 8%;">SELLO Y CÓDIGO</td>
                    <td class="text-center"></td>
                </tr>
            </tbody>
        </table>
        @endif
    </main>
</body>
</html>
