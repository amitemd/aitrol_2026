<!DOCTYPE html>
<html>

<head>
    <title>CONSENTIMIENTO INFORMADO - DROGAS ABUSO</title>
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
    </style>
</head>

<body>
    <header>

    </header>
    <main>
        <table border="1" align="center" style="width: 85%">
            <tr>
                @php $empresa_nombre = Sis_medico\Empresa::where('prioridad_labs', '1')->where('estado', 1)->first();   @endphp

                <td rowspan="2" class="text-center td-border" style="height:65px; width: 20%;">
                @if(!is_null($empresa_nombre->logo))
                    <img src="{{base_path().'/storage/app/logo/'.$empresa_nombre->logo}}" style="width:150px;height: 75px;">
                @endif
                </td>
                <td style="width: 35%; font-size: 15px;" class="text-center td-border"><b>{{$empresa->razonsocial}}</b></td>
                <td rowspan="2" class="text-center td-border" style="width: 20%; font-size: 18px;">Vers 0.2</td>
            <tr>
                <td style="font-size: 14px;" class="text-center td-border"><b>CONSENTIMIENTO INFORMADO- <br> DROGAS ABUSO</b></td>
            </tr>
        </table>
        <br>
        <br>
        <br>
        <table align="center" style="width: 85%">
            <tr>
                <td style="font-size: 16px;" class="text-center"><b>CONSENTIMIENTO INFORMADO</b><br> <br>
            </tr>
            <tr>
                <td style="font-size: 16px;"><b>CONSENTIMIENTO DEL PACIENTE:</b></td>
            </tr>
        </table>
        <br>
        <table align="center" style="width: 85%">
            <tr>
                <td style="font-size: 16px;" class="justify-text">
                    Yo, &nbsp;<b>{{$paciente->paciente->nombre1}} &nbsp; {{$paciente->paciente->nombre2}} &nbsp; {{$paciente->paciente->apellido1}} &nbsp; {{$paciente->paciente->apellido2}}</b>
                    &nbsp;identificado con  C.I.<b style="font-size: 18px;">&nbsp;{{$paciente->paciente->id}}</b>
                    declaro de manera expresa que he sido informado por 
                    {{$empresa->razonsocial}} sobre las pruebas de drogas de abuso que se 
                    van a identificar y autorizo expresamente al laboratorio clínico {{$empresa->razonsocial}} para que supervise la toma de muestra de orina. <br> <br>
                    Fecha y hora de toma de muestra: ………………………………………………………. <br> <br>
                    <b>CONSENTIMIENTO DEL REPRESENTANTE LEGAL DEL PACIENTE (SI APLICA):</b> <br><br><br>
                    Yo, ………………………………………………. identificado con C.I. …………………………,
                    en calidad de representante legal de ……………………………………………………...
                    identificado con C.I. ………………………. declaro de manera expresa que he sido
                    informado por {{$empresa->razonsocial}} sobre las pruebas de drogas de
                    abuso que se van a identificar y autorizo expresamente al laboratorio clínico {{$empresa->razonsocial}} para que supervise la toma de muestra de orina de mi
                    representado. <br> <br> <br>    
                    <b>CONSENTIMIENTO DEL LABORATORIO CLÍNICO:</b> <br><br>

                    Yo, ………………………………………… como representante del laboratorio clínico
                    {{$empresa->razonsocial}} garantizo la supervisión de la toma de muestra
                    de orina y la integridad de la misma. <br> <br>
                    Reconozco que la legislación sobre protección de datos personales establece
                    obligaciones en el tratamiento de datos de carácter personal, entre las que se encuentra
                    la prohibición de usar o ceder datos personales sin la correspondiente autorización del
                    titular de los mismos, en tal efecto, autorizo a {{$empresa->razonsocial}} para utilizar los datos de carácter personal a los que tengan acceso por este
                    documento única y exclusivamente para cumplir con las obligaciones derivadas de éste
                    conforme a la legislación aplicable.
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <b>________________________________ @for($count=0; $count<=12;$count++) &nbsp; @endfor ________________________________</b>
                            @for($count=0; $count<=3;$count++) &nbsp; @endfor FIRMA DEL PACIENTE O 
                            @for($count=0; $count<=20;$count++) &nbsp; @endfor FIRMA DEL REPRESENTANTE DE <br>
                            <div>@for($count=0; $count<=2;$count++) &nbsp; @endfor REPRESENTANTE LEGAL
                            @for($count=0; $count<=10;$count++) &nbsp; @endfor {{$empresa->razonsocial}}<br></div>
                           
                          