<!DOCTYPE html>
<html>
@php $paciente = $orden->paciente; @endphp

@php
$fecha=date('Y-m-d');
$hora = date('H:i:s');
@endphp

<?php
$fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');
?>


<head>
    <title>Formulario10A</title>
    <style>
        @page {
            margin: 0 0;

        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin-top: 5px;
            margin-left: 20px;
            margin-right: 20px;
            margin-bottom: 1px;
            font-size: 10px;
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

        .td-all-border {
            border-top: 1px solid;
            border-left: 1px solid;
            border-right: 1px solid;
            border-bottom: 1px solid;
        }
        
        .td-border {
            border-top: 1px solid;
            border-bottom: 1px solid;
            font-size: 9px !important;
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
            font-size: 8px;
        }

        .bg-green {
            background-color: #BADAC8;
        }

        .bg-yellow {
            background-color: #F1FD77;
        }

        .bg-purple {
            background-color: #CCCCFF;
        }

        .bg-turquoise {
            background-color: #40E0D0;
        }

        .bg-celeste {
            background-color: #00BFFF;
        }

        .text-center {
            text-align: center;
            font-size: 9px;
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
        }

        td.contenedor-texto {
            padding: 20px;
            text-align: left;
        }

        .page-break-before {
            page-break-before: always;
        }

        .page-break-after {
            page-break-after: always;
        }

        ol {
            list-style-type: decimal;
            margin-left: 20px;
        }

        .cuadro1 {
            width: 33%;
            float: left;
        }

        .cuadro2 {
            width: 33%;
            float: left;
        }

        .cuadro3 {
            width: 33%;
            float: right;
        }

        .div_clear {
            clear: both;
        }

        .cuadro11 {
            width: 44%;
            position: relative;
        }

        .cuadro12 {
            width: 4%;
            position: relative;
            left: 45%;

        }

        .cuadro13 {
            width: 44%;
            position: relative;
            left: 50%;

        }

        .cuadro14 {
            width: 4%;
            position: relative;
            left: 95%;

        }

        .cuadro21 {
            width: 91%;
            position: relative;
        }

        .cuadro22 {
            width: 5%;
            position: relative;
            left: 92%;

        }

        .cuadro31 {
            width: 42%;
            position: relative;

        }

        .cuadro32 {
            width: 4%;
            position: relative;
            left: 45%;

        }

        .cuadro33 {
            width: 42%;
            position: relative;
            left: 50%;

        }

        .cuadro34 {
            width: 4%;
            position: relative;
            left: 94%;

        }

        .outer-box {
            width: 33.3%;
            display: inline-block;
            vertical-align: top;
            /*background-color: red;*/

        }

        .outer-box2 {
            width: 33%;
            display: inline-block;
            vertical-align: top;
            /*background-color: green;*/

        }

        .outer-box3 {
            width: 33%;
            display: inline-block;
            vertical-align: top;
            /*background-color: yellow;*/
        }

        .outer-space {
            width: 100%;
            display: block;
            height: 10px;
        }

        .inner-box {
            display: block;
            width: 44%;
            background-color: #DDEBF7;
            font-size: 8px;
            float: left;
            border-top: 1px solid black;
            /*border-right: 1px solid black;*/
        }

        .inner-box2 {
            display: block;
            width: 6%;
            float: left;
            border-top: 1px solid black;
            /*border-right: 1px solid black;*/
            text-align: center;
            font-size: 8px;
        }

        .inner-box3 {
            display: block;
            width: 44%;
            background-color: #DDEBF7;
            font-size: 8px;
            float: left;
            border-top: 1px solid black;
            /*border-right: 1px solid black;*/
            font-size: 8px;
        }

        .inner-box4 {
            display: block;
            width: 6%;
            float: right;
            border-top: 1px solid black;
            /*border-right: 1px solid black;*/
            text-align: center;
            font-size: 8px;
        }

        .contenido {
            width: 100%;
        }

        .inner-box21 {
            width: 94%;
            background-color: #DDEBF7;
            font-size: 8px;
            float: left;
            border-top: 1px solid black;
            /*border-right: 1px solid black;*/
        }

        .inner-box22 {
            width: 5%;
            float: right;
            border-top: 1px solid black;
            /*border-right: 1px solid black;*/
            font-size: 8px;
        }

        .molecular {
            position: relative;
            width: 66%;
            top: -50px;

        }

        .contenido1 {}

        .table-footer {
            position: relative;
            top: -50px;
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

        .td-border2 {
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
            font-size: 9px !important;
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
    </style>
</head>

<body>
    <header>

    </header>
    <main>
        <table border="1" style="width: 99%">
            <tr>
                <td colspan="12" style="text-align: center;">
                    @if(!is_null($empresa))
                    <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width: 140px;height: 45px">
                    @endif
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="12" style="font-size: 9px;" class="bg-blue td-border"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="bg-green text-center td-border"><b>INSTITUCION DEL SISTEMA</b></td>
                    <td class="bg-green text-center td-border"><b>UNICODIGO</b></td>
                    <td class="bg-green text-center td-border"><b>ESTABLECIMIENTO DE SALUD</b></td>
                    <td colspan="5" class="bg-green text-center td-border"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                    <td colspan="3" class="bg-green text-center td-border"><b>NÚMERO DE ARCHIVO</b></td>
                    <td class="bg-green text-center td-border"><b>No.HOJA</b></td>
                </tr>
                <tr>
                    <td class="td-border text-center" style="height: 18px;">{{$orden->seguro->nombre}}</td>
                    <td class="td-border text-center">{{$empresa->unicodigo}}</td>
                    <td class="td-border text-center">{{$empresa->nombrecomercial}}</td>
                    <td class="td-border text-center" colspan="5">{{$orden->id_paciente}}</td>
                    <td class="td-border text-center" colspan="3"></td>
                    <td class="td-border text-center"></td>
                </tr>
                <tr>
                    <td rowspan="2" colspan="2" class="bg-green text-center td-border"><b>PRIMER APELLIDO</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center td-border"><b>SEGUNDO APELLIDO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green text-center td-border"><b>PRIMER NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center td-border"><b>SEGUNDO NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center td-border"><b>SEXO</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center td-border"><b>EDAD</b></td>
                    <td colspan="4" class="bg-green text-center td-border"><b>CONDICIÓN EDAD (MARCAR)</b></td>
                </tr>
                <tr>
                    <td class="bg-green text-center td-border">D</td>
                    <td class="bg-green text-center td-border">H</td>
                    <td class="bg-green text-center td-border">M</td>
                    <td class="bg-green text-center td-border">A</td>
                </tr>
                <tr>
                    <td class="td-border text-center" colspan="2" style="height: 18px;">{{$paciente->apellido1}}</td>
                    <td class="td-border text-center">{{$paciente->apellido2}}</td>
                    <td class="td-border text-center" colspan="2">{{$paciente->nombre1}}</td>
                    <td class="td-border text-center">{{$paciente->nombre2}}</td>
                    <td class="td-border text-center">@if($paciente->sexo == 1) Masculino @endif @if($paciente->sexo == 2) Femenino @endif</td>
                    <td class="td-border text-center">{{$edad}}</td>
                    <td class="td-border text-center">{{$diaNacimiento}}</td>
                    <td class="td-border text-center"></td>
                    <td class="td-border text-center">{{$mesNacimiento}}</td>
                    <td class="td-border text-center">{{ date('Y', strtotime($paciente->fecha_nacimiento))}}</td>
                </tr>
            </tbody>
        </table>

        <table cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="11" style="font-size: 9px;" class="bg-blue td-border"><b>B. DATOS DEL SERVICIO</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" class="bg-green text-center td-border">DIAGNOSTICO</td>
                    <td class="bg-green text-center td-all-border" >CIE</td>
                    <td rowspan="3" class="bg-color1 text-center td-all-border" >SERVICIO</td>
                    <td class="bg-green text-center td-all-border" >EMERGENCIA</td>
                    <td class="text-center td-all-border" >&nbsp;</td>
                    <td class="bg-green text-center td-all-border" >ESPECIALIDAD</td>
                    <td class="text-center td-all-border" >&nbsp;</td>
                    <td colspan="2" class="bg-green text-center td-all-border">PRIORIDAD</td>
                    <td class="text-center td-all-border" >&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-center td-border">1</td>
                    <td class=" td-border" >@if(isset($diagnosticos[0])) {{$diagnosticos[0]['descripcion']}} @endif</td>
                    <td class=" td-border" >@if(isset($diagnosticos[0])) {{$diagnosticos[0]['cie10']}} @endif</td>
                    <td class="bg-color1 text-center td-border" >CONSULTA EXTERNA</td>
                    <td class="text-center td-border" > X </td>
                    <td class="text-center td-border" >SALA</td>
                    <td class="text-center td-border" >&nbsp;</td>
                    <td colspan="2" class="bg-color1 text-center td-border">URGENTE</td>
                    <td colspan="2"class="text-center td-border">&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-center td-border">2</td>
                    <td class=" td-border" >@if(isset($diagnosticos[1])) {{$diagnosticos[1]['descripcion']}} @endif</td>
                    <td class=" td-border" >@if(isset($diagnosticos[1])) {{$diagnosticos[1]['cie10']}} @endif</td>
                    <td class="bg-color1 text-center td-border" >HOSPITALIZACION</td>
                    <td class="text-center td-border" >&nbsp; </td>
                    <td class="text-center td-border" >CAMA</td>
                    <td class="text-center td-border" >&nbsp;</td>
                    <td colspan="2" class="bg-color1 text-center td-border">RUTINA</td>
                    <td colspan="2" class="text-center td-border"> X </td>
                </tr>
                <tr>
                    <td class="text-center td-border">3</td>
                    <td class=" td-border" >@if(isset($diagnosticos[2])) {{$diagnosticos[2]['descripcion']}} @endif</td>
                    <td class=" td-border" >@if(isset($diagnosticos[2])) {{$diagnosticos[2]['cie10']}} @endif</td>
                    <td  class="bg-color1 text-center td-border" >&nbsp;</td>
                    <td class="bg-color1 text-center td-border" ></td>
                    <td class="text-center td-border" >&nbsp; </td>
                    <td class="text-center td-border" >&nbsp;</td>
                    <td class="text-center td-border" >&nbsp;</td>
                    <td class="bg-color1 text-center td-border">&nbsp;</td>
                    <td colspan="2" class="text-center td-border"> &nbsp;</td>
                </tr>
                <tr>
                    <td class="text-center td-border"></td>
                    <td class=" td-border" >&nbsp;</td>
                    <td class=" td-border" >&nbsp;</td>
                    <td class="bg-color1 text-center td-border" >&nbsp;</td>
                    <td class="bg-color1 text-center td-border" ></td>
                    <td class="text-center td-border" >&nbsp; </td>
                    <td class="text-center td-border" >&nbsp;</td>
                    <td class="text-center td-border" >&nbsp;</td>
                    <td class="bg-color1 text-center td-border">&nbsp;</td>
                    <td colspan="2" class="text-center td-border"> &nbsp; </td>
                </tr>
            </tbody>
        </table>

        <table cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td colspan="14" style="font-size: 9px;" class="bg-purple td-border"><b>C. RESULTADO DE EXÁMENES</b></td>
                </tr>
            </thead>
        </table>


        <div class="contenido1">
            @php
            $cant = count($hematologia);
            if($cant < 22){ $cant=22; } $cant_coag=count($coagulacion); $x_coag=12; if($cant_coag < 12 ){ $x_coag=$x_coag - $cant_coag; } $cant2=count($quimica); if($cant2 < 34){ $cant2=32; } $cant_orina=count($orina); $x_orina=18; if($cant_orina < 18 ){ $x_orina=$x_orina - $cant_orina; } $cant3=count($inmunologia); $cant4=count($heces); $cant5=count($tumorales); $cant6=count($citologia); $cant7=count($cardiacos); $cant8=count($hormonas); $cant9=count($farmacos); $cant10=count($serologia); $cant11=count($biologia); @endphp <div class="outer-box">
                <div class="bg-purple"><b>HEMATOLÓGIA</b></div>
                @for($i=0;$i< $cant ;$i=$i+2) <div class="inner-box">@if(isset($hematologia[$i])){{$hematologia[$i]->nombre}}@endif</div>
        <div class="inner-box2">@if(isset($hematologia[$i])) @if($detalles->where('id_examen',$hematologia[$i]->id)->first() != null) X @endif @endif</div>
        <div class="inner-box3">@if(isset($hematologia[$i+1])){{$hematologia[$i + 1]->nombre}} @else &nbsp;@endif</div>
        <div class="inner-box4">@if(isset($hematologia[$i+1])) @if($detalles->where('id_examen',$hematologia[$i+1]->id)->first() != null) X @endif @endif</div>
        <div class="div_clear"></div>
        @endfor
        <div class="inner-box" style="border-bottom: 1px solid;">&nbsp;</div>
        <div class="inner-box2" style="border-bottom: 1px solid;">&nbsp;</div>
        <div class="inner-box3" style="border-bottom: 1px solid;">&nbsp;</div>
        <div class="inner-box4" style="border-bottom: 1px solid;">&nbsp;</div>
        <div class="div_clear"></div>
        <div class="bg-purple"><b>INMUNOLOGÍA / INFECCIOSAS</b></div>
        @for($i=0;$i< $cant3 ;$i=$i+2) <div class="inner-box">@if(isset($inmunologia[$i])){{$inmunologia[$i]->nombre}}@endif</div>
            <div class="inner-box2">@if(isset($inmunologia[$i])) @if($detalles->where('id_examen',$inmunologia[$i]->id)->first() != null) X @endif @endif</div>
            <div class="inner-box3">@if(isset($inmunologia[$i+1])){{$inmunologia[$i + 1]->nombre}} @else &nbsp;@endif</div>
            <div class="inner-box4">@if(isset($inmunologia[$i+1])) @if($detalles->where('id_examen',$inmunologia[$i+1]->id)->first() != null) X @endif @endif</div>
            <div class="div_clear"></div>
            @endfor
            <div class="inner-box" style="border-bottom: 1px solid;">&nbsp;</div>
            <div class="inner-box2" style="border-bottom: 1px solid;">&nbsp;</div>
            <div class="inner-box3" style="border-bottom: 1px solid;">&nbsp;</div>
            <div class="inner-box4" style="border-bottom: 1px solid;">&nbsp;</div>
            <div class="div_clear"></div>
            <div class="bg-purple"><b>MARCADORES CARDIACOS/VASCULARES</b></div>
            @for($i=0;$i< $cant7 ;$i=$i+2) <div class="inner-box">@if(isset($cardiacos[$i])){{$cardiacos[$i]->nombre}}@endif</div>
                <div class="inner-box2">@if(isset($cardiacos[$i])) @if($detalles->where('id_examen',$cardiacos[$i]->id)->first() != null) X @endif @endif</div>
                <div class="inner-box3">@if(isset($cardiacos[$i+1])){{$cardiacos[$i + 1]->nombre}} @else &nbsp;@endif</div>
                <div class="inner-box4">@if(isset($cardiacos[$i+1])) @if($detalles->where('id_examen',$cardiacos[$i+1]->id)->first() != null) X @endif @endif</div>
                <div class="div_clear"></div>
                @endfor
                <div class="inner-box" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box2" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box3" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="inner-box4" style="border-bottom: 1px solid;">&nbsp;</div>
                <div class="div_clear"></div>
                <div class="bg-purple"><b>HORMONAS</b></div>
                @for($i=0;$i< $cant8 ;$i=$i+2) <div class="inner-box">@if(isset($hormonas[$i])){{$hormonas[$i]->nombre}}@endif</div>
                    <div class="inner-box2">@if(isset($hormonas[$i])) @if($detalles->where('id_examen',$hormonas[$i]->id)->first() != null) X @endif @endif</div>
                    <div class="inner-box3">@if(isset($hormonas[$i+1])){{$hormonas[$i + 1]->nombre}} @else &nbsp;@endif</div>
                    <div class="inner-box4">@if(isset($hormonas[$i+1])) @if($detalles->where('id_examen',$hormonas[$i+1]->id)->first() != null) X @endif @endif</div>
                    <div class="div_clear"></div>
                    @endfor
                    <div class="inner-box" style="border-bottom: 1px solid;">&nbsp;</div>
                    <div class="inner-box2" style="border-bottom: 1px solid;">&nbsp;</div>
                    <div class="inner-box3" style="border-bottom: 1px solid;">&nbsp;</div>
                    <div class="inner-box4" style="border-bottom: 1px solid;">&nbsp;</div>
                    <div class="div_clear"></div>
                    </div>
                    <div class="outer-box2">
                        <div class="bg-purple"><b>COAGULACIÓN Y HEMOSTASIA</b></div>
                        @foreach($coagulacion as $value)
                        <div class="inner-box21">{{$value->nombre}}</div>
                        <div class="inner-box22">@if($detalles->where('id_examen',$value->id)->first() != null) X @endif</div>
                        <div class="div_clear"></div>
                        @endforeach
                        @for($xc = 0;$xc < $x_coag;$xc++) <div class="inner-box21">&nbsp;
                    </div>
                    <div class="inner-box22">&nbsp;</div>
                    <div class="div_clear"></div>
                    @endfor
                    <div class="inner-box21" style="border-bottom: 1px solid;">&nbsp;</div>
                    <div class="inner-box22" style="border-bottom: 1px solid;">&nbsp;</div>
                    <div class="div_clear"></div>
                    <div class="bg-purple"><b>ORINA</b></div>
                    @foreach($orina as $value)
                    <div class="inner-box21">{{$value->nombre}}</div>
                    <div class="inner-box22">@if($detalles->where('id_examen',$value->id)->first() != null) X @endif</div>
                    <div class="div_clear"></div>
                    @endforeach
                    @for($xo = 0;$xo < $x_orina;$xo++) <div class="inner-box21">&nbsp;</div>
                        <div class="inner-box22">&nbsp;</div>
                        <div class="div_clear"></div>
                        @endfor

                        <div class="inner-box21" style="border-bottom: 1px solid;">&nbsp;</div>
                        <div class="inner-box22" style="border-bottom: 1px solid;">&nbsp;</div>
                        <div class="div_clear"></div>
                        <div class="bg-purple"><b>INMUNOSUPRESORES</b></div>
                        @foreach($inmunosupresor as $value)
                        <div class="inner-box21">{{$value->nombre}}</div>
                        <div class="inner-box22">@if($detalles->where('id_examen',$value->id)->first() != null) X @endif</div>
                        <div class="div_clear"></div>
                        @endforeach
                        <div class="inner-box21" style="border-bottom: 1px solid;">&nbsp;</div>
                        <div class="inner-box22" style="border-bottom: 1px solid;">&nbsp;</div>
                        <div class="div_clear"></div>
                        <div class="bg-purple"><b>GASES Y ELECTROLITOS</b></div>
                        @foreach($gases as $value)
                        <div class="inner-box21">{{$value->nombre}}</div>
                        <div class="inner-box22">@if($detalles->where('id_examen',$value->id)->first() != null) X @endif</div>
                        <div class="div_clear"></div>
                        @endforeach
                        <div class="inner-box21" style="border-bottom: 1px solid;">&nbsp;</div>
                        <div class="inner-box22" style="border-bottom: 1px solid;">&nbsp;</div>
                        <div class="div_clear"></div>
                        <div class="bg-purple"><b>MEDICINA TRANSFUSIONAL</b></div>
                        @foreach($trasfusional as $value)
                        <div class="inner-box21">{{$value->nombre}}</div>
                        <div class="inner-box22">@if($detalles->where('id_examen',$value->id)->first() != null) X @endif</div>
                        <div class="div_clear"></div>
                        @endforeach
                        <div class="inner-box21" style="border-bottom: 1px solid;">&nbsp;</div>
                        <div class="inner-box22" style="border-bottom: 1px solid;">&nbsp;</div>
                        <div class="div_clear"></div>
                        </div>

                        <div class="outer-box3">
                            <div class="bg-purple"><b>QUÍMICA SANGUÍNEA</b></div>
                            @for($j=0;$j< $cant2 ;$j=$j+2) <div class="inner-box">@if(isset($quimica[$j])){{$quimica[$j]->nombre}} @else &nbsp; @endif
                        </div>
                        <div class="inner-box2">@if(isset($quimica[$j])) @if($detalles->where('id_examen',$quimica[$j]->id)->first() != null) X @endif @endif</div>
                        <div class="inner-box3">@if(isset($quimica[$j+1])){{$quimica[$j + 1]->nombre}} @else &nbsp; @endif</div>
                        <div class="inner-box4">@if(isset($quimica[$j+1])) @if($detalles->where('id_examen',$quimica[$j+1]->id)->first() != null) X @endif @endif</div>
                        <div class="div_clear"></div>
                        @endfor
                        <div class="div_clear"></div>
                        <div class="bg-purple"><b>HECES</b></div>
                        @for($j=0;$j< $cant4 ;$j=$j+2) <div class="inner-box">@if(isset($heces[$j])){{$heces[$j]->nombre}} @else &nbsp; @endif</div>
                            <div class="inner-box2">@if(isset($heces[$j])) @if($detalles->where('id_examen',$heces[$j]->id)->first() != null) X @endif @endif</div>
                            <div class="inner-box3">@if(isset($heces[$j+1])){{$heces[$j + 1]->nombre}} @else &nbsp; @endif</div>
                            <div class="inner-box4">@if(isset($heces[$j+1])) @if($detalles->where('id_examen',$heces[$j+1]->id)->first() != null) X @endif @endif</div>
                            <div class="div_clear"></div>
                            @endfor
                            <div class="div_clear"></div>
                            <div class="bg-purple"><b>MARCADORES TUMORALES</b></div>
                            @for($j=0;$j< $cant5 ;$j=$j+2) <div class="inner-box">@if(isset($tumorales[$j])){{$tumorales[$j]->nombre}} @else &nbsp; @endif</div>
                                <div class="inner-box2">@if(isset($tumorales[$j])) @if($detalles->where('id_examen',$tumorales[$j]->id)->first() != null) X @endif @endif</div>
                                <div class="inner-box3">@if(isset($tumorales[$j+1])){{$tumorales[$j + 1]->nombre}} @else &nbsp; @endif</div>
                                <div class="inner-box4">@if(isset($tumorales[$j+1])) @if($detalles->where('id_examen',$tumorales[$j+1]->id)->first() != null) X @endif @endif</div>
                                <div class="div_clear"></div>
                                @endfor
                                <div class="div_clear"></div>
                                <div class="bg-purple"><b>CITOQUÍMICO Y BACTERIOLÓGICO DE LÍQUIDOS</b></div>
                                @for($j=0;$j< $cant6 ;$j=$j+2) <div class="inner-box">@if(isset($citologia[$j])){{$citologia[$j]->nombre}} @else &nbsp; @endif</div>
                                    <div class="inner-box2">@if(isset($citologia[$j])) @if($detalles->where('id_examen',$citologia[$j]->id)->first() != null) X @endif @endif</div>
                                    <div class="inner-box3">@if(isset($citologia[$j+1])){{$citologia[$j + 1]->nombre}} @else &nbsp; @endif</div>
                                    <div class="inner-box4">@if(isset($citologia[$j+1])) @if($detalles->where('id_examen',$citologia[$j+1]->id)->first() != null) X @endif @endif</div>
                                    <div class="div_clear"></div>
                                    @endfor
                                    <div class="div_clear"></div>
                                    <div class="div_clear"></div>
                                    <div class="bg-purple"><b>NIVELES DE FÁRMACOS TERAPÉUTICAS</b></div>
                                    @for($j=0;$j< $cant9 ;$j=$j+2) <div class="inner-box">@if(isset($farmacos[$j])){{$farmacos[$j]->nombre}} @else &nbsp; @endif</div>
                                        <div class="inner-box2">@if(isset($farmacos[$j])) @if($detalles->where('id_examen',$farmacos[$j]->id)->first() != null) X @endif @endif</div>
                                        <div class="inner-box3">@if(isset($farmacos[$j+1])){{$farmacos[$j + 1]->nombre}} @else &nbsp; @endif</div>
                                        <div class="inner-box4">@if(isset($farmacos[$j+1])) @if($detalles->where('id_examen',$farmacos[$j+1]->id)->first() != null) X @endif @endif</div>
                                        <div class="div_clear"></div>
                                        @endfor
                                        <div class="inner-box" style="border-bottom: 1px solid;">&nbsp;</div>
                                        <div class="inner-box2" style="border-bottom: 1px solid;">&nbsp;</div>
                                        <div class="inner-box3" style="border-bottom: 1px solid;">&nbsp;</div>
                                        <div class="inner-box4" style="border-bottom: 1px solid;">&nbsp;</div>
                                        <div class="div_clear"></div>
                                        <div class="div_clear"></div>
                                        <div class="bg-purple"><b>SEROLOGÍA</b></div>
                                        @for($j=0;$j< $cant10 ;$j=$j+2) <div class="inner-box">@if(isset($serologia[$j])){{$serologia[$j]->nombre}} @else &nbsp; @endif</div>
                                            <div class="inner-box2">@if(isset($serologia[$j])) @if($detalles->where('id_examen',$serologia[$j]->id)->first() != null) X @endif @endif</div>
                                            <div class="inner-box3">@if(isset($serologia[$j+1])){{$serologia[$j + 1]->nombre}} @else &nbsp; @endif</div>
                                            <div class="inner-box4">@if(isset($serologia[$j+1])) @if($detalles->where('id_examen',$serologia[$j+1]->id)->first() != null) X @endif @endif</div>
                                            <div class="div_clear"></div>
                                            @endfor
                                            <div class="inner-box" style="border-bottom: 1px solid;">&nbsp;</div>
                                            <div class="inner-box2" style="border-bottom: 1px solid;">&nbsp;</div>
                                            <div class="inner-box3" style="border-bottom: 1px solid;">&nbsp;</div>
                                            <div class="inner-box4" style="border-bottom: 1px solid;">&nbsp;</div>
                                            <div class="div_clear"></div>
                                            <div class="div_clear"></div>
                                            <div class="bg-purple"><b>MICROBIOLOGÍA</b></div>
                                            @for($j=0;$j< $cant11;$j=$j+2) <div class="inner-box">@if(isset($biologia[$j])){{$biologia[$j]->nombre}} @else &nbsp; @endif</div>
                                                <div class="inner-box2">@if(isset($biologia[$j])) @if($detalles->where('id_examen',$biologia[$j]->id)->first() != null) X @endif @endif</div>
                                                <div class="inner-box3">@if(isset($biologia[$j+1])){{$biologia[$j + 1]->nombre}} @else &nbsp; @endif</div>
                                                <div class="inner-box4">@if(isset($biologia[$j+1])) @if($detalles->where('id_examen',$biologia[$j+1]->id)->first() != null) X @endif @endif</div>
                                                <div class="div_clear"></div>
                                                @endfor
                                                <div class="inner-box" style="border-bottom: 1px solid;">&nbsp;</div>
                                                <div class="inner-box2" style="border-bottom: 1px solid;">&nbsp;</div>
                                                <div class="inner-box3" style="border-bottom: 1px solid;">&nbsp;</div>
                                                <div class="inner-box4" style="border-bottom: 1px solid;">&nbsp;</div>
                                                <div class="div_clear"></div>
                                                </div>
                                                </div>
                                                <div class="div_clear"></div>

                                                <div class="bg-purple molecular" style="border-bottom: 1px solid;"><b>BIOLOGÍA MOLECULAR Y GENÉTICA</b></div>
                                                <div class="molecular" style="border-bottom: 1px solid;height: 35px !important">&nbsp;</div>


                                                <table class="table" cellpadding="0" cellspacing="0" style="width: 100%;margin-top: -90px;">
                                                    <thead>
                                                        <tr>
                                                            <td colspan="5" style="font-size: 9px;" class="bg-blue td-border"><b>D. DATOS DEL PROFESIONAL RESPONSABLE</b></td>
                                                        </tr>
                                                    </thead>

                                                    <tr>
                                                        <td style="width:20%; height: 10px;" class="text-center td-border bg-green"><b>FECHA GENERACIÓN PEDIDO </b></td>
                                                        <td style="width:20%; height: 10px;" class="text-center td-border bg-green"><b>HORA GENERACIÓN PEDIDO </b></td>
                                                        <td style="width:20%" class="text-center td-border bg-green"><b>PRIMER NOMBRE</b></td>
                                                        <td style="width:20%" class="text-center td-border bg-green"><b>PRIMER APELLIDO</b></td>
                                                        <td style="width:20%" class="text-center td-border bg-green"><b>SEGUNDO APELLIDO</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="td-border text-center" style="width:5%; ">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,0,10) }} @else{{ substr($orden->fecha_convenios,0,10) }} @endif</td>
                                                        <td class="td-border text-center" style="width:5%; ">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,11,8) }}@else {{ substr($orden->fecha_convenios,11,8) }}@endif</td>
                                                        <!--td class="td-border text-center" style="width:5%; ">{{$fecha}}</td>
                                                        <td class="td-border text-center" style="width:5%; ">{{$hora}}</td-->
                                                        <td class="td-border text-center" style="width:14%; ">{{$orden->doctor->nombre1}}</td>
                                                        <td class="td-border text-center" style="width:14%; ">{{$orden->doctor->apellido1}}</td>
                                                        <td class="td-border text-center" style="width:14%; ">{{$orden->doctor->apellido2}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="td-border bg-green text-center"><b>NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
                                                        <td colspan="2" class="td-border bg-green text-center"><b>FIRMA</b></td>
                                                        <td class="td-border bg-green text-center"><b>SELLO</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="td-border text-center" style="width:12%; ">{{$orden->id_doctor_ieced}}</td>
                                                        @php
                                                        $ruta='';
                                                        $firma = Sis_medico\Firma_Usuario::where('id_usuario',$orden->id_doctor_ieced)->first();


                                                        if(!is_null($firma)){
                                                        $ruta = $firma->firma;
                                                        }

                                                        if($ruta==''){
                                                        $firma = Sis_medico\Firma_Usuario::where('id_usuario','1307189140')->first();
                                                        if(!is_null($firma)){
                                                        $ruta = $firma->nombre;
                                                        }
                                                        }
                                                        @endphp
                                                        <td colspan="2" class="td-border text-center" style="width:25%; ">
                                                            @if(!is_null($firma) && !is_null($firma->firma))
                                                            <img width=100 height=30 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" style="" align=center hspace=12>
                                                            @else
                                                            <br><br><br>
                                                            <b style="font-size: 8px;">@if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif @endif  {{$orden->id_doctor_ieced->apellido1}} @if($orden->id_doctor_ieced->apellido2!='N/A'){{$orden->id_doctor_ieced->apellido2}}@endif {{$orden->id_doctor_ieced->nombre1}} {{$orden->id_doctor_ieced->nombre2}}</b><br>
                                                            @endif

                                                        </td>
                                                        <td class="td-border" style="width:25%; font-size: 8px; text-align: center; ">
                                                        @if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif @endif {{$orden->doctor->apellido1}} @if($orden->doctor->apellido2!='N/A'){{$orden->doctor->apellido2}}@endif {{$orden->doctor->nombre1}} {{$orden->doctor->nombre2}}<br>
                                                            Cedula No. {{$orden->id_doctor_ieced}}

                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td style="width:14%; " class="text-center td-border bg-green"><b>FECHA DE TOMA DE MUESTRA </b></td>
                                                        <td style="width:15%;" class="text-center td-border bg-green"><b>HORA DE TOMA DE MUESTRA </b></td>
                                                        <td colspan="2" style="width:20%" class="text-center td-border bg-green"><b>NOMBRE Y APELLIDO DE LA PERSONA QUE TOMA LA MUESTRA</b></td>
                                                        <td style="width:20%" class="text-center td-border bg-green"><b>FIRMA</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="td-border text-center" style="width:5%; ">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,0,10) }} @else{{ substr($orden->fecha_convenios,0,10) }} @endif</td>
                                                        <td class="td-border text-center">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,11,8) }}@else {{ substr($orden->fecha_convenios,11,8) }}@endif</td>
                                                        @php
                                                        $firma = Sis_medico\Firma_Usuario::where('nombre', 'labs_responsable.jpg')->first();
                                                        @endphp
                                                        @if(!is_null($firma))
                                                        @php
                                                        $id_laboratorista = $firma->id_usuario;
                                                        $laboratorista = Sis_medico\User::where('id',$id_laboratorista)->first();
                                                        @endphp
                                                        <td colspan="2" class="td-border" style="text-align: center;">{{$laboratorista->nombre1}} {{$laboratorista->nombre2}} {{$laboratorista->apellido1}}  {{$laboratorista->apellido2}}</td>
                                                        @else
                                                        <td colspan="2" class="td-border"></td>
                                                        @endif

                                                        <td class="td-border" style="color: black;text-align: center;width: 60%; height:25;">
                                                            <!--br-->
                                                            @if( $orden->fecha_orden < '2021-06-01  0:00:00' ) 
                                                                <img style="position: absolute;margin-left: 150px" width=100 height=35 src="{{base_path().'/storage/app/logo/labs_karla.png'}}" align=center hspace=12>
                                                            @else
                                                                <img style='position: absolute;margin-left: 150px' width=100 height=35 src="{{base_path().'/storage/app/logo/labs_responsable.jpg'}}" align=center hspace=12>
                                                            @endif
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="3">SNS-MSP/HCU-form.010A/2021</td>
                                                        <td colspan="2" style="text-align: right;">LABORATORIO CLÍNICO - SOLICITUD (1)</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <div class="page-break-after"></div>
                                                
                                              
                                                <table cellpadding="0" cellspacing="0" style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <td colspan="8" style="font-size: 14px;" class="bg-blue td-border"><b>A. VIH / ITS</b></td>
                                                        </tr>
                                                        <tr>
                                                            @for($i = 0; $i < 4; $i++) <td class="bg-green td-border">@if(isset($vih[$i])) {{$vih[$i]->nombre}} @else &nbsp; @endif</td>
                                                                <td style="width: 4%" class="td-border">@if(isset($vih[$i])) @if($detalles->where('id_examen',$vih[$i]->id)->first() != null) X @endif @endif</td>
                                                                @endfor
                                                        </tr>
                                                        <tr>
                                                            @for($i = 4; $i < 8; $i++) <td class="bg-green td-border">@if(isset($vih[$i])) {{$vih[$i]->nombre}} @else &nbsp; @endif</td>
                                                                <td style="width: 4%" class="td-border">@if(isset($vih[$i])) @if($detalles->where('id_examen',$vih[$i]->id)->first() != null) X @endif @endif</td>
                                                                @endfor
                                                        </tr>
                                                        </tbody>
                                                </table>
                                                <br>
                                                <table cellpadding="0" cellspacing="0" style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <td colspan="31" style="font-size: 14px;" class="bg-blue td-border"><b>B. TUBERCULOSIS</b></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="31" style="font-size: 14px;" class="bg-green text-center td-border"><b>Tipo de
                                                                    afectado</b></td>
                                                        </tr>
                                                        <td>&nbsp;</td>
                                                    </thead>
                                                </table>
                                                <table style="width: 100%">
                                                    <tbody>
                                                        <tr>
                                                            <td style="width: 5%; height: 5px; font-size: 10px" class="text-center">Nuevo </td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td>&nbsp;</td>
                                                            <td style="width: 5%; height: 5px; font-size: 10px" class="text-center">Recaída</td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td style="width: 5%; height: 5px; font-size: 10px" class="text-center">Fracaso</td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td style="width: 25%; height: 5px; font-size: 10px" class="text-center">Pérdida en el seguimiento
                                                            </td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td>&nbsp;</td>
                                                            <td style="width: 5%; height: 5px;  font-size: 10px" class="text-centrer">PVV</td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td>&nbsp;</td>
                                                            <td style="width: 5%; height: 5px;  font-size: 10px" class="text-center">PPL</td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td>&nbsp;</td>
                                                            <td style="width: 9%; height: 5px  font-size: 10px;" class="text-center">Niño 5 años</td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 10%"></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="31">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 14%; height: 5px; font-size: 10px" colspan="6" class="text-center">Sospecha de
                                                                Meningitis TB </td>
                                                            <td></td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td></td>
                                                            <td style="height: 5px; font-size: 10px" colspan="7" class="text-center">Alta sospecha clínica
                                                                y/o radiológica BK (-) </td>
                                                            <td></td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td style=" height: 5px; font-size: 10px" colspan="3">Comorbilidad</td>
                                                            <td></td>
                                                            <td class="td-border2" style="width: 1%"></td>
                                                            <td></td>
                                                            <td class="td-border2" colspan="2" style="width: 10%"></td>
                                                            <td></td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="31">&nbsp;</td>
                                                        </tr>



                                                        <tr>
                                                            <td style="width: 12%; height: 5px; font-size: 10px " colspan="3" class="text-center">Contacto TBR
                                                            </td>
                                                            <td></td>
                                                            <td></td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td></td>
                                                            <td colspan="31"></td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="31">&nbsp;</td>
                                                        </tr>

                                                        <tr>
                                                            <td style="width: 20%;" class="text-center">Sospecha de TB EP</td>
                                                            <td></td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td></td>
                                                            <td style="width: 20%; height: 5px; font-size: 8px" class="text-center; " colspan="6">Talento humano
                                                                en salud </td>
                                                            <td></td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td></td>
                                                            <td style="width: 20%; height: 5px; font-size: 8px" class="text-center; " colspan="4">Irregularida en
                                                                la toma del Ttos</td>
                                                            <td></td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td></td>
                                                            <td style="width: 10%; height: 5px; font-size: 10px" class="text-center; " colspan="6">Reversión</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="31">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="31">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size: 10px" class="text-center;">Embarazo</td>
                                                            <td></td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td></td>
                                                            <td style="width: 20%; height: 5px; font-size: 10px" class="text-center; " colspan="6">BK (+) al
                                                                2do. mes </td>
                                                            <td></td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td></td>
                                                            <td style="font-size: 10px" class="text-center; " colspan="3">Condiciones especiales </td>
                                                            <td></td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td></td>
                                                            <td class="td-border2" colspan="2" style="width: 10%"></td>
                                                            <td></td>
                                                            <td style="font-size: 10px" class="text-center;">otros</td>
                                                            <td></td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td></td>
                                                            <td class="td-border2" style="width: 10%"></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="62">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="31"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table cellpadding="0" cellspacing="0" style="width: 100%">
                                                    <thead>
                                                        <tr>
                                                            <td style="font-size: 14px;" class="bg-blue text-center td-border" colspan="31"><b>Antecedentes de tuberculosis</b></td>
                                                            <td style="font-size: 14px; width: 45%;" class="bg-blue text-center td-border" colspan="31"><b>Tipo de muestra</b>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="62">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td style=" text-align: right; font-size: 10px;" class="text-center;" colspan="10"><b>TIPO DE
                                                                    RESISTENCIA</b></td>
                                                            <td colspan="21"></td>
                                                            <td colspan="31">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="62">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 18%; height: 5px; " class="text-center" colspan="2">TB sensible </td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td>&nbsp;</td>
                                                            <td style="width: 18%; height: 8px;" class="text-center">TB resistente</td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 4%"></td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 15%"></td>
                                                            <td colspan="17"></td>
                                                            <td colspan="5" style="width: 5%; height: 8px;" class="text-center">Esputo</td>
                                                            <td colspan="3">&nbsp;</td>
                                                            <td class="td-border2" style="width: 4%"></td>
                                                            <td style="width: 2%; height: 8px;" class="text-center" colspan="2">Otro</td>
                                                            <td colspan="1">&nbsp;</td>
                                                            <td class="td-border2" style="width: 3%"></td>
                                                            <td></td>
                                                            <td class="td-border2" style="width: 15%"></td>
                                                            <td colspan="12"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="40">&nbsp;</td>
                                                            <td colspan="12">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="62" style="font-size: 14px;" class="bg-blue td-border text-center"><b>Solicitud
                                                                    para diagnóstico</b></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="62">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 9%; height: 5px; " class="text-center" colspan="2"><b>Ada</b></td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 3%"></td>
                                                            <td colspan="2">&nbsp;</td>
                                                            <td style="width: 9%; height: 5px; " class="text-center" colspan="5"><b>Baciloscopia</b></td>
                                                            <td>&nbsp;</td>
                                                            <td style="width: 9%; height: 5px; " class="text-center" colspan="5">Diagnóstico</td>
                                                            <td colspan="2">&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td>&nbsp;</td>
                                                            <td style="width: 9%; height: 5px; " class="text-center" colspan="2">No.</td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td style="width: 30%; height: 5px; " class="text-center" colspan="10"><b>Cultivo medio
                                                                    sólido(OK)<b></td>
                                                            <td>&nbsp;</td>
                                                            <td style="width: 9%; height: 5px; " class="text-center">Diagnóstico</td>
                                                            <td colspan="2">&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td style="width: 9%; height: 5px; " class="text-center">No.</td>
                                                            <td class="td-border2" style="width: 9%"></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="40">&nbsp;</td>
                                                            <td colspan="12">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="6">&nbsp;</td>
                                                            <td style="width: 9%; height: 5px; " class="text-center">Control</td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td style="width: 12%; height: 5px; " class="text-center" colspan="2">No. Mes</td>
                                                            <td></td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td>&nbsp;</td>
                                                            <td colspan="15"></td>
                                                            <td style="width: 9%; height: 5px; " class="text-center">Control</td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td style="width: 9%; height: 5px; " class="text-center" colspan="2">No. Mes</td>
                                                            <td></td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 4%"></td>
                                                            <td>&nbsp;</td>
                                                            <td colspan="15"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="62">&nbsp;</td>
                                                        </tr>

                                                        <tr>
                                                            <td style="width: 24%; height: 5px; " class="text-center" colspan="2"><b>PCR tiempo real</b>
                                                            </td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td colspan="2">&nbsp;</td>
                                                            <td style="width: 9%; height: 5px; " class="text-center" colspan="5"><b>Nitrato reductasa</b>
                                                            </td>
                                                            <td colspan="1">&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td style="width: 30%; height: 5px; " class="text-center" colspan="10"><b>Cultivo
                                                                    medioliquido<b></td>
                                                            <td colspan="4">&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td>&nbsp;</td>
                                                            <td style="width: 11%; height: 5px; " class="text-center"><b>Genotipificación</b></td>
                                                            <td colspan="2">&nbsp;</td>
                                                            <td class="td-border2" style="width: 3%"></td>
                                                            <td colspan="3">&nbsp;</td>
                                                            <td style="width: 9%; height: 5px; " class="text-center"><b>Tipificación</b></td>
                                                            <td colspan="2">&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td></td>
                                                        </tr>

                                                        <tr>
                                                            <td style="width: 9%; height: 5px; " class="text-center" colspan="2">(Xpert/MTB/RIF)</td>
                                                            <td colspan="4">&nbsp;</td>
                                                            <td style="width: 9%; height: 5px; " class="text-center" colspan="5">(GRIESS)*</td>
                                                            <td colspan="6">&nbsp;</td>
                                                            <td style="width: 9%; height: 5px; " class="text-center" colspan="5">Diagnóstico</td>
                                                            <td colspan="20">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="62">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 24%; height: 5px; " class="text-center" colspan="2"><b>PSD 1ra. Línea</b>
                                                            </td>
                                                            <td>&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td colspan="2">&nbsp;</td>
                                                            <td style="width: 9%; height: 5px; " class="text-center" colspan="5"><b>PSD 1ra. Línea</b>
                                                            </td>
                                                            <td colspan="1">&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td>&nbsp;</td>

                                                            <td>&nbsp;</td>
                                                            <td style="width: 30%; height: 5px; " class="text-center" colspan="10"><b>PSD 2da. Línea<b>
                                                            </td>
                                                            <td colspan="4">&nbsp;</td>
                                                            <td class="td-border2" style="width: 2%"></td>
                                                            <td>&nbsp;</td>

                                                            <td style="width: 11%; height: 5px; " class="text-center"><b>PSD 2da. Línea</b></td>
                                                            <td colspan="2">&nbsp;</td>
                                                            <td class="td-border2" style="width: 3%"></td>
                                                            <td colspan="3">&nbsp;</td>
                                                            <td></td>
                                                        </tr>

                                                        <tr>
                                                            <td style="width: 9%; height: 5px; " class="text-center" colspan="7">(Proporciones-Medio
                                                                sólido)</td>
                                                            <td style="width: 9%; height: 5px; " class="text-center" colspan="5">(MGIT-Medio líquido)</td>
                                                            <td colspan="5">&nbsp;</td>

                                                            <td style="width: 9%; height: 5px; " class="text-center" colspan="6">(Proporciones-Medio
                                                                sólido)</td>
                                                            <td colspan="8">&nbsp;</td>
                                                            <td style="width: 9%; height: 5px; " class="text-center" colspan="6">(Proporciones-Medio
                                                                sólido)</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="62">&nbsp;</td>
                                                        </tr>
                                                    </thead>
                                                </table>

                                                <table class="table-footer2" cellpadding="0" cellspacing="0" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <td colspan="5" style="font-size: 9px;" class="bg-blue td-border"><b>D. DATOS DEL PROFESIONAL RESPONSABLE</b></td>
                                                        </tr>
                                                    </thead>

                                                    <tr>
                                                        <td style="width:14%; height: 10px;" class="text-center td-border bg-green"><b>FECHA GENERACIÓN PEDIDO </b></td>
                                                        <td style="width:15%; height: 10px;" class="text-center td-border bg-green"><b>HORA GENERACIÓN PEDIDO </b></td>
                                                        <td style="width:20%" class="text-center td-border bg-green"><b>PRIMER NOMBRE</b></td>
                                                        <td style="width:20%" class="text-center td-border bg-green"><b>PRIMER APELLIDO</b></td>
                                                        <td style="width:15%" class="text-center td-border bg-green"><b>SEGUNDO APELLIDO</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="td-border text-center" style="width:5%; ">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,0,10) }} @else{{ substr($orden->fecha_convenios,0,10) }} @endif</td>
                                                        <td class="td-border text-center" style="width:5%; ">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,11,8) }}@else {{ substr($orden->fecha_convenios,11,8) }}@endif</td>
                                                        <!--td class="td-border text-center" style="width:5%; ">{{$fecha}}</td>
                                                        <td class="td-border text-center" style="width:5%; ">{{$hora}}</td-->
                                                        <td class="td-border text-center" style="width:14%; ">{{$orden->doctor->nombre1}}</td>
                                                        <td class="td-border text-center" style="width:14%; ">{{$orden->doctor->apellido1}}</td>
                                                        <td class="td-border text-center" style="width:14%; ">{{$orden->doctor->apellido2}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="td-border bg-green text-center"><b>NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
                                                        <td colspan="2" class="td-border bg-green text-center"><b>FIRMA</b></td>
                                                        <td class="td-border bg-green text-center"><b>SELLO</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="td-border text-center" style="width:12%; ">{{$orden->id_doctor_ieced}}</td>
                                                        @php
                                                        $ruta='';
                                                        $firma = Sis_medico\Firma_Usuario::where('id_usuario',$orden->id_doctor_ieced)->first();


                                                        if(!is_null($firma)){
                                                        $ruta = $firma->firma;
                                                        }

                                                        if($ruta==''){
                                                        $firma = Sis_medico\Firma_Usuario::where('id_usuario','1307189140')->first();
                                                        if(!is_null($firma)){
                                                        $ruta = $firma->nombre;
                                                        }
                                                        }
                                                        @endphp
                                                        <td colspan="2" class="td-border text-center" style="width:25%; ">
                                                            @if(!is_null($firma) && !is_null($firma->firma))
                                                            <img width=120 height=30 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" style="" align=center hspace=12>
                                                            @else
                                                            <br><br><br>
                                                            <b style="font-size: 8px;">@if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif @endif {{$orden->id_doctor_ieced->apellido1}} @if($orden->id_doctor_ieced->apellido2!='N/A'){{$orden->id_doctor_ieced->apellido2}}@endif {{$orden->id_doctor_ieced->nombre1}} {{$orden->id_doctor_ieced->nombre2}}</b><br>
                                                            @endif

                                                        </td>
                                                        <td class="td-border" style="width:25%; font-size: 8px; text-align: center;">
                                                        @if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif @endif  {{$orden->doctor->apellido1}} @if($orden->doctor->apellido2!='N/A'){{$orden->doctor->apellido2}}@endif {{$orden->doctor->nombre1}} {{$orden->doctor->nombre2}} <br>
                                                            Cedula No. {{$orden->id_doctor_ieced}}
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td style="width:14%; " class="text-center td-border bg-green"><b>FECHA DE TOMA DE MUESTRA </b></td>
                                                        <td style="width:15%;" class="text-center td-border bg-green"><b>HORA DE TOMA DE MUESTRA </b></td>
                                                        <td colspan="2" style="width:20%" class="text-center td-border bg-green"><b>NOMBRE Y APELLIDO DE LA PERSONA QUE TOMA LA MUESTRA</b></td>
                                                        <td style="width:20%" class="text-center td-border bg-green"><b>FIRMA</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="td-border text-center" style="width:5%; ">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,0,10) }} @else{{ substr($orden->fecha_convenios,0,10) }} @endif</td>
                                                        <td class="td-border text-center">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,11,8) }}@else {{ substr($orden->fecha_convenios,11,8) }}@endif</td>
                                                        @php
                                                        $firma = Sis_medico\Firma_Usuario::where('nombre', 'labs_responsable.jpg')->first();
                                                        @endphp
                                                        @if(!is_null($firma))
                                                        @php
                                                        $id_laboratorista = $firma->id_usuario;
                                                        $laboratorista = Sis_medico\User::where('id',$id_laboratorista)->first();
                                                        @endphp
                                                        <td colspan="2" class="td-border" style="text-align: center;">{{$laboratorista->nombre1}} {{$laboratorista->nombre2}} {{$laboratorista->apellido1}} {{$laboratorista->apellido2}} </td>
                                                        @else
                                                        <td colspan="2" class="td-border"></td>
                                                        @endif

                                                        <td class="td-border" style="color: black;text-align: center;width: 60%; height:40;">
                                                            <br>
                                                            @if( $orden->fecha_orden < '2021-06-01  0:00:00' ) 
                                                                <img style="position: absolute;margin-left: 150px" width=100 height=30 src="{{base_path().'/storage/app/logo/labs_karla.png'}}" align=center hspace=12>
                                                            @else
                                                                <img style='position: absolute;margin-left: 150px' width=100 height=30 src="{{base_path().'/storage/app/logo/labs_responsable.jpg'}}" align=center hspace=12>
                                                            @endif
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="3">SNS-MSP/HCU-form.010A/2021</td>
                                                        <td colspan="2" style="text-align: right;">LABORATORIO CLÍNICO - SOLICITUD (1)</td>
                                                    </tr>
                                                    </tbody>
                                                </table>