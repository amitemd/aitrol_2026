<!DOCTYPE html>
<html>
@php
$fecha=date('Y-m-d');
@endphp

<?php
$fechaNacimiento = new DateTime($paciente->paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($paciente->paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($paciente->paciente->fecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');

?>

<head>
    <title>Consentimiento informado pruebas geneticas diagnosticas</title>
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
    </style>

    </style>
</head>

<body>
    <header>

    </header>
    <main>



        <table border="1" align="center" style="width: 75%">
            <tr>
                @php $empresa_nombre = Sis_medico\Empresa::where('prioridad_labs', '1')->where('estado', 1)->first(); @endphp

                <td rowspan="2" class="text-center td-border" style="height:65px; width: 20%;">
                    @if(!is_null($empresa_nombre->logo))
                    <img src="{{base_path().'/storage/app/logo/'.$empresa_nombre->logo}}" style="width:100px; height: 80px;">
                    @endif
                </td>
                <td style="width: 50%; font-size: 13px;" class="text-center td-border"><b>{{$empresa->razonsocial}}</b></td>
                <td rowspan="2" class="text-center td-border" style="width: 18%; font-size: 16px;">Vers 0.1</td>
            <tr>
                <td style="font-size: 14px;" class="text-center td-border"><b>CONSENTIMIENTO INFORMADO – PRUEBAS DE EMBARAZO PARA MENORES DE EDAD</b></td>
            </tr>
        </table>
        <br>
        <br>
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td class="text-center" style="font-size: 14px;"><b>CONSENTIMIENTO INFORMADO <br>
                            <br>
                            PRUEBAS DE EMBARAZO PARA MENORES DE EDAD </b>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <br>
        <br>
        <table style="width: 74%;" align="center">
            <tbody>
                <tr>
                    <td style="font-size: 16px; text-align: justify;" class="justify-text">
                        Yo, ……………………………………………………………… identificado con C.I.


                        …………………………………, &nbsp;&nbsp;en mi calidad de representante legal del menor


                        {{$paciente->paciente->nombre1.' '.' '.$paciente->paciente->nombre2.' '.' '.$paciente->paciente->apellido1.' '.' '.$paciente->paciente->apellido2}}
                        &nbsp;&nbsp; con &nbsp; C.I. &nbsp;&nbsp; {{$paciente->paciente->id}}


                        declaro de manera expresa que he sido informado por HumanLabs Medicina


                        Diagnóstica S.A. sobre las pruebas de embarazo que se van a realizar a mi


                        representado y autorizo expresamente al laboratorio la realización de dichas pruebas.

                    </td>
                </tr>
            </tbody>
        </table>

        <br>
        <br>
        <br>

        <table style="width: 75%;" align="center">
            <tbody>
                <tr>
                    <td style="font-size: 16px; text-align: justify;" class="justify-text">
                        Reconozco que la legislación sobre protección de datos personales establece

                        obligaciones en el tratamiento de datos de carácter personal, entre las que se encuentra

                        la prohibición de usar o ceder datos personales sin la correspondiente autorización del

                        titular de los mismos, en tal efecto, autorizo a HUMANLABS MEDICINA DIAGNOSTICA

                        S.A. para utilizar los datos de carácter personal a los que tengan acceso por este

                        documento para cumplir con las obligaciones derivadas de los servicios prestados.
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <table style="width: 75%;" align="center">
            <tbody>
                <tr>
                    <td style="font-size: 14px;">
                        ___________________________________
                        <br>
                        &nbsp; &nbsp;&nbsp; &nbsp; FIRMA DEL REPRESENTANTE <br>
                        &nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; LEGAL
                        <br>
                        Nombre:
                        <br>
                        Cédula:
                    </td>
                </tr>
            </tbody>
        </table>

</html>