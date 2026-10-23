<!DOCTYPE html>
<html>
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
    <title>INFORME 007</title>
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
    </style>
</head>
<body>
    <header>

    </header>
    <main>

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
                    <td class="text-center" colspan="2">IESS</td>
                    <td class="text-center" colspan="2">026247</td>
                    <td class="text-center" colspan="2">{{$empresa->nombre_corto}}</td>
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
                    <td class="bg-green text-center">D</td>
                    <td class="bg-green text-center">H</td>
                    <td class="bg-green text-center">M</td>
                    <td class="bg-green text-center">A</td>
                </tr>
                <tr>
                <td class="text-center" class="text-center" style="height: 15px;" colspan="2">{{$paciente->apellido1}}</td>
                    <td class="text-center" colspan="1">{{$paciente->apellido2}}</td>
                    <td class="text-center" colspan="2">{{$paciente->nombre1}}</td>
                    <td class="text-center" colspan="1">{{$paciente->nombre2}}</td>
                    <td class="text-center" colspan="1">@if($paciente->sexo == 1) Masculino @endif @if($paciente->sexo == 2) Femenino @endif</td>
                    <td class="text-center" colspan="1">{{$edad}}</td>
                    <td class="text-center" colspan="1">{{$diaNacimiento}}</td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1">{{$mesNacimiento}}</td>
                    <td class="text-center" colspan="1">{{ date('Y', strtotime($paciente->fecha_nacimiento))}}</td>
                </tr>
            </tbody>
        </table>
        <br>

        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 14px;" class="bg-blue"><b>B. CUADRO CLÍNICO DE INTERCONSULTA</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border" style="width:14%; height: 40px;">{{strip_tags($interconsulta->evolucion)}}</td>


                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 14px;" class="bg-blue"><b>C. RESUMEN DEL CRITERIO CLÍNICO </b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border" style="width:14%; height: 40px;"></td>


                </tr>
            </tbody>
        </table>
        <br>
        <table style="width: 100%">
            <thead>
                <tr>
                    <td colspan="5" style="width: 20%;" class="bg-blue td-not-border-right text-left"><b>E. DIAGNÓSTICO</b></td>
                    <td colspan="5" style="width: 20%;" class="bg-blue td-not-border-left text-right">PRE= PRESUNTIVO <br> DEF= DEFINITIVO</td>
                    <td class="bg-blue td-border text-center" style="width: 5%;"><b>CIE</b></td>
                    <td class="bg-blue td-border text-center" style="width: 5%;"><b>PRE</b></td>
                    <td class="bg-blue td-border text-center" style="width: 5%;"><b>DEF</b></td>
                    <td colspan="10" class="bg-blue td-border" style="width: 30%;"><b></b></td>
                    <td class="bg-blue td-border text-center" style="width: 5%;"><b>CIE</b></td>
                    <td class="bg-blue td-border text-center" style="width: 5%;"><b>PRE</b></td>
                    <td class="bg-blue td-border text-center" style="width: 5%;"><b>DEF</b></td>
                </tr>
            </thead>
            <tbody>
                @foreach ($cie10 as $key=>$val)
                @php
                $c3 = Sis_medico\Cie_10_3::find($val->cie10);
                $c4 = Sis_medico\Cie_10_4::find($val->cie10);
                $texto = '';
                if(!is_null($c3)){
                $texto = $c3->descripcion;
                }
                if(!is_null($c4)){
                $texto = $c4->descripcion;
                }
                @endphp
                @if ($key%2 == 0)
                <tr>
                    @endif
                    @if ($key%2 == 0)
                    <td colspan="1" class="td-border bg-green"><b>{{($key) + 1}}</b></td>
                    <td colspan="9" class="td-border">{{$val->cie10}}-{{$texto}}</td>
                    <td class="text-center td-border">{{$val->cie10}}</td>
                    <td class="text-center td-border">@if ($val->presuntivo_definitivo == 'PRESUNTIVO') X @endif</td>
                    <td class="text-center td-border">@if ($val->presuntivo_definitivo == 'DEFINITIVO') X @endif</td>
                    @if (count($cie10) == ($key + 1))
                    @if ($key%2 == 0)
                    <td class="td-border bg-green"><b>{{($key) + 2}}</b></td>
                    <td colspan="9" class="td-border"></td>
                    <td class="td-border"></td>
                    <td class="td-border"></td>
                    <td class="td-border"></td>
                    @endif
                    @endif
                    @else
                    <td class="td-border bg-green"><b>{{($key) + 1}}</b></td>
                   <td colspan="9" class="td-border">{{$val->cie10}}-{{$texto}}</td>
                    <td class="text-center td-border">{{$val->cie10}}</td>
                    <td class="text-center td-border">@if ($val->presuntivo_definitivo == 'PRESUNTIVO') X @endif</td>
                    <td class="text-center td-border">@if ($val->presuntivo_definitivo == 'DEFINITIVO') X @endif</td>
                    @endif
                    @if ($key%2 == 1)
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 14px;" class="bg-blue"><b>E. PLAN DE DIAGNÓSTICO PROPUESTO </b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border" style="width:14%; height: 40px;"></td>
                </tr>
            </tbody>
        </table>
        <br>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 14px;" class="bg-blue"><b>F. PLAN TERAPEÚTICO PROPUESTO </b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border" style="width:14%; height: 40px;">
                    @foreach($interconsulta->historia->evoluciones as $val)

                        @if(!is_null($val->child_pug))
                        <span><?php echo ($val->child_pug->examen_fisico) ?></span> <br>
                        @endif

                    @endforeach
                </td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td colspan="5" style="font-size: 14px;" class="bg-blue"><b>G. DATOS DEL PROFESIONAL RESPONSABLE</b></td>
                </tr>
            </thead>
            <tr>
                <td style="width:14%; height: 10px;" class="td-border bg-green"><b>FECHA</b></td>
                <td style="width:8%; height: 10px;" class="td-border bg-green"><b>HORA</b></td>
                <td style="width:20%" class="td-border bg-green"><b>PRIMER NOMBRE</b></td>
                <td style="width:20%" class="td-border bg-green"><b>PRIMER APELLIDO</b></td>
                <td style="width:15%" class="td-border bg-green"><b>SEGUNDO APELLIDO</b></td>
            </tr>
            <tr>
            <td class="text-center" style="width:5%; height: 18px;">{{$fecha}}</td>
                <td class="text-center" style="width:5%; height: 18px;">{{$hora}}</td>
                <td class="text-center" style="width:14%; height: 18px;">{{$interconsulta->historia->doctor_1->apellido1 }} </td>
                <td class="text-center" style="width:14%; height: 18px;">{{ $interconsulta->historia->doctor_1->apellido2 }}</td>
                <td class="text-center" style="width:14%; height: 18px;">{{ $interconsulta->historia->doctor_1->nombre1 }}</td>
            </tr>
            <tr>
                <td colspan="2" class="td-border bg-green"><b>NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
                <td colspan="2" class="td-border bg-green text-center"><b>FIRMA</b></td>
                <td class="td-border bg-green text-center"><b>SELLO</b></td>
            </tr>
            <tr>
                <td colspan="2" class="text-center" style="width:12%; height:60px">{{ $interconsulta->historia->doctor_1->id }}</td>
                <td colspan="2" class="td-border" style="width:25%; height:60px">
                    @php
                    $firma = Sis_medico\Firma_Usuario::where('id_usuario',$interconsulta->historia->procedimiento[0]->id_doctor_examinador)->first();

                    @endphp
                    @if(!is_null($firma))
                    <img width=170 height=80 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center hspace=12><br>
                    @endif
                </td>
                <td class="td-border" style="width:25%; height:60px;"></td>
            </tr>
            <tr>
                <td colspan="2"><b>SNS-MSP/HCU-form.007/2021</b></td>
                <td colspan="3" class="text-right"><b>INTERCONSULTA - INFORME</b></td>
            </tr>
            </tbody>
        </table>