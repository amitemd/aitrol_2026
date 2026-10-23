<!DOCTYPE html>
<html>

@php
//$fecha=date('Y-m-d');
$fecha = substr($protocolo->historiaclinica->agenda->fechaini,0,10);
//$hora = date('H:i:s');
$hora = substr($protocolo->historiaclinica->agenda->fechaini,11);
@endphp


<?php
$fechaNacimiento = new DateTime($protocolo->historiaclinica->paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($protocolo->historiaclinica->paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($protocolo->historiaclinica->paciente->fecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');
?>



<head>
    <title>ORDEN 12B</title>
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

    <main>

    <div class="box-body card" style="text-align: center; padding:0; margin:0;">
        @if(!is_null($protocolo->procedimiento->empresa))
            @if(!is_null($protocolo->procedimiento->empresa->logo_form))
            <p><img style="width:150px;height: 50px" src="{{base_path().'/storage/app/logo/'.$protocolo->procedimiento->empresa->logo_form}}" hspace=12></p>
            @else
            <p style="width:150px;height: 50px"><b>{{$protocolo->procedimiento->empresa->nombre_form}}</b></p>
            @endif
        @endif    
        </div>

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
                    <td class="text-center">{{$protocolo->procedimiento->historia->seguro->nombre}}</td>
                    <td colspan="2" class="text-center">{{ $protocolo->procedimiento->empresa->unicodigo ?? '' }}</td>
                    <td colspan="2" class="text-center">{{$protocolo->historiaclinica->agenda->empresa->nombrecomercial}}</td>
                    <td class="text-center" colspan="4">{{$protocolo->historiaclinica->paciente->id}}</td>
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
                    <td style="height: 18px;" class="text-center" colspan="2">{{$protocolo->historiaclinica->paciente->apellido1}}</td>
                    <td class="text-center" colspan="2">{{$protocolo->historiaclinica->paciente->apellido2}}</td>
                    <td class="text-center" colspan="2">{{$protocolo->historiaclinica->paciente->nombre1}}</td>
                    <td class="text-center" colspan="1">{{$protocolo->historiaclinica->paciente->nombre2}}</td>
                    <td class="text-center" colspan="1">@if($protocolo->historiaclinica->paciente->sexo == 1) Masculino @endif @if($protocolo->historiaclinica->paciente->sexo == 2) Femenino @endif</td>
                    <td class="text-center" colspan="2">{{$edad}}</td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1">{{$diaNacimiento}}</td>
                    <td class="text-center" colspan="1">{{$mesNacimiento}}</td>
                    <td class="text-center" colspan="1">{{ date('Y', strtotime($protocolo->historiaclinica->paciente->fecha_nacimiento))}}</td>
                </tr>
            </tbody>
        </table>
        <br>
        @php $especialidad = Sis_medico\Especialidad::find($protocolo->historiaclinica->agenda->espid) @endphp

        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="10" class="bg-blue"><b>B. DATOS DEL SERVICIO</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="bg-green text-center"><b>PROFESIONAL QUE REALIZA</b></td>
                    <td class="bg-green text-center"><b>FECHA DE REALIZACIÓN</b></td>
                    <td class="bg-green text-center"><b>PROFESIONAL SOLICITANTE</b></td>
                    <td colspan="6" class="bg-green text-center"><b>SERVICIO</b></td>
                    <td class="bg-green text-center"><b>ESPECIALIDAD</b></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td>EMERGENCIA</td>
                    <td></td>
                    <td>CONSULTA EXTERNA</td>
                    <td></td>
                    <td>HOSPITALIZACION</td>
                    <td></td>
                    <td class="text-center">{{$especialidad->nombre}}</td>

                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="21" style="font-size: 14px;" class="bg-blue"><b>C. ESTUDIO DE IMAGENOLOGÍA REALIZADO</b></td>
                </tr>
            </thead>
            <tr>
                <td>RX CONVENCIONAL</td>
                <td style="width:2%"></td>
                <td>RX PORTÁTIL</td>
                <td style="width:2%"></td>
                <td>TOMOGRAFÍA</td>
                <td style="width:2%"></td>
                <td>RESONANCIA</td>
                <td style="width:2%"></td>
                <td>ECOGRAFÍA</td>
                <td style="width:2%"></td>
                <td>MAMOGRAFÍA</td>
                <td style="width:2%"></td>
                <td>PROCEDIMIENTO</td>
                <td style="width:2%"></td>
                <td>OTRO</td>
                <td style="width:2%"></td>
                <td>SEDACIÓN</td>
                <td class="text-center" style="width:3%">SI</td>
                <td style="width:3%"></td>
                <td>NO</td>
                <td style="width:4%"></td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">DESCRIPCION</td>
                <td colspan="19" style="height: 20px;"></td>
            </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 14px;" class="bg-blue"><b>D. HALLAZGOS POR IMAGENOLOGÍA </b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border" style="width:14%; height: 70px;font-size: 12px !important;"> <?php echo ($protocolo->hallazgos) ?> </td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 14px;" class="bg-blue"><b>E. CONCLUSIONES Y SUGERENCIAS </b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border" style="width:14%; height: 70px;font-size: 12px !important;"><?php echo ($protocolo->conclusion) ?> </td>
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
                <td style="width:14%; height: 10px;" class="td-border text-center bg-green"><b>FECHA</b></td>
                <td style="width:8%; height: 10px;" class="td-border text-center bg-green"><b>HORA</b></td>
                <td style="width:20%" class="td-border text-center bg-green"><b>PRIMER NOMBRE</b></td>
                <td style="width:20%" class="td-border text-center bg-green"><b>PRIMER APELLIDO</b></td>
                <td style="width:15%" class="td-border text-center bg-green"><b>SEGUNDO APELLIDO</b></td>
            </tr>
            <tr>
                <td class="td-border text-center" style="width:5%; height: 18px;">{{$fecha}}</td>
                <td class="td-border text-center" style="width:5%; height: 18px;">{{$hora}}</td>
                <td class="td-border text-center" style="width:14%; height: 18px;">@if($firma != null) {{$firma->doctor->nombre1}} @endif</td>
                <td class="td-border text-center" style="width:14%; height: 18px;">@if($firma != null) {{$firma->doctor->apellido1}} @endif</td>
                <td class="td-border text-center" style="width:14%; height: 18px;">@if($firma != null) @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif @endif</td>
            </tr>
            <tr>
                <td colspan="2" class="td-border bg-green text-center"><b>NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
                <td colspan="2" class="td-border bg-green text-center"><b>FIRMA</b></td>
                <td class="td-border bg-green text-center"><b>SELLO</b></td>
            </tr>


            <tr>
                <td colspan="2" class="td-border text-center" style="width:12%; height:60px; font-size: 12px;"><b> @if($firma != null) {{ $firma->doctor->id }} @endif </b></td>
                <td colspan="2" class="text-center" style="height: 70px;">
                    <p style="text-align: center;"> 
                        @if(!is_null($firma) and $firma->firma != null)
                        <img width=150 height=60 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" style="" align=center hspace=12><br>
                        @else
                        <br><br><br>
                        <b style="font-size: 10px;"> @if($firma != null) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @endif</b><br>
                        @endif
                    </p>
                </td>
                <td class="text-center" style="height: 50px;">
                    @if($firma != null) @if($firma->sexo == '2') Dra. @else Dr. @endif  {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @if($firma->doctor->nombre2!='N/A'){{$firma->doctor->nombre2}}@endif <br>
                    @if(!is_null($especialidad)) {{$especialidad->nombre}} @endif<br>
                    Cedula No. {{$firma->doctor->id}} @endif
                </td>
            </tr>
            <tr>
                <td colspan="5"><b>La aproximación diagnóstica emitida en el presente informe, constituye tan solo una
                        prueba complementaria al diagnóstico clínico definitivo, motivo por el cual se recomienda
                        correlacionar con antecedentes clínicos/quirúrgicos, datos clínicos, exámenes de laboratorio
                        complementarios, así como seguimiento imagenológico del paciente.</b>
            </tr>
            <tr>
                <td colspan="2"><b>SNS-MSP/HCU-form.012B/2021</b></td>
                <td style="font-size: 14px;" colspan="3" class="text-right"><b>IMAGENOLOGÍA - INFORME</b></td>
            </tr>
            </tbody>
        </table>

    </main>

</body>

</html>