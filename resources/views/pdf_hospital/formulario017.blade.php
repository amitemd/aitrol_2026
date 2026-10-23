<!DOCTYPE html>
<html>

@php
//$fecha=date('d');
//$fecha = substr($hc_procedimiento->historia->agenda->fechaini,8,2);
$fecha = substr($protocolo->fecha_operacion,8,2);
//$fecha1=date('m');

//$fecha1=substr($hc_procedimiento->historia->agenda->fechaini,5,2);
$fecha1=substr($protocolo->fecha_operacion,5,2);
//$fecha2=date('y');
//$fecha2=substr($hc_procedimiento->historia->agenda->fechaini,0,4);
$fecha2=substr($protocolo->fecha_operacion,0,4);
//$hora = date('H:i:s');
//$hora = substr($hc_procedimiento->historia->agenda->fechaini,11);
$hora = substr($protocolo->fecha_operacion,11);
@endphp


<?php

$fechaNacimiento = new DateTime($hc_procedimiento->historia->paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($hc_procedimiento->historia->paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($hc_procedimiento->historia->paciente->fecha_nacimiento);
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
    <title>HCU_017</title>
    <style>
        @page {
            margin: 5 0;
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
    <main>
        <!--div class="box-body card" style="text-align: center">
			@if(!is_null($empresa))
				<img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:250px;height: 75px">
			@endif
		</div><br-->

        <div class="box-body card" style="text-align: center; padding:0; margin:0;">
        @if(!is_null($hc_procedimiento->empresa))
            @if(!is_null($hc_procedimiento->empresa->logo_form))
            <p><img style="width:150px;height: 50px" src="{{base_path().'/storage/app/logo/'.$hc_procedimiento->empresa->logo_form}}" hspace=12></p>
            @else
            <p style="width:150px;height: 50px"><b>{{$hc_procedimiento->empresa->nombre_form}}</b></p>
            @endif
        @endif    
        </div>

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
                    <td class="text-center" style="font-size: 14px;">{{$hc_procedimiento->historia->seguro->nombre}}</td>
                    <td class="text-center" colspan="2" style="font-size: 14px;">@isset($hc_procedimiento->empresa){{$hc_procedimiento->empresa->unicodigo}}@endif</td>
                    <td colspan="2" class="text-center" style="font-size: 14px;">@isset($hc_procedimiento->empresa){{$hc_procedimiento->empresa->nombrecomercial}}@endif</td>
                    <td class="text-center" colspan="4" style="font-size: 16px;">{{$hc_procedimiento->historia->paciente->id}}</td>
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
                    <td class="text-center" colspan="2" style="height: 20px;">{{$hc_procedimiento->historia->paciente->apellido1}}</td>
                    <td class="text-center" colspan="2">{{$hc_procedimiento->historia->paciente->apellido2}}</td>
                    <td class="text-center" colspan="2">{{$hc_procedimiento->historia->paciente->nombre1}}</td>
                    <td class="text-center" colspan="1">{{$hc_procedimiento->historia->paciente->nombre2}}</td>
                    <td class="text-center" colspan="1">@if($hc_procedimiento->historia->paciente->sexo == 1) Masculino @else Femenino @endif</td>
                    </td>
                    <td class="text-center" colspan="2">{{$edad}}</td>
                    <td class="text-center" colspan="1"></td>
                    <td class="text-center" colspan="1">{{$diaNacimiento}}</td>
                    <td class="text-center" colspan="1">{{$mesNacimiento}}</td>
                    <td class="text-center" colspan="1">{{ date('Y', strtotime($hc_procedimiento->historia->paciente->fecha_nacimiento))}}</td>
                </tr>
            </tbody>
        </table>
        <br>

        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="3" class="bg-purple" style="font-size: 10;"><b>B. DIAGNÓSTICOS</b></td>
                    <td class="bg-purple text-center" style="width: 20%;"><b>CIE</b></td>
                </tr>
            </thead>
            <tbody>
                @php $cont = 0; @endphp
                @foreach ($cie10 as $key=>$val)
                    @php
                        $cont ++;
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
               
                    <tr>
                        <td style="width: 12%; font-size: 8;" class="bg-green"><b>Pre Operatorio:</b></td>
                        <td class="text-center" style="width: 3%;">{{ $cont }}</td>
                        <td>{{$texto}}</td>
                        <td class="text-center td-border">{{$val->cie10}}</td>
                    </tr>
                
                @endforeach
                @foreach ($cie10_eg as $key=>$val)
                    @php
                        $cont ++;
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
               
                    <tr>
                        <td style="width: 12%; font-size: 8;" class="bg-green"><b>Post Operatorio:</b></td>
                        <td class="text-center" style="width: 3%;">{{ $cont }}</td>
                        <td>{{$texto}}</td>
                        <td class="text-center td-border">{{$val->cie10}}</td>
                    </tr>
                
                @endforeach

            </tbody>
        </table>

        <br>



        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="4" class="bg-purple" style="font-size: 10;"><b>C. PROCEDIMIENTO</b></td>
                    <td class="bg-green">Electiva</td>
                    <td class="text-center"></td>
                    <td class="bg-green">Emergencia</td>
                    <td class="text-center"></td>
                    <td class="bg-green">Urgencia</td>
                    <td class="text-center"></td>
                </tr>
                <tr>
                    <td style="width: 12%; height: 3%; font-size: 8;" class="bg-green"><b>Proyectado:</b></td>
                    @php
                        $observacionesConcatenadas = "";
                    
                        $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $hc_procedimiento->id)->get();

                        $mas = true;
                        $texto = "";
                        foreach($adicionales as $value2)
                        {
                            if($mas == true){
                                $texto = $texto.$value2->procedimiento->nombre ;
                                $mas = false;
                            }
                            else{
                                $texto = $texto.' + '. $value2->procedimiento->nombre ;
                            }
                        }
                        if($texto == ""){
                            {{--$procedimiento_nombre_2 = \Sis_medico\procedimiento_completo::find($procedimiento->id_procedimiento_completo);
                            if(!is_null($procedimiento_nombre_2)){
                                $texto = $procedimiento_nombre_2->nombre_general;
                            }--}}
                        }
                    @endphp

                    @foreach ($pentax->procedimientos as $procedimiento)
                        @php
                            $observacion = $procedimiento->procedimiento->observacion;
                            $observacionesConcatenadas .= ($observacion . ', ');
                        @endphp
                    @endforeach
                    <!--td colspan="9">
                        {{ rtrim($observacionesConcatenadas, ', ') }}
                    </td-->
                    <td colspan="9">
                        {{$texto}}
                    </td>
                    
                </tr>

                <tr>
                    <td style="width: 12%; height: 3%; font-size: 8;" class="bg-green"><b>Realizado:</b></td>
                    

                    <td colspan="9">@if($protocolo_original != null) {{$protocolo->ntxt_procedimiento}}  @else {{$texto}} @endif </td>
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

                    <td>
                        @if(!is_null($firma)) 
                            @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A') {{$firma->doctor->apellido2}} @endif {{$firma->doctor->nombre1}} @if($firma->doctor->nombre2!='N/A') {{$firma->doctor->nombre2}} @endif 
                        @endif
                    </td>
                    <td class="bg-green" style="width: 15%;"><b>Instrumentista: </b></td>
                    <td></td>
                </tr>
                @if($xtipo == 'auditoria') 
                    @php 
                        $cirujano2 = $protocolo->auditoria_procedimiento->doctor_ayudante; 
                        $doctor_ayudante = $protocolo->auditoria_procedimiento->doctor_ayudante;
                    @endphp
                @else 
                    @php 
                        $cirujano2 = $protocolo->procedimiento->doctor_ayudante;
                        $doctor_ayudante = $protocolo->procedimiento->doctor_ayudante; 
                    @endphp 
                @endif 
                
                <tr> 
                    <td class="bg-green"><b>Cirujano 2:</b></td>
                    <td>  
                         </td>
                    <td class="bg-green"><b>Circulante:</b></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bg-green"><b>Primer Ayudante:</b></td>
                    
                    <td>@if($doctor_ayudante != null) Dr(a). {{$doctor_ayudante->apellido1}} @if($doctor_ayudante->apellido2!='N/A'){{$doctor_ayudante->apellido2}}@endif {{$doctor_ayudante->nombre1}} @if($doctor_ayudante->nombre2!='N/A'){{$doctor_ayudante->nombre2}}@endif @endif</td>
                    
                       
                    <td class="bg-green"><b>Anestesiologo/a:</b></td>
                    @if(!is_null($protocolo->usuario_anestesiologo))
                    <td>Dr(a). {{$protocolo->usuario_anestesiologo->apellido1}} @if($protocolo->usuario_anestesiologo->apellido2!='N/A'){{$protocolo->usuario_anestesiologo->apellido2}}@endif {{$protocolo->usuario_anestesiologo->nombre1}} @if($protocolo->usuario_anestesiologo->nombre2!='N/A'){{$protocolo->usuario_anestesiologo->nombre2}}@endif</td>
                    @else
                    <td></td>
                    @endif
                </tr>
                <tr>
                    <td class="bg-green"><b>Segundo Ayudante:</b></td>
                    <td></td>
                    <td class="bg-green"><b>Ayudante Anestesia:</b></td>
                    <td></td>
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
                    <td style="width: 3%;" class="text-center">@if($protocolo->tipo_anestesia=='GENERAL') <b>X</b> @endif </td>
                    </td>
                    <td class="bg-green text-center">Regional</td>
                    <td style="width: 3%;"></td>
                    <td class="bg-green text-center">Sedación</td>
                    <td style="width: 3%;" class="text-center">@if($protocolo->tipo_anestesia=='SEDACION') <b>X</b> @endif</td>
                    <td class="bg-green text-center">Otros</td>
                    <td style="width: 3%;"></td>
                </tr>
            </tbody>
        </table>
        <br>

        @php
        $fecha = $protocolo->fecha_operacion;
        //$fecha = substr($hc_procedimiento->historia->agenda->fechaini,0,10);
        $fechaObj = new DateTime($fecha);
        $anio = $fechaObj->format('Y');
        $mes = $fechaObj->format('m');
        $dia = $fechaObj->format('d');
        @endphp


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
                    <td class="text-center">{{$dia}}</td>
                    <td class="text-center">{{$mes}}</td>
                    <td class="text-center">{{$anio}}</td>
                    <td class="text-center">@if($protocolo->id == 37109) 09:30 @else {{$protocolo->hora_inicio}} @endif</td>
                    <td class="text-center">@if($protocolo->id == 37109) 10:07 @else {{$protocolo->hora_fin}} @endif</td>
                </tr>
                <tr>
                    <td style="height: 18px; font-size: 10;" class="bg-green"><b>Dieresis:</b></td>
                    <td colspan="6"></td>
                </tr>
                <tr>
                    <td style="height: 18px;" colspan="7">&nbsp;</td>
                </tr>
                <tr>
                    <td style="height: 18px;  font-size: 10;" class="bg-green"><b>Exposición y Exploración:</b></td>
                    <td colspan="6"></td>
                </tr>
                <tr>
                    <td style="height: 18px;" colspan="7">&nbsp;</td>
                </tr>
                <tr>
                    <td style="height: 18px;  font-size: 10;" class="bg-green"><b>Hallazgos Quirúrgicos:</b></td>
                    <td colspan="6"></td>
                </tr>
                <tr>
                    <td colspan="7" style="font-size: 9px !important">@if(!is_null($protocolo))<?php echo $protocolo->conclusion ?>@endif</td>
                </tr>
                <tr>
                    <td style="height: 18px; width: 12%;  font-size: 10;" class="bg-green"><b>Procedimiento Quirúrgico:</b></td>
                    <td colspan="6"></td>
                </tr>
                <tr>
                    <td colspan="7" style="font-size: 9px !important;"><?php echo $protocolo->hallazgos ?></td>
                </tr>
            </tbody>
        </table>
        <div>
            <footer>
                <p>@for($count=0; $count<=10;$count++) &nbsp; @endfor SNS-MSP/HCU-form. 017/2021 @for($count=0; $count<=54;$count++) &nbsp; <b> @endfor PROTOCOLO QUIRÚRGICO (1) </b></p>
                <footer>
        </div>

        <p class="page-break-before">



        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td class="bg-purple" style="font-size: 10; width: 50%;"><b>G. COMPLICACIONES DEL PROCEDIMIENTO QUIRÚRGICO</b></td>
                </tr>
                <tr>
                    <td class="" style="font-size: 12px;">NO</td>
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
                    <td class="td-border text-center" style="width: 12%;"></td>
                    <td style="width: 20; font-size: 11;" class="text-center">ml</td>
                    <td class="td-border" style="width: 5%;"></td>
                    <td style="width: 1%;"></td>
                    <td style="width: 10%; font-size: 11;" class="bg-green td-border text-center">Muestra:</td>
                    <td class="td-border"></td>
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
                    <td style="height: 20px; font-size: 12;" colspan="15" class="td-border">Descripción:&nbsp;&nbsp;@if($protocolo_original != null) {{$protocolo_original->descripcion}} @else {{$protocolo->descripcion}} @endif</td>
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
                    <td colspan="2" class="td-border"></td>
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
                    <td class="td-border text-center" style="width: 12%;"></td>


                    <td style="width: 20; font-size: 11;" class="text-center">NO</td>
                    <td class="td-border text-center" style="width: 5%;"></td>
                    <td style="width: 1%;"></td>
                    <td style="width: 10%; font-size: 11;" class="bg-green td-border text-center">Resultado:</td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="5" style=" font-size: 10;">Patólogo que reporta:</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bg-green td-border text-center" style="font-size: 11; width: 25%; ">Histopatológico: </td>
                    <td style="width: 20; font-size: 11;" class="text-center">SI</td>
                    <td class="td-border text-center" style="width: 12%;">@if($orden_biopsias->count() > 0) X @endif</td>
                    <td style="width: 20; font-size: 11;" class="text-center">NO</td>
                    <td class="td-border text-center" style="width: 5%;"> @if($orden_biopsias->count() <= 0) X @endif </td>
                    <td style="width: 1%;"></td>
                    <td style="width: 10%; font-size: 11;" class="bg-green td-border text-center">Muestra</td>
                    <td class="td-border">;@if($protocolo_original != null) {{$protocolo_original->muestra}} @else {{$protocolo->muestra}} @endif</td>
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
                    <td style="height: 125px; width: 100%; text-align: center; vertical-align: middle ;"> 
                        @if ($imagen && $imagen->imagen_url !== null)
                        <img style="height: 100px; margin: 15px; display: inline-block;" src="{{ public_path().'/imagenes/' . $imagen->imagen_url }}"/>
                        @else
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
                    @php $especialidad = Sis_medico\Especialidad::find($hc_procedimiento->historia->agenda->espid) @endphp
                    <td style="height: 50px;" class="text-center"> @if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @if($firma->doctor->nombre2!='N/A'){{$firma->doctor->nombre2}}@endif @endif</td>
                    <td style="height: 50px;" class="text-center">@if(!is_null($especialidad)) {{$especialidad->nombre}} @endif</td>
                    <td class="text-center" style="height: 50px;">
                        <p style="text-align: center;">
                            @if(!is_null($firma) && !is_null($firma->firma))
                            <img width=150 height=60 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" style="" align=center hspace=12><br>
                            @else
                            <br><br><br>
                            <b style="font-size: 10px;">@if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @endif</b><br>
                            @endif
                        </p>
                    </td>
                    <td class="text-center" style="height: 50px;">
                        @if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @if($firma->doctor->nombre2!='N/A'){{$firma->doctor->nombre2}}@endif<br>
                        @if(!is_null($especialidad)) {{$especialidad->nombre}} @endif <br>
                        cedula No. {{$firma->doctor->id}} @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <div>
            <footer>
                <p>@for($count=0; $count<=10;$count++) &nbsp; @endfor SNS-MSP/HCU-form. 017/2021 @for($count=0; $count<=80;$count++) &nbsp; @endfor <b> PROTOCOLO QUIRÚRGICO (2) <b></b></p>
                <footer>
        </div>