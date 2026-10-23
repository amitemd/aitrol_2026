<!DOCTYPE html>
<html>


<head>
    <title>INFORME TECNICO</title>
    <style>
        @page {
            margin: 0 0;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin-top: 5px;
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
        <table style="width: 100%">
            <tr>
                <td style="text-align: center;">
                    @if(!is_null($hc_id_procedimiento->empresa))
                    <p><img src="{{base_path().'/storage/app/logo/'.$hc_id_procedimiento->empresa->logo_form}}" style="width:150px;height: 50px"></p>
                    @endif
                </td>
            </tr>
        </table>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td rowspan="2" style="width: 45%; height: 47px;" class="text-center"><b>SERVICIO GASTROENTEROLOGIA</b></td>
                    <td style="width: 10%;" class="text-center">Fecha de Elaboración:</td>
                    <td colspan="2" style="width: 45%;" class="text-center">{{ substr($archivo_plano->fecha_ing, 0, 10)}}</td>
                </tr>
                <tr>
                    <td style="width: 10%;" class="text-center">Área-Proceso:</td>
                    <td colspan="2" style="width: 45%;" class="text-center">
                        <p style="color: red;">AUDITORIA MEDICA</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="text-center" style=" height: 32px;"><b>INFORME TÉCNICO-MÉDICO PARA LA JUSTIFICACIÓN DE PRESTACIONES ADICIONALES A LAS AUTORIZADAS</b></td>
                    <td class="text-center">Página: 1</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="2" style="width: 45%; " class="text-center"><b>DATOS GENERALES</b></td>
                </tr>
                <tr>
                    <td>NOMBRES Y APELLIDOS DE PACIENTE:</td>
                    <td class="text-center"> {{ $archivo_plano->paciente->nombre1.' '.$archivo_plano->paciente->nombre2.' '.$archivo_plano->paciente->apellido1.' '.$archivo_plano->paciente->apellido2}}</td>
                </tr>
                <tr>
                    <td>CÉDULA DE IDENTIDAD:</td>
                    <td class="text-center"> {{$archivo_plano->id_paciente}}</td>
                </tr>
                <tr>
                    <td>CÓDIGO DE VALIDACIÓN QUE AUTORIZÓ PRESTACIÓN:</td>
                    <td class="text-center"> {{$archivo_plano->codigo_iess}} </td>
                </tr>
                <tr>
                    <td>FECHA DE INGRESO DEL PACIENTE:</td>
                    <td class="text-center">{{ substr($archivo_plano->fecha_ing, 0, 10)}}</td>
                </tr>
                <tr>
                    <td>MÉDICO SOLICITANTE:</td>
                    <td class="text-center"> @if($hc_id_procedimiento->doctor_firma != null) {{ $hc_id_procedimiento->doctor_firma->apellido1.' '.$hc_id_procedimiento->doctor_firma->apellido2.' '.$hc_id_procedimiento->doctor_firma->nombre1.' '.$hc_id_procedimiento->doctor_firma->nombre2}} @endif</td>
                </tr>
                <tr>
                    <td>ESPECIALIDAD / SUBESPECIALIDAD:</td>
                    @if(!is_null($hc_id_procedimiento))
                    @php
                    $especialidad = null;$txt_esp = '';
                    if($hc_id_procedimiento->doctor_firma != null){
                        $uesp = Sis_medico\User_espe::where('usuid', $hc_id_procedimiento->doctor_firma->id)->first();
                        if(!is_null($uesp)){
                            $especialidad = Sis_medico\Especialidad::find($uesp->espid);
                            if(!is_null($especialidad)){
                                $txt_esp = $especialidad->nombre;
                            }
                        }
                    }
                    @endphp

                    @endif
                    <td class="text-center">{{ $txt_esp }}</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="2" style="width: 45%; " class="text-center"><b>DIAGNÓSTICOS (CIE-10):</b></td>
                </tr>
                <tr>
                    <td>DIAGNÓSTICO(S) QUE JUSTIFICARON EL CÓDIGO DE VALIDACIÓN:</td>
                    @php
                    $cie10 = Sis_medico\Cie_10_3::where('id',$archivo_plano->cie10)->where('estado','1')->first();
                    if(is_null($cie10)){
                    $cie10 = Sis_medico\Cie_10_4::where('id',$archivo_plano->cie10)->where('estado','1')->first();
                    }
                    @endphp
                    <td>{{ $archivo_plano->cie10 }} @if(!is_null($cie10)) {{ $cie10->descripcion }} @endif </td>
                </tr>
                @php
                    if(isset($cie10_eg[0])){
                        $c10 = $cie10_eg[0];
                        $c3 = Sis_medico\Cie_10_3::find($c10->cie10);
                        $c4 = Sis_medico\Cie_10_4::find($c10->cie10);

                        $texto_2 = '';
                        if(!is_null($c3)){
                            $texto_2 = $c3->descripcion;
                        }
                        if(!is_null($c4)){
                            $texto_2 = $c4->descripcion;
                        }
                    }
    
                    
                    
                @endphp
                <tr>
                    <td>DIGANÓSTICOS ACTUALES QUE JUSTIFICARÁN LA PRESTACIÓN ADICIONAL:</td>
                    <td>@if(isset($cie10_eg[0])){{ $c10->cie10 }} {{ $texto_2 }} @endif</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td class="text-center"><b>CUADRO CLÍNICO DEL PACIENTE QUE JUSTIFICARÁ PRESTACIÓN ADICIONAL</b></td>
                </tr>
                @php
                    if(isset($cie10_in[0])){
                        $c10_in = $cie10_in[0];
                        $c3_in = Sis_medico\Cie_10_3::find($c10_in->cie10);
                        $c4_in = Sis_medico\Cie_10_4::find($c10_in->cie10);

                        $texto_2_in = '';
                        if(!is_null($c3_in)){
                            $texto_2_in = $c3_in->descripcion;
                        }
                        if(!is_null($c4_in)){
                            $texto_2_in = $c4_in->descripcion;
                        }
                    }
                @endphp    
                <tr>
                    <td class="" style="font-size: 10px;"> 
                        @if(!is_null($evolucion)) 
                            {{ strip_tags($evolucion->cuadro_clinico) }} 
                        @endif
                        <br>Diagnostico: 
                            @if(isset($cie10_eg[0])){{ $c10->cie10 }} {{ $texto_2 }} @endif
                        <br>Conclusion:
                        @if(!is_null($protocolo))
                            {{ strip_tags($protocolo->conclusion) }}
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="6" style="width: 45%; " class="text-center"> PROTOCOLOS UTILIZADOS QUE JUSTIFICAN LA PRESTACIÓN ADICIONAL REALIZADA:</td>
                </tr>
                <tr>
                    <td class="text-center" rowspan="2" style="width: 5%;">NO.</td>
                    <td class="text-center" rowspan="2" style="width: 23%;">PROTOCOLOS</td>
                    <td class="text-center" colspan="3">TIPO * (SELECCIONE CON UNA X)</td>
                    <td class="text-center" rowspan="2" style="width: 30%;">OBERVACIONES</td>
                </tr>
                <tr>
                    <td class="text-center">NAC</td>
                    <td class="text-center">INTERN.</td>
                    <td class="text-center">MBE</td>
                </tr>
               
                @php $cont = 0; @endphp
                @foreach($protos as $proto)
                @php $cont = $cont + 1; @endphp
                <tr>
                    <td class="text-center">{{ $cont }}</td>
                    <td class="text-center">{{ $proto->maestro_codigo->protocolos }}</td>
                    <td class="text-center">@if($proto->nac == '1') X @endif</td>
                    <td class="text-center">@if($proto->intern == '1') X @endif</td>
                    <td class="text-center">@if($proto->mbe == '1') X @endif</td>
                    <td class="text-center">{{ $proto->observaciones }}</td>
                </tr>
                @endforeach
                
              
                @foreach($tarifario as $value)
                @php
                $cont = $cont + 1;
                @endphp
                <tr>
                    <td class="text-center">{{ $cont }}</td>
                    <td class="text-center">{{ $value->protocolos }}</td>
                    <td class="text-center">@if($value->nac == '1') X @endif</td>
                    <td class="text-center">@if($value->intern == '1') X @endif</td>
                    <td class="text-center">@if($value->mbe == '1') X @endif</td>
                    <td class="text-center">{{ $value->observaciones }}</td>
                </tr>

                @endforeach
                <tr>
                    <td colspan="6">*TIPO: NACIONALES (NAC), INTERNACIONALES (INTERN), BUENAS PRACTICAS MÉDICAS BASADAS EN MEDICNA BASADA EN EVIDENCIA (MBE)</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="4" style="width: 45%; " class="text-center">PROCEDIMIENTOS ADICIONALES AL AUTORIZADO QUE SERÁN JUSTIFICADOS CON ESTE INFORME:</td>
                </tr>
                <tr>
                    <td class="text-center">NO.</td>
                    <td class="text-center">COD. TARIFARIO</td>
                    <td colspan="2" class="text-center">DESCRIPCIÓN</td>
                </tr>
                @php $cont = 0; @endphp
                @foreach($detalles as $detalle)
                @php $cont = $cont + 1; @endphp
                <tr>
                    <td class="text-center">
                        {{ $cont }}
                    </td>
                    <td class="text-center">
                        {{ $detalle->codigo }}
                    </td>
                    <td colspan="2" class="text-center">
                        {{ $detalle->descripcion }}
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="4" style="text-align: justify;">Nota: Este informe no justifica la prescripción de medicamentos fuera del Cuadro Nacional de Medicamentos Básicos sin la autorización debida según la normativa legal vigente para el caso, Homologaciones de procedimientos y/o laboratorios no autorizadas por el Ministerio de Salud Pública, facturación indebida de acuerdo al Tarifario de Prestaciones de Salud pertinente y otras situaciones de igual similitud.</td>
                </tr>
                <tr>
                    <td class="text-center"></td>
                    <td class="text-center"><b>NOMBRE</b></td>
                    <td class="text-center"><b>CARGO/ESPECIALIDAD</b></td>
                    <td class="text-center"><b>FIRMA Y SELLO</b></td>
                </tr>
                <tr>
                    <td class="text-center">Elaborado por:</td>
                    <td class="text-center">@if(!is_null($hc_id_procedimiento)) @if($hc_id_procedimiento->doctor_firma != null) {{ $hc_id_procedimiento->doctor_firma->apellido1}} {{ $hc_id_procedimiento->doctor_firma->apellido2}} {{ $hc_id_procedimiento->doctor_firma->nombre1}} {{ $hc_id_procedimiento->doctor_firma->nombre2}} @endif @endif</td>
                    <td class="text-center">{{ $txt_esp }}</td>
                    @php
                    $firma = null;
                    if(!is_null($hc_id_procedimiento->doctor_firma)){

                    $firma = Sis_medico\Firma_Usuario::where('id_usuario', $hc_id_procedimiento->doctor_firma->id)->first();
                    }
                    @endphp
                    <td class="text-center">
                        @if(!is_null($firma) && !is_null($firma->firma))
                        <img width=120 height=40 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" style="" align=center hspace=12><br>
                        @if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @if($firma->doctor->nombre2!='N/A'){{$firma->doctor->nombre2}}@endif<br>
                        {{ $txt_esp }} <br>
                        cedula No. {{$firma->doctor->id}} @endif
                        @else
                        <br><br><br>
                        <b style="font-size: 10px;">@if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @endif</b><br>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </main>
</body>

</html>