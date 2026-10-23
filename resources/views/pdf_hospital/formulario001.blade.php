<!DOCTYPE html>
<html>

<head>
    <title>FORMULARIO 001</title>
    <style>
        @page {
            margin: 0;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin-top: 10px;
            margin-left: 10px;
            margin-right: 10px;
            margin-bottom: 10px;
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
            width: 100%;
            border-collapse: collapse;
        }

        .table2d {
            border: none;
            text-align: center;
        }

        .titulo {
            background-color: #C7C3C2;
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

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-top {
            text-align: top;
        }

        .text-bottom {
            text-align: bottom;
        }

        .txt_vertical {
            transform: rotate(270deg); 
            padding: 0px;
        }

        .por {
            display: flex;
            align-items: stretch;
            justify-content: stretch;
            background-color: red;
            width: 1px;
            height: auto;
        }

        .hu {
            display: block;
            top:500px;
            font-weight: bold;
            font-size: 10px;
            padding: 5px;
            writing-mode: vertical-lr; 
            text-orientation: upright;
            width: 1px;
        }

        .title1{
            font-size: 16px;
        }

        .title2{
            font-size: 12px;
        }

        .content{
            font-size: 12px;
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
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td style="text-align: center;" colspan="2">
                    @if(!is_null($empresa))
                        <center><img src="{{base_path().'/storage/app/logo/'}}/{{$empresa->logo}}" style="width:200px;height: 100px"></center>
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table border="1">
                        <thead>
                            <tr>
                                <td colspan="5" class="bg-purple title1"><b>A. DATOS DEL ESTABLECIMIENTO</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="bg-green title2 text-center"><b>INSTITUCIÓN DEL SISTEMA</b></td>
                                <td class="bg-green title2 text-center"><b>ESTABLECIMIENTO DE SALUD</b></td>
                                <td class="bg-green title2 text-center"><b>UNICÓDIGO</b></td>
                                <td class="bg-green title2 text-center"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                                <td class="bg-green title2 text-center"><b>NÚMERO DE ARCHIVO</b></td>
                            </tr>
                            <tr>
                                <td class="text-center content">IESS</td>
                                <td class="text-center content">@if(!is_null($empresa)) {{$empresa->nombrecomercial}} @else &nbsp; @endif</td>
                                <td class="text-center content">&nbsp;</td>
                                <td class="text-center content">@if(!is_null($paciente)) {{$paciente->id}} @else &nbsp; @endif</td>
                                <td class="text-center content">&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <!-- <tr>
                <td colspan="12"><br></td>
            </tr> -->
            <tr>
                <td colspan="2">
                    <table border="1">
                        <thead>
                            <tr>
                                <td colspan="2" class="bg-purple title1"><b>B. REGISTRO DE ADMISIÓN</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="bg-green title2 text-center"><b>FECHA DE ADMISIÓN</b></td>
                                <td class="bg-green title2 text-center"><b>NOMBRE Y APELLIDO DEL ADMISIONISTA</b></td>
                            </tr>
                            <tr>
                                <td class="text-center content">&nbsp;</td>
                                <td class="text-center content">&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <!-- <tr>
                <td colspan="12"><br></td>
            </tr> -->
            <tr>
                <td colspan="2">
                    <table border="1">
                        <thead>
                            <tr>
                                <td colspan="12" class="bg-purple title1"><b>C. DATOS PERSONALES DEL USUARIO</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2" class="bg-green title2 text-center"><b>PRIMER APELLIDO</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>SEGUNDO APELLIDO</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>PRIMER NOMBRE</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>SEGUNDO NOMBRE</b></td>
                                <td colspan="4" class="bg-green title2 text-center"><b>TIPO DE IDENTIFICACION</b></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center content">@if(!is_null($paciente)) {{$paciente->apellido1}} @else &nbsp; @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($paciente)) {{$paciente->apellido2}} @else &nbsp; @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($paciente)) {{$paciente->nombre1}} @else &nbsp; @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($paciente)) {{$paciente->nombre2}} @else &nbsp; @endif</td>
                                <td colspan="4" class="text-center content">@if(!is_null($paciente)) {{$paciente->id}} @else &nbsp; @endif</td>
                            </tr>
                            <tr>
                                <td colspan="1" class="bg-green title2 text-center"><b>ESTADO CIVIL</b></td>
                                <td colspan="1" class="bg-green title2 text-center"><b>SEXO</b></td>
                                <td colspan="3" class="bg-green title2 text-center"><b>No. TELÉFONO FIJO</b></td>
                                <td colspan="3" class="bg-green title2 text-center"><b>No. TELÉFONO CELULAR</b></td>
                                <td colspan="4" class="bg-green title2 text-center"><b>CORREO ELECTRÓNICO</b></td>
                            </tr>
                            <tr>
                                <td colspan="1" class="text-center">
                                    @if(!is_null($paciente))
                                        @if($paciente->estadocivil==1)
                                            SOLTERO
                                        @elseif($paciente->estadocivil==2)
                                            CASADO
                                        @elseif($paciente->estadocivil==3)
                                            VIUDO
                                        @elseif($paciente->estadocivil==4)
                                            DIVORCIADO
                                        @elseif($paciente->estadocivil==5)
                                            UNION LIBRE
                                        @else
                                            UNION DE HECHO
                                        @endif
                                    @endif
                                </td>
                                <td colspan="1" class="text-center content">@if(!is_null($paciente)) @if($paciente->sexo==1) HOMBRE @else MUJER @endif @endif</td>
                                <td colspan="3" class="text-center content">@if(!is_null($paciente)) {{$paciente->telefono1}} @else &nbsp; @endif</td>
                                <td colspan="3" class="text-center content">@if(!is_null($paciente)) {{$paciente->telefono2}} @else &nbsp; @endif</td>
                                <td colspan="4" class="text-center content">@if(!is_null($paciente)) {{$paciente->email}} @else &nbsp; @endif</td>
                            </tr>
                            <tr>
                                <td rowspan="2" colspan="3" class="bg-green title2 text-center"><b>FECHA NACIMIENTO</b></td>
                                <td rowspan="2" colspan="2" class="bg-green title2 text-center"><b>LUGAR DE NACIMIENTO</b></td>
                                <td rowspan="2" colspan="2" class="bg-green title2 text-center"><b>NACIONALIDAD</b></td>
                                <td rowspan="2" colspan="1" class="bg-green title2 text-center"><b>EDAD</b></td>
                                <td colspan="4" class="bg-green title2 text-center"><b>CONDICIÓN DE LA EDAD</b></td>
                            </tr>
                            <tr>
                                <td class="bg-green title2 text-center">H</td>
                                <td class="bg-green title2 text-center">D</td>
                                <td class="bg-green title2 text-center">M</td>
                                <td class="bg-green title2 text-center">A</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-center content">@if(!is_null($paciente)) {{$paciente->fecha_nacimiento}} @else &nbsp; @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($paciente)) {{$paciente->lugar_nacimiento}} @else &nbsp; @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($paciente)) {{$datos_paciente->nacionalidad}} @else &nbsp; @endif</td>
                                <td colspan="1" class="text-center content">{{$edad}} AÑOS</td>
                                <td colspan="1" class="text-center content">&nbsp;</td>
                                <td colspan="1" class="text-center content">&nbsp;</td>
                                <td colspan="1" class="text-center content">&nbsp;</td>
                                <td colspan="1" class="text-center content">X</td>
                            </tr>
                            <tr>
                                <td rowspan="4"><b>RESIDENCIA HABITUAL</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>PROVINCIA</b></td>
                                <td colspan="3" class="bg-green title2 text-center"><b>CANTÓN</b></td>
                                <td colspan="3" class="bg-green title2 text-center"><b>PARROQUIA</b></td>
                                <td colspan="3" class="bg-green title2 text-center"><b>BARRIO O SECTOR</b></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->provincia}} @else &nbsp; @endif</td>
                                <td colspan="3" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->canton}} @else &nbsp; @endif</td>
                                <td colspan="3" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->parroquia}} @else &nbsp; @endif</td>
                                <td colspan="3" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->barrio}} @else &nbsp; @endif</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="bg-green title2"><b>CALLE PRINCIPAL</b></td>
                                <td colspan="3" class="bg-green title2"><b>CALLE SECUNDARIA</b></td>
                                <td colspan="4" class="bg-green title2 text-center"><b>REFERENCIA</b></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-center" style="height: 15px;">@if(!is_null($datos_paciente)) {{$datos_paciente->calle_principal}} @else &nbsp; @endif</td>
                                <td colspan="3" class="text-center" style="height: 15px;">@if(!is_null($datos_paciente)) {{$datos_paciente->calle_secundaria}} @else &nbsp; @endif</td>
                                <td colspan="4" class="text-center" style="height: 15px;">@if(!is_null($datos_paciente)) {{$datos_paciente->referencia}} @else &nbsp; @endif</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="bg-green title2 text-center"><b>AUTOIDENTIFICACIÓN ÉTNICA</b></td>
                                <td colspan="3" class="bg-green title2 text-center"><b>NACIONALIDAD ÉTNICA</b></td>
                                <td colspan="3" class="bg-green title2 text-center"><b>*PUEBLOS</b></td>
                                <td colspan="3" class="bg-green title2 text-center"><b>NIVEL DE EDUCACIÓN</b></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->autoidentificacion_etnica}} @else &nbsp; @endif</td>
                                <td colspan="3" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->grupo_cultural}} @else &nbsp; @endif</td>
                                <td colspan="3" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->pueblo}} @else &nbsp; @endif</td>
                                <td colspan="3" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->instruccion}} @else &nbsp; @endif</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="bg-green title2 text-center"><b>ESTADO DE NIVEL DE EDUCACIÓN</b></td>
                                <td colspan="3" class="bg-green title2 text-center"><b>OCUPACIÓN / PROFESIÓN PRINCIPAL</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>TIPO DE EMPRESA DE TRABAJO</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>SEGURO SALUD PRINCIPAL</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>TIPO DE BONO QUE RECIBE</b></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->estado_educacion}} @else &nbsp; @endif</td>
                                <td colspan="3" class="text-center content">@if(!is_null($paciente)) {{$paciente->ocupacion}} @else &nbsp; @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->tipo_empresa}} @else &nbsp; @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($seguro)) {{$seguro->nombre}} @else &nbsp; @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->tipo_bono}} @else &nbsp; @endif</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="12">* Aplica unicamente para nacionalidad étnica Kichwa</td>
            </tr>
            <!-- <tr>
                <td colspan="12"><br></td>
            </tr> -->
            <tr>
                <td colspan="2">
                    <table border="1">
                        <thead>
                            <tr>
                                <td colspan="4" class="bg-purple title1"><b>D. DATOS DE CONTACTO</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="bg-green title2 text-center"><b>EN CASO NECESARIO LLAMAR A:</b></td>
                                <td class="bg-green title2 text-center"><b>PARENTESCO</b></td>
                                <td class="bg-green title2 text-center"><b>DIRECCIÓN</b></td>
                                <td class="bg-green title2 text-center"><b>No. TELÉFONO</b></td>
                            </tr>
                            <tr>
                                <td class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->llamar_a}} @else &nbsp; @endif</td>
                                <td class="text-center content">@if(!is_null($paciente)) {{$paciente->parentescofamiliar}} @else &nbsp; @endif</td>
                                <td class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->direccion_familiar}} @else &nbsp; @endif</td>
                                <td class="text-center content">@if(!is_null($paciente)) {{$paciente->telefono_llamar}} @else &nbsp; @endif</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <!-- <tr>
                <td colspan="12"><br></td>
            </tr> -->
            <tr>
                <td colspan="2">
                    <table border="1">
                        <thead>
                            <tr>
                                <td colspan="12" class="bg-purple title1"><b>E. REGISTRO DE CAMBIOS</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="6" class="bg-green title2 text-center"><b></b></td>
                                <td colspan="1" class="bg-green title2 text-center"><b>FECHA DE REGISTRO</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>ESTADO CIVIL</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>NIVEL DE EDUCACIÓN</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>TIPO EMPRESA TRABAJO</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>OCUPACIÓN / PROFESIÓN PRINCIPAL</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>SEGURO DE SALUD PRINCIPAL</b></td>
                            </tr>
                            <tr>
                                <td colspan="1" class="text-center content">@php $hoy = date('Y-m-d'); @endphp {{$hoy}}</td>
                                <td colspan="2" class="text-center content">@if(!is_null($paciente))
                                        @if($paciente->estadocivil==1)
                                            SOLTERO
                                        @elseif($paciente->estadocivil==2)
                                            CASADO
                                        @elseif($paciente->estadocivil==3)
                                            VIUDO
                                        @elseif($paciente->estadocivil==4)
                                            DIVORCIADO
                                        @elseif($paciente->estadocivil==5)
                                            UNION LIBRE
                                        @else
                                            UNION DE HECHO
                                        @endif
                                        @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->instruccion}} @else &nbsp; @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->tipo_empresa}} @else &nbsp; @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($paciente)) {{$paciente->ocupacion}} @else &nbsp; @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($seguro)) {{$seguro->nombre}} @else &nbsp; @endif</td>
                            </tr>
                            <tr>
                                <td rowspan="4" class="text-center"><b>RESIDENCIA HABITUAL</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>PROVINCIA</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>CANTÓN</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>PARROQUIA</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>BARRIO O SECTOR</b></td>
                                <td colspan="2" class="bg-green title2 text-center"><b>No. TELÉFONO</b></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->provincia}} @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->canton}} @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->parroquia}} @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($datos_paciente)) {{$datos_paciente->barrio}} @endif</td>
                                <td colspan="2" class="text-center content">@if(!is_null($paciente)) {{$paciente->telefono1}} @endif</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="bg-green title2 text-center"><b>CALLE PRINCIPAL</b></td>
                                <td colspan="3" class="bg-green title2 text-center"><b>CALLE SECUNDARIA</b></td>
                                <td colspan="4" class="bg-green title2 text-center"><b>REFERENCIA</b></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-center content" style="height: 15px;">@if(!is_null($datos_paciente)) {{$datos_paciente->calle_principal}} @endif</td>
                                <td colspan="3" class="text-center content" style="height: 15px;">@if(!is_null($datos_paciente)) {{$datos_paciente->calle_secundaria}} @endif</td>
                                <td colspan="4" class="text-center content" style="height: 15px;">@if(!is_null($datos_paciente)) {{$datos_paciente->referencia}} @endif</td>
                            </tr>                     
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td><b>SNS-MSP / HCU-form.001 / 2019</b></td>
                <td style="text-align: right;"><b>ADMISIÓN</b></td>
            </tr>
        </table>
        <br>
        <br>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <table border="1">
                        <thead>
                            <tr>
                                <td colspan="17" class="bg-purple title1"><b>F. EGRESO HOSPITALARIO</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="width: 20%;" colspan="4" class="bg-green title2 text-center"><b>CARACTERÍSTICAS</b></td>
                                <td style="width: 40%;" colspan="6" class="bg-green title2 text-center"><b>DIAGNÓSTICO</b></td>
                                <td style="width: 10%;" colspan="3" class="bg-green title2 text-center"><b>CONDICIÓN DEL EGRESO</b></td>
                                <td style="width: 30%;" colspan="4" class="bg-green title2 text-center"><b>TRATAMIENTO</b></td>
                            </tr>
                            <tr>
                                <td style="height: 10px;width: 10px;" class="bg-blue text-right"><p class="text-left"><b>NUMERO DE ORDEN</b></p></td>
                                <td style="height: 50px;" class="bg-blue text-center"><p><b>FECHAS DE ADMISIÓN Y EGRESO <br> AÑO-MES-DIA</b></p></td>
                                <td style="height: 50px;" class="bg-blue text-center"><p><b>NUMERO DE DIAS DE ESTADA</b></p></td>
                                <td style="height: 50px;" class="bg-blue text-center"><p><b>CÓDIGO DEL SERVICIO (INEC)</b></p></td>

                                <td style="height: 50px;" class="bg-blue text-center"><p><b>DIAGNÓSTICOS O SÍNDROMES (PRINCIPAL)</b></p></td>
                                <td style="height: 50px;" class="bg-blue text-center"><p><b>CIE</b></p></td>
                                <td style="height: 50px;width: 10px;" class="bg-blue text-right"><p class="txt_vertical text-left"><b>DEFINITIVO</b></p></td>
                                <td style="height: 50px;" class="bg-blue text-center"><p><b>DIAGNÓSTICOS O SÍNDROMES (SECUNDARIO)</b></p></td>
                                <td style="height: 50px;" class="bg-blue text-center"><p><b>CIE</b></p></td>
                                <td style="height: 50px;width: 10px;" class="bg-blue text-right"><p class="txt_vertical text-left"><b>DEFINITIVO</b></p></td>

                                <td style="height: 10px;width: 10px;" class="bg-blue text-right"><p class="txt_vertical text-left"><b>ALTA</b></p></td>
                                <td style="height: 10px;width: 10px;" class="bg-blue text-right"><p class="txt_vertical text-left"><b>MUERTE MENOS DE 48 HORAS</b></p></td>
                                <td style="height: 10px;width: 10px;" class="bg-blue text-right"><p class="txt_vertical text-left"><b>MUERTE MAS DE 48 HORAS</b></p></td>
                                
                                <td style="height: 10px;width: 10px;" class="bg-blue text-right"><p class="txt_vertical text-left"><b>CLINICO</b></p></td>
                                <td style="height: 10px;width: 10px;" class="bg-blue text-right"><p class="txt_vertical text-left"><b>QUIRURGICO</b></p></td>
                                <td style="height: 50px;width: 150px" class="bg-blue text-center"><p><b>PROCEDIMIENTOS CLÍNICOS O QUIRÚRGICOS PRINCIPALES</b></p></td>
                                <td style="height: 50px;width: 145px;" class="bg-blue text-center"><p><b>SELLO Y FIRMA DEL RESPONSABLE</b></p></td>
                            </tr>
                            @foreach($data['logs_solicitud'] as $value)
                                <tr style="height: auto;">
                                    <td rowspan="2" class="bg-blue text-center"><b>{{$value['cont']}}</b></td>
                                    <td class="text-center">{{$value['fecha_ingreso']}}</td>
                                    
                                    <td rowspan="2" class="text-center">{{$value['dias']}}</td>
                                    <td rowspan="2" class="text-center">&nbsp;</td>

                                    <td class="text-center">@if(isset($value['cie10_ingreso'])) {{$value['cie10_ingreso']['descripcion']}} @endif</td>
                                    <td class="text-center">@if(isset($value['cie10_ingreso'])) {{$value['cie10_ingreso']['cie10']}} @endif</td>
                                    <td class="bg-yellow">@if(isset($value['cie10_ingreso'])) @if($value['cie10_ingreso']['definitivo'] == 'DEFINITIVO') X @endif @endif</td>
                                    <td class="text-center">&nbsp;</td>
                                    <td class="text-center">&nbsp;</td>
                                    <td class="bg-yellow">&nbsp;</td>

                                    <td rowspan="2" class="bg-yellow"></td>
                                    <td rowspan="2" class="bg-yellow"></td>
                                    <td rowspan="2" class="bg-yellow"></td>

                                    <td class="bg-yellow text-center"><b>@if(isset($value['cie10_ingreso'])) @if(!in_array($value['cie10_ingreso']['id_paso'],[4,5])) X @endif @endif</b></td>
                                    <td class="bg-yellow text-center"><b>@if(isset($value['cie10_ingreso'])) @if(in_array($value['cie10_ingreso']['id_paso'],[4,5])) X @endif @endif</b></td>
                                    <td class="text-center">
                                        @if(isset($value['cie10_ingreso']))
                                            {{$value['cie10_ingreso']['nombre_paso']}}
                                        @endif
                                    </td>
                                    <td rowspan="2">
                                        @if($value['firma_doctor']!='')
                                            <img src="{{$value['cie10_ingreso']['ruta_firma']}}" style="width: 100px;height: auto;"><br>
                                        @endif
                                    </td>
                                </tr>
                                <tr style="height: 20px;">
                                @php //dd($value['fecha_ingreso']); @endphp
                                    <td class="text-center">{{$value['fecha_salida']}}</td>

                                    <td class="text-center">@if(isset($value['cie10_salida'])) {{$value['cie10_salida']['descripcion']}} @endif</td>
                                    <td class="text-center">@if(isset($value['cie10_salida'])) {{$value['cie10_salida']['cie10']}} @endif</td>
                                    <td class="bg-yellow">@if(isset($value['cie10_salida'])) @if($value['cie10_salida']['definitivo'] == 'DEFINITIVO') X @endif @endif</td>
                                    <td class="text-center">&nbsp;</td>
                                    <td class="text-center">&nbsp;</td>
                                    <td class="bg-yellow">&nbsp;</td>
                                    <td class="bg-yellow text-center"><b>@if(isset($value['cie10_salida'])) @if(!in_array($value['cie10_salida']['id_paso'],[4,5])) X @endif @endif</b></td>
                                    <td class="bg-yellow text-center"><b>@if(isset($value['cie10_salida'])) @if(in_array($value['cie10_salida']['id_paso'],[4,5])) X @endif @endif</b></td>
                                    <td class="text-center">
                                        @if(isset($value['cie10_salida']))
                                            {{$value['cie10_salida']['nombre_paso']}}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach 
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <table border="1" style="width: 100%">
            <thead>
                <tr style="width: 100%;" >
                    <td colspan="2" class="text-center"><b>LISTA DE CATEGORIAS</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 20%;">TIPO DE IDENTIFICACIÓN</td>
                    <td style="width: 80%;">1. Cédula de Identidad o ciudadanía. 2. Pasaporte. 3. Carnet de refugiado. 4. Sin identificación</td>
                </tr>
                <tr>
                    <td style="width: 20%;">ESTADO CIVIL</td>
                    <td style="width: 80%;">1 Soltero. 2 Casado. 3 Divorciado.  4 Viudo. 5 Unido. 6 Unión de hecho. 7 No Registra</td>
                </tr>
                <tr>
                    <td style="width: 20%;">SEXO</td>
                    <td style="width: 80%;">1. Hombre. 2. Mujer</td>
                </tr>
                <tr>
                    <td style="width: 20%;">NACIONALIDAD</td>
                    <td style="width: 80%;">1. Ecuatoriana. 2. Colombiana. 3.  Peruana. 4. Estadounidense. 5. Venezolana. 6.  Panameña. 7.  Española. 8.  Argentina. 9.  Chilena. 10.  Cubana. 11. Holandesa. 12.  Mexicana. 13. Brasileña. 14. Salvadoreña. 15. Canadiense. 16. Alemana. 17. China. 18. Británica. 19. Francesa. 988. Otra.</td>
                </tr>
                <tr>
                    <td style="width: 20%;">CONDICIÓN EDAD</td>
                    <td style="width: 80%;">1. Horas.  2. Días. 3. Meses. 4. Años.</td>
                </tr>
                <tr>
                    <td style="width: 20%;">PROVINCIA</td>
                    <td style="width: 80%;">1. Azuay. 2. Bolivar. 3. Cañar. 4. Carchi. 5. Cotopaxi. 6. Chimborazo. 7. El Oro. 8. Esmeraldas. 9. Guayas. 10. Imbabura. 11. Loja. 12. Los Ríos. 13. Manabí. 14. Morona Santiago. 15. Napo. 16. Pastaza. 17. Pichincha. 18. Tungurahua. 19. Zamora Chinchipe. 20. Galápagos. 21. Sucumbios. 22. Orellana. 23. Santo Domingo De Los Tsáchilas. 24. Santa Elena. 90. Zona No Delimitada</td>
                </tr>
                <tr>
                    <td style="width: 20%;">AUTOIDENTIFICACIÓN ÉTNICA</td>
                    <td style="width: 80%;">0. No aplica. 1. Indígena. 2. Afroecuatoriano/a. 3. Negro/a. 4. Mulato/a. 5. Montubio/a. 6. Mestizo/a. 7. Blanco/a. 8. No sabe/No responde</td>
                </tr>
                <tr>
                    <td style="width: 20%;">NACIONALIDAD ÉTNICA</td>
                    <td style="width: 80%;">0. No aplica. 1. Achuar. 2. Awa. 3. Cofán. 4. Chachi. 5. Epera. 6. Kichwa. 7. Secoya. 8. Shuar. 9. Shiwiar. 10. Siona. 11. Tsáchila. 12. Waorani. 13. Zapara. 14. Andoa</td>
                </tr>
                <tr>
                    <td style="width: 20%;">PUEBLOS (solo para nacionalidad étnica KICHWA)</td>
                    <td style="width: 80%;">0. No aplica. 1. Chibuleo. 2. Huancavilca. 3. Karanki. 4. Kañari. 5. Kayambi. 6. Kisapincha. 7. Kitukara. 8. Manta. 9. Natabuela. 10. Otavalo. 11. Paltas. 12. Panzaleo.</td>
                </tr>
                <tr>
                    <td style="width: 20%;">NIVEL DE EDUCACION</td>
                    <td style="width: 80%;">0. No aplica. 1. Ninguno. 2. Inicial. 4. Educación Básica (preparatoria). 5. Educación Básica (Elemental o media). 6. Educación Básica (Superior). 7. Bachillerato. 8. Técnico Superior. 9. Tercer Nivel. 10. Cuarto Nivel. 11. Se ignora.</td>
                </tr>
                <tr>
                    <td style="width: 20%;">ESTADO DE NIVEL DE EDUCACIÓN</td>
                    <td style="width: 80%;">0. No aplica 1. Cursando. 2. Completa. 3. Incompleta.</td>
                </tr>
                <tr>
                    <td style="width: 20%;">TIPO DE EMPRESA DE TRABAJO</td>
                    <td style="width: 80%;">0. No aplica. 1. Pública. 2. Privada. 3. Ninguno.</td>
                </tr>
                <tr>
                    <td style="width: 20%;">SEGURO DE SALUD PRINCIPAL</td>
                    <td style="width: 80%;">0. No aporta 1. IESS General 2. IESS Campesino 3. ISSPOL  4. ISSFA  5. Privado 6. SPATT 7. Indirecto</td>
                </tr>
                <tr>
                    <td style="width: 20%;">TIPO DE BONO QUE RECIBE</td>
                    <td style="width: 80%;">0. Ninguno. 1. Desarrollo Humano. 2. Joaquín Gallegos. 3. Manuela Espejo. </td>
                </tr>
            </tbody>
        </table>
        <table cellpadding="0" cellspacing="0" style="width: 100%">
            <tr>
                <td><b>SNS-MSP / HCU-form.001 / 2019</b></td>
                <td style="text-align: right;"><b>EGRESO</b></td>
            </tr>
        </table>
    </main>
</body>

</html>