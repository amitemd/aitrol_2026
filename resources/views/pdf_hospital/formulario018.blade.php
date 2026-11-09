<!DOCTYPE html>
<html>

@php
$fecha=date('Y-m-d');
$hora = date('H:i:s');
$logoPath = public_path('assets/images/logo_certificado_borja.png');
$logoSrc = is_file($logoPath) && is_readable($logoPath)
    ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
    : '';
@endphp


<?php

$fechaNacimiento = new DateTime($pre_anestesico->anestesiologia->historia->paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($pre_anestesico->anestesiologia->historia->paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($pre_anestesico->anestesiologia->historia->paciente->fecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');

?>




<head>
    <meta charset="utf-8">
    <title>Formulario018</title>
    <style>
        @page {
            margin: 15 0;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin-top: 40px;
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



        ol {
            list-style-type: decimal;
            margin-left: 20px;
        }
    </style>
</head>

<body>
    <footer><span>SNS-MSP / HCU-form.018 / 2021</span><span class="document-name">PRE ANESTÉSICO (<span class="page-number"></span>)</span></footer>
    <header>

    </header>
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
        <br>

        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="14" style="font-size: 14px;" class="bg-blue"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="bg-green text-center" style="width: 20%;"><b>INSTITUCION DEL SISTEMA</b></td>
                    <td colspan="2" class="bg-green text-center"><b>UNICODIGO</b></td>
                    <td colspan="2" class="bg-green text-center" style="width: 22%;"><b>ESTABLECIMIENTO DE SALUD</b></td>
                    <td colspan="4" class="bg-green text-center"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                    <td colspan="5" class="bg-green text-center"><b>NÚMERO DE ARCHIVO</b></td>
                </tr>
                <tr>
                    <td class="text-center" style="height: 20; font-size: 12px;">{{$pre_anestesico->anestesiologia->historia->seguro->nombre}}</td>
                    <td colspan="2" style="font-size: 12px;" class="text-center">{{$pre_anestesico->anestesiologia->historia->agenda->empresa->unicodigo}}</td>
                    <td colspan="2" class="text-center" style="font-size: 12px;">{{$pre_anestesico->anestesiologia->historia->agenda->empresa->nombrecomercial}}</td>
                    <td class="text-center" colspan="4" style="font-size: 12px;">{{$pre_anestesico->anestesiologia->historia->paciente->id}}</td>
                    <td colspan="5"></td>
                </tr>
                @php 
                    $record = $pre_anestesico->anestesiologia;
                    $firma = null;
                    if($record->id_anestesiologo != null){
                        $firma  = Sis_medico\Firma_Usuario::where('id_usuario', $record->id_anestesiologo)->first();
                    }
                     
                @endphp
                <tr>
                    <td rowspan="2" colspan="2" class="bg-green text-center" style="height: 44px;"><b>PRIMER APELLIDO</b></td>
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
                    <td class="text-center" style="height: 25px;" colspan="2">{{$pre_anestesico->anestesiologia->historia->paciente->apellido1}}</td>
                    <td class="text-center" colspan="2">{{$pre_anestesico->anestesiologia->historia->paciente->apellido2}}</td>
                    <td class="text-center" colspan="2">{{$pre_anestesico->anestesiologia->historia->paciente->nombre1}}</td>
                    <td class="text-center" colspan="1">{{$pre_anestesico->anestesiologia->historia->paciente->nombre2}}</td>
                    <td class="text-center" colspan="1">@if($pre_anestesico->anestesiologia->historia->paciente->sexo == 1) Masculino @else Femenino @endif</td>
                    <td class="text-center" colspan="2">{{$edad}}</td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1">X</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="14" style="font-size: 14px;" class="bg-purple"><b>B. REGISTRO DE VALORACIÓN PREANESTÉSIA</b></td>
                </tr>
                <tr>
                    <td colspan="10" style="width: 85%; height: 35px;" class="text-center"><b>DIAGNOSTICOS</b></td>
                    <td colspan="4" class="text-center"><b> CIE</b></td>
                </tr>
                @foreach ($pre_anestesico->anestesiologia->historia->diagnosticos as $key=>$pre)
                @php
                $c3 = Sis_medico\Cie_10_3::find($pre->cie10);
                $c4 = Sis_medico\Cie_10_4::find($pre->cie10);
                $texto = '';
                if(!is_null($c3)){
                $texto = $c3->descripcion;
                }
                if(!is_null($c4)){
                $texto = $c4->descripcion;
                }
                @endphp
                <tr>
                    <td class="text-center" style="width: 1%;">{{($key) + 1}}</td>
                    <td colspan="9">{{ $texto }}</td>
                    <td colspan="4" class="text-center">
                        {{ $pre->cie10 }}
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3" style="width: 15%; height: 35px;" class="text-center"><b>PROCEDIMIENTOS/S PROPUESTO/S</b></td>
                    <td colspan="11">{{$texto}}</td>
                </tr>


                <tr>
                    <td class="text-center" style="font-size: 7; width: 5%; height: 17px;"><b>ELECTIVA</b></td>
                    <td class="text-center">@if($pre_anestesico->tipo == 'ELECTIVA') X @endif</td>
                    <td class="text-center" style="font-size: 7; width: 5%;" colspan="2"><b>EMERGENCIA</b></td>
                    <td class="text-center" style="width: 1;">@if($pre_anestesico->tipo == 'EMERGENCIA') X @endif</td>
                    <td class="text-center" style="font-size: 7;"><b>URGENCIA</b></td>
                    <td class="text-center">@if($pre_anestesico->tipo == 'URGENCIA') X @endif</td>
                    <td class="text-center" style="font-size: 7; width: 15%;"><b>RIESGO QUIRURGICO</b></td>
                    <td class="text-center" style="font-size: 7;"><b>BAJO</b></td>
                    <td class="text-center" style="font-size: 7; width: 15%;">@if($pre_anestesico->riesgo_quirurgico == 'BAJO') X @endif</td>
                    <td class="text-center" style="font-size: 7;"><b>MODERADO</b></td>
                    <td class="text-center" style="font-size: 7;">@if($pre_anestesico->riesgo_quirurgico == 'MODERADO') X @endif</td>
                    <td class="text-center" style="font-size: 7;"><b>ALTO</b></td>
                    <td class="text-center" style="font-size: 7;">@if($pre_anestesico->riesgo_quirurgico == 'ALTO') X @endif</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="5" style="font-size: 14px;" class="bg-purple"><b>C. ANAMNESIS</b></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-center bg-green"><b>ANTECEDENTES PATOLÓGICOS PERSONALES</b></td>
                </tr>
                <tr>
                    <td class="bg-green" style="width: 1;"></td>
                    <td class="text-center bg-green" colspan="2" style="width: 35%;"><b>DIAGNÓSTICOS</b></td>
                    <td class="text-center bg-green" style="width: 30%;"><b>TIEMPO DE EVOLUCION</b></td>
                    <td class="text-center bg-green"><b>TRATAMIENTO</b></td>
                </tr>
                @php
                $contador = 1;
                @endphp

                @foreach ($rc_antecedentes as $rc)
                <tr>
                    <td style="height: 15px;" class="text-center">{{ $contador++ }}</td>
                    <td style="height: 15px;" class="text-center" colspan="2">{{ $rc->diagnostico }}</td>
                    <td style="height: 15px;" class="text-center">{{$rc->tiempo_evolucion}} </td>
                    <td style="height: 15px;" class="text-center">{{$rc->tratamiento}}</td>
                </tr>
                @endforeach


                <tr>
                    <td colspan="2" style="height: 35px;" class="text-center bg-green">ANESTÉSICO</td>
                    <td colspan="3">&nbsp;&nbsp; {{$pre_anestesico->anestesico}}</td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 35px;" class="text-center bg-green">QUIRÚRGICOS</td>
                    <td colspan="3">&nbsp;&nbsp; {{$pre_anestesico->quirurgicos}}</td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 35px;" class="text-center bg-green">ALÉRGICOS</td>
                    <td colspan="3">&nbsp;&nbsp; {{$pre_anestesico->alergicos}}</td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 35px;" class="text-center bg-green">TRANSFUSIONES</td>
                    <td colspan="3">&nbsp;&nbsp; {{$pre_anestesico->transfuciones}}</td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 35px;" class="text-center bg-green">HÁBITOS</td>
                    <td colspan="3">&nbsp;&nbsp; {{$pre_anestesico->habitos}}</td>
                </tr>
                <tr>
                    <td colspan="5" class="bg-green text-center" style="height: 20px;"><b>ANTECEDENTES PATOLÓGICOS FAMILIARES</b></td>
                </tr>
                <tr>
                    <td colspan="5">{{$pre_anestesico->anestesiologia->historia->paciente->antecedentes_fam}}</td>
                </tr>

            </tbody>
        </table>
        <p class="page-break-before">

        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="30" style="font-size: 12px;" class="bg-purple"><b>D. EXAMEN FÍSICO</b></td>
                </tr>
                <tr>@php $historia = $pre_anestesico->anestesiologia->historia; @endphp
                    <td style="width: 16%;" class="text-center bg-green">CONTANTES VITALES</td>
                    <td class="text-center bg-green" style="width: 5%;">TA</td>
                    <td colspan="3" style="width: 8%;">{{ $historia->presion }}</td>
                    <td class="text-center bg-green" style="width: 5%;">FC</td>
                    <td colspan="3" style="width: 8%;">{{ $historia->pulso }}</td>
                    <td colspan="2" class="text-center bg-green" style="width: 5%;">FR</td>
                    <td colspan="3" style="width: 8%;"></td>
                    <td class="text-center bg-green">T°</td>
                    <td colspan="3" style="width: 8%;">{{ $historia->temperatura }}</td>
                    <td colspan="3" class="text-center bg-green" style="width: 6%;">SAT02</td>
                    <td colspan="4" style="width: 5%;">{{ $historia->o2 }}</td>
                    <td colspan="3" class="text-center bg-green" style="width: 8%;">GLASGOW</td>
                    <td colspan="2"></td>
                </tr>
                @php  
                    if($historia->altura > 0){
                        $imc = $historia->peso / ($historia->altura/100 * $historia->altura/100);
                        $imc = round( $imc, 2  );
                    }else{
                        $imc = 0;
                    }     
                @endphp
                <tr>
                    <td class="text-center bg-green">ANTROPOMETRÍA</td>
                    <td colspan="2" class="text-center bg-green" style="width: 7%;">PESO(KG)</td>
                    <td colspan="6">{{ $historia->peso }}</td>
                    <td colspan="3" class="text-center bg-green" style="width: 7%;">TALLA(cm)</td>
                    <td colspan="8">{{ $historia->altura }}</td>
                    <td colspan="3" class="text-center bg-green" style="width: 9%;">IMC(kg/m2)</td>
                    <td colspan="7">{{ $imc }}</td>
                </tr>
                <tr>
                    <td rowspan="6" class="text-center bg-green">VÍA AÉREA</td>
                    <td colspan="9" class="text-center bg-green">APERTURA BUCAL(cm)</td>
                    <td colspan="7" class="text-center bg-green" style="font-size: 7;">DISTANCIA TIROMENTONEANA(cm)</td>
                    <td colspan="13" class="text-center bg-green">MALLAMPATI</td>
                </tr>
                <tr>
                    <td class="text-center">&lt;2</td>
                    <td class="text-center">@if($pre_anestesico->apertura_bucal == '<2') X @endif</td>
                    <td style="width: 4%;" class="text-center">2-2,5</td>
                    <td class="text-center">@if($pre_anestesico->apertura_bucal == '2-2,5') X @endif</td>
                    <td colspan="2" class="text-center">2.6-3</td>
                    <td class="text-center">@if($pre_anestesico->apertura_bucal == '>3') X @endif</td>
                    <td colspan="2" class="text-center">&gt;3</td>
                    <td colspan="2" class="text-center">&lt;6</td>
                    <td class="text-center">@if($pre_anestesico->distancia_tiromentoneana == '<6') X @endif</td>
                    <td colspan="2" class="text-center">6 - 6.5</td>
                    <td class="text-center">@if($pre_anestesico->distancia_tiromentoneana == '6 - 6.5') X @endif</td>
                    <td style="width: 4%;" class="text-center">&gt;6.5</td>
                    <td colspan="2" class="text-center">@if($pre_anestesico->distancia_tiromentoneana == '>6.5') X @endif</td>
                    <td colspan="2" class="text-center">I</td>
                    <td class="text-center">@if($pre_anestesico->mallanpati == 'I') X @endif</td>
                    <td class="text-center" colspan="2">II</td>
                    <td class="text-center">@if($pre_anestesico->mallanpati == 'II') X @endif</td>
                    <td class="text-center">III</td>
                    <td class="text-center">@if($pre_anestesico->mallanpati == 'III') X @endif</td>
                    <td class="text-center" colspan="2">IV</td>
                    <td class="text-center">@if($pre_anestesico->mallanpati == 'IV') X @endif</td>
                </tr>
                <tr>
                    <td colspan="7" class="text-center bg-green" style="font-size: 7;">PROTRUSIÓN MANDIBULAR</td>
                    <td colspan="5" class="text-center bg-green" style="font-size: 6;">PERIMETRO CERVICAL(cm)</td>
                    <td colspan="6" class="text-center bg-green" style="font-size: 6;">MOVILIDAD CERVICAL(°)</td>
                    <td colspan="6" class="text-center bg-green" style="font-size: 6;">HISTORIA DE INTUBACIÓN DIFICIL</td>
                    <td colspan="5" class="text-center bg-green" style="font-size: 5;"><b>PATOLOGÍA ASOCIADA A INTUBACIÓN DIFICIL</b></td>
                </tr>
                <tr>
                    <td class="text-center">&lt;0</td>
                    <td class="text-center">@if($pre_anestesico->protusion_mandibular == '<0') X @endif</td>
                    <td class="text-center">0</td>
                    <td class="text-center">@if($pre_anestesico->protusion_mandibular == '0') X @endif</td>
                    <td colspan="2" class="text-center">&gt;0</td>
                    <td class="text-center">@if($pre_anestesico->protusion_mandibular == '>0') X @endif</td>
                    <td style="width: 4%;" class="text-center">&lt;40</td>
                    <td class="text-center">@if($pre_anestesico->perimetro_cervical == '<40') X @endif</td>
                    <td colspan="2" class="text-center">&gt;40</td>
                    <td class="text-center">@if($pre_anestesico->perimetro_cervical == '>40') X @endif</td>
                    <td colspan="2" class="text-center">&lt;35</td>
                    <td class="text-center">@if($pre_anestesico->movilidad_cervical == '<35') X @endif</td>
                    <td class="text-center">&gt;35</td>
                    <td colspan="2" class="text-center">@if($pre_anestesico->movilidad_cervical == '>35') X @endif</td>
                    <td colspan="2" class="text-center">SI</td>
                    <td class="text-center">@if($pre_anestesico->intubacion_dificil == 'SI') X @endif</td>
                    <td class="text-center">NO</td>
                    <td colspan="2" class="text-center">@if($pre_anestesico->intubacion_dificil == 'NO') X @endif</td>
                    <td class="text-center">SI</td>
                    <td class="text-center">@if($pre_anestesico->patologia == 'SI') X @endif</td>
                    <td colspan="2" class="text-center">NO</td>
                    <td class="text-center">@if($pre_anestesico->patologia == 'NO') X @endif</td>
                </tr>
                <tr>
                    <td rowspan="2" colspan="3" class="text-center bg-green" style="height: 30px;">OTROS</td>
                    <td colspan="26"></td>
                </tr>
                <tr>
                    <td colspan="26"></td>
                </tr>
                <tr>
                    <td colspan="4" class="bg-green">TÓRAX</td>
                    <td colspan="26">&nbsp; {{$pre_anestesico->torax}}</td>
                </tr>
                <tr>
                    <td colspan="4" class="bg-green">CORAZÓN</td>
                    <td colspan="26">&nbsp; {{$pre_anestesico->corazon}}</td>
                </tr>
                <tr>
                    <td colspan="4" class="bg-green">PULMONES</td>
                    <td colspan="26">&nbsp; {{$pre_anestesico->pulmones}}</td>
                </tr>
                <tr>
                    <td colspan="4" class="bg-green">ABDÓMEN</td>
                    <td colspan="26">&nbsp; {{$pre_anestesico->abdomen}}</td>
                </tr>
                <tr>
                    <td colspan="4" class="bg-green">EXTREMIDADES</td>
                    <td colspan="26">&nbsp; {{$pre_anestesico->extremidades}}</td>
                </tr>
                <tr>
                    <td colspan="4" class="bg-green">SISTEMA NERVIOSO CENTRAL</td>
                    <td colspan="26">&nbsp; {{$pre_anestesico->sistema_nervisoso}}</td>
                </tr>
                <tr>
                    <td colspan="4" class="bg-green">EQUIVALENTE METABÓLICO (METS)</td>
                    <td colspan="26">&nbsp; {{$pre_anestesico->equivalente_metabolico}}</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="4" style="font-size: 12px;" class="bg-purple"><b>E. RESULTADOS DE ÉXAMENES DE LABORATORIO, GABINETE E IMAGEN</b></td>
                </tr>
                <tr>
                    <td class="text-center bg-green"  colspan="2">EXAMEN</td>
                    <td class="text-center bg-green" >AGRUPADOR</td>
                    <td class="text-center bg-green" >RESULTADO</td>
                </tr>

                @php
                $contador = 1;
                @endphp
                @foreach ($rc_examen as $exa)

                <tr>
                    <td class="text-center bg-green" style="width: 1%;">{{ $contador++ }}</td>
                    <td style="height: 18px;" class="text-center">{{$exa->nombre}}</td>
                    <td style="height: 18px;" class="text-center">@if(isset($exa->examen)) {{$exa->examen->agrupador_018->nombre}} @endif</td>
                    <td> &nbsp; &nbsp;{{ $exa->resultado }}</td>
                </tr>
                @endforeach
               
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="15" style="font-size: 12px;" class="bg-purple"><b>F. ESCALA E ÍNDICES</b></td>
                </tr>
                <tr>
                    <td style="width: 15%;" class="bg-green">ESTADO FISICO ASA</td>
                    <td style="width: 3%;" class="text-center bg-green">I</td>
                    <td style="width: 3%;" class="text-center">@if($pre_anestesico->estado_fisico == 'I') X @endif</td>
                    <td style="width: 3%;" class="text-center bg-green">II</td>
                    <td style="width: 3%;" class="text-center">@if($pre_anestesico->estado_fisico == 'II') X @endif</td>
                    <td style="width: 3%;" class="text-center bg-green">III</td>
                    <td style="width: 3%;" class="text-center">@if($pre_anestesico->estado_fisico == 'III') X @endif</td>
                    <td style="width: 3%;" class="text-center bg-green">IV</td>
                    <td style="width: 3%;" class="text-center">@if($pre_anestesico->estado_fisico == 'IV') X @endif</td>
                    <td style="width: 3%;" class="text-center bg-green">V</td>
                    <td style="width: 3%;" class="text-center">@if($pre_anestesico->estado_fisico == 'V') X @endif</td>
                    <td style="width: 3%;" class="text-center bg-green">VI</td>
                    <td style="width: 3%;" class="text-center">@if($pre_anestesico->estado_fisico == 'VI') X @endif</td>
                    <td style="width: 18%;" class="bg-green">RIESGO CARDÍACO</td>
                    <td>&nbsp; {{$pre_anestesico->riesgo_cardiaco}}</td>
                </tr>
                <tr>
                    <td class="bg-green">RIESGO PULMONAR</td>
                    <td colspan="12">&nbsp; {{$pre_anestesico->riesgo_pulmonar}}</td>
                    <td class="bg-green">RIESGO TROMBOEMBÓLICO</td>
                    <td>&nbsp; {{$pre_anestesico->riesgo_tromboembolico}}</td>
                </tr>
                <tr>
                    <td class="bg-green">OTROS</td>
                    <td colspan="14"></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="4" style="font-size: 12px;" class="bg-purple"><b>G. TIEMPO DE ULTIMA INGESTA</b></td>
                </tr>
                <tr>
                    <td class="bg-green text-center" style="width: 15%;">LIQUIDOS CLAROS</td>
                    <td>&nbsp; {{$pre_anestesico->liquidos_claros}}</td>
                    <td class="bg-green text-center" style="width: 15%;">LECHE MATERNA</td>
                    <td>&nbsp; {{$pre_anestesico->leche_materna}}</td>
                </tr>
                <tr>
                    <td class="bg-green text-center">LECHE MATERNA</td>
                    <td>&nbsp; {{$pre_anestesico->leche_materna}}</td>
                    <td class="bg-green text-center">SOLIDOS</td>
                    <td>&nbsp; {{$pre_anestesico->solidos}}</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="2" style="font-size: 12px;" class="bg-purple"><b>H. INDICACIONES</b></td>
                </tr>
                @php
                $contador = 1;
                @endphp

                @foreach ($indicaciones as $indicacione)
                <tr>
                    <td class="text-center bg-green" style="width: 1%;">{{ $contador++ }}</td>
                    <td style="height: 18px;">&nbsp; {{ $indicacione->indicaciones }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td style="font-size: 12px;" class="bg-purple"><b>I. PLAN ANESTÉSICO</b></td>
                </tr>
                <tr>
                    <td style="height: 40px;">&nbsp; {{$pre_anestesico->plan_anestesico}}</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td style="font-size: 12px;" class="bg-purple"><b>J. OBSERVACIONES</b></td>
                </tr>
                <tr>
                    <td style="height: 40px;">&nbsp; {{$pre_anestesico->observaciones}}</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td style="font-size: 12px;" colspan="7" class="bg-purple"><b>K DATOS DEL PROFESIONAL RESPONSABLE</b></td>
                </tr>
                <tr>
                    <td style="width: 8%; font-size: 6;" class="text-center bg-green"><b>FECHA</b> <br>(aaa-mm-dd) </br></td>
                    <td style="width: 8%; font-size: 6;" class="text-center bg-green"><b>HORA</b> <br> (hh-mm) </br></td>
                    <td class="text-center bg-green" style="width: 25%;"><b>PRIMER NOMBRE</b></td>
                    <td class="text-center bg-green" style="width: 25%;" colspan="3"><b>PRIMER APELLIDO</b></td>
                    <td class="text-center bg-green" style="width: 25%;"><b>SEGUNDO APELLIDO</b></td>
                </tr>
                @php 
                    $fecha_new = substr($pre_anestesico->anestesiologia->fecha,0,10); 
                    $hora_new  = substr($pre_anestesico->anestesiologia->fecha,10);
                @endphp
                <tr>
                    <td class="text-center" style="height: 18px;">{{$fecha_new}}</td>
                    <td class="text-center">{{$hora_new}}</td>
                    <td class="text-center">@if(!is_null($firma)) {{ $firma->doctor->nombre1 }} @endif</td>
                    <td class="text-center" colspan="3">@if(!is_null($firma)) {{ $firma->doctor->apellido1 }} @endif</td>
                    <td class="text-center">@if(!is_null($firma)) {{ $firma->doctor->apellido2 }} @endif</td>
                </tr>
                <tr>
                    <td style="font-size: 6;" colspan="2" class="text-center bg-green"><b>NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
                    <td style="font-size: 6;" colspan="2" class="text-center bg-green"><b>FIRMA</b></td>
                    <td style="font-size: 6;" colspan="3" class="text-center bg-green"><b>SELLO</b></td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 12px; height: 60px;" class="text-center">@if(!is_null($firma)) {{ $firma->doctor->id }} @endif</td>
                    <td colspan="2">
                    @if(!is_null($firma))
                        <img width=170 height=80 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center hspace=12><br>
                    </td>
                    @endif </td>
                    <td colspan="3" class="td-border text-center" style="width:25%; height:60px">
                    @if(!is_null($firma))    
                        @if(!is_null($firma->doctor)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @if($firma->doctor->nombre2!='N/A'){{$firma->doctor->nombre2}}@endif @endif<br>
                        @if(!is_null($firma->doctor)) Cedula No. {{$firma->doctor->id}} @endif
                    @endif    
                    </td>

                </tr>
            </tbody>
        </table>
