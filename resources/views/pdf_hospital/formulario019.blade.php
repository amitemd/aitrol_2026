<!DOCTYPE html>
<html>
<?php
$fecha_actual = date('Y-m-d');
$hora_actual = date('H:i:s');

$usuarioLogueado = auth()->user();
$rd = rand();
$fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$diaNacimiento = $fechaNacimiento->format('d');

$mesNacimiento = $fechaNacimiento->format('m');
?>

<head>
    <title>HCU_019</title>
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

        /* Define the footer rules */
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 0cm;
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
            background-color: #9abedd;
        }

        .bg-green {
            background-color: #BADAC8;
            font-size: 8px;
        }

        .bg-yellow {
            background-color: #F1FD77;
        }

        .text-center {
            text-align: center;
            font-size: 8px;
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
            font-size: 8px;
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
    <header>

    </header>
    <main>
        @php
        $cont = 0;
        $nombre = '';
        $nombrePre='';
        $ciePre='';
        $nombrePost='';
        $ciePost='';
        $id_anestesiologo = null;
        if(isset($anestesiologia->anestesiologo)) {
        $nombre = $anestesiologia->anestesiologo->nombre1 . ' ' . $anestesiologia->anestesiologo->nombre2 . ' ' . $anestesiologia->anestesiologo->apellido1 . ' ' . $anestesiologia->anestesiologo->apellido2;
        $id_anestesiologo = $anestesiologia->anestesiologo->id;
        }
        $nombreAyudante = isset($anestesiologia->ayudante) ? $anestesiologia->ayudante->nombre1.' '. $anestesiologia->ayudante->nombre2 .' '.$anestesiologia->ayudante->apellido1.' '.$anestesiologia->ayudante->apellido2 : '';
        $nombreInstrumentista = isset($anestesiologia->instrumentista) ? $anestesiologia->instrumentista->nombre1.' '.$anestesiologia->instrumentista->nombre2 .' '.$anestesiologia->instrumentista->apellido1.' '.$anestesiologia->instrumentista->apellido2 : '';
        $nombrePost='';
        if(!is_null($cie10_eg)){
        $c3 = Sis_medico\Cie_10_3::find($cie10_eg->cie10);
        $c4 = Sis_medico\Cie_10_4::find($cie10_eg->cie10);
        if(!is_null($c3)){
        $nombrePost = $c3->descripcion;
        }
        if(!is_null($c4)){
        $nombrePost = $c4->descripcion;
        }
        $ciePost = $cie10_eg->cie10;
        }
        $csv_record = "";
        if(!is_null($anestesiologia)){
        $csv_record = Sis_medico\hc_anestesiologia_csv::where('id_hc_anestesiologia',$anestesiologia->id)->first();
        }

        @endphp

        <div class="box-body card" style="text-align: center">
            @if(!is_null($empresa))
            <?php
            //dd(base_path().'/storage/app/logo/'.$empresa->logo); 
            ?>
            <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:200px;height: 50px">
            @endif
        </div>
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
                        <p class=MsoNormal align=center style='text-align:center;line-height:normal'><span style='font-size:8px;'>{{$historia->seguro->nombre}} </span></p>
                    </td>
                    <td colspan="3">
                        <p class=MsoNormal align=center style='text-align:center;line-height:normal'><span style='font-size:8px;'>{{$empresa->nombrecomercial}} </span></p>
                    </td>
                    <td colspan="3">
                        <p class=MsoNormal align=center style='text-align:center;line-height:normal'><span style='font-size:8px;'>{{$paciente->id}}</span></p>
                    </td>
                    <td colspan="5">&nbsp;</td>
                </tr>
                <tr>
                    <td rowspan="2" colspan="3" class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>SEGUNDO NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center">
                        <p align=center style='text-align:center;line-height:normal;'><b>SEXO</b></p>
                    </td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>FECHA DE NACIMIENTO</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>EDAD</b></td>
                    <td colspan="4" class="bg-green text-center"><b>CONDICIÓN EDAD</b></td>
                </tr>
                <tr>
                    <td class="bg-green text-center">H</td>
                    <td class="bg-green text-center">D</td>
                    <td class="bg-green text-center">M</td>
                    <td class="bg-green text-center">A</td>
                </tr>
                <tr>
                    <td colspan="3">
                        <p class=MsoNormal align=center style='text-align:center;line-height:normal'><span style='font-size:8px;'>{{$paciente->apellido1}}</span></p>
                    </td>
                    <td colspan="2">
                        <p class=MsoNormal align=center style='text-align:center;line-height:normal'><span style='font-size:8px;'>{{$paciente->apellido2}}</span></p>
                    </td>
                    <td colspan="1">
                        <p class=MsoNormal align=center style='text-align:center;line-height:normal'><span style='font-size:8px;'>{{$paciente->nombre1}}</span></p>
                    </td>
                    <td colspan="1">
                        <p class=MsoNormal align=center style='text-align:center;line-height:normal'><span style='font-size:8px;'>{{$paciente->nombre2}}</span></p>
                    </td>
                    <td colspan="1" valign=top style='width:56.25pt;padding:0cm 5.4pt 0cm 5.4pt'>
                        <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
                    text-align:center;line-height:normal'><span style='font-size:8px;'>@if($paciente->sexo=='1')MASCULINO @elseif($paciente->sexo=='2')FEMENINO @endif</span></p>
                    </td>
                    <td class="text-center" colspan="1">{{$paciente->fecha_nacimiento}}</td>
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
                    <td style="width: 40%;" class="text-center">{{$especialidad->nombre}}</td>
                    <td style="width: 10%;" class="bg-green text-center"><b>DIAGNÓSTICO</b></td>
                    <td colspan="6" style="width: 40%;" class="text-center">{{$ciePost}} - {{$nombrePost}}</td>
                </tr>
                <tr>
                    <td rowspan="2" style="width: 10%;" class="bg-green text-center"><b>CIRUGÍA REALIZADA</b></td>
                    <td rowspan="2" style="width: 40%;" class="text-center">{{$anestesiologia->operacion_realizada}}</td>
                    <td rowspan="2" style="width: 10%;" class="bg-green text-center"><b>TÉCNICA ANESTÉSICA</b></td>
                    <td style="width: 10%;" class="text-center">GENERAL</td>
                    <td style="width: 3%;" class="text-center">@if($evolucion_anestesiologo->tecnica_anestesica == '0') X @else &nbsp; @endif</td>
                    <td style="width: 10%;" class="text-center">LOCAL</td>
                    <td style="width: 3%;" class="text-center">@if($evolucion_anestesiologo->tecnica_anestesica == '1') X @else &nbsp; @endif</td>
                    <td style="width: 10%;" class="text-center">TRONCULAR</td>
                    <td style="width: 4%;" class="text-center">@if($evolucion_anestesiologo->tecnica_anestesica == '2') X @else &nbsp; @endif</td>
                </tr>
                <tr>
                    <td style="width: 10%;" class="text-center">NEUROAXIAL</td>
                    <td style="width: 3%;" class="text-center">@if($evolucion_anestesiologo->tecnica_anestesica == '3') X @else &nbsp; @endif</td>
                    <td style="width: 10%;" class="text-center">INTRAVENOSA</td>
                    <td style="width: 3%;" class="text-center">@if($evolucion_anestesiologo->tecnica_anestesica == '4') X @else &nbsp; @endif</td>
                    <td style="width: 10%;" class="text-center">SEDACIÓN</td>
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
                    <td class="width100 text-center"> @if(!is_null($anestesiologia->anestesiologo)) {{$anestesiologia->anestesiologo->apellido1}} {{$anestesiologia->anestesiologo->nombre1}} @endif</td>
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
                    <td colspan="2">100%</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->bromage == '1') X @else &nbsp; @endif</td>
                    <td colspan="2">66%</td>
                    <td class="text-center width20">@if($evolucion_anestesiologo->bromage == '2') X @else &nbsp; @endif</td>
                    <td colspan="2">33%</td>
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
                    <td class="width100">
                        @if(!is_null($firma))
                        <img width=75 height=65 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" style="" align=center hspace=12><br>
                        @endif
                    </td>
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
                    @php
                    $firma_doctor = null;
                    if(!is_null($aud_hc_procedimiento)){
                    $id_doctor = $aud_hc_procedimiento->doctor_firma->id;
                    if(!is_null($id_doctor)){
                    $firma_doctor = Sis_medico\Firma_Usuario::where('id_usuario', $id_doctor)->first();
                    }
                    }
                    @endphp
                    @if(!is_null($aud_hc_procedimiento))
                    <td class="width100 text-center"> @if(!is_null($aud_hc_procedimiento->doctor_firma)) {{$aud_hc_procedimiento->doctor_firma->apellido1}} {{$aud_hc_procedimiento->doctor_firma->nombre1}} @endif</td>
                    @else
                    <td class="width100 text-center"> @if(!is_null($anestesiologia->anestesiologo)) {{$anestesiologia->anestesiologo->apellido1}} {{$anestesiologia->anestesiologo->nombre1}} @endif</td>
                    @endif
                </tr>
                <tr>
                    <td class="bg-green text-left" colspan="2">OTRO</td>
                    <td class="text-center" colspan="35">{{ $evolucion_anestesiologo->otro ?? '' }}</td>
                    <td class="text-center">FIRMA</td>
                    <td class="width100">
                        @if(!empty($firma_doctor->firma))
                        <img width="75" height="65" src="{{ base_path().'/storage/app/avatars/'.$firma_doctor->firma }}"><br>
                        @elseif(!empty($firma->firma))
                        <img width="75" height="65" src="{{ base_path().'/storage/app/avatars/'.$firma->firma }}"><br>
                        @else
                        <span style="font-size:10px; color:#999;">Sin firma</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>


        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="7" class="bg-blue"><b>D. MONITORIZACIÓN</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" class="text-center">
                        @if(is_null($anestesiologia->url_imagen))
                        <img style="width: 1070px; height: 150px;" src="{{base_path().'/storage/app/hc_ima/trans/imagen_post.jpeg'}}" style="background-size: cover !important;">
                        @else
                        <img style="width: 1070px; height: 150px;" src="{{base_path().'/storage/app/hc_ima/'.$anestesiologia->url_imagen}}" style="background-size: cover !important;">
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
                    <td style="width: 25%;" class="text-left">{{$evolucion_anestesiologo->oral}}</td>
                    <td style="width: 25%;" class="text-left">DIURESIS O SONDA VESICAL</td>
                    <td style="width: 25%;" class="text-left">{{$evolucion_anestesiologo->diuresis_sonda_vesical}}</td>
                </tr>
                <tr>
                    <td style="width: 25%;" class="text-left">INFUSIONES</td>
                    <td style="width: 25%;" class="text-left">{{$evolucion_anestesiologo->infusion}}</td>
                    <td style="width: 25%;" class="text-left">VÓMITO O SONDA NASOGÁSTRICO</td>
                    <td style="width: 25%;" class="text-left">{{$evolucion_anestesiologo->vomito}}</td>
                </tr>
                <tr>
                    <td style="width: 25%;" class="text-left">SANGRE Y DERIVADOS</td>
                    <td style="width: 25%;" class="text-left">{{$evolucion_anestesiologo->sangre_derivados}}</td>
                    <td style="width: 25%;" class="text-left">TORÁCICO</td>
                    <td style="width: 25%;" class="text-left">{{$evolucion_anestesiologo->toracico}}</td>
                </tr>
                <tr>
                    <td style="width: 25%;" class="text-left">OTROS</td>
                    <td style="width: 25%;" class="text-left">{{$evolucion_anestesiologo->vomito}}</td>
                    <td style="width: 25%;" class="text-left">DRENAJES</td>
                    <td style="width: 25%;" class="text-left">{{$evolucion_anestesiologo->drenajes}}</td>
                </tr>
                <tr>
                    <td rowspan="2" style="width: 25%;" class="text-left"><b>TOTAL</b></td>
                    <td rowspan="2" style="width: 25%;">{{$evolucion_anestesiologo->total_ingresos}}</td>
                    <td style="width: 25%;" class="text-left">OTRAS</td>
                    <td style="width: 25%;" class="text-left">{{$evolucion_anestesiologo->eliminacion_otras}}</td>
                </tr>
                <tr>
                    <td style="width: 25%;" class="text-left"><b>TOTAL</b></td>
                    <td style="width: 25%;" class="text-left">{{$evolucion_anestesiologo->total_eliminacion}}</td>
                </tr>
            </tbody>

        </table>
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;"><b>SNS-MSP / HCU-form.019/2021</b></td>
                <td style="width: 50%;" class="text-right"><b>CUIDADOS POSTANESTÉSICO (1)</b></td>
            </tr>
        </table>
    </main>
    <main class="page-break-before">
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
                    <td class="text-center">@if ($agenda->seguro->id == 6)
                        PACIENTE EGRESA ESTABLE HEMODINÁMICAMENTE LUEGO DE LA REALIZACIÓN DE ESTUDIOS ENDOSCÓPICOS.
                        PACIENTE EVOLUCIONA FAVORABLEMENTE DESPUÉS DEL PROCEDIMIENTO.
                        SE MANTIENE ESTABLE HEMODINÁMICAMENTE. CLÍNICAMENTE BIEN.
                        @else
                        {{ $evolucion_anestesiologo->evolucion }}
                        @endif
                    </td>
                    <td class="text-center">@if ($agenda->seguro->id == 6) CUIDADOS DE MEDICO RESIDENTE CSV
                        @else {{$evolucion_anestesiologo->prescripcion}} @endif</td>
                    <td class="text-center">@if ($agenda->seguro->id == 6) SOLUSION SALINA 1000 CC PASAR A 30 LTX MIN
                        KETOROLACO AMP 60 MG IV PRN @else {{$evolucion_anestesiologo->administracion_farmacos}} @endif</td>
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
                    <td class="text-center"><b>TA:</b> @if($evolucion_anestesiologo->presion != '0' ) {{$evolucion_anestesiologo->presion}} @else {{$csv_record->presion_arterial}} @endif</td>
                    <td class="text-center"><b>FC:</b> @if($evolucion_anestesiologo->pulso != '0' ){{$evolucion_anestesiologo->pulso}} @else {{$csv_record->pulso}} @endif</td>
                    <td class="text-center"><b>FR:</b> @if($evolucion_anestesiologo->fecuencia_respiratoria != '0' ) {{$evolucion_anestesiologo->fecuencia_respiratoria}} @else {{$csv_record->respiracion}} @endif</td>
                    <td class="text-center"><b>SAT. O2:</b> @if($evolucion_anestesiologo->o2 != '0' ) {{$evolucion_anestesiologo->o2}} @else {{$csv_record->o2}} @endif</td>
                    <td class="text-center"><b>T°:</b> @if($evolucion_anestesiologo->temperatura != '0' ){{$evolucion_anestesiologo->temperatura}} @else {{$csv_record->temperatura}} @endif</td>
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
                    <td colspan="2" class="text-center">@if(!is_null($anestesiologia->anestesiologo)) {{$anestesiologia->anestesiologo->apellido1}} {{$anestesiologia->anestesiologo->nombre1}} @endif</td>
                </tr>
                <tr>
                    <td class="text-left" rowspan="2"><b>FECHA:</b><?php $fecha_recepcion = date('Y-m-d', strtotime($agenda->fechaini)); ?>{{$fecha_recepcion}}</td>
                    <td class="text-right">RAMSAY</td>
                    <td class="text-center">{{$evolucion_anestesiologo->valor_ramsay_egreso}}</td>
                    <td class="text-left"><b>FIRMA</b></td>
                    <td colspan="2" class="text-center">
                        @if(!is_null($firma))
                        <img width=150 height=50 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" style="" align=center hspace=12><br>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="text-right">EVA</td>
                    <td class="text-center">{{$evolucion_anestesiologo->valor_eva_egreso}}</td>
                    <td colspan="3" class="text-center"><b>RECIBE</b></td>
                </tr>
                <tr>
                    <td class="text-left" rowspan="2"><b>HORA:</b> <?php $fecha_recepcion = date('H:i:s', strtotime($agenda->fechaini)); ?>{{$fecha_recepcion}}</td>
                    <td class="text-right">STEWARD</td>
                    <td class="text-center">{{$evolucion_anestesiologo->valor_steward_egreso}}</td>
                    <td class="text-left"><b>NOMBRE</b></td>
                    <td colspan="2" class="text-center"> @if(!is_null($anestesiologia->anestesiologo)) {{$anestesiologia->anestesiologo->apellido1}} {{$anestesiologia->anestesiologo->nombre1}} @endif </td>
                </tr>
                <tr>
                    <td class="text-right">OTRO:</td>
                    <td class="text-center">{{$evolucion_anestesiologo->valor_otro_egreso}}</td>
                    <td class="text-left"><b>FIRMA</b></td>
                    <td colspan="2" class="text-center">
                        @if(!is_null($firma))
                        <img width=150 height=50 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" style="" align=center hspace=12><br>
                        @endif
                    </td>

                </tr>
            </tbody>
        </table>
        <table border="1" style="width: 100%">
            <tr>
                <td rowspan="2" style="width: min-content;"><b>CONDICIONES AL SALIR</b></td>
                <td style="width: min-content;">EXTUBADO</td>
                <td class="text-center">@if($evolucion_anestesiologo->condiciones_al_salir == '1') X @else &nbsp; @endif</td>
                <td style="width: min-content;" rowspan="2"><b>CONDUCIDO A:</b></td>
                <td style="width: min-content;" rowspan="2">HOSPITALIZACIÓN</td>
                <td class="text-center" rowspan="2">@if($evolucion_anestesiologo->conducido == '0') X @else &nbsp; @endif</td>
                <td style="width: min-content;" rowspan="2">UNIDAD CUIDADOS INTENSIVOS</td>
                <td class="text-center" rowspan="2">@if($evolucion_anestesiologo->conducido == '1') X @else &nbsp; @endif</td>
                <td style="width: min-content;" rowspan="2">CRÍTICOS DE EMERGENCIA</td>
                <td class="text-center" rowspan="2">@if($evolucion_anestesiologo->conducido == '2') X @else &nbsp; @endif</td>
                <td style="width: min-content;" rowspan="2">DOMICILIO</td>
                <td class="text-center" rowspan="2">@if($evolucion_anestesiologo->conducido == '3') X @else &nbsp; @endif</td>
                <td style="width: min-content;" rowspan="2">MORGUE</td>
                <td class="text-center" rowspan="2">@if($evolucion_anestesiologo->conducido == '4') X @else &nbsp; @endif</td>
            </tr>
            <tr>
                <td style="width: min-content;">INTUBADO</td>
                <td class="text-center">@if($evolucion_anestesiologo->condiciones_al_salir == '2') X @else &nbsp; @endif</td>
            </tr>
        </table>
        <br>
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="5" class="bg-blue"><b>I. DATOS DEL PROFESIONAL QUE AUTORIZA EL EGRESO</b></td>
                </tr>
            </thead>
            @php
            $fecha_new = substr($anestesiologia->fecha,0,10);
            $hora_new = substr($anestesiologia->fecha,10);
            $user_espe = Sis_medico\User_espe::where('usuid',$anestesiologia->id_anestesiologo)->first();
            $especialidad = null;
            if(!is_null($user_espe)){
            $especialidad = Sis_medico\Especialidad::find($user_espe->espid);
            }


            @endphp
            <tbody>
                <tr>
                    <td style="width: 15%" class="bg-green text-center"><b>FECHA <br> (aaaa-mm-dd)</b></td>
                    <td style="width: 10%" class="bg-green text-center"><b>HORA <br> (hh:mm)</b></td>
                    <td style="width: 25%" class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                    <td style="width: 25%" class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                    <td style="width: 25%" class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
                </tr>
                <tr>
                    <td class="text-center"><?php $fecha_recepcion = date('Y-m-d', strtotime($agenda->fechaini)); ?>{{$fecha_recepcion}}</td>
                    <td class="text-center"><?php $fecha_recepcion = date('H:i:s', strtotime($agenda->fechaini)); ?>{{$fecha_recepcion}}</td>
                    <td class="text-center">@if(!is_null($anestesiologia->anestesiologo)){{$anestesiologia->anestesiologo->nombre1}} @endif</td>
                    <td class="text-center">@if(!is_null($anestesiologia->anestesiologo)){{$anestesiologia->anestesiologo->apellido1}} @endif</td>
                    <td class="text-center">@if(!is_null($anestesiologia->anestesiologo)){{$anestesiologia->anestesiologo->apellido2}} @endif</td>
                </tr>
                <tr>
                    <td colspan="2" class="bg-green text-center">NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</td>
                    <td class="bg-green text-center">FIRMA</td>
                    <td colspan="2" class="bg-green text-center">SELLO</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center">@if(!is_null($anestesiologia->anestesiologo)){{$anestesiologia->anestesiologo->id}} @endif</td>
                    <td style="height: 100px;">
                        @if(!is_null($firma))
                        <img width=130 height=50 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" style="" align=center hspace=12><br>
                        @endif
                    </td>
                    <td colspan="2" class="text-center">
                        @if(!is_null($firma))
                        @if(!is_null($firma->doctor)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @if($firma->doctor->nombre2!='N/A'){{$firma->doctor->nombre2}}@endif @endif<br>
                        @if(!is_null($especialidad)) {{$especialidad->nombre}} @endif<br>
                        @if(!is_null($firma->doctor)) Cedula No. {{$firma->doctor->id}} @endif
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>




    </main>
</body>

</html>