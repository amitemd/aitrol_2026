<!DOCTYPE html>
<html>

@php
$fecha=date('d');
$fecha1=date('m');
$fecha2=date('y');
$hora = date('H:i:s');
@endphp


<?php
$fechaNacimiento = new DateTime($hc_protocolo->historiaclinica->paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($hc_protocolo->historiaclinica->paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($hc_protocolo->historiaclinica->paciente->fecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');
?>


<!--?php
$fechaNacimiento = new DateTime($orden->pfecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($orden->pfecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($orden->pfecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');
?-->


<head>
    <meta charset="utf-8">
    <title>Formulario017</title>
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
    <footer><span>SNS-MSP / HCU-form.017 / 2021</span><span class="document-name">PROTOCOLO QUIRÚRGICO (<span class="page-number"></span>)</span></footer>
    <header>

    </header>
    <main>

        <table border="1" style="width: 99%">
            <tr>
                <td colspan="12">
                    <img src="{{base_path().'/storage/app/logo/cabecera.jpg'}}" style="width:750px;height: 100px;">
                </td>
            </tr>
        </table>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="14" style="font-size: 14px;" class="bg-blue"><b>111A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</b></td>
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
                    <td class="text-center" style="font-size: 14px;">IEES</td>
                    <td class="text-center" colspan="2" style="font-size: 14px;">026247</td>
                    <td colspan="2" class="text-center" style="font-size: 14px;">{{$hc_protocolo->historiaclinica->agenda->empresa->nombrecomercial}}</td>
                    <td class="text-center" colspan="4" style="font-size: 16px;">{{$hc_protocolo->historiaclinica->paciente->id}}</td>
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
                    <td class="text-center" colspan="2" style="height: 20px;">{{$hc_protocolo->historiaclinica->paciente->apellido1}}</td>
                    <td class="text-center" colspan="2">{{$hc_protocolo->historiaclinica->paciente->apellido2}}</td>
                    <td class="text-center" colspan="2">{{$hc_protocolo->historiaclinica->paciente->nombre1}}</td>
                    <td class="text-center" colspan="1">{{$hc_protocolo->historiaclinica->paciente->nombre2}}</td>
                    <td class="text-center" colspan="1">@if($hc_protocolo->historiaclinica->paciente->sexo == 1) Masculino @endif @if($hc_protocolo->historiaclinica->paciente->sexo == 2) Femenino @endif</td>
                    </td>
                    <td class="text-center" colspan="2">{{$edad}}</td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1">{{$diaNacimiento}}</td>
                    <td class="text-center" colspan="1">{{$mesNacimiento}}</td>
                    <td class="text-center" colspan="1">{{ date('Y', strtotime($hc_protocolo->historiaclinica->paciente->fecha_nacimiento))}}</td>
                </tr>
            </tbody>
        </table>
        <br>
        <br>


        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="3" class="bg-purple" style="font-size: 10;"><b>B. DIAGNÓSTICOS</b></td>
                    <td class="bg-purple text-center" style="width: 20%;"><b>CIE</b></td>
                </tr>
            </thead>
            <tbody>
                @php
                $contador = 0;
                @endphp

                @foreach ($cie10i as $key=>$val)
                @php
                //dd($cie10e);
                $c3 = Sis_medico\Cie_10_3::find($val->cie10);
                $c4 = Sis_medico\Cie_10_4::find($val->cie10);
                $texto = '';
                if(!is_null($c3)){
                $texto = $c3->descripcion;
                }
                if(!is_null($c4)){
                $texto = $c4->descripcion;
                }
                $contador++;
                @endphp

                <tr>

                    <td style="width: 12%; font-size: 8;" class="bg-green"><b>Pre Operatorio:</b></td>
                    <td class="text-center" style="width: 3%;">{{$contador}}</td>
                    <td style="width: 75%;">{{$texto}}</td>
                    <td class="text-center">{{$val->cie10}}</td>

                </tr>
                @endforeach

                @foreach ($cie10e as $key=>$val)
                @php
                //dd($cie10e);
                $c3 = Sis_medico\Cie_10_3::find($val->cie10);
                $c4 = Sis_medico\Cie_10_4::find($val->cie10);
                $texto = '';
                if(!is_null($c3)){
                $texto = $c3->descripcion;
                }
                if(!is_null($c4)){
                $texto = $c4->descripcion;
                }
                $contador++;
                @endphp
                <tr>
                <td style="width: 12%; font-size: 8;" class="bg-green"><b>Post Operatorio:</b></td>
                    <td class="text-center" style="width: 3%;">{{$contador}}</td>
                    <td style="width: 75%;">{{$texto}}</td>
                    <td class="text-center">{{$val->cie10}}</td>
                </tr>

                @endforeach
            </tbody>
        </table>
      
        <br>
        <br>


        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="4" class="bg-purple" style="font-size: 10;"><b>C. PROCEDIMIENTO</b></td>
                    <td class="bg-green">Electiva</td>
                    <td class="text-center">@if($cirugia->procedimiento == 'ELECTIVA') X @endif</td>
                    <td class="bg-green">Emergencia</td>
                    <td class="text-center">@if($cirugia->procedimiento == 'EMERGENCIA') X @endif</td>
                    <td class="bg-green">Urgencia</td>
                    <td class="text-center">@if($cirugia->procedimiento == 'URGENCIA') X @endif</td>
                </tr>
                <tr>
                    <td style="width: 12%; height: 3%; font-size: 8;" class="bg-green"><b>Proyectado:</b></td>
                    <td colspan="9">{{$cirugia->proyectado}}</td>
                </tr>

                <tr>
                    <td style="width: 12%; height: 3%; font-size: 8;" class="bg-green"><b>Realizado:</b></td>
                    <td colspan="9">{{$cirugia->realizado}}</td>
                </tr>

            </tbody>
        </table>
        <br>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="4" class="bg-purple" style="font-size: 10;"><b>D. INTEGRANTES DEL EQUIPO QUIRÚRGICO</b></td>
                </tr>
                <tr>
                    <td style="width: 12%;" class="bg-green"><b>Cirujano 1:</b></td>
                    <td>{{ optional($doctor)->nombre1 }}</td>
                    <td class="bg-green" style="width: 15%;"><b>Instrumentista: </b></td>
                    <td>{{$cirugia->intrumentista}}</td>
                </tr>
                <tr>
                    <td class="bg-green"><b>Cirujano 2:</b></td>
                    <td></td>
                    <td class="bg-green"><b>Circulante:</b></td>
                    <td>{{$cirugia->circulante}}</td>
                </tr>
                <tr>
                    <td class="bg-green"><b>Primer Ayudante:</b></td>
                    <td>{{$cirugia->ayudante1}}</td>
                    <td class="bg-green"><b>Anestesiologo/a:</b></td>
                    <td>{{$cirugia->anestesiologo}}</td>
                </tr>
                <tr>
                    <td class="bg-green"><b>Segundo Ayudante:</b></td>
                    <td>{{$cirugia->ayudante2}}</td>
                    <td class="bg-green"><b>Ayudante Anestesia:</b></td>
                    <td>{{$cirugia->ayudante_anestesia}}</td>
                </tr>
                <tr>
                    <td class="bg-green"><b>Tercer Ayudante:</b></td>
                    <td></td>
                    <td class="bg-green"><b>Otros:</b></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td class="bg-purple" style="font-size: 10; width: 50%;"><b> E. TIPO ANESTESIA</b></td>
                    <td class="bg-green text-center">General</td>
                    <td style="width: 3%;">@if($cirugia->tipo_anestesia == 'GENERAL') X @endif</td>
                    </td>
                    <td class="bg-green text-center">Regional</td>
                    <td style="width: 3%;">@if($cirugia->tipo_anestesia == 'REGIONAL') X @endif</td>
                    <td class="bg-green text-center">Sedación</td>
                    <td style="width: 3%;">@if($cirugia->tipo_anestesia == 'SEDACION') X @endif</td>
                    <td class="bg-green text-center">Otros</td>
                    <td style="width: 3%;">@if($cirugia->tipo_anestesia == 'OTROS') X @endif</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="7" class="bg-purple" style="font-size: 10; width: 50%;"><b>F. TIEMPOS QUIRÚRGICOS</b></td>
                </tr>
                <tr>
                    <td rowspan="2" colspan="2" class="text-center bg-green" style="width: 12%; height: 4%; font-size: 9;"><b>FECHA DE OPERACIÓN</b></td>
                    <td class="text-center bg-green">DIA</td>
                    <td class="text-center bg-green">MES</td>
                    <td class="text-center bg-green">AÑO</td>
                    <td class="text-center bg-green">HORA DE INICIO</td>
                    <td class="text-center bg-green" style="font-size: 7;">HORA DE TERMINACION</td>
                </tr>
                <tr>
                    <td class="text-center">{{$fecha}}</td>
                    <td class="text-center">{{$fecha1}}</td>
                    <td class="text-center">{{$fecha2}}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="height: 18px; font-size: 10;" class="bg-green"><b>Dieresis:</b></td>
                    <td colspan="6">{{$cirugia->dieresis}}</td>
                </tr>
                <tr>
                    <td style="height: 18px;" colspan="7">&nbsp;</td>
                </tr>
                <tr>
                    <td style="height: 18px;  font-size: 10;" class="bg-green"><b>Exposición y Exploración:</b></td>
                    <td colspan="6">{{$cirugia->exploracion}}</td>
                </tr>
                <tr>
                    <td style="height: 18px;" colspan="7">&nbsp;</td>
                </tr>
                <tr>
                    <td style="height: 18px;  font-size: 10;" class="bg-green"><b>Hallazgos Quirúrgicos:</b></td>
                    <td colspan="6"></td>
                </tr>
                <tr>
                    <td colspan="7" style="height: 7%; font-size: 10"><?php echo ($hc_protocolo->hallazgos) ?></td>
                </tr>
                <tr>
                    <td style="height: 18px; width: 12%;  font-size: 10;" class="bg-green"><b>Procedimiento Quirúrgico:</b></td>
                    <td colspan="6"></td>
                </tr>
                <tr>
                    <td colspan="7" style="height: 7%; font-size: 10"><?php echo optional($hc_protocolo)->conclusion ?? '' ?></td>
                </tr>
            </tbody>
        </table>

        <p class="page-break-before">

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td class="bg-purple" style="font-size: 10; width: 50%;"><b>G. COMPLICACIONES DEL PROCEDIMIENTO QUIRÚRGICO</b></td>
                </tr>
                <tr>
                    <td style="height: 75px; font-size: 10">{{$cirugia->complicaciones}}</td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="height: 3;"></td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td class="bg-green td-border text-center" style="font-size: 11;">Pérdida Sanguínea total: </td>
                    <td style="width: 20;" class="text-center">&nbsp;</td>
                    <td class="td-border text-center" style="width: 12%;">{{$cirugia->perdida_sangre}}</td>
                    <td style="width: 20; font-size: 11;" class="text-center">ml</td>
                    <td class="td-border" style="width: 5%;"></td>
                    <td style="width: 1%;"></td>
                    <td style="width: 10%; font-size: 11;" class="bg-green td-border text-center">Muestra:</td>
                    <td class="td-border">{{$cirugia->muestra1}}</td>
                </tr>
            </tbody>
        </table>
        <table>
            <tbody>
                <td style="height: 3;"></td>
            </tbody>
        </table>
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="height: 20px; font-size: 12;" colspan="15" class="td-border">Descripción: {{$cirugia->descripcion}}</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="3" class="bg-purple td-border" style="font-size: 12;"><b>H. EXÁMENES HISTOPATOLÓGICOS</b></td>
                </tr>
                <tr>
                    <td class="bg-green td-border" style="font-size: 12; width: 8%;">Transquirúrgico:</td>
                    <td colspan="2" class="td-border">{{$cirugia->transquirurgico}}</td>
                </tr>
                <tr>
                    <td colspan="3" style="height: 2;"></td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td class="bg-green td-border text-center" style="font-size: 11; width: 25%; ">Biopsia por congelación: </td>
                    <td style="width: 20; font-size: 11;" class="text-center">SI</td>
                    <td class="td-border text-center" style="width: 12%;">@if($cirugia->bioxsia == 0) X @endif</td>
                    <td style="width: 20; font-size: 11;" class="text-center">NO</td>
                    <td class="td-border text-center" style="width: 5%;">@if($cirugia->bioxsia == 1) X @endif</td>
                    <td style="width: 1%;"></td>
                    <td style="width: 10%; font-size: 11;" class="bg-green td-border text-center">Resultado:</td>
                    <td class="td-border">{{$cirugia->resultado}}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="5" style=" font-size: 10;">Patólogo que reporta:</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bg-green td-border text-center" style="font-size: 11; width: 25%; ">Histopatológico: </td>
                    <td style="width: 20; font-size: 11;" class="text-center">SI</td>
                    <td class="td-border text-center" style="width: 12%;">@if($cirugia->histopatologico == 1) X @endif</td>
                    <td style="width: 20; font-size: 11;" class="text-center">NO</td>
                    <td class="td-border text-center" style="width: 5%;">@if($cirugia->histopatologico == 0) X @endif</td>
                    <td style="width: 1%;"></td>
                    <td style="width: 10%; font-size: 11;" class="bg-green td-border text-center">Muestra:</td>
                    <td class="td-border">{{$cirugia->muestra}}</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td class="bg-purple" style="font-size: 11;"><b>I. DIAGRAMA DEL PROCEDIMIENTO</b></td>
                </tr>
                <tr>
                    <td style="height: 200px;" class="text-center">
                       @if(!is_null($cirugia->archivo))
                        <img src="{{base_path().'/storage/app/avatars/'.$cirugia->archivo}}" style="width: 200px; height: 150px">
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="4" class="bg-purple" style="font-size: 11;"><b>J. DATOS DEL PROFESIONAL RESPONSABLE</b></td>
                </tr>
                <tr>
                    <td style="font-size: 9;" class="text-center bg-green"><b>NOMBRE Y APELLIDOS</b></td>
                    <td style="font-size: 9;" class="text-center bg-green"><b>ESPECIALIDAD</b></td>
                    <td style="font-size: 9; width: 28%;" class="text-center bg-green"><b>FIRMA</b></td>
                    <td style="font-size: 8;" class="text-center bg-green"><b>SELLO Y NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
                </tr>
                <tr>
                    @php
                    $nombreDoctor = $doctor
                        ? trim($doctor->nombre1 . " " . $doctor->nombre2 . " " . $doctor->apellido1 . " " . $doctor->apellido2)
                        : '';
                    @endphp

                    <td style="height: 50px;" class="text-center">{{$nombreDoctor}}</td>
                    <td style="height: 50px;" class="text-center">{{ optional($doctor)->ocupacion }}</td>
                    <td class="text-center">
                        @php
                            $firma = $doctor
                                ? Sis_medico\Firma_Usuario::where('id_usuario', $doctor->id)->first()
                                : null;
                        @endphp
                        @if(!is_null($firma))
    					   <img width=170 height=80 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center hspace=12><br>
                        @endif
                    </td>
                    <td style="height: 50px;"></td>
                </tr>
            </tbody>
        </table>
    </main>
</body>
</html>
