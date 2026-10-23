@extends('preanestesico.postanestesico')
@section('action-content')
<?php
$fecha = date('Y-m-d');
$hora = date('H:i:s');

$fecha_egreso = date('Y-m-d', strtotime($post_anestesico->historiaclinica->paciente->created_at));
$hora_egreso = date('H:i', strtotime($post_anestesico->historiaclinica->paciente->created_at));

$fechaNacimiento = new DateTime($post_anestesico->historiaclinica->paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($post_anestesico->historiaclinica->paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($post_anestesico->historiaclinica->paciente->fecha_nacimiento);
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

    .container {
        display: grid;
        grid-template-columns: repeat(38, 1fr);
        gap: 2px;
        max-width: 100%;
        overflow: auto;
    }

    .bg-blue {
        background-color: #007bff;
        color: white;
        grid-column: span 38;
        text-align: center;
    }

    .bg-green {
        background-color: #28a745;
        color: white;
        text-align: center;
    }

    .text-center {
        text-align: center;
    }

    .text-left {
        text-align: left;
    }

    .width20 {
        width: 20px;
    }

    input[type="text"],
    input[type="radio"] {
        width: 100%;
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
    <input type="hidden" value="{{$post_anestesico->hc_anestesiologia->id}}" name="id" id="id" />

    <input type="hidden" value="" name="id_pre" id="id_pre" />



    <input type="hidden" value="{{$post_anestesico->id_hc}}" name="id_hc" id="id_hc">



    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
        <thead>
            <tr>
                <td colspan="14" style="font-size: 14px;" class="bg-blue"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</b></td>
            </tr>
        </thead>
        <tbody>
            <!--tr>
                <td class="bg-green text-center" style="width: 20%;"><b>INSTITUCION DEL SISTEMA</b></td>
                <td colspan="2" class="bg-green text-center"><b>UNICODIGO</b></td>
                <td colspan="3" class="bg-green text-center" style="width: 22%;"><b>ESTABLECIMIENTO DE SALUD</b></td>
                <td colspan="4" class="bg-green text-center"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                <td colspan="5" class="bg-green text-center"><b>NÚMERO DE ARCHIVO</b></td>
            </tr>
            <tr>
                <td class="text-center" style="font-size: 14px;"></td>
                <td class="text-center" colspan="2" style="font-size: 14px;">026247</td>
                <td colspan="3" class="text-center" style="font-size: 14px;"></td>
                <td class="text-center" colspan="4" style="font-size: 16px;"></td>
                <td colspan="5"></td>
            </tr-->
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
                <td class="text-center" colspan="2" style="height: 20px;">{{$post_anestesico->historiaclinica->paciente->apellido1}}</td>
                <td class="text-center" colspan="2">{{$post_anestesico->historiaclinica->paciente->apellido2}}</td>
                <td class="text-center" colspan="2">{{$post_anestesico->historiaclinica->paciente->nombre1}}</td>
                <td class="text-center" colspan="1">{{$post_anestesico->historiaclinica->paciente->nombre2}}</td>
                <td class="text-center" colspan="1">@if($post_anestesico->historiaclinica->paciente->sexo == '1') Masculino @ELSE Femenino @endif</td>

                <td class="text-center" colspan="2">{{$edad}}</td>
                <td class="text-center" colspan="1"></td>
                <td class="text-center" colspan="1">{{$diaNacimiento}}</td>
                <td class="text-center" colspan="1">{{$mesNacimiento}}</td>
                <td class="text-center" colspan="1">{{ date('Y', strtotime($post_anestesico->historiaclinica->paciente->fecha_nacimiento))}}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <table border="1" style="width: 100%">
        <thead>
            <tr>
                <td colspan="6" style="width: 100%;" class="bg-blue"><b>B. PROCEDIMIENTO QUIRÚRGICO ANESTÉSICO</b></td>
            </tr>
        </thead>
        <tbody>
            <!--tr>
                <td colspan="1" style="width: 20%;" class="bg-green text-center"><b>ESPECIALIDAD</b></td>
                <td colspan="5" class="text-center" style="width: 80%;"> {{$especialidad->nombre}}</td>
            </tr>
            <tr>
                <td colspan="1" style="width: 20%;" class="bg-green text-center"><b>DIAGNÓSTICO</b></td>
                <td colspan="5" style="width: 80%;"><input onchange="handlePublica(this)" type="text" name="diagnostico" id="diagnostico" style="width: 100%"></td>
            </tr>
            <tr>
                <td colspan="1" style="width: 20%;" class="bg-green text-center"><b>CIRUGÍA REALIZADA</b></td>
                <td colspan="5" style="width: 80%;"><input onchange="handlePublica(this)" type="text" name="cirugia_realizada" id="cirugia_realizada" style="width: 100%"></td>
            </tr-->
            <tr>
                <td colspan="6" style="width: 100%;" class="bg-green text-center"><b>TÉCNICA ANESTÉSICA</b></td>
            </tr>
            <tr>
                <td style="width: 25%;">GENERAL</td>
                <td style="width: 8%;" class="text-center"><input onchange="handlePublica(this)" type="checkbox" value="0" name="tecnica_anestesica" id="general" style="width: 100%" @if($post_anestesico->tecnica_anestesica == '0') checked @endif> </td>
                <td style="width: 25%;">LOCAL</td>
                <td style="width: 9%;" class="text-center"><input onchange="handlePublica(this)" type="checkbox" value="1" name="tecnica_anestesica" id="local" style="width: 100%" @if($post_anestesico->tecnica_anestesica == '1') checked @endif> </td>
                <td style="width: 25%;">TRONCULAR</td>
                <td style="width: 8%;" class="text-center"><input onchange="handlePublica(this)" type="checkbox" value="2" name="tecnica_anestesica" id="troncular" style="width: 100%" @if($post_anestesico->tecnica_anestesica == '2') checked @endif> </td>
            </tr>
            <tr>
                <td style="width: 25%;">NEUROAXIAL</td>
                <td style="width: 8%;" class="text-center"><input onchange="handlePublica(this)" type="checkbox" value="3" name="tecnica_anestesica" id="neuroaxial" style="width: 100%" @if($post_anestesico->tecnica_anestesica == '3') checked @endif> </td>
                <td style="width: 25%;">INTRAVENOSA</td>
                <td style="width: 9%;" class="text-center"><input onchange="handlePublica(this)" type="checkbox" value="4" name="tecnica_anestesica" id="intravenosa" style="width: 100%" @if($post_anestesico->tecnica_anestesica == '4') checked @endif> </td>
                <td style="width: 25%;">SEDACIÓN</td>
                <td style="width: 8%;" class="text-center"><input onchange="handlePublica(this)" type="checkbox" value="5" name="tecnica_anestesica" id="sedacion" style="width: 100%" @if($post_anestesico->tecnica_anestesica == '5') checked @endif> </td>
            </tr>
        </tbody>
    </table>
    <br>

    <div class="container" style="width: 100%">
        <div class="bg-blue"><b>C. ENTREGA - RECEPCIÓN DEL PACIENTE</b></div>

        <div class="text-center bg-green" style="grid-column: span 8;"><b>ALDRETE MODIFICADO</b></div>
        <div class="text-center bg-green" style="grid-column: span 12;"><b>BROMAGE</b></div>
        <div class="text-center bg-green" style="grid-column: span 18;"><b>RAMSAY</b></div>

        <div class="text-center width20">7</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="aldrete_modificado" value="1" @if($post_anestesico->aldrete_modificado == '1') checked @endif></div>
        <div class="text-center width20">8</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="aldrete_modificado" value="2" @if($post_anestesico->aldrete_modificado == '2') checked @endif></div>
        <div class="text-center width20">9</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="aldrete_modificado" value="3" @if($post_anestesico->aldrete_modificado == '3') checked @endif></div>
        <div class="text-center width20">10</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="aldrete_modificado" value="4" @if($post_anestesico->aldrete_modificado == '4') checked @endif></div>
        <div class="text-center" style="grid-column: span 2;">100%</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="bromage" value="1" @if($post_anestesico->bromage == '1') checked @endif></div>
        <div class="text-center" style="grid-column: span 2;">66%</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="bromage" value="2" @if($post_anestesico->bromage == '2') checked @endif></div>
        <div class="text-center" style="grid-column: span 2;">33%</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="bromage" value="3" @if($post_anestesico->bromage == '3') checked @endif></div>
        <div class="text-center" style="grid-column: span 2;">0%</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="bromage" value="4" @if($post_anestesico->bromage == '4') checked @endif></div>
        <div class="text-center width20">0</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="ramsay" value="1" @if($post_anestesico->ramsay == '1') checked @endif></div>
        <div class="text-center width20">1</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="ramsay" value="2" @if($post_anestesico->ramsay == '2') checked @endif></div>
        <div class="text-center width20">2</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="ramsay" value="3" @if($post_anestesico->ramsay == '3') checked @endif></div>
        <div class="text-center width20">3</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="ramsay" value="4" @if($post_anestesico->ramsay == '4') checked @endif></div>
        <div class="text-center width20">4</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="ramsay" value="5" @if($post_anestesico->ramsay == '5') checked @endif></div>
        <div class="text-center" style="grid-column: span 2;">5</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="ramsay" value="6" @if($post_anestesico->ramsay == '6') checked @endif></div>
        <div class="text-center" style="grid-column: span 2;">6</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="ramsay" value="7" @if($post_anestesico->ramsay == '7') checked @endif></div>

        <div class="bg-green text-center" style="grid-column: span 22;">EVA</div>
        <div class="bg-green text-center" style="grid-column: span 16;">STEWARD</div>

        <div class="text-center width20">0</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="eva" value="1" @if($post_anestesico->eva == '1') checked @endif></div>
        <div class="text-center width20">1</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="eva" value="2" @if($post_anestesico->eva == '2') checked @endif></div>
        <div class="text-center width20">2</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="eva" value="3" @if($post_anestesico->eva == '3') checked @endif></div>
        <div class="text-center width20">3</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="eva" value="4" @if($post_anestesico->eva == '4') checked @endif></div>
        <div class="text-center width20">4</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="eva" value="5" @if($post_anestesico->eva == '5') checked @endif></div>
        <div class="text-center width20">5</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="eva" value="6" @if($post_anestesico->eva == '6') checked @endif></div>
        <div class="text-center width20">6</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="eva" value="7" @if($post_anestesico->eva == '7') checked @endif></div>
        <div class="text-center width20">7</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="eva" value="8" @if($post_anestesico->eva == '8') checked @endif></div>
        <div class="text-center width20">8</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="eva" value="9" @if($post_anestesico->eva == '9') checked @endif></div>
        <div class="text-center width20">9</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="eva" value="10" @if($post_anestesico->eva == '10') checked @endif></div>
        <div class="text-center width20">10</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="eva" value="11" @if($post_anestesico->eva == '11') checked @endif></div>
        <div class="text-center width20">0</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="steward" value="1" @if($post_anestesico->steward == '1') checked @endif></div>
        <div class="text-center width20">1</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="steward" value="2" @if($post_anestesico->steward == '2') checked @endif></div>
        <div class="text-center width20">2</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="steward" value="3" @if($post_anestesico->steward == '3') checked @endif></div>
        <div class="text-center width20">3</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="steward" value="4" @if($post_anestesico->steward == '4') checked @endif></div>
        <div class="text-center width20">4</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="steward" value="5" @if($post_anestesico->steward == '5') checked @endif></div>
        <div class="text-center width20">5</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="steward" value="6" @if($post_anestesico->steward == '6') checked @endif></div>
        <div class="text-center width20">6</div>
        <div class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="steward" value="7" @if($post_anestesico->steward == '7') checked @endif></div>

        <div class="bg-green text-left" style="grid-column: span 3;">OTRO</div>
        <div class="text-center" style="grid-column: span 35;"><input input onchange="handlePublica(this)" type="text" name="otro" value="{{$post_anestesico->otro}}"></div>
    </div>
    <br>
    <div class="container" style="width: 100%">
        <div class="bg-blue"><b>D. MONITORIZACIÓN</b></div>
        <div id="vistaImagen" style="grid-column: span 36;"></div>

        <div class="bg-green text-left" style="grid-column: span 6;">ALDRETE MODIFICADO</div>
        <div class="text-center" style="grid-column: span 5;"><input value="{{$post_anestesico->valor_aldrete1}}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_aldrete1" id="valor_aldrete1"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_aldrete2" id="valor_aldrete2" value="{{$post_anestesico->valor_aldrete2}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_aldrete3" id="valor_aldrete3" value="{{$post_anestesico->valor_aldrete3}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_aldrete4" id="valor_aldrete4" value="{{$post_anestesico->valor_aldrete4}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_aldrete5" id="valor_aldrete5" value="{{$post_anestesico->valor_aldrete5}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_aldrete6" id="valor_aldrete6" value="{{$post_anestesico->valor_aldrete6}}"></div>

        <div class="bg-green text-left" style="grid-column: span 6;">BROMAGE</div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_bromage1" id="valor_bromage1" value="{{$post_anestesico->valor_bromage1}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_bromage2" id="valor_bromage2" value="{{$post_anestesico->valor_bromage2}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_bromage3" id="valor_bromage3" value="{{$post_anestesico->valor_bromage3}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_bromage4" id="valor_bromage4" value="{{$post_anestesico->valor_bromage4}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_bromage5" id="valor_bromage5" value="{{$post_anestesico->valor_bromage5}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_bromage6" id="valor_bromage6" value="{{$post_anestesico->valor_bromage6}}"></div>

        <div class="bg-green text-left" style="grid-column: span 6;">RAMSAY</div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_ramsay1" id="valor_ramsay1" value="{{$post_anestesico->valor_ramsay1}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_ramsay2" id="valor_ramsay2" value="{{$post_anestesico->valor_ramsay2}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_ramsay3" id="valor_ramsay3" value="{{$post_anestesico->valor_ramsay3}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_ramsay4" id="valor_ramsay4" value="{{$post_anestesico->valor_ramsay4}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_ramsay5" id="valor_ramsay5" value="{{$post_anestesico->valor_ramsay5}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_ramsay6" id="valor_ramsay6" value="{{$post_anestesico->valor_ramsay6}}"></div>

        <div class="bg-green text-left" style="grid-column: span 6;">EVA</div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_eva1" id="valor_eva1" value="{{$post_anestesico->valor_ramsay6}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_eva2" id="valor_eva2" value="{{$post_anestesico->valor_ramsay6}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_eva3" id="valor_eva3" value="{{$post_anestesico->valor_ramsay6}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_eva4" id="valor_eva4" value="{{$post_anestesico->valor_ramsay6}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_eva5" id="valor_eva5" value="{{$post_anestesico->valor_ramsay6}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_eva6" id="valor_eva6" value="{{$post_anestesico->valor_ramsay6}}"></div>

        <div class="bg-green text-left" style="grid-column: span 6;">STEWARD</div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_steward1" id="valor_steward1" value="{{$post_anestesico->valor_steward1}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_steward2" id="valor_steward2" value="{{$post_anestesico->valor_steward2}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_steward3" id="valor_steward3" value="{{$post_anestesico->valor_steward3}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_steward4" id="valor_steward4" value="{{$post_anestesico->valor_steward4}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_steward5" id="valor_steward5" value="{{$post_anestesico->valor_steward5}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_steward6" id="valor_steward6" value="{{$post_anestesico->valor_steward6}}"></div>

        <div class="bg-green text-left" style="grid-column: span 6;">OTRO</div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_otro1" id="valor_otro1" value="{{$post_anestesico->valor_otro1}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_otro2" id="valor_otro2" value="{{$post_anestesico->valor_otro2}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_otro3" id="valor_otro3" value="{{$post_anestesico->valor_otro3}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_otro4" id="valor_otro4" value="{{$post_anestesico->valor_otro4}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_otro5" id="valor_otro5" value="{{$post_anestesico->valor_otro5}}"></div>
        <div class="text-center" style="grid-column: span 5;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_otro6" id="valor_otro6" value="{{$post_anestesico->valor_otro6}}"></div>
    </div>
    <br>
    <table border="1" style="width: 100%">
        <thead>
            <tr>
                <td colspan="2" style="width: 50%;" class="bg-blue text-left"><b>E. INGRESOS</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 50%;" class="text-left">ORAL</td>
                <td style="width: 50%;">
                    <input onchange="handlePublica(this)" type="text" name="oral" id="oral" style="width: 100%" value="{{$post_anestesico->oral}}">
                </td>
            </tr>
            <tr>
                <td style="width: 50%;" class="text-left">INFUSIONES</td>
                <td style="width: 50%;"><input onchange="handlePublica(this)" type="text" name="infusion" id="infusion" style="width: 100%" value="{{$post_anestesico->infusion}}"></td>
            </tr>
            <tr>
                <td style="width: 50%;" class="text-left">SANGRE Y DERIVADOS</td>
                <td style="width: 50%;"><input onchange="handlePublica(this)" type="text" name="sangre_derivados" id="sangre_derivados" style="width: 100%" value="{{$post_anestesico->sangre_derivados}}"></td>
            </tr>
            <tr>
                <td style="width: 50%;" class="text-left">OTROS</td>
                <td style="width: 50%;"><input onchange="handlePublica(this)" type="text" name="otros" id="otros" style="width: 100%" value="{{$post_anestesico->otros}}"></td>
            </tr>
            <tr>
                <td style="width: 50%;" class="text-left"><b>TOTAL</b></td>
                <td style="width: 50%;"><input onchange="handlePublica(this)" type="text" name="total_ingresos" id="total_ingresos" style="width: 100%; height: 55px;" value="{{$post_anestesico->total_ingresos}}"></td>
            </tr>
        </tbody>
    </table>
    <table border="1" style="width: 100%">
        <thead>
            <tr>
                <td colspan="2" style="width: 50%;" class="bg-blue text-left"><b>F. ELIMINACIÓN</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 50%;">DIURESIS O SONDA VESICAL</td>
                <td style="width: 50%;"><input onchange="handlePublica(this)" type="text" name="diuresis_sonda_vesical" id="diuresis_sonda_vesical" style="width: 100%" value="{{$post_anestesico->diuresis_sonda_vesical}}"></td>
            </tr>
            <tr>
                <td style="width: 50%;">VÓMITO O SONDA NASOGÁSTRICO</td>
                <td style="width: 50%;"><input onchange="handlePublica(this)" type="text" name="vomito" id="vomito" style="width: 100%" value="{{$post_anestesico->vomito}}"></td>
            </tr>
            <tr>
                <td style="width: 50%;">TORÁCICO</td>
                <td style="width: 50%;"><input onchange="handlePublica(this)" type="text" name="toracico" id="toracico" style="width: 100%" value="{{$post_anestesico->toracico}}"></td>
            </tr>
            <tr>
                <td style="width: 50%;">DRENAJES</td>
                <td style="width: 50%;"><input onchange="handlePublica(this)" type="text" name="drenajes" id="drenajes" style="width: 100%" value="{{$post_anestesico->drenajes}}"></td>
            </tr>
            <tr>
                <td style="width: 50%;">OTRAS</td>
                <td style="width: 50%;"><input onchange="handlePublica(this)" type="text" name="eliminacion_otras" id="eliminacion_otras" style="width: 100%" value="{{$post_anestesico->eliminacion_otras}}"></td>
            </tr>
            <tr>
                <td style="width: 50%;" class="text-left"><b>TOTAL</b></td>
                <td style="width: 50%;"><input onchange="handlePublica(this)" type="text" name="total_eliminacion" id="total_eliminacion" style="width: 100%" value="{{$post_anestesico->total_eliminacion}}"></td>
            </tr>
        </tbody>
    </table>
    
    <br>


    <table border="1" style="width: 100%">
        <thead>
            <tr>
                <td colspan="5" class="bg-blue"><b>G. EVOLUCIÓN POSTANESTÉSICA</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="bg-green text-center">FECHA</td>
                <td class="bg-green text-center">HORA</td>
                <td class="bg-green text-center"><b>EVOLUCIÓN</b></td>
                <td class="bg-green text-center"><b>PRESCRIPCIÓN</b></td>
                <td class="bg-green text-center">ADMINISTRACIÓN FÁRMACOS</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td><input onchange="handlePublica(this)" type="text" name="evolucion" id="evolucion" style="width: 100%" value="{{$post_anestesico->evolucion}}"></td>
                <td><input onchange="handlePublica(this)" type="text" name="prescripcion" id="prescripcion" style="width: 100%" value="{{$post_anestesico->prescripcion}}"></td>
                <td><input onchange="handlePublica(this)" type="text" name="administracion_farmacos" id="administracion_farmacos" style="width: 100%" value="{{$post_anestesico->administracion_farmacos}}"></td>
            </tr>
        </tbody>
    </table>
    <br>
    <div class="table-responsive">
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="14" class="bg-blue"><b>H. CONDICIÓN DE EGRESO</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 20%;"><b>CONSTANTES VITALES DE ENTREGA</b></td>
                    <td style="font-size: 18px">TA <input onchange="handlePublica(this)" style="width: 150px;" value="{{$post_anestesico->presion}}" name="presion" id="ta" type="text">
                    </td>
                    <td style="font-size: 18px">FC <input onchange="handlePublica(this)" style="width: 150px;" value="{{$post_anestesico->pulso}}" name="pulso" id="fc" type="text">
                    </td>
                    <td style="font-size: 18px">FR <input onchange="handlePublica(this)" style="width: 150px;" value="{{$post_anestesico->fecuencia_respiratoria}}" name="fecuencia_respiratoria" id="fr" type="text">
                    </td>
                    <td style="font-size: 18px">SAT. O2 <input onchange="handlePublica(this)" style="width: 150px;" value="{{$post_anestesico->o2}}" name="o2" id="sat" type="text">
                    </td>
                    <td style="font-size: 18px">T° <input onchange="handlePublica(this)" style="width: 150px;" value="{{$post_anestesico->temperatura}}" name="temperatura" id="t" type="text">
                    </td>
                </tr>
                <tr>
                    <td style="width: 50px;" rowspan="3"><b>EGRESO DE LA UNIDAD CUIDADOS POSTANESTÉSICOS</b></td>
                    <td class="text-center"><b>ESCALA / ÍNDICE</b></td>
                    <td class="text-center"><b>PUNTAJE</b></td>
                    <td colspan="3" class="text-center"><b>RESPONSABLES</b></td>
                </tr>
                <tr>
                    <td class="text-right">ALDRETE MODIFICADO</td>
                    <td><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_aldrete_egreso" id="valor_aldrete_egreso" style="width: 100%" value="{{$post_anestesico->valor_aldrete_egreso}}"></td>
                    <td colspan="3" class="text-center"><b>ENTREGA</b></td>
                </tr>
                <tr>
                    <td class="text-right">BROMAGE</td>
                    <td><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_bromage_egreso" id="valor_bromage_egreso" style="width: 100%" value="{{$post_anestesico->valor_bromage_egreso}}"></td>
                    <td class="text-left"><b>NOMBRE</b></td>
                    <td colspan="2"><input onchange="handlePublica(this)" type="text" name="nom_texto" id="nom_texto" style="width: 100%" readonly value="@if(!is_null($anestesiologia->anestesiologo)){{$anestesiologia->anestesiologo->apellido1}}  {{$anestesiologia->anestesiologo->nombre1}} @endif"></td>
                </tr>
                <tr>
                    <td class="text-left" rowspan="2"><b>FECHA:</b></td>
                    <td class="text-right">RAMSAY</td>
                    <td><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_ramsay_egreso" id="valor_ramsay_egreso" style="width: 100%" value="{{$post_anestesico->valor_ramsay_egreso}}"></td>
                    <td class="text-left"><b>FIRMA</b></td>
                    <td colspan="2"><input onchange="handlePublica(this)" type="text" name="firm_texto" id="firm_texto" style="width: 100%" readonly></td>
                </tr>
                <tr>
                    <td class="text-right">EVA</td>
                    <td><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_eva_egreso" id="valor_eva_egreso" style="width: 100%" value="{{$post_anestesico->valor_eva_egreso}}"></td>
                    <td colspan="3" class="text-center"><b>RECIBE</b></td>
                </tr>
                <tr>
                    <td class="text-left" rowspan="2"><b>HORA:</b> </td>
                    <td class="text-right">STEWARD</td>
                    <td><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_steward_egreso" id="valor_steward_egreso" style="width: 100%" value="{{$post_anestesico->valor_steward_egreso}}"></td>
                    <td class="text-left"><b>NOMBRE</b></td>
                    <td colspan="2"><input onchange="handlePublica(this)" type="text" name="nb_texto" id="nb_texto" style="width: 100%" readonly value="@if(!is_null($anestesiologia->anestesiologo)){{$anestesiologia->anestesiologo->apellido1}}  {{$anestesiologia->anestesiologo->nombre1}} @endif"></td>
                </tr>
                <tr>
                    <td class="text-right">OTRO:</td>
                    <td><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_otro_egreso" id="valor_otro_egreso" style="width: 100%" value="{{$post_anestesico->valor_otro_egreso}}"></td>
                    <td class="text-left"><b>FIRMA</b></td>
                    <td colspan="2"><input onchange="handlePublica(this)" type="text" name="firmatexto" id="firmatexto" style="width: 100%" readonly></td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <table border="1" style="width: 100%">
        <tr>
            <td rowspan="2" style="width: min-content;"><b>CONDICIONES AL SALIR</b></td>
            <td style="width: min-content;">EXTUBADO</td>
            <td class="text-center"><input onchange="handlePublica(this)" type="radio" value="1" name="condiciones_al_salir" id="exturbado" @if($post_anestesico->condiciones_al_salir == '1') checked @endif ></td>
            <td style="width: min-content;" rowspan="2"><b>CONDUCIDO A:</b></td>
            <td style="width: min-content;">HOSPITALIZACIÓN</td>
            <td class="text-center"><input onchange="handlePublica(this)" type="radio" value="0" name="conducido" id="hospitalizacion" @if($post_anestesico->conducido == '0') checked @endif></td>
            <td style="width: min-content;">CRÍTICOS DE EMERGENCIA</td>
            <td class="text-center"><input onchange="handlePublica(this)" type="radio" value="2" name="conducido" id="criticos_emergencia" @if($post_anestesico->conducido == '2') checked @endif></td>
            <td style="width: min-content;">MORGUE</td>
            <td class="text-center"><input onchange="handlePublica(this)" type="radio" value="4" name="conducido" id="morgue" @if($post_anestesico->conducido == '4') checked @endif></td>
        </tr>
        <tr>
            <td style="width: min-content;">INTUBADO</td>
            <td class="text-center"><input onchange="handlePublica(this)" type="radio" name="condiciones_al_salir" id="intubado" value="2" @if($post_anestesico->condiciones_al_salir == '2') checked @endif></td>
            <td style="width: min-content;">UNIDAD CUIDADOS INTENSIVOS</td>
            <td class="text-center"><input onchange="handlePublica(this)" type="radio" value="1" name="conducido" id="cuidados_intensivos" @if($post_anestesico->conducido == '1') checked @endif></td>
            <td style="width: min-content;">DOMICILIO</td>
            <td class="text-center"><input onchange="handlePublica(this)" type="radio" value="3" name="conducido" id="domicilio" @if($post_anestesico->conducido == '3') checked @endif></td>
        </tr>
    </table>
    <br>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/4.5.0/fabric.min.js"></script>

    <script src="{{ asset ('/js/jquery-ui.js')}}"></script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: "{{route('formulario.imagenpreanestico')}}",
                data: {
                    id: $("#id").val(),
                    tipo: 1
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

        function handlePublica(input) {

            let nombre, valor;

            if (input.name === 'valor_aldrete1' || input.name === 'valor_aldrete2' || input.name === 'valor_aldrete3' || input.name === 'valor_aldrete4' || input.name === 'valor_aldrete5' || input.name === 'valor_aldrete6' ||
                input.name === 'valor_bromage1' || input.name === 'valor_bromage2' || input.name === 'valor_bromage3' || input.name === 'valor_bromage4' || input.name === 'valor_bromage5' || input.name === 'valor_bromage6' ||
                input.name === 'valor_ramsay1' || input.name === 'valor_ramsay2' || input.name === 'valor_ramsay3' || input.name === 'valor_ramsay4' || input.name === 'valor_ramsay5' || input.name === 'valor_ramsay6' ||
                input.name === 'valor_eva1' || input.name === 'valor_eva2' || input.name === 'valor_eva3' || input.name === 'valor_eva4' || input.name === 'valor_eva5' || input.name === 'valor_eva6' ||
                input.name === 'valor_steward1' || input.name === 'valor_steward2' || input.name === 'valor_steward3' || input.name === 'valor_steward4' || input.name === 'valor_steward5' || input.name === 'valor_steward6' ||
                input.name === 'valor_otro1' || input.name === 'valor_otro2' || input.name === 'valor_otro3' || input.name === 'valor_otro4' || input.name === 'valor_otro5' || input.name === 'valor_otro6' ||
                input.name === 'condicion_salir' || input.name === 'conducido_a' || input.name === 'aldrete_modificado' || input.name === 'especialidad_select' ||
                input.name === 'evolucion' || input.name === 'bromage' || input.name === 'ramsay' || input.name === 'eva' || input.name === 'steward' || input.name === 'tecnica_anestesica' ||
                input.name === 'otro' || input.name === 'oral' || input.name === 'infusion' || input.name === 'sangre_derivados' || input.name === 'otros' || input.name === 'diuresis_sonda_vesical' || input.name === 'vomito' ||
                input.name === 'toracico' || input.name === 'drenajes' || input.name === 'eliminacion_otras' || input.name === 'valor_aldrete_egreso' || input.name === 'valor_bromage_egreso' || input.name === 'valor_ramsay_egreso' ||
                input.name === 'valor_eva_egreso' || input.name === 'valor_steward_egreso' || input.name === 'valor_otro_egreso' || input.name === 'fecha_egreso' || input.name === 'prescripcion' || input.name === 'administracion_farmacos' ||
                input.name === 'total_eliminacion' || input.name === 'total_ingresos' || input.name === 'presion' || input.name === 'pulso' || input.name === 'fecuencia_respiratoria' || input.name === 'o2' || input.name === 'temperatura' || 
                input.name === 'condiciones_al_salir' || input.name === 'conducido'
            ) {

                nombre = input.name;
                valor = input.value;
            } else {
                nombre = input.name;
                valor = input.checked ? 1 : 0;
            }

            $.ajax({
                url: "{{ route('formato.postanestesico_guardar') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                data: {
                    [nombre]: valor,
                    id_hc: $("#id_hc").val(),
                    id_hc_anestesiologia: $("#id").val(),


                },
                success: function(response) {
                    console.log(response);
                    if (response.success) {

                        //sumaTotales(input);
                    }
                },
                error: function(error) {
                    console.error(error);
                }
            });
        }

        /*function sumaTotales(input) {

            var valorOral = parseFloat(document.getElementById('oral').value) || 0;
            var valorInfusion = parseFloat(document.getElementById('infusion').value) || 0;
            var valorDerivados = parseFloat(document.getElementById('sangre_derivados').value) || 0;
            var valorOtros = parseFloat(document.getElementById('otros').value) || 0;


            var suma = valorOral + valorInfusion + valorDerivados + valorOtros;


            document.getElementById('totals_texto').value = suma;


            var valorDiuresis = parseFloat(document.getElementById('diuresis_sonda_vesical').value) || 0;
            var valorVomito = parseFloat(document.getElementById('vomito').value) || 0;
            var valorToracico = parseFloat(document.getElementById('toracico').value) || 0;
            var valorDrenaje = parseFloat(document.getElementById('drenajes').value) || 0;
            var valorOtroseli = parseFloat(document.getElementById('eliminacion_otras').value) || 0;


            var suma2 = valorDiuresis + valorVomito + valorToracico + valorDrenaje + valorOtroseli;


            document.getElementById('total_eliminacion').value = suma2;
        }*/
    </script>
</section>
@endsection