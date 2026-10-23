@extends('preanestesico.transanestesico')
@section('action-content')


@php
$fecha=date('Y-m-d');
$hora = date('H:i:s');
@endphp


<?php

$fechaNacimiento = new DateTime($transanestesico->historia->paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($transanestesico->historia->paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($transanestesico->historia->paciente->fecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');

?>



<style type="text/css">
    .icheckbox_flat-orange.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }

    td {
        padding: 3px !important;

    }

    div.formgroup.col-md-4 {
        margin-bottom: 0px !important;
    }

    .ui-corner-all {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget {
        font-family: Verdana, Arial, sans-serif;
        font-size: 12px;
    }

    .ui-menu {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
        opacity: 1;
    }

    .ui-autocomplete {
        opacity: 1;
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 470px !important;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    .ui-menu .ui-menu-item {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .container {
        display: grid;
        grid-template-columns: repeat(38, 1fr);
        gap: 2px;
        max-width: 100%;
        overflow: auto;
    }

    .ui-menu .ui-menu-item a {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }

    .ui-menu .ui-menu-item a:hover {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }

    .ui-widget-content a {
        color: #222222;
    }

    .bg-light-green {
        background-color: #7FE290;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
        /* Desplazamiento horizontal */
        -webkit-overflow-scrolling: touch;
        /* Para un scroll más suave en dispositivos móviles */
    }

    /* Asegurarse de que las celdas se ajusten correctamente */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    td,
    th {
        padding: 8px;
        text-align: center;
        font-size: 12px;
        /* Ajustar el tamaño de fuente si es necesario */
    }

    /* Ajustes para pantallas pequeñas */
    @media (max-width: 768px) {

        td,
        th {
            font-size: 10px;
            /* Reducir el tamaño de la fuente en pantallas más pequeñas */
        }

        input[type="text"] {
            width: 80%;
            /* Ajustar el tamaño de los inputs para pantallas pequeñas */
        }

        table {
            width: 100%;
            /* Asegura que la tabla no exceda el ancho de la pantalla */
        }
    }
</style>
<section class="content">


    <input type="hidden" value="{{$transanestesico->id}}" name="id" id="id" />
    <input type="hidden" value="{{$pre_anestesico->id}}" name="id_pre" id="id_pre" />
    <input type="hidden" value="{{$transanestesico->id_hc}}" name="id_hc" id="id_hc" />
    <input type="hidden" value="{{$transanestesico->id_hc_procedimientos}}" name="id_hc_procedimientos" id="id_hc_procedimientos" />

    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
        <thead>
            <tr>
                <td colspan="14" style="font-size: 14px;" class="bg-blue"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</b></td>
            </tr>
        </thead>
        <tbody>
            <!--tr>
                <td class="bg-light-green text-center" style="width: 20%;"><b>INSTITUCION DEL SISTEMA</b></td>
                <td colspan="2" class="bg-light-green text-center"><b>UNICODIGO</b></td>
                <td colspan="3" class="bg-light-green text-center" style="width: 22%;"><b>ESTABLECIMIENTO DE SALUD</b></td>
                <td colspan="4" class="bg-light-green text-center"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                <td colspan="5" class="bg-light-green text-center"><b>NÚMERO DE ARCHIVO</b></td>
            </tr>
            <tr>
                <td class="text-center" style="font-size: 14px;"></td>
                <td class="text-center" colspan="2" style="font-size: 14px;">026247</td>
                <td colspan="3" class="text-center" style="font-size: 14px;"></td>
                <td class="text-center" colspan="4" style="font-size: 16px;">{{$transanestesico->historia->paciente->id}}</td>
                <td colspan="5"></td>
            </tr-->
            <tr>
                <td rowspan="2" colspan="2" class="bg-light-green text-center"><b>PRIMER APELLIDO</b></td>
                <td rowspan="2" colspan="2" class="bg-light-green text-center"><b>SEGUNDO APELLIDO</b></td>
                <td rowspan="2" colspan="2" class="bg-light-green text-center"><b>PRIMER NOMBRE</b></td>
                <td rowspan="2" colspan="1" class="bg-light-green text-center" style="width: 15%;"><b>SEGUNDO NOMBRE</b></td>
                <td rowspan="2" colspan="1" class="bg-light-green text-center"><b>SEXO</b></td>
                <td rowspan="2" colspan="2" class="bg-light-green text-center"><b>EDAD</b></td>
                <td colspan="4" class="bg-light-green text-center" style="font-size: 6;"><b>CONDICIÓN EDAD (MARCAR)</b></td>
            </tr>
            <tr>
                <td class="bg-light-green text-center">H</td>
                <td class="bg-light-green text-center">D</td>
                <td class="bg-light-green text-center">M</td>
                <td class="bg-light-green text-center" style="width: 5%;">A</td>
            </tr>
            <tr>
                <td class="text-center" colspan="2" style="height: 20px;">{{$transanestesico->historia->paciente->apellido1}}</td>
                <td class="text-center" colspan="2">{{$transanestesico->historia->paciente->apellido2}}</td>
                <td class="text-center" colspan="2">{{$transanestesico->historia->paciente->nombre1}}</td>
                <td class="text-center" colspan="1">{{$transanestesico->historia->paciente->nombre2}}</td>
                <td class="text-center" colspan="1">@if($transanestesico->historia->paciente->sexo == 1) Masculino @else Femenino @endif</td>

                <td class="text-center" colspan="2">{{$edad}}</td>
                <td class="text-center" colspan="1"></td>
                <td class="text-center" colspan="1">{{$diaNacimiento}}</td>
                <td class="text-center" colspan="1">{{$mesNacimiento}}</td>
                <td class="text-center" colspan="1">{{ date('Y', strtotime($transanestesico->historia->paciente->fecha_nacimiento))}}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <table border="1" style="width: 100%">
        <thead>
            <tr>
                <td colspan="5" class="bg-purple" style="font-size: 10;"><b>REGIÓN OPERATORIA</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">
                    CABEZA &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="cabeza" id="cabeza" required="required" @if($transanestesico->cabeza == '1') checked @endif>
                </td>
                <td class="text-center">
                    ORGANOS &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="organos" id="organos" required="required" @if($transanestesico->organos == '1') checked @endif>
                </td>
                <td class="text-center">
                    CUELLO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="cuello" id="cuello" required="required" @if($transanestesico->cuello == '1') checked @endif>
                </td>
                <td class="text-center">
                    COLUMNA &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="columna" id="columna" required="required" @if($transanestesico->columna == '1') checked @endif>
                </td>
                <td class="text-center">
                    TORÁX &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="torax" id="torax" required="required" @if($transanestesico->torax == '1') checked @endif>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    ABDOMEN &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="abdomen" id="abdomen" required="required" @if($transanestesico->abdomen == '1') checked @endif>
                </td>
                <td class="text-center">
                    PELVIS &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="pelvis" id="pelvis" required="required" @if($transanestesico->pelvis == '1') checked @endif>
                </td>
                <td class="text-center">
                    EXTREMIDADES SUPERIORES &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="e_superiores" id="e_superiores" required="required" @if($transanestesico->e_superiores == '1') checked @endif>
                </td>

                <td class="text-center">
                    EXTREMIDADES INFERIORES &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="e_inferiores" id="e_inferiores" required="required" @if($transanestesico->e_inferiores == '1') checked @endif>
                </td>

                <td class="text-center">
                    PERINEAL &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="perineal" id="perineal" required="required" @if($transanestesico->perineal == '1') checked @endif>
                </td>
            </tr>

        </tbody>
    </table>

    <br>

    <table border="1" style="width: 100%;">
        <thead>
            <tr>
                <td class="bg-purple" style="font-size: 10;"><b>REGISTRO TRANSANESTÉSICO</b></td>
            </tr>
            <tr>
                <td>
                    <!--td id="vistaImagen"></td-->
                    @if(is_null($transanestesico->url_imagen))
                    <img width="1450" height="500" src="{{base_path().'/public/img_formato/record.png'}}"></span></p>
                    @else
                    <img width="1450" height="500" src="{{asset('hc_ima')}}/{{$transanestesico->url_imagen}}">
                    @endif
                </td>
            </tr>
        </thead>
    </table>


    <br>

    <table style="width: 100%;">
        <tbody>
            <tr>
                <td colspan="3" style="font-size: 14px;" class="bg-purple"><b>DROGAS ADMINISTRADAS</b></td>
            </tr>
            <tr>
                <td></td>

                <td>
                    <input style="width: 98%; height: 35px;" type="text" name="drogas" id="drogas" required>
                </td>

                <td style="text-align: center;">
                    <button class="btn btn-primary bg-green" id="boton_enviar" onclick="guardar();" type="button">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <div id="tabla_drogas">
        <table style="width: 100%;">
            <tbody>
                <!--tr>
                    <td colspan="3" style="font-size: 14px;" class="bg-purple"><b>DROGAS ADMINISTRADAS</b></td>
                </tr>
                <tr>
                    <td></td>

                    <td>
                        <input style="width: 98%; height: 35px;" type="text" name="drogas" id="drogas" required>
                    </td>

                    <td style="text-align: center;">
                        <button class="btn btn-primary bg-green" id="boton_enviar" onclick="guardar();" type="button">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr-->
                @php
                $contador = 1;
                @endphp
                @foreach ($drogas as $rc)
                <tr>
                    <td>{{ $contador++ }}</td>
                    <td>
                        <input style="width: 98%;" type="text" value="{{ $rc->drogas }}" disabled>
                    </td>
                    <td>
                        <a onclick="eliminar_droga({{ $rc->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                    <td>
                        <hr>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <table border="1" style="width: 100%">

        <tbody>
            <tr>
                <td colspan="5" class="bg-purple" style="font-size: 8;"><b> TÉCNICA ANESTÉSICA</b></td>
            </tr>
            <tr>
                <td colspan="5" class="text-center bg-light-green"><b>GENERAL</b></td>
            </tr>
            <tr>
                <td colspan="3" class="text-center" style="width: 60%;"><b>SISTEMA</b></td>
                <td colspan="2" class="text-center"><b>APARATO</b></td>
            </tr>
            <tr>
                @foreach($sistemas as $sistema)
                @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $sistema->id)->first(); @endphp
                <td class="">
                    {{ $sistema->nombre_new }} &nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="{{$sistema->id}}" onclick="actualiza_tecnicas(this)">
                </td>
                @endforeach

                @foreach($aparatos as $aparato)
                @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $aparato->id)->first(); @endphp
                <td class="">
                    {{ $aparato->nombre_new }} &nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="{{$aparato->id}}" onclick="actualiza_tecnicas(this)">
                </td>
                @endforeach
            </tr>
            <tr>
                <td colspan="5" class="text-center bg-light-green" style="width: 60%;"><b>MANEJO DE VÍA AÉREA</b></td>
            </tr>
            <tr>
                @foreach($manejos as $manejo)
                @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $manejo->id)->first(); @endphp
                <td class="">
                    {{ $manejo->nombre_new }} &nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="{{$manejo->id}}" onclick="actualiza_tecnicas(this)">
                </td>
                @endforeach
            </tr>
            <tr>
                <td colspan="5" class="text-center bg-light-green" style="width: 60%;"><b>INTUBACIÓN</b></td>
            </tr>
            <tr>
                @foreach($intubuaciones as $intubacion)
                @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $intubacion->id)->first(); @endphp
                <td class="">
                    {{ $intubacion->nombre_new }} &nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="{{$intubacion->id}}" onclick="actualiza_tecnicas(this)">
                </td>
                @endforeach
            </tr>
        </tbody>
    </table>
    <table border="1" style="width: 100%">
        <tbody>
            <tr>
                <td colspan="5" class="text-center bg-light-green" style="width: 60%;"><b>TIPO DE TUBO</b></td>
            </tr>
            <tr>
                @foreach($tipos as $tipos)
                @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $tipos->id)->first(); @endphp
                <td class="">
                    {{ $tipos->nombre_new }} &nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="{{$tipos->id}}" onclick="actualiza_tecnicas(this)">
                </td>
                @endforeach
            </tr>
            <tr>
                <td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '53')->first(); @endphp
                    DÍAMETRO &nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="53" onclick="actualiza_tecnicas(this)">
                </td>
                <td colspan="2">
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '54')->first(); @endphp
                    BALON &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; SI &nbsp;&nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="54" onclick="actualiza_tecnicas(this)">
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '55')->first(); @endphp
                    &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; NO &nbsp;&nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="55" onclick="actualiza_tecnicas(this)">
                </td>
                <td colspan="2">
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '56')->first(); @endphp
                    TAPONAMIENTO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; SI &nbsp;&nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="56" onclick="actualiza_tecnicas(this)">
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '57')->first(); @endphp
                    &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; NO &nbsp;&nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="57" onclick="actualiza_tecnicas(this)">
                </td>
            </tr>
            <tr>
                <td colspan="5" class="bg-light-green"><b>EQUIPO PARA INTUBACION</b></td>
            </tr>
            <tr>
                <td>
                    CORMACK &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
                <td>
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '58')->first(); @endphp
                    &nbsp; &nbsp;&nbsp; I &nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="58" onclick="actualiza_tecnicas(this)">
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '59')->first(); @endphp
                    &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; II &nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="59" onclick="actualiza_tecnicas(this)">
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '60')->first(); @endphp
                    &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; III &nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="60" onclick="actualiza_tecnicas(this)">
                    @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', '61')->first(); @endphp
                    &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; IV &nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="61" onclick="actualiza_tecnicas(this)">
                </td>
                <td>
                    NÚMERO DE INTENTOS
                </td>
                <td colspan="2"><input onchange="handlePublica(this)" style="width: 100%;" class="flat-orange" type="text" name="intentos" value="{{$transanestesico->intentos}}" id="doble" required="required"></td>
            </tr>
            <tr>
                <td colspan="3" class="text-center" style="width: 60%;"><b>INDUCCION</b></td>
                <td colspan="2" class="text-center"><b>MANTENIMIENTO</b></td>
            </tr>
            <tr>
                @foreach($inducciones as $induccion)
                @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $induccion->id)->first(); @endphp
                <td class="">
                    {{ $induccion->nombre_new }} &nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="{{$induccion->id}}" onclick="actualiza_tecnicas(this)">
                </td>
                @endforeach

                @foreach($mantenimientos as $mantenimiento)
                @php $dato = $rc_tecnicas->where('id_tecnicas_anestesicas', $mantenimiento->id)->first(); @endphp
                <td class="">
                    {{ $mantenimiento->nombre_new }} &nbsp;<input name="lista[]" @if(!is_null($dato)) checked @endif type="checkbox" class="flat-green" value="{{$mantenimiento->id}}" onclick="actualiza_tecnicas(this)">
                </td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <form method="post" id="form_trans">
        <input type="hidden" name="id_trans" value="{{$pre_anestesico->id}}" />

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="2" class="text-center bg-light-green" style="width: 35%;"><b>REGIONAL</b></td>
                    <td colspan="4" class="text-center bg-light-green" style="width: 65%;">TRONCULAR</td>
                </tr>
                <tr>
                    <td class="bg-light-green" style="width: 17%;">ASEPSIA CON</td>
                    <td style="width: 18%;"><input type="text" onchange="actualiza_trans()" style="width: 100%;" name="asepsia" id="asepsia" value="{{$pre_anestesico->asepsia}}"></td>
                    <td class="bg-light-green">BLOQUEO DE NERVIO</td>
                    <td style="width: 15%;"><input type="text" onchange="actualiza_trans()" style="width: 100%;" name="bloqueo_nervio" id="bloqueo_nervio" value="{{$pre_anestesico->bloqueo_nervio}}"></td>
                    <td class="bg-light-green">NÚMERO DE INTENTOS</td>
                    <td style="width: 15%;"><input type="text" onchange="actualiza_trans()" style="width: 100%;" name="nervio_intentos" id="nervio_intentos" value="{{$pre_anestesico->nervio_intentos}}"></td>
                </tr>
                <tr>
                    <td class="bg-light-green">HABÓN CON</td>
                    <td><input type="text" onchange="actualiza_trans()" style="width: 100%;" name="habon" id="habon" value="{{$pre_anestesico->habon}}"></td>
                    <td class="bg-light-green">BLOQUEO DE PLEXO</td>
                    <td><input type="text" onchange="actualiza_trans()" style="width: 100%;" name="bloqueo_plexo" id="bloqueo_plexo" value="{{$pre_anestesico->bloqueo_plexo}}"></td>
                    <td class="bg-light-green">NÚMERO DE INTENTOS</td>
                    <td><input type="text" onchange="actualiza_trans()" style="width: 100%;" name="plexo_intentos" id="plexo_intentos" value="{{$pre_anestesico->plexo_intentos}}"></td>
                </tr>
                <tr>
                    <td class="bg-light-green">LOCAL ASISTIDA</td>
                    <td><input type="text" style="width: 100%;" onchange="actualiza_trans()" style="width: 100%;" name="local_asistida" id="local_asistida" value="{{$pre_anestesico->local_asistida}}"></td>
                    <td class="bg-light-green">ANESTÉSICO LOCAL</td>
                    <td><input type="text" style="width: 100%;" onchange="actualiza_trans()" style="width: 100%;" name="anestesico_local" id="anestesico_local" value="{{$pre_anestesico->anestesico_local}}"></td>
                    <td class="bg-light-green">COADYUVANTE</td>
                    <td><input type="text" style="width: 100%;" onchange="actualiza_trans()" style="width: 100%;" name="coadyuvante" id="coadyuvante" value="{{$pre_anestesico->coadyuvante}}"></td>
                </tr>
                <tr>
                    <td class="bg-light-green">INTRAVENOSA</td>
                    <td><input type="text" style="width: 100%;" onchange="actualiza_trans()" style="width: 100%;" name="intravenosa" id="intravenosa" value="{{$pre_anestesico->intravenosa}}"></td>
                    <td class="bg-light-green">TIPO DE AGUJA</td>
                    <td><input type="text" style="width: 100%;" onchange="actualiza_trans()" style="width: 100%;" name="tipo_aguja" id="tipo_aguja" value="{{$pre_anestesico->tipo_aguja}}"></td>
                    <td class="bg-light-green">EQUIPO</td>
                    <td><input type="text" style="width: 100%;" onchange="actualiza_trans()" style="width: 100%;" name="equipo" id="equipo" value="{{$pre_anestesico->equipo}}"></td>
                </tr>
            </tbody>
        </table>

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="10" class="text-center bg-light-green">NEUROAXIAL</td>
                </tr>
                <tr>
                    <td>RAQUIDEA</td>
                    <td><input onchange="actualiza_trans()" type="text" style="width: 100%;" id="raquidea" name="raquidea" value="{{$pre_anestesico->raquidea}}"></td>
                    <td colspan="3" class="text-center">EPIDURAL</td>
                    <td><input onchange="actualiza_trans()" type="text" style="width: 100%;" id="epidural" name="epidural" value="{{$pre_anestesico->epidural}}"></td>
                    <td>CAUDAL</td>
                    <td><input onchange="actualiza_trans()" type="text" style="width: 100%;" id="caudal" name="caudal" value="{{$pre_anestesico->caudal}}"></td>
                    <td>CATETER</td>
                    <td><input onchange="actualiza_trans()" type="text" style="width: 100%;" id="cateter" name="cateter" value="{{$pre_anestesico->cateter}}"></td>
                </tr>
                <tr>
                    <td colspan="2">TIPO DE AGUA</td>
                    <td><input onchange="actualiza_trans()" type="text" style="width: 100%;" id="tipo_aguja_2" name="tipo_aguja_2" value="{{$pre_anestesico->tipo_aguja_2}}"></td>
                    <td colspan="2">NUMERO DE AGUJA</td>
                    <td><input onchange="actualiza_trans()" type="text" style="width: 100%;" id="numero_aguja" name="numero_aguja" value="{{$pre_anestesico->numero_aguja}}"></td>
                    <td colspan="3">N° DE INTENTOS</td>
                    <td><input onchange="actualiza_trans()" type="text" style="width: 100%;" id="neuroaxial_intentos" name="neuroaxial_intentos" value="{{$pre_anestesico->neuroaxial_intentos}}"></td>
                </tr>
                <tr>
                    <td rowspan="2">BORBOTAJE</td>
                    <td rowspan="2">SI</td>
                    <td rowspan="2" style="width: 5%;" class="text-center"><input type="radio" onchange="handlePublica(this)" style="width: 100%; height: 20px;" value="SI" name="borbotaje" @if($transanestesico->borbotaje == 'SI') checked @endif></td>
                    <td rowspan="2">NO</td>
                    <td rowspan="2" style="width: 5%;"><input type="radio" onchange="handlePublica(this)" style="width: 100%; height: 20px;" value="NO" name="borbotaje" @if($transanestesico->borbotaje == 'NO') checked @endif></td>
                    <td rowspan="2" class="text-center">ACCESO</td>
                    <td>MEDIAL</td>
                    <td>LATERAL</td>
                    <td rowspan="2">SITIO DE PUNCIÓN</td>
                    <td rowspan="2"><input onchange="actualiza_trans()" type="text" style="width: 100%; height: 60px;" id="sitio_puncion" name="sitio_puncion" value="{{$pre_anestesico->sitio_puncion}}"></td>
                </tr>
                <tr>
                    <td style="height: 25px;"><input type="radio" onchange="handlePublica(this)" style="width: 100%; height: 20px;" value="medial" name="acceso" @if($transanestesico->acceso == 'medial') checked @endif></td>
                    <td><input type="radio" onchange="handlePublica(this)" style="width: 100%; height: 20px;" value="lateral" name="acceso" @if($transanestesico->acceso == 'lateral') checked @endif> </td>
                </tr>
                <tr>
                    <td class="bg-light-green" colspan="2">DERMATOMA</td>
                    <td colspan="2"><input onchange="actualiza_trans()" type="text" style="width: 100%;" name="dermatoma" id="dermatoma" value="{{$pre_anestesico->dermatoma}}"></td>
                    <td class="bg-light-green" colspan="2">POSICIÓN</td>
                    <td colspan="4"><input onchange="actualiza_trans()" type="text" style="width: 100%;" name="posicion" id="posicion" value="{{$pre_anestesico->posicion}}"></td>
                </tr>
            </tbody>
        </table>


        <br>
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="font-size: 14px;" class="bg-purple"><b>SEDO-ANALGESIA</b></td>
                </tr>
                <tr>

                    <td>
                        <input onchange="actualiza_trans()" style="width: 100%; height: 45px;" type="text" name="sedo_analgesia" id="sedo_analgesia" value="{{$pre_anestesico->sedo_analgesia}}">
                    </td>
            </tbody>
        </table>
        <br>


        <table border="1" style="width: 100%;">
            <tbody>
                <tr>
                    <td colspan="12" style="font-size: 14px;" class="bg-purple"><b>ESCALA DE RAMSAY</b></td>
                </tr>
                <tr>
                    <td class="text-center"><b>1</b></td>
                    <td> &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="radio" value="1" name="escala" id="1" required="required" @if($transanestesico->escala == '1') checked @endif></td>
                    <td class="text-center"><b>2</b></td>
                    <td> &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="radio" value="2" name="escala" id="1" required="required" @if($transanestesico->escala == '2') checked @endif></td>
                    <td class="text-center"><b>3</b></td>
                    <td> &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="radio" value="3" name="escala" id="1" required="required" @if($transanestesico->escala == '3') checked @endif></td>
                    <td class="text-center"><b>4</b></td>
                    <td> &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="radio" value="4" name="escala" id="1" required="required" @if($transanestesico->escala == '4') checked @endif></td>
                    <td class="text-center"><b>5</b></td>
                    <td> &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="radio" value="5" name="escala" id="1" required="required" @if($transanestesico->escala == '5') checked @endif></td>
                    <td class="text-center"><b>6</b></td>
                    <td> &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="radio" value="6" name="escala" id="1" required="required" @if($transanestesico->escala == '6') checked @endif></td>
                </tr>
            </tbody>
        </table>
        <div class="table-responsive">
            <table border="1" style="width: 100%">
                <tbody>
                    <tr>
                        <td colspan="3" class="bg-purple" style="font-size: 8;"><b>G. ACCESOS VASCULARES</b></td>
                        <td colspan="4" class="bg-purple" style="font-size: 8;"><b>H. REPOSICÍON VOLÉMICA</b></td>
                        <td colspan="2" class="bg-purple" style="font-size: 8;"><b>I. PÉRDIDAS</b></td>
                    </tr>
                    <tr>
                        <td class="text-center bg-light-green" style="">TIPO</td>
                        <td class="text-center bg-light-green" style="">CALIBRE</td>
                        <td class="text-center bg-light-green" style="">SITIO</td>
                        <td class="text-center bg-light-green" style="">DEXTROSA 5%</td>
                        <td style="text-align: center; vertical-align: middle;"><input onchange="actualiza_trans()" type="text" id="dextrosa_5" name="dextrosa_5" value="{{$pre_anestesico->dextrosa_5}}"></td>
                        <td class="text-center bg-light-green" style="">SANGRE</td>
                        <td style="text-align: center; vertical-align: middle;"><input onchange="actualiza_trans()" type="text" id="sangre" name="sangre" value="{{$pre_anestesico->sangre}}"></td>
                        <td class="text-center bg-light-green" style="">SANGRADO</td>
                        <td style="text-align: center; vertical-align: middle;"><input onchange="actualiza_trans()" type="text" id="sangrado" name="sangrado" value="{{$pre_anestesico->sangrado}}"></td>
                    </tr>
                    <tr>
                        <td class="text-center">IV PERIFÉRICO 1</td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="text" id="calibre_1" onchange="actualiza_trans()" name="calibre_1" value="{{$pre_anestesico->calibre_1}}">
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="text" id="sitio_1" onchange="actualiza_trans()" name="sitio_1" value="{{$pre_anestesico->sitio_1}}">
                        </td>
                        <td class="text-center bg-light-green">DEXTROSA 10%</td>
                        <td style="text-align: center; vertical-align: middle;"><input onchange="actualiza_trans()" type="text" id="dextrosa_10" name="dextrosa_10" value="{{$pre_anestesico->dextrosa_10}}"></td>
                        <td class="text-center bg-light-green">PLASMA</td>
                        <td style="text-align: center; vertical-align: middle;"><input onchange="actualiza_trans()" type="text" id="plasma" name="plasma" value="{{$pre_anestesico->plasma}}"></td>
                        <td class="text-center bg-light-green">DIURESIS</td>
                        <td style="text-align: center; vertical-align: middle;"><input onchange="actualiza_trans()" type="text" id="diuresis" name="diuresis" value="{{$pre_anestesico->diuresis}}"></td>
                    </tr>
                    <tr>
                        <td class="text-center">IV PERIFÉRICO 2</td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="text" id="calibre_2" onchange="actualiza_trans()" name="calibre_2" value="{{$pre_anestesico->calibre_2}}">
                        </td>

                        <td style="text-align: center; vertical-align: middle;">
                            <input type="text" id="sitio_2" onchange="actualiza_trans()" name="sitio_2" value="{{$pre_anestesico->sitio_2}}">
                        </td>
                        <td class="text-center bg-light-green">DEXTROSA 50%</td>
                        <td style="text-align: center; vertical-align: middle;"><input onchange="actualiza_trans()" type="text" id="dextrosa_50" name="dextrosa_50" value="{{$pre_anestesico->dextrosa_50}}"></td>
                        <td class="text-center bg-light-green">PLAQUETAS</td>
                        <td style="text-align: center; vertical-align: middle;"><input onchange="actualiza_trans()" type="text" id="plaquetas" name="plaquetas" value="{{$pre_anestesico->plaquetas}}"></td>
                        <td class="text-center bg-light-green">OTROS</td>
                        <td><input onchange="actualiza_trans()" type="text" id="otros_perdidas" name="otros_perdidas" value="{{$pre_anestesico->otros_perdidas}}"></td>
                    </tr>
                    <tr>
                        <td class="text-center">IV PERIFÉRICO 3</td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="text" id="calibre_3" onchange="actualiza_trans()" name="calibre_3" value="{{$pre_anestesico->calibre_3}}">
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="text" id="sitio_3" onchange="actualiza_trans()" name="sitio_3" value="{{$pre_anestesico->sitio_3}}">
                        </td>
                        <td class="text-center bg-light-green">DEXTROSA EN SS</td>
                        <td style="text-align: center; vertical-align: middle;"><input onchange="actualiza_trans()" type="text" id="dextrosa_ss" name="dextrosa_ss" value="{{$pre_anestesico->dextrosa_ss}}"></td>
                        <td class="text-center bg-light-green">CRIOPRECIPITADOS</td>
                        <td style="text-align: center; vertical-align: middle;"><input onchange="actualiza_trans()" type="text" id="crioprecipitados" name="crioprecipitados" value="{{$pre_anestesico->crioprecipitados}}"></td>
                        <td rowspan="3" class="text-center bg-light-green">TOTAL</td>
                        <td rowspan="3"><input onchange="actualiza_trans()" style="height: 130px;" type="text" id="total_perdidas" name="total_perdidas" value="{{$pre_anestesico->total_perdidas}}"></td>
                    </tr>
                    <tr>
                        <td class="text-center">IV CENTRAL</td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="text" id="calibre_central" onchange="actualiza_trans()" name="calibre_central" value="{{$pre_anestesico->calibre_central}}">
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="text" id="sitio_central" onchange="actualiza_trans()" name="sitio_central" value="{{$pre_anestesico->sitio_central}}">
                        </td>
                        <td class="text-center bg-light-green">SS 0.9%</td>
                        <td style="text-align: center; vertical-align: middle;"><input onchange="actualiza_trans()" type="text" id="ss_09" name="ss_09" value="{{$pre_anestesico->ss_09}}"></td>
                        <td rowspan="2" class="text-center bg-light-green">OTROS</td>
                        <td rowspan="2"><input onchange="actualiza_trans()" style="height: 80px;" type="text" id="otros_reposicion" name="otros_reposicion" value="{{$pre_anestesico->otros_reposicion}}"></td>

                    </tr>
                    <tr>
                        <td class="text-center">INTRA ARTERIAL</td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="text" id="calibre_arterial" onchange="actualiza_trans()" name="calibre_arterial" value="{{$pre_anestesico->calibre_arterial}}">
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="text" id="sitio_arterial" onchange="actualiza_trans()" name="sitio_arterial" value="{{$pre_anestesico->sitio_arterial}}">
                        </td>
                        <td class="text-center bg-light-green">LACTO RINGER</td>
                        <td style="text-align: center; vertical-align: middle;"><input onchange="actualiza_trans()" type="text" id="lactato_ringer" name="lactato_ringer" value="{{$pre_anestesico->lactato_ringer}}"></td>
                    </tr>
                    <tr>
                        <td class="text-center">OTRO</td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="text" id="calibre_otro" onchange="actualiza_trans()" name="calibre_otro" value="{{$pre_anestesico->calibre_otro}}">
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="text" id="sitio_otro" onchange="actualiza_trans()" name="sitio_otro" value="{{$pre_anestesico->sitio_otro}}">
                        </td>
                        <td class="text-center bg-light-green">EXPANSORES</td>
                        <td style="text-align: center; vertical-align: middle;"><input onchange="actualiza_trans()" type="text" id="expansores" name="expansores" value="{{$pre_anestesico->expansores}}"></td>
                        <td class="text-center bg-light-green">TOTAL</td>
                        <td><input onchange="actualiza_trans()" type="text" id="total_reposicion" name="total_reposicion" value="{{$pre_anestesico->total_reposicion}}"></td>
                        <td class="text-center bg-light-green">BALANCE</td>
                        <td><input onchange="actualiza_trans()" type="text" id="balance" name="balance" value="{{$pre_anestesico->balance}}"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="9" class="bg-purple" style="font-size: 8;"><b>J. DATOS DEL RECIENTE NACIDO</b></td>
                </tr>
                <tr>
                    <td class="text-center bg-light-green"><b>APGAR</b></td>
                    <td class="text-center">FETO MUERTO</td>
                    <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" value="feto" name="feto" id="feto" @if($transanestesico->feto == 'feto') checked @endif></td>
                    <td class="text-center">1 MINUTO</td>
                    <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" value="1minuto" name="feto" id="feto" @if($transanestesico->feto == '1minuto') checked @endif></td>
                    <td class="text-center">5 MINUTOS</td>
                    <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" value="5minuto" name="feto" id="feto" @if($transanestesico->feto == '5minuto') checked @endif></td>
                    <td class="text-center">10 MINUTOS</td>
                    <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" value="10minuto" name="feto" id="feto" @if($transanestesico->feto == '10minuto') checked @endif></td>
                </tr>
                <tr>
                    <td class="text-center bg-light-green">DURACIÓN ANESTESIA</td>
                    <td colspan="8"><input onchange="handlePublica(this)" style="width: 100%;" type="text" id="duracion_anestesia" name="duracion_anestesia" value="{{$transanestesico->duracion_anestesia}}"></td>
                </tr>
                <tr>
                    <td class="text-center bg-light-green">DRACIÓN DE CIRUGIA</td>
                    <td colspan="8"><input onchange="handlePublica(this)" style="width: 100%;" type="text" name="duracion_operacion" id="duracion_cirugia" value="{{$transanestesico->duracion_operacion}}"></td>
                </tr>
            </tbody>
        </table>

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="10" class="bg-purple" style="font-size: 8;"><b>L. TECNICAS ESPECIALES</b></td>
                </tr>
                <tr>
                    <td class="text-center">HEMODILUCIÓN</td>
                    <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" value="hemodilucion" name="new_tecnicas_especiales" id="hemodilucion" @if($transanestesico->new_tecnicas_especiales == 'hemodilucion') checked @endif> </td>
                    <td class="text-center">AUTOTRANSFUSIÓN</td>
                    <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" value="autotransfusion" name="new_tecnicas_especiales" id="autotransfusion" @if($transanestesico->new_tecnicas_especiales == 'autotransfusion') checked @endif> </td>
                    <td class="text-center">HIPOTENSÍON</td>
                    <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" value="hipotension" name="new_tecnicas_especiales" id="hipotension" @if($transanestesico->new_tecnicas_especiales == 'hipotension') checked @endif></td>
                    <td class="text-center">HIPOTERMIA</td>
                    <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" value="hipotermia" name="new_tecnicas_especiales" id="hipotermia" @if($transanestesico->new_tecnicas_especiales == 'hipotermia') checked @endif></td>
                    <td class="text-center">CIRCULACIÓN EXTRACOPÓREA</td>
                    <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" value="circulacion_extracoperea" name="new_tecnicas_especiales" id="circulacion_extracoperea" @if($transanestesico->new_tecnicas_especiales == 'circulacion_extracoperea') checked @endif ></td>
                </tr>
            </tbody>
        </table>

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="6" class="bg-purple" style="font-size: 8;"><b>M. MANTENIMIENTO TEMPERATURA CORPORAL</b></td>
                </tr>
                <tr>
                    <td style="width: 15%;">MANTA TERMICA</td>
                    <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" name="mantenimiento_temperatura" id="hemodilucion" value="1" @if($transanestesico->mantenimiento_temperatura == '1') checked @endif ></td>
                    <td style="width: 15%;">CALENTAMIENTO DE FLUIDOS</td>
                    <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" name="mantenimiento_temperatura" id="hemodilucion" value="2" @if($transanestesico->mantenimiento_temperatura == '2') checked @endif ></td>
                    <td class="text-center" style="width: 3%;">OTROS</td>
                    <td style="width: 30%;"><input onchange="actualiza_trans()" style="width: 100%;" type="text" name="otros_mantenimiento_temperatura" id="temperatura" value="{{$pre_anestesico->otros_mantenimiento_temperatura}}"></td>
                </tr>
            </tbody>
        </table>

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="8" class="bg-purple" style="font-size: 8;"><b>N. INCIDENTES</b></td>
                </tr>
                <tr>
                    <td>ACTIVIDAD ELÉCTRICA SIN PULSO</td>
                    <td style="text-align: center; vertical-align: middle; width: 3%;"><input onchange="handlePublica(this)" type="radio" name="indice" id="actividad_electrica" value="1" @if($transanestesico->indice == '1') checked @endif ></td>
                    <td style="width: 15%;">ARRITMIA</td>
                    <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="arritmia" value="2" @if($transanestesico->indice == '2') checked @endif ></td>
                    <td>ASISTOLIA</td>
                    <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="asistolia" value="3" @if($transanestesico->indice == '3') checked @endif ></td>
                    <td>TROMBOEMBOLIA</td>
                    <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="tromboembolia" value="4" @if($transanestesico->indice == '4') checked @endif ></td>
                </tr>
                <tr>
                    <td>HIPERTERMIA MALIGNA</td>
                    <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="hipertermia" value="5" @if($transanestesico->indice == '5') checked @endif ></td>
                    <td style="width: 15%;">ANAFILAXIA</td>
                    <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="anafilaxia" value="6" @if($transanestesico->indice == '6') checked @endif ></td>
                    <td>ISQUEMIA MIOCÁRDICA</td>
                    <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="isquemia" value="7" @if($transanestesico->indice == '7') checked @endif ></td>
                    <td>NEUMOTÓRAX</td>
                    <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="neumotorax" value="8" @if($transanestesico->indice == '8') checked @endif ></td>
                </tr>
                <tr>
                    <td>BRONCOESPASMO</td>
                    <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="broncoespasmo" value="9" @if($transanestesico->indice == '9') checked @endif ></td>
                    <td style="width: 15%;">DESPERTAR PROLONGADO</td>
                    <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="despertar_prolongado" value="10" @if($transanestesico->indice == '10') checked @endif ></td>
                    <td>EMBOLIA AÉREA VENOSA</td>
                    <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="embolea" value="11" @if($transanestesico->indice == '11') checked @endif ></td>
                    <td>LARINGOESPASMO</td>
                    <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="laringoespasmo" value="12" @if($transanestesico->indice == '12') checked @endif ></td>
                </tr>
                <tr>
                    <td>DIFICULTAD DE LA TÉCNICA</td>
                    <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="dificultad" value="13" @if($transanestesico->indice == '13') checked @endif ></td>
                    <td style="width: 15%;">OTROS</td>
                    <td colspan="5"><input style="width: 100%;" onchange="actualiza_trans()" type="text" name="otros_indice" id="otros_indice" value="{{$pre_anestesico->otros_indice}}"></td>
                </tr>
            </tbody>
        </table>

        <br>
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="font-size: 14px;" class="bg-purple"><b>P. OBSERVACIONES</b></td>
                </tr>
                <tr>
                    <td>
                        <input onchange="actualiza_trans()" style="width: 100%; height: 45px;" type="text" name="observaciones" id="observaciones" value="{{$pre_anestesico->observaciones}}">
                    </td>
                </tr>
            </tbody>
        </table>

        <br>

        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="12" class="bg-purple" style="font-size: 8;"><b>R. CONDICIONES DE EGRESO</b></td>
                </tr>
                <tr>
                    <td class="text-center" style="font-size: 5; width: 12%;" rowspan="2">CONDICIONES AL SALIR</td>
                    <td class="text-center" style="font-size: 5;">EXTUBADO</td>
                    <td class="text-center" style="font-size: 5; width: 5%;"><input onchange="handlePublica(this)" type="radio" value="1" name="condiciones_al_salir" id="exturbado" @if($transanestesico->condiciones_al_salir == '1') checked @endif ></td>
                    <td class="text-center" style="font-size: 5;" rowspan="2">CONDUCIDO A:</td>
                    <td class="text-center" style="font-size: 5;" rowspan="2">UNIDAD DE CUIDADOS POST ANESTÉSICO</td>
                    <td class="text-center" style="font-size: 5; width: 5%;" rowspan="2"><input onchange="handlePublica(this)" type="radio" value="5" name="conducido" id="cuidados_post_anest" @if($transanestesico->conducido == '5') checked @endif></td>
                    <td class="text-center" style="font-size: 5;" rowspan="2">UNIDAD DE CUIDADOS INTENSIVOS</td>
                    <td class="text-center" style="font-size: 5; width: 5%;" rowspan="2"><input onchange="handlePublica(this)" type="radio" value="1" name="conducido" id="cuidados_intensivos" @if($transanestesico->conducido == '1') checked @endif></td>
                    <td class="text-center" style="font-size: 5;" rowspan="2">CRÍTICOS DE EMERGENCIA</td>
                    <td class="text-center" style="font-size: 5; width: 5%;" rowspan="2"><input onchange="handlePublica(this)" type="radio" value="2" name="conducido" id="criticos_emergencia" @if($transanestesico->conducido == '2') checked @endif></td>
                    <td class="text-center" style="font-size: 5; " rowspan="2">MORGUE</td>
                    <td class="text-center" style="font-size: 5; width: 5%" rowspan="2"><input onchange="handlePublica(this)" type="radio" value="4" name="conducido" id="morgue" @if($transanestesico->conducido == '4') checked @endif></td>
                </tr>
                <tr>
                    <td style="font-size: 5;" class="text-center">INTUBADO</td>
                    <td class="text-center"><input onchange="handlePublica(this)" type="radio" name="condiciones_al_salir" id="intubado" value="2" @if($transanestesico->condiciones_al_salir == '2') checked @endif></td>
                </tr>
                <tr>
                    <td style="font-size: 5;">CONSTANTES VITALES DE ENTREGA</td>
                    <td colspan="2"> <b>TA</b>&nbsp;<input style="width: 200px;" onchange="actualiza_trans()" type="text" name="presion" id="presion" value="{{$pre_anestesico->presion}}"></td>
                    <td colspan="2"> <b>FC</b>&nbsp;<input style="width: 200px;" onchange="actualiza_trans()" type="text" name="pulso" id="pulso" value="{{$pre_anestesico->pulso}}"></td>
                    <td colspan="2"> <b>FR</b>&nbsp;<input style="width: 200px;" onchange="actualiza_trans()" type="text" name="fecuencia_respiratoria" id="fecuencia_respiratoria" value="{{$pre_anestesico->fecuencia_respiratoria}}"></td>
                    <td colspan="3"> <b>SAT.O2</b>&nbsp;<input style="width: 200px;" onchange="actualiza_trans()" type="text" name="o2" id="o2" value="{{$pre_anestesico->o2}}"></td>
                    <td colspan="2"> <b>T°</b>&nbsp;<input style="width: 200px;" onchange="actualiza_trans()" type="text" name="temperatura" id="temperatura" value="{{$pre_anestesico->temperatura}}"></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" style="width: 100%">
            <tbody>
                <tr>
                    <td colspan="12" class="bg-purple" style="font-size: 8;"><b>O. RESULTADO DE EXÁMENES DE LABORATORIO</b></td>
                </tr>
            </tbody>
        </table>
    </form>
    <div class="box-body">
        <form id="examen" class="row">
            <input type="hidden" id="id_examen_buscador" />
            <div class="form-group col-md-5{{ $errors->has('buscador') ? ' has-error' : '' }}">
                <label for="buscador" class="control-label">Nombre Examen</label>
                <div class="input-group">
                    <input id="buscador" class="form-control input-sm" type="text" name="buscador" value="" required autofocus onchange="cargar_buscador();" style="background-color: #fff0e6;">
                    @if ($errors->has('buscador'))
                    <span class="help-block">
                        <strong>{{ $errors->first('buscador') }}</strong>
                    </span>
                    @endif
                    <div class="input-group-addon">
                        <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('buscador').value = '';cargar_buscador()"></i>
                    </div>
                    <div class="input-group-addon">
                        <i style="cursor: pointer" class="fa fa-search" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <div class="form-group col-md-5{{ $errors->has('resultado') ? ' has-error' : '' }}">
                <label for="resultado" class="control-label">Resultado </label>
                <input type="text" name="resultado" id="resultado" size="20" class="form-control" value="" required maxlength="255">
            </div>

            <!--div class="form-group col-md-2">
                <button type="button" class="btn btn-primary" onclick="agregarExamen()">Agregar</button>
            </div-->

        </form>
    </div>

    <br>


    <table style="width: 100%;">
        <tbody>
            @php
            $contador = 1;
            @endphp

            @foreach ($examen as $ex)
            <tr>
                <td>{{ $contador++ }}</td>
                <td>
                    <input style="width: 98%;" type="text" value="{{ $ex->nombre }} - @if(!is_null($ex->examen_parametro)) {{$ex->examen_parametro->nombre}} @endif" disabled>
                </td>
                <td>
                    <input style="width: 98%;" type="text" value="{{$ex->resultado}}" disabled>
                </td>
                <td>
                    <a onclick="eliminar_examen({{ $ex->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
                <td>
                    <hr>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>







</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/4.5.0/fabric.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script>
    $(document).ready(function() {
        $.ajax({
            url: "{{route('formulario.imagenpreanestico')}}",
            data: {
                id: $("#id").val(),
                tipo: 0
            },
            type: 'GET',
            dataType: 'html',
            success: function(response) {
                $("#vistaImagen").append(response);

            },
            error: function(error) {
                console.error(error);
            }
        });
    });


    function guardar() {
        var drogas = $("#drogas").val().trim();
        if (drogas === "") {
            alert("Por favor ingresa un valor en el campo de drogas.");
            return;
        }
        $.ajax({
            url: "{{route('formato.transanestesico_store')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            data: {
                drogas: $("#drogas").val(),
                id_anestesiologia: $("#id").val(),
            },
            success: function(response) {
                console.log(response);

                if (response.success) {
                  //  window.location.reload();
                  tabla_drogas();
                  $("#drogas").val("");
                }
                
            },
            error: function(error) {

                console.error(error);
            }
        });
    }

    function handlePublica(input) {
        let nombre, valor;

        if (input.name === 'sistema' || input.name === 'aparato' || input.name === 'manejo' || input.name === 'intubacion' || input.name === 'tipo_tubo' ||
            input.name === 'balon' || input.name === 'taponamiento' || input.name === 'cormack' || input.name === 'induccion' || input.name === 'mantenimiento' || input.name === 'intentos' ||
            input.name === 'asepsia' || input.name === 'bloqueo_de_nervio' || input.name === 'numero_intentos1' || input.name === 'habon' || input.name === 'bloqueo_plexo' ||
            input.name === 'numero_intentos2' || input.name === 'local' || input.name === 'anestesico' || input.name === 'coadyuvante' || input.name === 'intravenosa' ||
            input.name === 'tipo_aguja' || input.name === 'equipo' || input.name === 'borbotaje' || input.name === 'feto' || input.name === 'acceso' || input.name === 'raquidea' ||
            input.name === 'epidural' || input.name === 'caudal' || input.name === 'cateter' || input.name === 'neuroaxial_intentos' ||
            input.name === 'dermatoma' || input.name === 'posicion' || input.name === 'escala' || input.name === 'new_tecnicas_especiales' || input.name === 'duracion_anestesia' ||
            input.name === 'duracion_operacion' || input.name === 'sangre_derivados' || input.name === 'dextrosa' || input.name === 'lactato_ringer' || input.name === 'expansores' ||
            input.name === 'mantenimiento_temperatura' || input.name === 'apgar' || input.name === 'otros_temperatura' || input.name === 's1' || input.name === 's2' || input.name === 's3' ||
            input.name === 's4' || input.name === 's5' || input.name === 's6' || input.name === 'c1' || input.name === 'c2' || input.name === 'c3' || input.name === 'c4' || input.name === 'c5' ||
            input.name === 'c6' || input.name === 'destrosa10' || input.name === 'destrosa5' || input.name === 'destrosass' || input.name === 'ss_9' || input.name === 'plasma' ||
            input.name === 'crioprecipitados' || input.name === 'otros_reposicion' || input.name === 'otros_perdidas' || input.name === 'total_perdidas' || input.name === 'total_reposicion' ||
            input.name === 'balance' || input.name === 'otros_indice' || input.name === 'indice' || input.name === 'observaciones' || input.name === 'analgesia' || input.name === 'conducido' ||
            input.name === 'condiciones_al_salir' || input.name === 'puncion' || input.name === 'diuresis' || input.name === 'sangrado' || input.name === 'plaquetas' || input.name === 'ta' ||
            input.name === 'fc' || input.name === 'fr' || input.name === 'sat' || input.name === 't'
        ) {

            nombre = input.name;
            valor = input.value;
        } else {
            nombre = input.name;
            valor = input.checked ? 1 : 0;
        }
        $.ajax({
            url: "{{ route('formato.transanestesico_guardar') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            data: {
                [nombre]: valor,
                id: $("#id").val(),
                id_hc: $("#id_hc").val(),
                id_hc_procedimientos: $("#id_hc_procedimientos").val()
            },
            success: function(response) {
                console.log(response);
                if (response.success) {

                }
            },
            error: function(error) {
                console.error(error);
            }
        });
    }





    $("#buscador").autocomplete({
        source: function(request, response) {

            $.ajax({
                url: "{{route('examenes.buscar_aj')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    term: request.term
                },
                dataType: "json",
                type: 'post',
                success: function(data) {
                    response(data);
                    //console.log(data);

                },

            })
        },
        minLength: 3,
        select: function(data, ui) {
            console.log(ui);
            console.log(ui.item.label);
            $('#buscador').val(ui.item.label);
            $('#id_examen_buscador').val(ui.item.id);

        }
    });

    function actualiza_tecnicas(elemento) {


        let tecnica = elemento.value;
        let crear = '';
        if (elemento.checked) {
            crear = '1';
        } else {
            crear = 0;
        }
        $.ajax({
            url: "{{route('rc_pre_anestesica.actualiza_tecnicas')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            data: {

                id_hc_anestesiologia: $('#id').val(),
                tecnica: tecnica,
                crear: crear

            },
            success: function(response) {
                console.log(response);
                if (response.success) {
                    window.location.reload();
                }

            },
            error: function(error) {
                console.error(error);
            }
        });

    }


    function agregarExamen() {
        $.ajax({
            url: "{{route('formato.examen_preanestesico_store')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            data: {
                buscador: $('#buscador').val(),
                resultado: $('#resultado').val(),
                id_examen: $('#id_examen_buscador').val(),
                id_hc_anestesiologia: $('#id').val(),
                id_rc_pre_anestesico: $("#id_pre").val(),
            },
            success: function(response) {
                console.log(response);
                if (response.success) {
                    window.location.reload();
                }

            },
            error: function(error) {
                console.error(error);
            }
        });
    }


    function eliminar_examen(id) {
        Swal.fire({
            title: `Estas seguro de eliminar`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'get',
                    url: "{{url('examen/preanestesico/delete/')}}/" + id,
                    dataType: 'json',
                    success: function(data) {
                        location.reload();
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        });
    }

    function actualiza_trans() {

        $.ajax({
            url: "{{route('rc_pre_anestesica.guardar_trans')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            data: $('#form_trans').serialize(),
            success: function(response) {
                console.log(response);

            },
            error: function(error) {
                console.error(error);
            }
        });

    }
    function tabla_drogas(){

        $.ajax({
            type: 'get',
            url: "{{route('rc_pre_anestesica.carga_tabla_drogas',['id'=> $pre_anestesico->id_hc_anestesiologia])}}",
            dataType: 'html',
            success: function(data) {
            $('#tabla_drogas').html(data);
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
    
    function eliminar_droga(id) {
            Swal.fire({
                title: `Estas seguro de eliminar`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'get',
                        url: "{{url('drogas_administradas/transanestesico/delete/')}}/" + id,
                        dataType: 'json',
                        success: function(data) {
                            //location.reload();
                            tabla_drogas();
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            });
    }
    
</script>
@endsection