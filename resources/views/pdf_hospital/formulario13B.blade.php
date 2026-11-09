<!DOCTYPE html>
<html>
<?php

$fechaNacimiento = new DateTime($bioxia->historia->paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($bioxia->historia->paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($bioxia->historia->paciente->fecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');

?>

<head>
    <meta charset="utf-8">
    <title>Formulario 13B</title>
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

        .text-left {
            text-align: left;
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
    <footer>
        <span>SNS-MSP / HCU-form.013B / 2021</span>
        <span class="document-name">ANATOMÍA PATOLÓGICA - INFORME (<span class="page-number"></span>)</span>
    </footer>
    <header>

    </header>
    <main>

        <table border="1" style="width: 99%">
            <tr>
                <td colspan="12">
                    <img src="{{base_path().'/storage/app/logo/cabecera.jpg'}}" style="width:750px;height: 80px;">
                </td>
            </tr>
        </table>
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
                    <td class="text-center" style="font-size: 14px;">&nbsp;</td>
                    <td colspan="2" class="text-center">026247</td>
                    <td colspan="2" class="text-center" style="font-size: 14px;"></td>
                    <td class="text-center" colspan="4" style="font-size: 16px;">{{$bioxia->historia->paciente->id}}</td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center" style="width: 15%;"><b>SEGUNDO NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>SEXO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>EDAD</b></td>
                    <td colspan="4" class="bg-green text-center" style="font-size: 6;"><b>CONDICIÓN EDAD (MARCAR)</b></td>
                </tr>
                <tr>
                    <td class="bg-green text-center">H</td>
                    <td class="bg-green text-center">D</td>
                    <td class="bg-green text-center">M</td>
                    <td class="bg-green text-center" style="width: 5%;">A</td>
                </tr>
                <tr>
                    <td class="text-center" colspan="2" style="height: 20px;">{{$bioxia->historia->paciente->apellido1}}</td>
                    <td class="text-center" colspan="2">{{$bioxia->historia->paciente->apellido2}}</td>
                    <td class="text-center" colspan="2">{{$bioxia->historia->paciente->nombre1}}</td>
                    <td class="text-center" colspan="1">{{$bioxia->historia->paciente->nombre2}}</td>
                    <td class="text-center" colspan="1">@if($bioxia->historia->paciente->sexo == 1) Maculino @else Femenino @endif</td>
                    <td class="text-center" colspan="2">{{$edad}}</td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1">{{$diaNacimiento}}</td>
                    <td class="text-center" colspan="1">{{$mesNacimiento}}</td>
                    <td class="text-center" colspan="1">{{ date('Y', strtotime($bioxia->historia->paciente->fecha_nacimiento))}}</td>
                </tr>
            </tbody>
        </table>
        <br>

        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="4" style="font-size: 14px;" class="bg-purple"><b>B. DATOS DEL SERVICIO </b></td>
                </tr>
            </thead>
            <tr>
                <td class="text-center bg-green">ESTABLECIMIENTO DE SALUD SOLCIITANTE</td>
                <td class="text-center bg-green">PROFESIONAL SOLICITANTE</td>
                <td class="text-center bg-green">ESPECIALIDAD</td>
                <td class="text-center bg-green">CÓDIGO INTERNO DE MUESTRA DE ANATOMÍA PATOLÓGICA</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <br>

        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="2" style="font-size: 14px;" class="bg-purple"><b>C. DESCRIPCIÓN MACROSCÓPICA </b></td>
                </tr>
            </thead>
            <tr>
                <td style="height: auto; word-wrap: break-word; overflow-wrap: break-word;" colspan="2">{{$bioxia->descripcion_macroscopica}}</td>
            </tr>
            <tr>
                <td style="width: 45%;" class="bg-green">NOMBRE DEL PROFESIONAL RESPONSABLE DE LA MACROSCOPIA</td>
                <td></td>
            </tr>
        </table>

        <br>


        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="2" style="font-size: 14px;" class="bg-purple"><b>C. DESCRIPCIÓN MICROSCÓPICA </b></td>
                </tr>
            </thead>
            <tr>
                <td style="height: auto; word-wrap: break-word; overflow-wrap: break-word;" colspan="2">{{$bioxia->descripcion_microscopica}}</td>
            </tr>
            <tr>
                <td style="width: 45%;" class="bg-green">NOMBRE DEL PROFESIONAL RESPONSABLE DE LA MICROSCOPIA</td>
                <td></td>
            </tr>
        </table>

        <br>

        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="19" style="font-size: 14px;" class="bg-purple"><b>E. INFORME DE CITOLOGÍA VAGINAL</b></td>
                </tr>
            </thead>
            <tr>
                <td colspan="4" class="bg-green text-center">CLASIFICACIÓN BETHESDA</td>
                <td colspan="2" class="bg-green text-center">NORMAL</td>
                <td>{{$bioxia->normal}}</td>
                <td colspan="2" class="bg-green text-center">LIE BAJO</td>
                <td>{{$bioxia->lie_bajo}}</td>
                <td colspan="2" class="bg-green text-center">LIE ALTO</td>
                <td>{{$bioxia->lie_alto}}</td>
                <td colspan="2" class="bg-green text-center">CA</td>
                <td colspan="4">{{$bioxia->ca}}</td>
            </tr>
            <tr>
                <td colspan="19">&nbsp;</td>
            </tr>
            <tr>
                <td class="bg-green text-center">MUESTRA INADECUADA</td>
                <td>{{$bioxia->muestra_ina}}</td>
                <td class="bg-green text-center">HONGOS</td>
                <td colspan="2">{{$bioxia->hongos}}</td>
                <td colspan="2" class="bg-green text-center">ERITROCITOS</td>
                <td>{{$bioxia->eritrocitos}}</td>
                <td colspan="2" class="bg-green text-center">FLORA BACTERIANA</td>
                <td>{{$bioxia->flora}}</td>
                <td colspan="3" class="bg-green text-center">HISTIOCITOS</td>
                <td>{{$bioxia->histiocitos}}</td>
                <td class="bg-green text-center">NÚMERO DE MUESTRA</td>
                <td>{{$bioxia->numero_muestra}}</td>
                <td class="bg-green text-center">FECHA DE PROCESO</td>
                <td>{{$bioxia->fecha_proceso}}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-center bg-green">OBSERVACIONES</td>
                <td colspan="17"></td>
            </tr>
            <tr>
                <td colspan="19" style="height: 45px;">{{$bioxia->observaciones}}</td>
            </tr>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td style="font-size: 14px;" class="bg-purple"><b> F. DIAGNÓSTICO </b></td>
                </tr>
            </thead>
            <tr>
                <td style="height: auto; word-wrap: break-word; overflow-wrap: break-word;">{{$bioxia->diagnostico}}</td>
            </tr>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td style="font-size: 14px;" class="bg-purple"><b> G. RECOMENDACIONES </b></td>
                </tr>
            </thead>
            <tr>
                <td style="height: auto; word-wrap: break-word; overflow-wrap: break-word;">{{$bioxia->recomendaciones}}</td>
            </tr>
        </table>
        <br>

        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="6" style="font-size: 14px;" class="bg-purple"><b> H. DATOS DEL PROFESIONAL RESPONSABLE </b></td>
                </tr>
            </thead>
            <tr>
                <td class="text-center bg-green" style="width: 15%;">FECHA <br>
                    (aaaa-mm-dd)</td>
                <td class="text-center bg-green" style="width: 15%;"> HORA <br>
                    (hh:mm) </td>
                <td class="text-center bg-green" style="width: 23%;">PRIMER NOMBRE</td>
                <td class="text-center bg-green" colspan="2" style="width: 23%;">PRIMER APELLIDO</td>
                <td class="text-center bg-green" style="width: 23%;">SEGUNDO APELLIDO</td>
            </tr>

            <tr>
                <td style="height: 38px;" class="text-center">{{date("Y-m-d", strtotime($bioxia->historia->agenda->fechaini))}}</td>
                <td class="text-center">{{date("H:i:s", strtotime($bioxia->historia->agenda->fechaini))}}</td>
                @if(!is_null($bioxia->historia->doctor_1))
                <td class="text-center">{{$bioxia->historia->doctor_1->nombre1}}</td>
                <td class="text-center" colspan="2">{{$bioxia->historia->doctor_1->apellido1}}</td>
                <td class="text-center">{{$bioxia->historia->doctor_1->apellido2}}</td>
            </tr>
            <tr>
                <td class="text-center bg-green" colspan="2" style="font-size: 9px;">NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</td>
                <td colspan="2" class="text-center bg-green">FIRMA</td>
                <td colspan="2" class="text-center bg-green">SELLO</td>
            </tr>
            <tr>
                <td class="text-center" colspan="2" style="height: 38px; font-size: 16px;">{{$bioxia->historia->doctor_1->id}}</td>
                <td class="text-center" style="height: 75px;" colspan="2">
                    @if(!is_null($firma))
                    <img width=230 height=60 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center hspace=12><br>
                    @endif
                </td>
                <td colspan="2"></td>
            </tr>
            @endif
        </table>

    </main>

</body>

</html>
