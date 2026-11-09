<?php
ob_start(function ($html) {
    return preg_replace('/(>\s*)N\/A(\s*<)/i', '$1$2', $html);
});
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>formulario 005</title>
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
        <table border="1" style="width: 99%">
            <tr>
                <td colspan="12">
                    <img src="{{base_path().'/storage/app/logo/cabecera.jpg'}}" style="width: 100%; height: 80px">
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
                    <td class="text-center" colspan="4" style="font-size: 16px;"></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td rowspan="2" colspan="2" class="bg-green text-center" ><b>PRIMER APELLIDO</b></td>
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
                    <td class="text-center" colspan="2">&nbsp;</td>
                    <td class="text-center" colspan="2"></td>
                    <td class="text-center" colspan="2"></td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="2"></td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1"></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="9" style="font-size: 14px;" class="bg-blue td-border"><b>B. EVOLUCIÓN Y PRESCRIPCIONES</b>@for($count=0; $count<=5;$count++) &nbsp; @endfor <b style="font-size: 6px;">FIRMAR AL PIE DE CADA EVOLUCIÓN Y PRESCRIPCIÓN</b>
                            @for($count=0; $count<=1;$count++) &nbsp; @endfor<b style="font-size: 5px;">REGISTRAR CON ROJO LA ADMINISTRACIÓN DE FÁRMACOS Y COLOCACIÓN DE DISPOSITIVOS MÉDICOS</b></td>
                </tr>
                <tr>
                    <td colspan="5" style="font-size: 14px; width:30%; height: 15px;" class="bg-blue td-border text-center"><b>1. EVOLUCIÓN</b></td>
                    <td style="width:2%; height: 5px;"></td> text-center
                    <td colspan="3" style="font-size: 14px; width:25%; height: 15px;" class="bg-blue td-border text-center"><b>2. PRESCRIPCIONES</b></td>
                </tr>
                <tr>
                    <td class="bg-green td-border"><b>"FECHA <br>
                            <p style="font-size: 8px;">(aaaa-mm-dd)"</p></br>
                        </b></td>
                    <td class="bg-green td-border"><b>"HORA <br>
                            <p style="font-size: 8px;"> (hh:mm)"</p></br>
                        </b></td>
                    <td colspan="3" class="bg-green td-border" style="width:25%; height: 3px;"><b>NOTAS DE EVOLUCIÓN</b></td>
                    <td style="width: 5%"></td>

                    <td colspan="2" class="bg-green td-border text-center" style="width:20%; height: 13px;"><b>"FARMACOTERAPIA E INDICACIONES<br>
                            <p style="font-size: 8px;">(Para enfermería y otro profesional de salud)" </p></br>
                        </b></td>
                    <td colspan="1" class="bg-green td-border" style="width:2%;"><b>ADMINISTR.<br>
                            <p style="font-size: 7px; width:25%;">FÁRMACOS</p>
                            <p style="font-size: 7px; width:25%;">DISPOSITIVO"</p>
                        </b></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td colspan="5"><b>SNS-MSP / HCU-form.005/2021</b></td>
                    <td></td>
                    <td colspan="3" class="text-right"><b>EVOLUCIÓN Y PRESCRIPCIONES (1)</b></td>
                </tr>
        </table>
        <p class="page-break-before">
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="5" style="font-size: 14px;" class="bg-blue"><b>A. DATOS DEL USUARIO / PACIENTE</b></td>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td class="bg-green" style="width:18%;"><b>PRIMER APELLIDO</b></td>
                    <td class="bg-green" style="width:18%;"><b>PRIMER NOMBRE</b></td>
                    <td class="bg-green text-center"><b>EDAD</b></td>
                    <td class="bg-green" style="width:23%;"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                    <td class="bg-green" style=" width:12%;"><b>NÚMERO DE ARCHIVO</b></td>

                <tr>
                    <td style="height: 20px;">&nbsp;</td>
                    <td style="height: 20px;"></td>
                    <td style=" width:5%; height: 20px;"></td>
                    <td style="height: 20px;"></td>
                    <td style="height: 20px;"></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="9" style="font-size: 14px;" class="bg-blue td-border"><b>B. EVOLUCIÓN Y PRESCRIPCIONES</b>@for($count=0; $count<=5;$count++) &nbsp; @endfor <b style="font-size: 6px;">FIRMAR AL PIE DE CADA EVOLUCIÓN Y PRESCRIPCIÓN</b>
                            @for($count=0; $count<=1;$count++) &nbsp; @endfor<b style="font-size: 5px;">REGISTRAR CON ROJO LA ADMINISTRACIÓN DE FÁRMACOS Y COLOCACIÓN DE DISPOSITIVOS MÉDICOS</b></td>
                </tr>
                <tr>
                    <td colspan="5" style="font-size: 14px; width:30%; height: 15px;" class="bg-blue td-border text-center"><b>1. EVOLUCIÓN</b></td>
                    <td style="width:2%; height: 5px;"></td> text-center
                    <td colspan="3" style="font-size: 14px; width:25%; height: 15px;" class="bg-blue td-border text-center"><b>2. PRESCRIPCIONES</b></td>
                </tr>
                <tr>
                    <td class="bg-green td-border"><b>"FECHA <br>
                            <p style="font-size: 8px;">(aaaa-mm-dd)"</p></br>
                        </b></td>
                    <td class="bg-green td-border"><b>"HORA <br>
                            <p style="font-size: 8px;"> (hh:mm)"</p></br>
                        </b></td>
                    <td colspan="3" class="bg-green td-border" style="width:25%; height: 3px;"><b>NOTAS DE EVOLUCIÓN</b></td>
                    <td style="width: 5%"></td>

                    <td colspan="2" class="bg-green td-border text-center" style="width:20%; height: 13px;"><b>"FARMACOTERAPIA E INDICACIONES<br>
                            <p style="font-size: 8px;">(Para enfermería y otro profesional de salud)" </p></br>
                        </b></td>
                    <td colspan="1" class="bg-green td-border" style="width:2%;"><b>ADMINISTR.<br>
                            <p style="font-size: 7px; width:25%;">FÁRMACOS</p>
                            <p style="font-size: 7px; width:25%;">DISPOSITIVO"</p>
                        </b></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td colspan="3" class="td-border"></td>
                    <td></td>
                    <td colspan="2" class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td colspan="5"><b>SNS-MSP / HCU-form.005/2021</b></td>
                    <td></td>
                    <td colspan="3" class="text-right"><b>EVOLUCIÓN Y PRESCRIPCIONES (2)</b></td>
                </tr>
        </table>
    </main>
</body>
</html>
