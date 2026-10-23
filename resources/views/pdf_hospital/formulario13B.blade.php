<!DOCTYPE html>
<html>
<head>
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
        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    @foreach ($biopsias as $key => $biopsia)
      @php
        if(!is_null($biopsia->id_responsable)){
            $doctor = Sis_medico\User::find($biopsia->id_responsable);
            $firma = Sis_medico\Firma_Usuario::where('id_usuario', $doctor->id)->first();
        }else{
            $doctor = null;
            $firma = null;
        }
      @endphp  
      @php
      $fecha_chacal =  new DateTime($biopsia->updated_at);
      $fecha=$fecha_chacal->format('Y-m-d');
      $hora = $fecha_chacal->format('H:i:s');
      @endphp
      
      <?php
      $fechaNacimiento = new DateTime($biopsia->historia->paciente->fecha_nacimiento);
      $hoy = new DateTime();
      $edad = $hoy->diff($fechaNacimiento)->y;
      
      $fechaNacimiento = new DateTime($biopsia->historia->paciente->fecha_nacimiento);
      $diaNacimiento = $fechaNacimiento->format('d');
      
      $fechaNacimiento = new DateTime($biopsia->historia->paciente->fecha_nacimiento);
      $mesNacimiento = $fechaNacimiento->format('m');
      ?>
        <main>
            <div class="box-body card" style="text-align: center; padding:0; margin:0;">
                @if(!is_null($biopsia->historia->agenda->empresa))
                @if(!is_null($biopsia->historia->agenda->empresa->logo_form))
                <p><img style="width:150px;height: 50px" src="{{base_path().'/storage/app/logo/'.$biopsia->historia->agenda->empresa->logo_form}}" hspace=12></p>
                @else
                <p style="width:150px;height: 50px"><b>{{$biopsia->historia->agenda->empresa->nombre_form}}</b></p>
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
                        <td class="text-center" style="font-size: 14px;">{{$biopsia->historia->agenda->seguro->nombre}}</td>
                        <td colspan="2" class="text-center" style="font-size: 14px;">@isset($biopsia->historia->agenda->empresa){{$biopsia->historia->agenda->empresa->unicodigo}}@endif</td>
                        <td colspan="2" class="text-center" style="font-size: 14px;">@isset($biopsia->historia->agenda->empresa){{$biopsia->historia->agenda->empresa->nombrecomercial}}@endif</td>
                        <td class="text-center" colspan="4" style="font-size: 14px;">{{$biopsia->historia->paciente->id}}</td>
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
                        <td class="text-center" colspan="2" style="height: 20px;">{{$biopsia->historia->agenda->paciente->apellido1}}</td>
                        <td class="text-center" colspan="2">{{$biopsia->historia->agenda->paciente->apellido2}}</td>
                        <td class="text-center" colspan="2">{{$biopsia->historia->agenda->paciente->nombre1}}</td>
                        <td class="text-center" colspan="1">{{$biopsia->historia->agenda->paciente->nombre2}}</td>
                        <td class="text-center" colspan="1">@if($biopsia->historia->agenda->paciente->sexo == 1) Masculino @else Femenino @endif</td>
                        <td class="text-center" colspan="2">{{$edad}}</td>
                        <td class="text-center" colspan="1"></td>
                        <td class="text-center" colspan="1">{{$diaNacimiento}}</td>
                        <td class="text-center" colspan="1">{{$mesNacimiento}}</td>
                        <td class="text-center" colspan="1">{{ date('Y', strtotime($biopsia->historia->paciente->fecha_nacimiento))}}</td>
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
                @php 
                    $dsol = $biopsia->procedimiento->doctor; 
                    $aud_proc = Sis_medico\Aud_Hc_Procedimientos::where('id_procedimientos_org', $biopsia->procedimiento->id)->where('estado','1')->first();
                    if(!is_null($aud_proc)){
                        $dsol = $aud_proc->doctor_firma;
                    }
                @endphp
                <tr>
                    <td class="text-center">{{$biopsia->historia->agenda->empresa->razonsocial}}</td>
                    <td class="text-center">{{ $dsol->apellido1.' '.$dsol->apellido2.' '.$dsol->nombre1.' '.$dsol->nombre2 }}</td>
                    <td class="text-center">{{$dsol->cargo}}</td>
                    <td class="text-center">{{$biopsia->id}}</td>
                </tr>
            </table>
            <br>
            @php
            $nombre_macro = "";
            if(!is_null($biopsia->doctor_macro)){
                $nombre_macro = $biopsia->doctor_macro->nombre1.' '.$biopsia->doctor_macro->nombre2.' '.$biopsia->doctor_macro->apellido1.' '.$biopsia->doctor_macro->apellido2;
            }
            @endphp
            <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
                <thead>
                    <tr>
                        <td colspan="2" style="font-size: 14px;" class="bg-purple"><b>C. DESCRIPCIÓN MACROSCÓPICA </b></td>
                    </tr>
                </thead>
                <tr>
                    <td style="height: auto; word-wrap: break-word; overflow-wrap: break-word;" colspan="2">{{$biopsia->descripcion_macroscopica}}</td>
                </tr>
                <tr>
                    <td style="width: 45%;" class="bg-green">NOMBRE DEL PROFESIONAL RESPONSABLE DE LA MACROSCOPIA</td>
                    <td> &nbsp; &nbsp; {{$nombre_macro}}</td>
                </tr>
            </table>
            <br>
            @php
            $nombre_micro = "";
            if(!is_null($biopsia->doctor_micro)){
                $nombre_micro = $biopsia->doctor_micro->nombre1.' '.$biopsia->doctor_micro->nombre2.' '.$biopsia->doctor_micro->apellido1.' '.$biopsia->doctor_micro->apellido2;
            }
            @endphp
            <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
                <thead>
                    <tr>
                        <td colspan="2" style="font-size: 14px;" class="bg-purple"><b>C. DESCRIPCIÓN MICROSCÓPICA </b></td>
                    </tr>
                </thead>
                <tr>
                    <td style="height: auto; word-wrap: break-word; overflow-wrap: break-word;" colspan="2">{{$biopsia->descripcion_microscopica}}</td>
                </tr>
                <tr>
                    <td style="width: 45%;" class="bg-green">NOMBRE DEL PROFESIONAL RESPONSABLE DE LA MICROSCOPIA</td>
                    <td> &nbsp; &nbsp;{{$nombre_micro}}</td>
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
                    <td class="text-center">{{$biopsia->normal}}</td>
                    <td colspan="2" class="bg-green text-center">LIE BAJO</td>
                    <td class="text-center">{{$biopsia->lie_bajo}}</td>
                    <td colspan="2" class="bg-green text-center">LIE ALTO</td>
                    <td class="text-center">{{$biopsia->lie_alto}}</td>
                    <td colspan="2" class="bg-green text-center">CA</td>
                    <td colspan="4" class="text-center">{{$biopsia->ca}}</td>
                </tr>
                <tr>
                    <td colspan="19">&nbsp;</td>
                </tr>
                <tr>
                    <td class="bg-green text-center">MUESTRA INADECUADA</td>
                    <td class="text-center">{{$biopsia->muestra_ina}}</td>
                    <td class="bg-green text-center">HONGOS</td>
                    <td colspan="2" class="text-center">{{$biopsia->hongos}}</td>
                    <td colspan="2" class="bg-green text-center">ERITROCITOS</td>
                    <td class="text-center">{{$biopsia->eritrocitos}}</td>
                    <td colspan="2" class="bg-green text-center">FLORA BACTERIANA</td>
                    <td class="text-center">{{$biopsia->flora}}</td>
                    <td colspan="3" class="bg-green text-center">HISTIOCITOS</td>
                    <td class="text-center">{{$biopsia->histiocitos}}</td>
                    <td class="bg-green text-center">NÚMERO DE MUESTRA</td>
                    <td class="text-center">{{$biopsia->numero_muestra}}</td>
                    <td class="bg-green text-center">FECHA DE PROCESO</td>
                    <td class="text-center"></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center bg-green">OBSERVACIONES</td>
                    <td colspan="17"></td>
                </tr>
                <tr>
                    <td colspan="19" style="height: 45px;">{{$biopsia->observaciones}}</td>
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
                    <td style="height: auto; word-wrap: break-word; overflow-wrap: break-word;">{{$biopsia->diagnostico}}</td>
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
                    <td style="height: auto; word-wrap: break-word; overflow-wrap: break-word;">{{$biopsia->recomendaciones}}</td>
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
                    <td style="height: 38px;" class="text-center">{{$biopsia->fecha_proceso}}</td>
                    <td class="text-center">{{$hora}}</td>

                    <td class="text-center">@if(!is_null($firma)){{$firma->doctor->nombre1}}@endif</td>
                    <td class="text-center" colspan="2">@if(!is_null($firma)){{$firma->doctor->apellido1}}@endif</td>
                    <td class="text-center">@if(!is_null($firma)){{$firma->doctor->apellido2}}@endif</td>
                </tr>
                <tr>
                    <td class="text-center bg-green" colspan="2" style="font-size: 9px;">NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</td>
                    <td colspan="2" class="text-center bg-green">FIRMA</td>
                    <td colspan="2" class="text-center bg-green">SELLO</td>
                </tr>
                <tr>
                    <td class="text-center" colspan="2" style="height: 38px; font-size: 14px;">@if(!is_null($firma)){{$firma->doctor->id}}@endif</td>
                    <td colspan="2" class="text-center">
                        <p style="text-align: center;">
                            @if(!is_null($firma) && !is_null($firma->firma))
                            <img width=150 height=60 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" style="" align=center hspace=12><br>
                            @else
                            <br><br><br>
                            <b style="font-size: 10px;">@if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @endif</b><br>
                            @endif
                        </p>
                    </td>
                    <td colspan="2">
                        <p style="text-align: center;">
                            @if(!is_null($firma) && !is_null($firma->nombre))
                            <img width=150 height=60 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center hspace=12><br>
                            @endif
                        </p>
                    </td>
                </tr>
            </table>
            <br>
            <p>SNS-MSP/HCU-form.013B/2021 @for($count=0; $count<=85;$count++) &nbsp; @endfor <b style="font-size: 10px;">ANATOMÍA PATOLÓGICA - INFORME</b></p>
        </main>
        @if ($key != count($biopsias) - 1) 
            <div class="page-break"></div> <!-- Agrega un salto de página después de cada biopsia -->
        @endif
    @endforeach
</body>

</html>