<!DOCTYPE html>
<html>

<head>
    <title>FORMULARIO 024</title>
    <style>
        @page {
            margin: 0 0;
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
            height: 1cm;
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
            border-bottom: 1px solid;
            border-left: 1px solid;
            border-right: 1px solid;
        }

        .td-not-border-bottom {
            border-top: 1px solid;
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

        .td-border-rl {
            border-left: 1px solid;
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

        .txt_vertical {
            transform: rotate(-90deg);
            font-size: 8px;
            writing-mode: vertical-lr; 
            text-orientation: sideways;
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

        .text-content {
            font-size: 12px;
        }

        .page_break {
            page-break-before: auto;
        }
        .page_break_always {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <main>
        <table cellpadding="0" cellspacing="0" style="width: 100%">
            <tr style="height: max-content;">
                <td colspan="2">
                    <center><img src="{{base_path().'/storage/app/logo/'}}/{{$empresa->logo}}" style="width:250px;height: 100px"></center>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table border="1" style="width: 100%">
                        <thead>
                            <tr>
                                <td colspan="12" class="bg-blue"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" class="bg-green text-center"><b>INSTITUCIÓN DEL SISTEMA</b></td>
                                <td colspan="3" class="bg-green text-center"><b>ESTABLECIMIENTO DE SALUD</b></td>
                                <td colspan="3" class="bg-green text-center"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                                <td colspan="3" class="bg-green text-center"><b>NÚMERO DE ARCHIVO</b></td>
                            </tr>
                            <tr>
                                <td colspan="3">{{ $seguro_nombre }}</td>
                                <td colspan="3">{{ $empresa->nombrecomercial }}</td>
                                <td colspan="3">{{ $paciente->id }}</td>
                                <td colspan="3">{{ $paciente->id }}</td>
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
                                <td colspan="2">{{ $paciente->apellido1 }}</td>
                                <td colspan="1">{{ $paciente->apellido2 }}</td>
                                <td colspan="2">{{ $paciente->nombre1 }}</td>
                                <td colspan="1">{{ $paciente->nombre2 }}</td>
                                <td colspan="1">{{ $paciente->sexo==1?'MASCULINO':'FEMENINO'}}</td>
                                <td colspan="1">{{ $age }} AÑOS</td>
                                <td colspan="1"></td>
                                <td colspan="1"></td>
                                <td colspan="1"></td>
                                <td colspan="1" class="text-center"> x </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table border="1" style="width: 100%">
                        <thead>
                            <tr>
                                <td colspan="12" class="bg-blue"><b>B. CONSENTIMIENTO INFORMADO</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" style="width: 30%" class="bg-green text-center"><b>CONSENTIMIENTO INFORMADO PARA:</b></td>
                                <td colspan="9" style="width: 70%" class="text-center">{{ $formato->nombre }}</td>
                            </tr>
                            <tr>
                                <td colspan="1" style="width: 5%;" class="bg-green text-center"><b>SERVICIO:</b></td>
                                <td colspan="4" style="width: 25%;">GASTROENTEROLOGIA</td>
                                <td colspan="2" style="width: 10%;" class="bg-green text-center"><b>TIPO DE ATENCIÓN:</b></td>
                                <td colspan="1" style="width: 5%;" class="text-center"><b>AMBULATORIO</td>
                                <td colspan="1" style="width: 5%;" class="text-center"><b>@if($agenda->est_amb_hos == 0 ) X @endif</b></td>
                                <td colspan="2" style="width: 5%;" class="text-center"><b>HOSPITALIZACIÓN</b></td>
                                <td colspan="1" style="width: 10%;" class="text-center"><b>@if($agenda->est_amb_hos == 1 ) X @endif</b></td>
                            </tr>
                            <tr>
                                <td colspan="1" class="bg-green text-center"><b>DIAGNÓSTICO:</b></td>
                                <td colspan="8">{{$agenda->diagnostico_proc}}</td>
                                <td colspan="1" class="bg-green text-center"><b>CIE 10:</b></td>
                                <td colspan="2">{{$agenda->cie10}}</td>
                            </tr>
                            <tr>
                                <td colspan="1" class="bg-green text-center"><b>EN QUÉ CONSISTE:</b></td>
                                <td colspan="11" class="text-content">@if(!is_null($formato->consiste)) {{$formato->consiste}} @else &nbsp; @endif</td>
                            </tr>
                            <tr>
                                <td colspan="1" class="bg-green text-center"><b>CÓMO SE REALIZA:</b></td>
                                <td colspan="11" class="text-content">@if(!is_null($formato->realiza)) {{$formato->realiza}} @else &nbsp; @endif</td>
                            </tr>
                            <tr>
                                <td colspan="12" class="bg-green"><b>GRÁFICO DE LA INTERVENCIÓN (incluya un gráfico previamente seleccionado que facilite la comprensión al paciente)</b></td>
                            </tr>
                            <tr>
                                <td colspan="12" style="height: 200px;" class="text-center"><img style="height:200px;" src="{{$imagen}}" /> </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table class="page_break" border="1" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td colspan="1" style="width: 10%" class="bg-green"><b>DURACIÓN ESTIMADA DE LA INTERVENCIÓN: </b></td>
                                <td colspan="11" style="width: 90%" class="text-content">@if(!is_null($formato->duracion_estimada)) {{$formato->duracion_estimada}} @else &nbsp; @endif</td>
                            </tr>
                            <tr>
                                <td colspan="1" style="width: 10%" class="bg-green"><b>BENEFICIOS DEL PROCEDIMIENTO: </b></td>
                                <td colspan="11" style="width: 90%" class="text-content">@if(!is_null($formato->beneficios)) {{$formato->beneficios}} @else &nbsp; @endif</td>
                            </tr>        
                            <tr>
                                <td colspan="1" style="width: 10%" class="bg-green"><b>RIESGOS FRECUENTES (POCO GRAVES):</b></td>
                                <td colspan="11" style="width: 90%" class="text-content">@if(!is_null($formato->riesgos_frecuentes)) {{$formato->riesgos_frecuentes}} @else &nbsp; @endif</td>
                            </tr>
                            <tr>
                                <td colspan="1" style="width: 10%" class="bg-green"><b>RIESGOS POCO FRECUENTES (GRAVES):</b></td>
                                <td colspan="11" style="width: 90%" class="text-content">@if(!is_null($formato->riesgos_infrecuentes)) {{$formato->riesgos_infrecuentes}} @else &nbsp; @endif</td>
                            </tr>
                            <tr>
                                <td colspan="12" class="bg-green"><b>DE EXISTIR, ESCRIBA LOS RIESGOS ESPECÍFICOS RELACIONADOS CON EL PACIENTE (edad, estado de salud, creencias, valores, etc):</b></td>
                            </tr>
                            <tr>
                                <td colspan="12" class="text-content">@if(!is_null($formato->riesgos_adicionales)) {{$formato->riesgos_adicionales}} @else &nbsp; @endif</td>
                            </tr>
                            <tr>
                                <td colspan="1" style="width: 10%" class="bg-green"><b>ALTERNATIVAS AL PROCEDIMIENTO: </b></td>
                                <td colspan="11" style="width: 90%" class="text-content">@if(!is_null($formato->alternativas)) {{$formato->alternativas}} @else &nbsp; @endif</td>
                            </tr>
                            <tr>
                                <td colspan="1" style="width: 10%" class="bg-green"><b>DESCRIPCIÓN DEL MANEJO POSTERIOR AL PROCEDIMIENTO: </b></td>
                                <td colspan="11" style="width: 90%" class="text-content">@if(!is_null($formato->manejo)) {{$formato->manejo}} @else &nbsp; @endif</td>
                            </tr>
                            <tr>
                                <td colspan="1" style="width: 10%" class="bg-green"><b>CONSECUENCIAS POSIBLES SI NO SE REALIZA EL PROCEDIMIENTO:</b></td>
                                <td colspan="11" style="width: 90%" class="text-content">@if(!is_null($formato->consecuencias)) {{$formato->consecuencias}} @else &nbsp; @endif</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tfoot>
                <tr>
                    <td><b>SNS-MSP / HCU-form.024/2016</b></td>
                    <td style="text-align: right; font-size: 14px;"><b>CONSENTIMIENTO INFORMADO (1)</b></td>
                </tr>
            </tfoot>
        </table>
        <table class="page_break_always" cellpadding="0" cellspacing="0" style="width: 100%;margin-top: 0;">
            <thead>
                <tr>
                    <td class="bg-blue td-border" colspan="12">C. DECLARACIÓN DE CONSENTIMIENTO INFORMADO</td>
                </tr>
            </thead>
            <tbody class="td-not-border-top">
                <tr>
                    <td colspan="1" class="bg-green td-border">FECHA</td>
                    <td colspan="5" class="td-border">{{$fecha}}</td>
                    <td colspan="1" class="bg-green td-border">HORA</td>
                    <td colspan="5" class="td-border">{{$hora}}</td>
                </tr>
                <tr>
                    <td colspan="12" class="td-border">
                        <p>
                        He  facilitado la  información  completa que conozco,  y  me ha  sido solicitada,  sobre  los antecedentes personales,  
                        familiares y de  mi estado  de salud.   Soy consciente que de omitir estos datos puede afectarse los  resultados del  
                        tratamiento. Estoy de acuerdo con el procedimiento que se me ha propuesto; he sido informado de las ventajas e inconvenientes 
                        del mismo; se me ha explicado de forma clara en qué consiste, los beneficios  y posibles riesgos del procedimiento. 
                        He escuchado,  leído  y comprendido la información recibida  y  se me ha dado la  oportunidad  de preguntar sobre el 
                        procedimiento. He  tomado  consciente  y   libremente de decisión de  autorizar el  procedimiento adicional,  si es considerado 
                        necesario según el juicio del profesional de la salud, para mi beneficio. También conozco que puedo retirar mi consentimiento 
                        cuando lo estime oportuno.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-center">{{$paciente->apellido1}} @if($paciente->apellido2!="N/A") {{ $paciente->apellido2}} @endif {{$paciente->nombre1}} @if($paciente->apellido2!="N/A") {{ $paciente->apellido2}} @endif</td>
                    <td colspan="1"></td>
                    <td colspan="2" class="text-center">{{$paciente->id}}</td>
                    <td colspan="1"></td>
                    <td colspan="4" class="text-center"></td>
                </tr>
                <tr>
                    <td colspan="4" class="td-border-top text-center">Nombre completo del paciente.</td>
                    <td colspan="1"></td>
                    <td colspan="2" class="td-border-top text-center">Cédula de ciudadanía.</td>
                    <td colspan="1"></td>
                    <td colspan="4" class="td-border-top text-center">Firma del paciente o huella, según el caso.</td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="4"class="td-border-top text-center">Nombre de profesional que realiza el procedimiento.</td>
                    <td colspan="1"></td>
                    <td colspan="7" class="td-border-top text-center">Firma, sello y código del profesional de la salud que realizará el procedimiento.</td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="7" class="text-center"><b>Si el paciente no está en capacidad para firmar el consentimiento informado:</b></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="4" class="td-border-top text-center">Nombre del representante legal.</td>
                    <td colspan="1"></td>
                    <td colspan="2" class="td-border-top text-center">Cédula de ciudadanía.</td>
                    <td colspan="1"></td>
                    <td colspan="4" class="td-border-top text-center">Firma del representante legal.</td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="4"class="td-border-top text-center">Parentesco</td>
                    <td colspan="8"></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="7" class="text-center"><b>Si el paciente no acepta el procedimiento sugerido por el profesional y se niega a firmar este acápite:</b></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="4" class="td-border-top text-center">Nombre completo del testigo.</td>
                    <td colspan="1"></td>
                    <td colspan="2" class="td-border-top text-center">Cédula de ciudadanía.</td>
                    <td colspan="1"></td>
                    <td colspan="4" class="td-border-top text-center">Firma del testigo.</td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
            </tbody>
        </table>
        <table cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td class="bg-blue td-border" colspan="12">D. NEGATIVA DEL CONSENTIMIENTO INFORMADO</td>
                </tr>
            </thead>
            <tbody class="td-not-border-top">
                <tr>
                    <td colspan="2" class="bg-green td-border">FECHA</td>
                    <td colspan="10" class="td-border">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="12" class="td-border">
                        <p>
                        Una vez que he entendido claramente el procedimiento propuesto, así como las consecuencias posibles si no se realiza la 
                        intervención, no autorizo y me niego a que se me realice el procedimiento propuesto y desvinculo de responsabilidades futuras 
                        de cualquier índole al establecimiento de salud y al profesional sanitario que me atiende, por no realizar la intervención 
                        sugerida.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-center"></td>
                    <td colspan="1"></td>
                    <td colspan="2" class="text-center"></td>
                    <td colspan="1"></td>
                    <td colspan="4" class="text-center"></td>
                </tr>
                <tr>
                    <td colspan="4" class="td-border-top text-center">Nombre completo del paciente.</td>
                    <td colspan="1"></td>
                    <td colspan="2" class="td-border-top text-center">Cédula de ciudadanía.</td>
                    <td colspan="1"></td>
                    <td colspan="4" class="td-border-top text-center">Firma del paciente o huella, según el caso.</td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="4"class="td-border-top text-center">Nombre de profesional que realiza el procedimiento.</td>
                    <td colspan="1"></td>
                    <td colspan="7" class="td-border-top text-center">Firma, sello y código del profesional de la salud que realizará el procedimiento.</td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="7" class="text-center"><b>Si el paciente no está en capacidad para firmar el consentimiento informado:</b></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="4" class="td-border-top text-center">Nombre del representante legal.</td>
                    <td colspan="1"></td>
                    <td colspan="2" class="td-border-top text-center">Cédula de ciudadanía.</td>
                    <td colspan="1"></td>
                    <td colspan="4" class="td-border-top text-center">Firma del representante legal.</td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="4"class="td-border-top text-center">Parentesco</td>
                    <td colspan="8"></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="7" class="text-center"><b>Si el paciente no acepta el procedimiento sugerido por el profesional y se niega a firmar este acápite:</b></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="4" class="td-border-top text-center">Nombre completo del testigo.</td>
                    <td colspan="1"></td>
                    <td colspan="2" class="td-border-top text-center">Cédula de ciudadanía.</td>
                    <td colspan="1"></td>
                    <td colspan="4" class="td-border-top text-center">Firma del testigo.</td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
            </tbody>
        </table>
        <table cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td class="bg-blue td-border" colspan="12">E. REVOCATORIA DEL CONSENTIMIENTO INFORMADO</td>
                </tr>
            </thead>
            <tbody class="td-not-border-top">
                <tr>
                    <td colspan="12" class="td-border">
                        <p>
                        De forma libre y voluntaria, revoco el consentimiento realizado en fecha y manifiesto expresamente mi deseo de no continuar con 
                        el procedimiento médico que doy por finalizado en esta fecha:
                        Libero de responsabilidades futuras de cualquier índole al establecimiento de salud y al profesional sanitario que me atiende.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-center"></td>
                    <td colspan="1"></td>
                    <td colspan="2" class="text-center"></td>
                    <td colspan="1"></td>
                    <td colspan="4" class="text-center"></td>
                </tr>
                <tr>
                    <td colspan="4" class="td-border-top text-center">Nombre completo del paciente.</td>
                    <td colspan="1"></td>
                    <td colspan="2" class="td-border-top text-center">Cédula de ciudadanía.</td>
                    <td colspan="1"></td>
                    <td colspan="4" class="td-border-top text-center">Firma del paciente o huella, según el caso.</td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="4"class="td-border-top text-center">Nombre de profesional que realiza el procedimiento.</td>
                    <td colspan="1"></td>
                    <td colspan="7" class="td-border-top text-center">Firma, sello y código del profesional de la salud que realizará el procedimiento.</td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="7" class="text-center"><b>Si el paciente no está en capacidad para firmar el consentimiento informado:</b></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="4" class="td-border-top text-center">Nombre del representante legal.</td>
                    <td colspan="1"></td>
                    <td colspan="2" class="td-border-top text-center">Cédula de ciudadanía.</td>
                    <td colspan="1"></td>
                    <td colspan="4" class="td-border-top text-center">Firma del representante legal.</td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="4"class="td-border-top text-center">Parentesco</td>
                    <td colspan="8"></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="7" class="text-center"><b>Si el paciente no acepta el procedimiento sugerido por el profesional y se niega a firmar este acápite:</b></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="12"><br></td>
                </tr>
                <tr>
                    <td colspan="4" class="td-border-top text-center">Nombre completo del testigo.</td>
                    <td colspan="1"></td>
                    <td colspan="2" class="td-border-top text-center">Cédula de ciudadanía.</td>
                    <td colspan="1"></td>
                    <td colspan="4" class="td-border-top text-center">Firma del testigo.</td>
                </tr>
            </tbody>
        </table>
        <table cellpadding="0" cellspacing="0" style="width: 100%">
            <tr>
                <td><b>SNS-MSP / HCU-form.024/2016</b></td>
                <td style="text-align: right; font-size: 14px;"><b>CONSENTIMIENTO INFORMADO (2)</b></td>
            </tr>
        </table> 
    </main>
</body>

</html>