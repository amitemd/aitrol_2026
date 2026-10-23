<!DOCTYPE html>
<html>

@php
$fecha=date('Y-m-d');
$hora = date('H:i:s');
@endphp


<?php

$fechaNacimiento = new DateTime($record->historia->paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($record->historia->paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($record->historia->paciente->fecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');

?>



<head>
    <title>HCU_018A</title>
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
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
        }

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
    </style>
</head>
@php $empresa = $hc_procedimiento->empresa; @endphp

<body>
    <main>
        <div style="text-align: center">
            @if(!is_null($hc_procedimiento->historia->agenda->empresa))
            @if(!is_null($hc_procedimiento->historia->agenda->empresa->logo_form))
            <p><img style="width:150px;height: 50px" src="{{base_path().'/storage/app/logo/'.$hc_procedimiento->historia->agenda->empresa->logo_form}}" alt="none" ></p>
            @else
            <p style="width:150px;height: 50px"><b>{{$hc_procedimiento->historia->agenda->empresa->nombre_form}}</b></p>
            @endif
            @endif
        </div> 
            

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
                    <td style="width: 7%;">{{ substr($record->fecha, 0, 10) }}</td>
                    <td class="bg-green text-center" style="width: 5%;">TALLA</td>
                    <td style="width: 10%;">{{$historia_clinica->altura}}</td>
                    <td class="bg-green text-center" style="width: 7%;">PESO KG</td>
                    <td style="width: 12%;">{{$historia_clinica->peso}}</td>
                    @php
                    $altura = $historia_clinica->altura;
                    $imc = "";
                    if(!is_null($altura) && $altura > 0) {
                    $new_altura = $historia_clinica->altura / 100;
                    if(!is_null($new_altura && $new_altura > 0)){
                    $imc = $historia_clinica->peso / ($new_altura * $new_altura);
                    $imc = round($imc,2);
                    }

                    }
                    @endphp
                    <td class="bg-green text-center" style="width: 5%;">IMC</td>
                    <td style="width: 8%;">{{$imc}}</td>
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
        $ciePre='';
        $nombre2 = '';
        
        
        $ciePost='';
        $id_anestesiologo = null;
        if(!is_null($record->anestesiologo)) {
        $nombre = $record->anestesiologo->nombre1 . ' ' . $record->anestesiologo->nombre2 . ' ' . $record->anestesiologo->apellido1 . ' ' . $record->anestesiologo->apellido2;
        $nombre2 = $record->anestesiologo->apellido1 . ' ' . $record->anestesiologo->apellido2 . ' ' . $record->anestesiologo->nombre1 . ' ' . $record->anestesiologo->nombre2;
        $id_anestesiologo = $record->anestesiologo->id;
            
        }
        if(!is_null($aud_protocolo)){
                if(!is_null($aud_protocolo->id_anestesiologo)){
                    if(!is_null($aud_protocolo->usuario_anestesiologo)){
                        $nombre = $aud_protocolo->usuario_anestesiologo->nombre1 . ' ' . $aud_protocolo->usuario_anestesiologo->nombre2 . ' ' . $aud_protocolo->usuario_anestesiologo->apellido1 . ' ' . $aud_protocolo->usuario_anestesiologo->apellido2;
                        $nombre2 = $aud_protocolo->usuario_anestesiologo->apellido1 . ' ' . $aud_protocolo->usuario_anestesiologo->apellido2 . ' ' . $aud_protocolo->usuario_anestesiologo->nombre1 . ' ' . $aud_protocolo->usuario_anestesiologo->nombre2;
                        $id_anestesiologo = $aud_protocolo->usuario_anestesiologo->id;
                    }
                }


        }
        $nombreAyudante = isset($record->ayudante) ? $record->ayudante->nombre1.' '. $record->ayudante->nombre2 .' '.$record->ayudante->apellido1.' '.$record->ayudante->apellido2 : '';
        $nombreInstrumentista = isset($record->instrumentista) ? $record->instrumentista->nombre1.' '.$record->instrumentista->nombre2 .' '.$record->instrumentista->apellido1.' '.$record->instrumentista->apellido2 : '';
        @endphp
        
                    @php
                    $nombrePre='';
                    if(!is_null($cie10)){
                        $c3 = Sis_medico\Cie_10_3::find($cie10->cie10);
                        $c4 = Sis_medico\Cie_10_4::find($cie10->cie10);

                        
                        if(!is_null($c3)){
                            $nombrePre = $c3->descripcion;
                            
                        }
                        if(!is_null($c4)){
                            $nombrePre = $c4->descripcion;
                        }
                        $ciePre = $cie10->cie10;
                    }   
                    @endphp

                    @php
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
                    @endphp

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="12" class="bg-purple"><b>B. SERVICIO Y PRIORIDAD DE ATENCION</b></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center bg-green" style="width: 200px;">DIAGNÓSTICO PREOPERATORIO</td>
                    <td>@if(!is_null($nombrePre)){{$nombrePre}} @endif</td>
                    <td class="text-center bg-green">CIE</td>
                    <td>@if(!is_null($ciePre)){{$ciePre}} @endif</td>
                    @php
                    //$adicionales = Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $pre_anestesico->anestesiologia->hc_procedimientos->id)->get();
                    $adicionales = collect();
                    if (!empty($pre_anestesico) 
                        && !empty($pre_anestesico->anestesiologia) 
                        && !empty($pre_anestesico->anestesiologia->hc_procedimientos) 
                        && !empty($pre_anestesico->anestesiologia->hc_procedimientos->id)
                    ) {
                        $adicionales = Sis_medico\Hc_Procedimiento_Final::where(
                            'id_hc_procedimientos',
                            $pre_anestesico->anestesiologia->hc_procedimientos->id
                        )->get();
                    }
                    $mas = true;
                    $cirugia = "";
                    foreach ($adicionales as $value2) {
                    if ($mas == true) {
                    $cirugia = $cirugia . $value2->procedimiento->nombre;
                    $mas = false;
                    } else {
                    $cirugia = $cirugia . ' + ' . $value2->procedimiento->nombre;
                    }
                    }
                    @endphp
                    <td class="text-center bg-green" style="width: 15%;">CIRUGÍA PROPUESTA</td>
                    <td colspan="2"> {{$record->operacion_propuesta}}</td>
                    <td class="text-center bg-green" style="width: 10%;">ESPECIALDIDAD</td>
                    <td rowspan="4" class="text-center bg-green" style="width: 5%;">PRIORIDAD</td>
                    <td style="width: min-content;">EMERGENTE</td>
                    <td style="width: 2%;"></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center bg-green" style="width: 200px;">DIAGNÓSTICO POSTOPERATORIO</td>
                    <td>@if(!is_null($nombrePost)){{$nombrePost}} @endif</td>
                    <td class="text-center bg-green">CIE</td>
                    <td>@if(!is_null($ciePost)){{$ciePost}}@endif</td>

                    <td class="text-center bg-green" style="width: 15%;">CIRUGÍA REALIZADA</td>
                    <td colspan="2">{{$record->operacion_realizada}}</td>
                    <td class="text-center" style="width: 10%;"> {{$historia_clinica->agenda->especialidad->nombre}}</td>
                    <td style="width: min-content;">URGENTE</td>
                    <td style="width: 2%;"></td>
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
                    <td style="width: 2%;">X</td>
                </tr>
                <tr>
                    <td class="text-center bg-green" style="width: 20%;">CIRUJANO</td>
                    <td colspan="2">
                        @php
                            $cirujano = null;
                
                            if (!empty($aud_hc_procedimiento->doctor_firma)) {
                                $cirujano = $aud_hc_procedimiento->doctor_firma;
                            } elseif (!empty($hc_procedimiento->doctor)) {
                                $cirujano = $hc_procedimiento->doctor;
                            }
                        @endphp
                
                        @if($cirujano)
                            {{ $cirujano->nombre1 }} 
                            {{ $cirujano->nombre2 }} 
                            {{ $cirujano->apellido1 }} 
                            {{ $cirujano->apellido2 }}
                        @else
                            <span style="color:#999;">No registrado</span>
                        @endif
                    </td>
                
                    <td class="text-center bg-green" style="width: 10%;">AYUDANTE (S)</td>
                    <td>
                        @if(!empty($aud_hc_procedimiento->doctor_ayudante))
                            {{ $aud_hc_procedimiento->doctor_ayudante->nombre1 }} 
                            {{ $aud_hc_procedimiento->doctor_ayudante->nombre2 }} 
                            {{ $aud_hc_procedimiento->doctor_ayudante->apellido1 }} 
                            {{ $aud_hc_procedimiento->doctor_ayudante->apellido2 }}
                        @else
                            <span style="color:#999;">No registrado</span>
                        @endif
                    </td>
                
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
                        @if(is_null($record->url_imagen))
                        <img width="1000" height="500" src="{{base_path().'/public/img_formato/record.png'}}"></span></p>
                        @else
                        <img style="width: 1100px;height: 100;" src="{{base_path().'/storage/app/hc_ima/'.$record->url_imagen}}">
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
                @for($j = 0;$j < $row;$j++) <tr>
                    @for($i = 0;$i < $col;$i++) @php $contador++; @endphp <td style="width: 2%;" class="text-center">{{ $contador }}</td>
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
                    <td colspan="12" class="text-center bg-green">SEDO - ANALGESIA</td>
                </tr>
                <tr>
                    <td class="text-center bg-green" colspan="8"><b>SISTEMA</b></td>
                    <td class="text-center bg-green" colspan="6"><b>APARATO</b></td>
                    <td class="bg-green" colspan="3">ASEPSIA CON</td>
                    <td colspan="2" style="width: 10%;">{{$pre_anestesico->asepsia}}</td>
                    <td class="bg-green" colspan="2">HABÓN CON</td>
                    <td colspan="3" style="width: 10%;">{{$pre_anestesico->habon}}</td>
                    <td colspan="12">{{$pre_anestesico->sedo_analgesia}}</td>
                </tr>
                <tr>
                    <td style="width: 5%;">{{$sistemas[0]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $sistemas[0]->id)->first(); @endphp
                    <td class="text-center">@if(!is_null($dato)) X @endif</td>
                    <td colspan="2" style="width: 5%;">{{$sistemas[1]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $sistemas[1]->id)->first(); @endphp
                    <td class="text-center">@if(!is_null($dato)) X @endif</td>
                    <td colspan="2" style="width: 5%;">{{$sistemas[2]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $sistemas[2]->id)->first(); @endphp
                    <td class="text-center">@if(!is_null($dato)) X @endif</td>
                    <td colspan="2" style="width: 5%;">{{$aparatos[0]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $aparatos[0]->id)->first(); @endphp
                    <td class="text-center">@if(!is_null($dato)) X @endif</td>
                    <td colspan="2" style="width: 5%;">{{$aparatos[1]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $aparatos[1]->id)->first(); @endphp
                    <td class="text-center">@if(!is_null($dato)) X @endif</td>
                    <td colspan="3" class="bg-green">LOCAL ASISTIDA</td>
                    <td colspan="2">{{$pre_anestesico->local_asistida}}</td>
                    <td colspan="2" class="bg-green">INTRAVENOSA</td>
                    <td colspan="3">{{$pre_anestesico->intravenosa}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td class="text-center bg-green" colspan="14"><b>MANEJO DE VÍA AÉREA</b></td>
                    <td class="text-center bg-green" colspan="10"><b>TRONCULAR</b></td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-center" style="font-size:5;">{{$manejos[0]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $manejos[0]->id)->first(); @endphp                   
                    <td>@if(!is_null($dato)) X @endif</td>
                    <td colspan="4" class="text-center" style="font-size:5;">{{$manejos[1]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $manejos[1]->id)->first(); @endphp
                    <td>@if(!is_null($dato)) X @endif</td>
                    <td colspan="3" class="text-center" style="font-size:5;">{{$manejos[2]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $manejos[2]->id)->first(); @endphp
                    <td>@if(!is_null($dato)) X @endif</td>
                    <td colspan="3" style="font-size:5;" class="bg-green">BLOQUEO DE NERVIO</td>
                    <td colspan="2">{{$pre_anestesico->bloqueo_nervio}}</td>
                    <td colspan="2" style="font-size:5;" class="bg-green">NÚMERO DE INTENTOS</td>
                    <td colspan="3">{{$pre_anestesico->nervio_intentos}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td class="text-center bg-green" colspan="14"><b>INTUBACIÓN</b></td>
                    <td class="bg-green" colspan="3">BLOQUEO DE PLEXO</td>
                    <td colspan="2">{{$pre_anestesico->bloqueo_plexo}}</td>
                    <td class="bg-green" colspan="2">NÚMERO DE INTENTOS</td>
                    <td colspan="3">{{$pre_anestesico->plexo_intentos}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td style="width: 5%;" class="text-center">{{$intubuaciones[0]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $intubuaciones[0]->id)->first(); @endphp
                    <td style="width: 1;">@if(!is_null($dato)) X @endif</td>
                    <td style="width: 5%;" class="text-center">{{$intubuaciones[1]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $intubuaciones[1]->id)->first(); @endphp
                    @php $rc_tec_mas = $rc_tecnicas->where('id_tecnicas_anestesicas','7')->first(); @endphp
                    <td style="width: 1;">@if(!is_null($dato)) X @elseif(!is_null($rc_tec_mas)) X @endif</td>
                    <td colspan="3" style="width: 5%;" class="text-center">{{$intubuaciones[2]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $intubuaciones[2]->id)->first(); @endphp
                    <td style="width: 1;">@if(!is_null($dato)) X @endif </td>
                    <td colspan="2" style="width: 5%;" class="text-center">{{$intubuaciones[3]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $intubuaciones[3]->id)->first(); @endphp
                    <td style="width: 1;">@if(!is_null($dato)) X @endif</td>
                    <td colspan="2" style="width: 5%;" class="text-center">{{$intubuaciones[4]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $intubuaciones[4]->id)->first(); @endphp
                    <td style="width: 1;">@if(!is_null($dato)) X @endif</td>
                    <td class="bg-green" colspan="3">ANESTÉSICO LOCAL</td>
                    <td colspan="2">{{$pre_anestesico->anestesico_local}}</td>
                    <td class="bg-green" colspan="2">COADYUVANTE</td>
                    <td colspan="3">{{$pre_anestesico->coadyuvante}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td class="text-center bg-green" style="width: 40%;" colspan="14"><b>TIPO DE TUBO</b></td>
                    <td style=" width: 10%;" class="bg-green" colspan="3">TIPO DE AGUJA</td>
                    <td colspan="2">{{$pre_anestesico->tipo_aguja}}</td>
                    <td style=" width: 11%;" class="bg-green" colspan="2">EQUIPO</td>
                    <td colspan="3">{{$pre_anestesico->equipo}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td colspan="2" class="align-left">{{$tipos[0]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $tipos[0]->id)->first(); @endphp
                    @php $rc_tec_tubo = $rc_tecnicas->where('id_tecnicas_anestesicas','13')->first(); @endphp
                    <td>@if(!is_null($dato)) X @elseif(!is_null($rc_tec_tubo)) X @endif</td>
                    <td colspan="3" class="align-left">{{$tipos[1]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $tipos[1]->id)->first(); @endphp
                    <td>@if(!is_null($dato)) X @endif</td>
                    <td colspan="3" class="align-left">{{$tipos[2]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $tipos[2]->id)->first(); @endphp
                    <td>@if(!is_null($dato)) X @endif</td>
                    <td colspan="2" class="align-left">{{$tipos[3]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $tipos[3]->id)->first(); @endphp
                    <td>@if(!is_null($dato)) X @endif</td>
                    <td class="text-center bg-green" colspan="10"><b>NEUROAXIAL</b></td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td class="align-left">{{$tipos[4]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $tipos[4]->id)->first(); @endphp
                    <td class="text-center">@if(!is_null($dato)) X @endif</td>
                    <td class="align-left">DIÁMETRO</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '53')->first(); @endphp
                    <td class="text-center">@if(!is_null($dato)) X @elseif(!is_null($record->tubo)) {{$record->tubo}}@endif</td>
                    <td class="align-left">BALÓN</td>
                    <td class="align-left">SI</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '54')->first(); @endphp
                    <td class="text-center">@if(!is_null($dato)) X @endif</td>
                    <td style="width: 2%;">NO</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '55')->first(); @endphp
                    <td class="text-center">@if(!is_null($dato)) X @endif</td>
                    <td class="align-left">TAPONAMIENTO</td>
                    <td class="align-left">SI</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '56')->first(); @endphp
                    <td class="text-center">@if(!is_null($dato)) X @endif</td>
                    <td class="align-left">NO</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '57')->first(); @endphp
                    <td class="text-center">@if(!is_null($dato)) X @endif</td>

                    <td class="align-left">RAQUIDEA</td>
                    <td style="width: 1;">{{$pre_anestesico->raquidea}}</td>
                    <td colspan="3" class="text-center">EPIDURAL</td>
                    <td>{{$pre_anestesico->epidural}}</td>
                    <td class="align-left">CAUDAL</td>
                    <td>{{$pre_anestesico->caudal}}</td>
                    <td class="align-left">CATETER</td>
                    <td>{{$pre_anestesico->cateter}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td class="text-center bg-green" colspan="14"><b>EQUIPO DE INTUBACION</b></td>
                    <td colspan="2" class="align-left">TIPO DE AGUJA</td>
                    <td>{{$pre_anestesico->tipo_aguja_2}}</td>
                    <td colspan="2" class="align-left">NUMERO DE AGUJA</td>
                    <td>{{$pre_anestesico->numero_aguja}}</td>
                    <td colspan="3" class="align-left">N° DE INTENTOS</td>
                    <td>{{$pre_anestesico->neuroaxial_intentos}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td rowspan="2" colspan="2" class="text-center">CORMACK</td>
                    <td rowspan="2" class="text-center">I</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '58')->first(); @endphp
                    <td rowspan="2" class="text-center" class="text-center">@if(!is_null($dato)) X @endif</td>
                    <td rowspan="2" class="text-center">II</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '59')->first(); @endphp
                    <td rowspan="2" class="text-center">@if(!is_null($dato)) X @endif</td>
                    <td rowspan="2" class="text-center">III</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '60')->first(); @endphp
                    <td rowspan="2" class="text-center">@if(!is_null($dato)) X @endif</td>
                    <td rowspan="2" class="text-center">IV</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '61')->first(); @endphp
                    <td rowspan="2" class="text-center">@if(!is_null($dato)) X @endif</td>
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
                    <td rowspan="2">{{$pre_anestesico->sitio_puncion}}</td>
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
                    <td rowspan="2" colspan="2">{{$pre_anestesico->dermatoma}}</td>
                    <td rowspan="2" colspan="2" class="bg-green">POSICIÓN</td>
                    <td rowspan="2" colspan="4">{{$pre_anestesico->posicion}}</td>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <td class="align-left">{{$inducciones[0]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $inducciones[0]->id)->first(); @endphp
                    <td class="text-center">@if(!is_null($dato)) X @endif</td>
                    <td colspan="2" class="align-left">{{$inducciones[1]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $inducciones[1]->id)->first(); @endphp
                    <td class="text-center">@if(!is_null($dato)) X @endif</td>
                    <td colspan="3" class="align-left">{{$inducciones[2]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $inducciones[2]->id)->first(); @endphp
                    <td class="text-center">@if(!is_null($dato)) X @endif</td>
                    <td class="align-left">{{$mantenimientos[0]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $mantenimientos[0]->id)->first(); @endphp
                    <td>@if(!is_null($dato)) X @endif</td>
                    <td colspan="2" class="align-left">{{$mantenimientos[1]->nombre_new}}</td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $mantenimientos[1]->id)->first(); @endphp
                    <td>@if(!is_null($dato)) X @endif</td>
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
                    <td colspan="3" class="bg-purple"><b>G. ACCESOS VASCULARES</b></td>
                    <td colspan="4" class="bg-purple"><b>H. REPOSICÍON VOLÉMICA</b></td>
                    <td colspan="2" class="bg-purple"><b>I. PÉRDIDAS</b></td>
                </tr>
                <tr>
                    <td class="text-center bg-green">TIPO</td>
                    <td class="text-center bg-green" style="width: 7%;">CALIBRE</td>
                    <td class="text-center bg-green">SITIO</td>
                    <td class="text-center bg-green">DEXTROSA 5%</td>
                    <td>{{$pre_anestesico->dextrosa_5}}</td>
                    <td class="text-center bg-green">SANGRE</td>
                    <td>{{$pre_anestesico->sangre}}</td>
                    <td class="text-center bg-green">SANGRADO</td>
                    <td>{{$pre_anestesico->sangrado}}</td>
                </tr>
                <tr>
                    <td class="text-center">IV PERIFÉRICO 1</td>
                    <td>{{$pre_anestesico->calibre_1}}</td>
                    <td>{{$pre_anestesico->sitio_1}}</td>
                    <td class="text-center bg-green">DEXTROSA 10%</td>
                    <td>{{$pre_anestesico->dextrosa_10}}</td>
                    <td class="text-center bg-green">PLASMA</td>
                    <td>{{$pre_anestesico->plasma}}</td>
                    <td class="text-center bg-green">DIURESIS</td>
                    <td>{{$pre_anestesico->diuresis}}</td>
                </tr>
                <tr>
                    <td class="text-center">IV PERIFÉRICO 2</td>
                    <td>{{$pre_anestesico->calibre_2}}</td>
                    <td>{{$pre_anestesico->sitio_2}}</td>
                    <td class="text-center bg-green">DEXTROSA 50%</td>
                    <td>{{$pre_anestesico->dextrosa_50}}</td>
                    <td class="text-center bg-green">PLAQUETAS</td>
                    <td>{{$pre_anestesico->plaquetas}}</td>
                    <td class="text-center bg-green">OTROS</td>
                    <td>{{$pre_anestesico->otros_perdidas}}</td>
                </tr>
                <tr>
                    <td class="text-center">IV PERIFÉRICO 3</td>
                    <td>{{$pre_anestesico->calibre_3}}</td>
                    <td>{{$pre_anestesico->sitio_3}}</td>
                    <td class="text-center bg-green">DEXTROSA EN SS</td>
                    <td>{{$pre_anestesico->dextrosa_ss}}</td>
                    <td class="text-center bg-green">CRIOPRECIPITADOS</td>
                    <td>{{$pre_anestesico->crioprecipitados}}</td>
                    <td rowspan="3" class="text-center bg-green">TOTAL</td>
                    <td rowspan="3">{{$pre_anestesico->total_perdidas}}</td>
                </tr>
                <tr>
                    <td class="text-center">IV CENTRAL</td>
                    <td>{{$pre_anestesico->calibre_central}}</td>
                    <td>{{$pre_anestesico->sitio_central}}</td>
                    <td class="text-center bg-green">SS 0.9%</td>
                    <td>{{$record->cloruro_sodio}}</td>
                    <td rowspan="2" class="text-center bg-green">OTROS</td>
                    <td rowspan="2">{{$pre_anestesico->otros_reposicion}}</td>
                </tr>
                <tr>
                    <td class="text-center">INTRA ARTERIAL</td>
                    <td>{{$pre_anestesico->calibre_arterial}}</td>
                    <td>{{$pre_anestesico->sitio_arterial}}</td>
                    <td class="text-center bg-green">LACTO RINGER</td>
                    <td>{{$record->lactato_ringer}}</td>
                </tr>
                <tr>
                    <td class="text-center">OTRO</td>
                    <td>{{$pre_anestesico->calibre_otro}}</td>
                    <td>{{$pre_anestesico->sitio_otro}}</td>
                    <td class="text-center bg-green">EXPANSORES</td>
                    <td>{{$pre_anestesico->expansores}}</td>
                    <td class="text-center bg-green">TOTAL</td>
                    <td>{{$record->total}}</td>
                    <td class="text-center bg-green">BALANCE</td>
                    <td>{{$pre_anestesico->balance}}</td>
                </tr>
            </tbody>
        </table>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="9" class="bg-purple"><b>J. DATOS DEL RECIENTE NACIDO</b></td>
                </tr>
                <tr>
                    <td class="text-center bg-green"><b>APGAR</b></td>
                    <td class="text-center">FETO MUERTO</td>
                    <td class="text-center" style="width: 3%;">@if($record->feto == 'feto') X @endif</td>
                    <td class="text-center">1 MINUTO</td>
                    <td class="text-center" style="width: 3%;">@if($record->feto == '1minuto') X @endif</td>
                    <td class="text-center">5 MINUTOS</td>
                    <td class="text-center" style="width: 3%;">@if($record->feto == '5minuto') X @endif</td>
                    <td class="text-center">10 MINUTOS</td>
                    <td class="text-center" style="width: 3%;">@if($record->feto == '10minuto') X @endif</td>
                </tr>
            </tbody>
        </table>
        <table>
            <tr>
                <td class="align-left">
                    <b> SNS-MSP/HCU-form. 018A/2021 </b>
                </td>
                <td class="align-right">
                    <b>TRANSANESTÉSICO (1)</b>
                </td>
            </tr>
        </table>
        <div>
            <p>@for($count=0; $count<=8;$count++) &nbsp; @endfor @for($count=0; $count<=54;$count++) &nbsp; <b> @endfor </b></p>
        </div>
        <table border="1" style="width: 100%" class="page-break-before">
            <tbody>
                <tr>
                    <td colspan="4" class="bg-purple"><b>K. TIEMPOS TRANSCURRIDOS</b></td>
                </tr>
                <tr>
                    <td class="text-center bg-green">DURACION ANESTESIA</td>
                    <td class="text-center">{{$record->duracion_anestesia}}</td>
                    <td class="text-center bg-green">DURACION CIRUGIA</td>
                    <td class="text-center">{{$record->duracion_operacion}}</td>
                </tr>
            </tbody>
        </table>

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="10" class="bg-purple"><b>L. TECNICAS ESPECIALES</b></td>
                </tr>
                <tr>
                    <td class="text-center">HEMODILUCIÓN</td>
                    <td class="text-center" style="width: 3%;">@if($record->new_tecnicas_especiales == 'hemodilucion') X @endif</td>
                    <td class="text-center">AUTOTRANSFUSIÓN</td>
                    <td class="text-center" style="width: 3%;">@if($record->new_tecnicas_especiales == 'autotransfusion') X @endif</td>
                    <td class="text-center">HIPOTENSÍON</td>
                    <td class="text-center" style="width: 3%;">@if($record->new_tecnicas_especiales == 'hipotension') X @endif</td>
                    <td class="text-center">HIPOTERMIA</td>
                    <td class="text-center" style="width: 3%;">@if($record->new_tecnicas_especiales == 'hipotermia') X @endif</td>
                    <td class="text-center">CIRCULACIÓN EXTRACOPÓREA</td>
                    <td class="text-center" style="width: 3%;">@if($record->new_tecnicas_especiales == 'circulacion_extracoperea') X @endif</td>
                </tr>
            </tbody>
        </table>

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="6" class="bg-purple"><b>M. MANTENIMIENTO TEMPERATURA CORPORAL</b></td>
                </tr>
                <tr>
                    <td style="width: 15%;">MANTA TERMICA</td>
                    <td class="text-center" style="width: 3%;">@if($record->mantenimiento_temperatura == '1') X @endif</td>
                    <td style="width: 15%;">CALENTAMIENTO DE FLUIDOS</td>
                    <td class="text-center" style="width: 3%;">@if($record->mantenimiento_temperatura == '2') X @endif</td>
                    <td class="text-center" style="width: 3%;">OTROS</td>
                    <td style="width: 30%;">{{$pre_anestesico->otros_mantenimiento_temperatura}}</td>
                </tr>
            </tbody>
        </table>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="8" class="bg-purple"><b>N. INCIDENTES</b></td>
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
                    <td colspan="5">{{$pre_anestesico->otros_indice}}</td>
                </tr>
            </tbody>
        </table>
        <!--table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="4" class="bg-purple"><b> O. RESULTADO DE EXÁMENES DE LABORATORIO</b></td>
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
        </table-->

        <p class="bg-purple"><b> O. RESULTADO DE EXÁMENES DE LABORATORIO</b></p>

        @php

        $isAgrupadosValid = isset($agrupados1) && is_array($agrupados1) && !empty($agrupados1);
        if ($isAgrupadosValid) {
        $agrupadores = array_keys($agrupados1);
        $maxFilas = !empty($agrupados1) ? max(array_map('count', $agrupados1)) : 0;
        $maxFilas = ($maxFilas / 2) + 1;
        }
        $isAgrupados2Valid = isset($agrupados2) && is_array($agrupados2) && !empty($agrupados2);
        if ($isAgrupados2Valid) {
        $agrupadores2 = array_keys($agrupados2);
        $maxFilas2 = !empty($agrupados2) ? max(array_map('count', $agrupados2)) : 0;
        $maxFilas2 = ($maxFilas2 / 2) + 1;

        }

        @endphp

        @if($isAgrupadosValid)
        <!--table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    @foreach ($agrupadores as $agrupador)
                    @if($agrupador == 'HEMATOLOGIA')
                    <th class="text-center bg-green" colspan="2">{{$agrupador}}</th>
                    <th class="text-center bg-green" colspan="2">{{$agrupador}}</th>
                    @else
                    <th class="text-center bg-green" colspan="2">{{$agrupador}}</th>
                    @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < $maxFilas; $i++)
                    <tr>
                    @foreach ($agrupadores as $agrupador)
                    @if($agrupador == 'HEMATOLOGIA')
                    @if(isset($agrupados1[$agrupador][2*$i]))
                    <td class="text-center bg-green">{{$agrupados1[$agrupador][2*$i]['parametro']}}</td>
                    <td>{{$agrupados1[$agrupador][2*$i]['resultado']}}</td>
                    @else
                    <td></td>
                    <td></td>
                    @endif

                    @if(isset($agrupados1[$agrupador][2*$i+1]))
                    <td class="text-center bg-green">{{$agrupados1[$agrupador][2*$i+1]['parametro']}}</td>
                    <td>{{ $agrupados1[$agrupador][2*$i+1]['resultado']}}</td>
                    @else
                    <td></td>
                    <td></td>
                    @endif
                    @else
                    @if(isset($agrupados1[$agrupador][$i]))
                    <td class="text-center bg-green">{{$agrupados1[$agrupador][$i]['parametro']}}</td>
                    <td>{{ $agrupados1[$agrupador][$i]['resultado']}}</td>
                    @else
                    <td></td>
                    <td></td>
                    @endif
                    @endif
                    @endforeach
                    </tr>
                    @endfor
            </tbody>
        </table-->
        @endif

        @if($isAgrupados2Valid)
        <!--table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    @foreach ($agrupadores2 as $agrupador2)
                    @if($agrupador2 == 'HEMATOLOGIA')
                    <th class="text-center bg-green" colspan="2">{{$agrupador2}}</th>
                    <th class="text-center bg-green" colspan="2">{{$agrupador2}}</th>
                    @else
                    <th class="text-center bg-green" colspan="2">{{$agrupador2}}</th>
                    @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < $maxFilas2; $i++)
                    <tr>
                    @foreach ($agrupadores2 as $agrupador2)
                    @if($agrupador2 == 'HEMATOLOGIA')
                    @if(isset($agrupados2[$agrupador2][2*$i]))
                    <td class="text-center bg-green">{{$agrupados2[$agrupador2][2*$i]['parametro']}}</td>
                    <td>{{$agrupados2[$agrupador2][2*$i]['resultado']}}</td>
                    @else
                    <td></td>
                    <td></td>
                    @endif

                    @if(isset($agrupados2[$agrupador2][2*$i+1]))
                    <td class="text-center bg-green">{{$agrupados2[$agrupador2][2*$i+1]['parametro']}}</td>
                    <td>{{ $agrupados2[$agrupador2][2*$i+1]['resultado']}}</td>
                    @else
                    <td></td>
                    <td></td>
                    @endif
                    @else
                    @if(isset($agrupados2[$agrupador2][$i]))
                    <td class="text-center bg-green">{{$agrupados2[$agrupador2][$i]['parametro']}}</td>
                    <td>{{ $agrupados2[$agrupador2][$i]['resultado']}}</td>
                    @else
                    <td></td>
                    <td></td>
                    @endif
                    @endif
                    @endforeach
                    </tr>
                    @endfor
            </tbody>
        </table-->
        @endif

        <!--p class="page-break-before"-->

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td class="bg-purple"><b>P. OBSERVACIONES</b></td>
                </tr>
                <tr>
                    <td style="height: 30px;">@if(!is_null($pre_anestesico->observaciones)){{$pre_anestesico->observaciones}} @else PACIENTE PASA A SALA DE RECUPERACIÓN EN CONDICIONES ESTABLES @endif</td>
                </tr>
            </tbody>
        </table>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="12" class="bg-purple"><b>Q. CONDICIONES DE EGRESO</b></td>
                </tr>
                <tr>
                    <td class="text-center" style="width: 18%;" rowspan="2">CONDICIONES AL SALIR</td>
                    <td class="text-center">EXTUBADO</td>
                    <td class="text-center" style="width: 5%;">@if($record->condiciones_al_salir == '1') X @endif</td>
                    <td class="text-center" rowspan="2">CONDUCIDO A</td>
                    <td class="text-center" rowspan="2">UNIDAD DE CUIDADOS POST ANESTÉSICO</td>
                    <td class="text-center" rowspan="2">@if($record->conducido == '5') X @else X @endif</td>
                    <td class="text-center" rowspan="2">UNIDAD DE CUIDADOS INTENSIVOS</td>
                    <td class="text-center" rowspan="2">@if($record->conducido == '1') X @endif</td>
                    <td class="text-center" rowspan="2">CRÍTICOS DE EMERGENCIA</td>
                    <td class="text-center" rowspan="2">@if($record->conducido == '2') X @endif</td>
                    <td class="text-center" rowspan="2">MORGUE</td>
                    <td class="text-center" rowspan="2">@if($record->conducido == '4') X @endif</td>
                </tr>
                <tr>
                    <td class="text-center">INTUBADO</td>
                    <td>@if($record->condiciones_al_salir == '2') X @endif</td>
                </tr>
                @php
                    $fecha = $record->fecha;
                    $hora = "";
                    $csv_record = Sis_medico\hc_anestesiologia_csv::where('id_hc_anestesiologia',$record->id)->first();
                    if(!is_null($csv_record)){
                        $hora = $csv_record->hora;
                    }
                    //$hora = date("H:i:s", strtotime($fecha));
                @endphp
                <tr>
                    <td>CONSTANTES VITALES DE ENTREGA</td>
                    <td> <b>TA</b></td>
                    <td class="text-center">@if($pre_anestesico->presion != '0' ) {{$pre_anestesico->presion}} @else {{$csv_record->presion_arterial}} @endif</td>
                    <td> <b>FC</b></td>
                    <td class="text-center">@if($pre_anestesico->pulso != '0' ){{$pre_anestesico->pulso}} @else  {{$csv_record->pulso}} @endif</td>
                    <td> <b>FR</b></td>
                    <td class="text-center">@if($pre_anestesico->fecuencia_respiratoria != '0' ) {{$pre_anestesico->fecuencia_respiratoria}} @else  {{$csv_record->respiracion}} @endif</td>
                    <td colspan="2"> <b>SAT.O2</b></td>
                    <td class="text-center"> @if($pre_anestesico->o2 != '0' ) {{$pre_anestesico->o2}} @else  {{$csv_record->o2}} @endif</td>
                    <td> <b>T°</b></td>
                    <td class="text-center">@if($pre_anestesico->temperatura != '0' ){{$pre_anestesico->temperatura}} @else {{$csv_record->temperatura}} @endif</td>
                </tr>
            </tbody>
        </table>

        @php
        $fecha_new = substr($pre_anestesico->anestesiologia->fecha,0,10);
        $hora_new = substr($pre_anestesico->anestesiologia->fecha,10);
        $user_espe = Sis_medico\User_espe::where('usuid',$record->id_anestesiologo)->first();
        $especialidad = null;
        if(!is_null($user_espe)){
        $especialidad = Sis_medico\Especialidad::find($user_espe->espid);
        }
        $record = $pre_anestesico->anestesiologia;
        $firma = null;
        
        if(!is_null($aud_protocolo)){
            $firma = Sis_medico\Firma_Usuario::where('id_usuario', $aud_protocolo->id_anestesiologo)->first();
        }
        
        if($record->id_anestesiologo != null && $firma == null){
            $firma = Sis_medico\Firma_Usuario::where('id_usuario', $record->id_anestesiologo)->first();
        }

        @endphp
        

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="8" class="bg-purple"><b>R. DATOS DEL PROFESIONAL RESPONSABLE</b></td>
                </tr>
                <tr>
                    <td class="text-center bg-green" style="height: 45px; width: 6%;">HORA</td>
                    <td class="text-center" style="width: 8%;">{{$record->hora}}</td>
                    <td class="text-center bg-green" style="width: 15%;">NOMBRE Y APELLIDO DEL PROFESIONAL</td>
                    <td class="text-center" style="width: 25%;">{{$nombre2}}</td>
                    <td class="text-center bg-green" style="width: 6%;">FIRMA</td>
                    <td class="text-center">
                        @if(!is_null($firma))
                        <img width=130 height=50 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" style="" align=center hspace=12><br>
                    </td>
                    @endif </td>
                    <td class="text-center bg-green" style="width: 8%;">SELLO Y CÓDIGO</td>
                    <td class="text-center">
                        @if(!is_null($firma))
                        @if(!is_null($firma->doctor)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @if($firma->doctor->nombre2!='N/A'){{$firma->doctor->nombre2}}@endif @endif<br>
                        @if(!is_null($especialidad)) {{$especialidad->nombre}} @endif<br>
                        @if(!is_null($firma->doctor)) Cedula No. {{$firma->doctor->id}} @endif
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        <table>
            <tr>
                <td class="align-left">
                    <b> SNS-MSP/HCU-form. 018A/2021 </b>
                </td>
                <td class="align-right">
                    <b>TRANSANESTÉSICO (2)</b>
                </td>
            </tr>
        </table>