@extends('preanestesico.postanestesico')
@section('action-content')
<?php
$fecha = date('Y-m-d');
$hora = date('H:i:s');

$fecha_egreso = date('Y-m-d', strtotime($post_anestesico->historiaclinica->paciente->created_at));
$hora_egreso = date('H:i', strtotime($post_anestesico->historiaclinica->paciente->created_at));

$fechaNacimientoValor = trim((string) optional($post_anestesico->historiaclinica->paciente)->fecha_nacimiento);
$fechaNacimiento = null;

if ($fechaNacimientoValor !== '' && $fechaNacimientoValor !== '0000-00-00') {
    try {
        $fechaNacimiento = \Carbon\Carbon::parse($fechaNacimientoValor);
        if ($fechaNacimiento->isFuture()) {
            $fechaNacimiento = null;
        }
    } catch (\Throwable $exception) {
        $fechaNacimiento = null;
    }
}

$edad = '';
$unidadEdad = '';
if ($fechaNacimiento) {
    $diferenciaEdad = $fechaNacimiento->diff(now());
    if ($diferenciaEdad->y >= 1) {
        $edad = $diferenciaEdad->y;
        $unidadEdad = 'A';
    } elseif ($diferenciaEdad->m >= 1) {
        $edad = $diferenciaEdad->m;
        $unidadEdad = 'M';
    } else {
        $edad = $diferenciaEdad->days;
        $unidadEdad = 'D';
    }
}
$diaNacimiento = $fechaNacimiento ? $fechaNacimiento->format('d') : '';
$mesNacimiento = $fechaNacimiento ? $fechaNacimiento->format('m') : '';
$pacientePost = $post_anestesico->historiaclinica->paciente;
$nombrePacientePost = trim($pacientePost->apellido1 . ' ' . $pacientePost->apellido2 . ' ' . $pacientePost->nombre1 . ' ' . $pacientePost->nombre2);
$sexoPacientePost = $pacientePost->sexo == 1 ? 'Masculino' : 'Femenino';

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
</style>


<section class="content anesthesia-record-v3 postanestesico-v3" data-anesthesia-record>
    <input type="hidden" value="{{$post_anestesico->hc_anestesiologia->id}}" name="id" id="id" />

    <input type="hidden" value="" name="id_pre" id="id_pre" />



    <input type="hidden" value="{{$post_anestesico->id_hc}}" name="id_hc" id="id_hc">

    <div class="anesthesia-record-hero">
        <div class="anesthesia-record-hero__title">
            <span class="anesthesia-record-hero__icon"><i class="fa fa-heartbeat" aria-hidden="true"></i></span>
            <div><h1>Recuperación postanestésica</h1><p>Monitoreo, evolución y condiciones de egreso del paciente.</p></div>
        </div>
        <div class="anesthesia-record-hero__meta">
            <div class="anesthesia-save-status is-saved" data-anesthesia-save-status role="status" aria-live="polite"><i class="fa fa-check-circle" aria-hidden="true"></i><span>Guardado</span></div>
            <div class="anesthesia-record-chip">Paciente<strong>{{ strtoupper($nombrePacientePost) }}</strong></div>
            <div class="anesthesia-record-chip">Identificación<strong>{{ $pacientePost->id }}</strong></div>
            <div class="anesthesia-record-chip">Edad / sexo<strong>{{ $edad }} {{ $unidadEdad }} · {{ $sexoPacientePost }}</strong></div>
        </div>
    </div>

    <nav class="anesthesia-record-nav" aria-label="Secciones de recuperación postanestésica">
        <button type="button" data-scroll-target="post-procedimiento"><i class="fa fa-clipboard" aria-hidden="true"></i> Procedimiento</button>
        <button type="button" data-scroll-target="post-recepcion"><i class="fa fa-exchange" aria-hidden="true"></i> Recepción</button>
        <button type="button" data-scroll-target="post-monitorizacion"><i class="fa fa-line-chart" aria-hidden="true"></i> Monitorización</button>
        <button type="button" data-scroll-target="post-balance"><i class="fa fa-balance-scale" aria-hidden="true"></i> Ingresos y eliminación</button>
        <button type="button" data-scroll-target="post-evolucion"><i class="fa fa-file-text-o" aria-hidden="true"></i> Evolución</button>
        <button type="button" data-scroll-target="post-egreso"><i class="fa fa-sign-out" aria-hidden="true"></i> Egreso</button>
    </nav>



    <table class="anesthesia-patient-source" border="1" cellpadding="0" cellspacing="0" style="width: 100%">
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
                <td class="text-center" colspan="1">{{ $unidadEdad === 'H' ? 'X' : '' }}</td>
                <td class="text-center" colspan="1">{{ $unidadEdad === 'D' ? 'X' : '' }}</td>
                <td class="text-center" colspan="1">{{ $unidadEdad === 'M' ? 'X' : '' }}</td>
                <td class="text-center" colspan="1">{{ $unidadEdad === 'A' ? 'X' : '' }}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <table border="1" style="width: 100%">
        <thead>
            <tr>
                <td colspan="9" class="bg-blue"><b>B. PROCEDIMIENTO QUIRÚRGICO ANESTÉSICO</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 10%;" class="bg-green text-center"><b>ESPECIALIDAD</b></td>
                <td class="text-center" style="width: 40%;">{{ optional($especialidad)->nombre }}</td>
                <td style="width: 10%;" class="bg-green text-center"><b>DIAGNÓSTICO</b></td>
                <td colspan="6" style="width: 40%;"><input onchange="handlePublica(this)" type="text" name="diagnostico" id="diagnostico" style="width: 100%"></td>
            </tr>
            <tr>
                <td rowspan="2" style="width: 10%;" class="bg-green text-center"><b>CIRUGÍA REALIZADA</b></td>
                <td rowspan="2" style="width: 40%;"><input onchange="handlePublica(this)" type="text" name="cirugia_realizada" id="cirugia_realizada" style="width: 100%"></td>
                <td rowspan="2" style="width: 10%;" class="bg-green text-center"><b>TÉCNICA ANESTÉSICA</b></td>
                <td style="width: 10%;">GENERAL</td>
                <td style="width: 3%;" class="text-center"><input onchange="handlePublica(this)" type="checkbox" name="tecnica_anestesica" id="general" value="0" style="width: 100%"> </td>
                <td style="width: 10%;">LOCAL</td>
                <td style="width: 3%;" class="text-center"><input onchange="handlePublica(this)" type="checkbox" name="tecnica_anestesica" id="local" value="1" style="width: 100%"> </td>
                <td style="width: 10%;">TRONCULAR</td>
                <td style="width: 4%;" class="text-center"><input onchange="handlePublica(this)" type="checkbox" name="tecnica_anestesica" id="troncular" value="2" style="width: 100%"> </td>
            </tr>
            <tr>
                <td style="width: 10%;">NEUROAXIAL</td>
                <td style="width: 3%;" class="text-center"><input onchange="handlePublica(this)" type="checkbox" name="tecnica_anestesica" id="neuroaxial" value="3" style="width: 100%"> </td>
                <td style="width: 10%;">INTRAVENOSA</td>
                <td style="width: 3%;" class="text-center"><input onchange="handlePublica(this)" type="checkbox" name="tecnica_anestesica" id="intravenosa" value="4" style="width: 100%"> </td>
                <td style="width: 10%;">SEDACIÓN</td>
                <td style="width: 4%;" class="text-center"><input onchange="handlePublica(this)" type="checkbox" name="tecnica_anestesica" id="sedacion" value="5" style="width: 100%"> </td>
            </tr>
        </tbody>
    </table>
    <br>

    <table border="1" style="width: 100%">
        <thead>
            <tr>
                <td colspan="40" class="bg-blue"><b>C. ENTREGA - RECEPCIÓN DEL PACIENTE</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="3" class="text-center width100">INGRESO A UNIDAD CUIDADOS POSTANESTÉSICOS</td>
                <td rowspan="2" colspan="37" style="width: 60%;" class="text-center">ESCALAS / ÍNDICES / PUNTAJES</td>
                <td colspan="2" class="text-center width100"><b>RESPONSABLES</b></td>
            </tr>
            <tr>
                <td class="text-center" colspan="2"><b>ENTREGA</b></td>
            </tr>
            <tr>
                <td colspan="8" class="bg-green text-center"><b>ALDRETE MODIFICADO</b></td>
                <td colspan="12" class="bg-green text-center"><b>BROMAGE</b></td>
                <td colspan="14" class="bg-green text-center"><b>RAMSAY</b></td>
                <td rowspan="2" colspan="3" class="bg-green text-center"></td>
                <td>NOMBRE</td>
                <td style="width: 40%;"><input onchange="handlePublica(this)" type="text" name="nombre" id="nombre" style="width: 100%"></td>
            </tr>
            <tr>
                <td rowspan="2" style="width: 20%;" class="text-left">FECHA:</td>
                <td class="text-center width20">7</td>
                <td class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="aldrete_modificado" id="7" value="1"></td>
                <td class="text-center width20">8</td>
                <td class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="aldrete_modificado" id="8" value="2"></td>
                <td class="text-center width20">9</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="aldrete_modificado" id="9" value="3"> </td>
                <td class="text-center width20">10</td>
                <td class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="aldrete_modificado" id="10" value="4"> </td>
                <td colspan="2">100%</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="bromage" id="100" value="1"> </td>
                <td colspan="2">66%</td>
                <td class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="bromage" id="66" value="2"> </td>
                <td colspan="2">33%</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="bromage" id="33" value="3"></td>
                <td colspan="2">0%</td>
                <td class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="bromage" id="0" value="4"> </td>
                <td class="text-center width20">0</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="ramsay" id="0" value="1"></td>
                <td class="text-center width20">1</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="ramsay" id="1" value="2"></td>
                <td class="text-center width20">2</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="ramsay" id="2" value="3"></td>
                <td class="text-center width20">3</td>
                <td class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="ramsay" id="3" value="4"> </td>
                <td class="text-center width20">4</td>
                <td class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="ramsay" id="4" value="5"> </td>
                <td class="text-center width20">5</td>
                <td class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="ramsay" id="5" value="6"> </td>
                <td class="text-center width20">6</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="ramsay" id="6" value="7"></td>
                <td>FIRMA</td>
                <td style="width: 40%;"><input onchange="handlePublica(this)" type="text" name="firm" id="firm" style="width: 100%"></td>
            </tr>
            <tr>
                <td class="bg-green text-center" colspan="22">EVA</td>
                <td class="bg-green text-center" colspan="15">STEWARD</td>
                <td class="text-center" colspan="2"><b>RECIBE</b></td>
            </tr>
            <tr>
                <td rowspan="2" style="width: 20%;" class="text-left"> HORA:</td>
                <td class="text-center width20">0</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="eva" id="0" value="1"></td>
                <td class="text-center width20">1</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="eva" id="1" value="2"></td>
                <td class="text-center width20">2</td>
                <td class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="eva" id="2" value="3"> </td>
                <td class="text-center width20">3</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="eva" id="3" value="4"></td>
                <td class="text-center width20">4</td>
                <td class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="eva" id="4" value="5"> </td>
                <td class="text-center width20">5</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="eva" id="5" value="6"></td>
                <td class="text-center width20">6</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="eva" id="6" value="7"></td>
                <td class="text-center width20">7</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="eva" id="7" value="8"></td>
                <td class="text-center width20">8</td>
                <td class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="eva" id="8" value="9"> </td>
                <td class="text-center width20">9</td>
                <td class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="eva" id="9" value="10"> </td>
                <td class="text-center width20">10</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="eva" id="10" value="11"></td>
                <td class="text-center width20">0</td>
                <td class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="steward" id="0" value="1"> </td>
                <td class="text-center width20">1</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="steward" id="1" value="2"></td>
                <td class="text-center width20">2</td>
                <td class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="steward" id="2" value="3"> </td>
                <td class="text-center width20">3</td>
                <td class="text-center width20"><input onchange="handlePublica(this)" type="radio" name="steward" id="3" value="4"> </td>
                <td class="text-center width20">4</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="steward" id="4" value="5"></td>
                <td class="text-center width20">5</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="steward" id="5" value="6"></td>
                <td class="text-center width20" colspan="2">6</td>
                <td class="text-center width20"> <input onchange="handlePublica(this)" type="radio" name="steward" id="6" value="7"></td>
                <td>NOMBRE</td>
                <td style="width: 40%;"><input onchange="handlePublica(this)" type="text" name="name" id="name" style="width: 100%"></td>
            </tr>
            <tr>
                <td class="bg-green text-left" colspan="2">OTRO</td>
                <td class="text-center" colspan="35"><input onchange="handlePublica(this)" type="text" name="otro" id="otro" style="width: 100%"></td>
                <td class="text-left">FIRMA</td>
                <td style="width: 40%;"><input onchange="handlePublica(this)" type="text" name="firma" id="fir" style="width: 100%"></td>
            </tr>
        </tbody>
    </table>
    <br>
    <table border="1" style="width: 100%">
        <thead>
            <tr>
                <td colspan="7" class="bg-blue"><b>D. MONITORIZACIÓN</b></td>
            </tr>
            <tr>
                <td colspan="7" id="vistaImagen"></td>
            </tr>
            <tr>
                <td style="width: 2%;" class="bg-green text-left">ALDRETE MODIFICADO</td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_aldrete1" id="valor_aldrete1" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_aldrete2" id="valor_aldrete2" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_aldrete3" id="valor_aldrete3" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_aldrete4" id="valor_aldrete4" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_aldrete5" id="valor_aldrete5" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_aldrete6" id="valor_aldrete6" style="width: 100%"></td>

            </tr>
            <tr>
                <td style="width: 2%;" class="bg-green text-left">BROMAGE</td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_bromage1" id="valor_bromage1" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_bromage2" id="valor_bromage2" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_bromage3" id="valor_bromage3" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_bromage4" id="valor_bromage4" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_bromage5" id="valor_bromage5" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_bromage6" id="valor_bromage6" style="width: 100%"></td>
            </tr>
            <tr>
                <td style="width: 2%;" class="bg-green text-left">RAMSAY</td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_ramsay1" id="valor_ramsay1" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_ramsay2" id="valor_ramsay2" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_ramsay3" id="valor_ramsay3" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_ramsay4" id="valor_ramsay4" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_ramsay5" id="valor_ramsay5" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_ramsay6" id="valor_ramsay6" style="width: 100%"></td>

            </tr>
            <tr>
                <td style="width: 2%;" class="bg-green text-left">EVA</td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_eva1" id="valor_eva1" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_eva2" id="valor_eva2" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_eva3" id="valor_eva3" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_eva4" id="valor_eva4" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_eva5" id="valor_eva5" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_eva6" id="valor_eva6" style="width: 100%"></td>


            </tr>
            <tr>
                <td style="width: 2%;" class="bg-green text-left">STEWARD</td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_steward1" id="valor_steward1" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_steward2" id="valor_steward2" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_steward3" id="valor_steward3" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_steward4" id="valor_steward4" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_steward5" id="valor_steward5" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_steward6" id="valor_steward6" style="width: 100%"></td>

            </tr>
            <tr>
                <td style="width: 2%;" class="bg-green text-left">OTRO</td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_otro1" id="valor_otro1" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_otro2" id="valor_otro2" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_otro3" id="valor_otro3" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_otro4" id="valor_otro4" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_otro5" id="valor_otro5" style="width: 100%"></td>
                <td class="text-center" style="width: 14%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_otro6" id="valor_otro6" style="width: 100%"></td>
            </tr>
            </tbody>
    </table>
    <br>
    <table border="1" style="width: 100%">
        <thead>
            <tr>
                <td colspan="2" style="width: 50%;" class="bg-blue text-left"><b>E. INGRESOS</b></td>
                <td colspan="2" style="width: 50%;" class="bg-blue text-left"><b>F. ELIMINACIÓN</b></td>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td style="width: 25%;" class="text-left">ORAL</td>
                <td style="width: 25%;">
                    <input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="oral" id="oral" style="width: 100%">
                </td>

                <td style="width: 25%;">DIURESIS O SONDA VESICAL</td>
                <td style="width: 25%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="diuresis_sonda_vesical" id="diuresis_sonda_vesical" style="width: 100%"></td>
            </tr>
            <tr>
                <td style="width: 25%;" class="text-left">INFUSIONES</td>
                <td style="width: 25%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="infusion" id="infusion" style="width: 100%"></td>
                <td style="width: 25%;">VÓMITO O SONDA NASOGÁSTRICO</td>
                <td style="width: 25%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="vomito" id="vomito" style="width: 100%"></td>
            </tr>
            <tr>
                <td style="width: 25%;" class="text-left">SANGRE Y DERIVADOS</td>
                <td style="width: 25%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="sangre_derivados" id="sangre_derivados" style="width: 100%"></td>
                <td style="width: 25%;">TORÁCICO</td>
                <td style="width: 25%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="toracico" id="toracico" style="width: 100%"></td>
            </tr>
            <tr>
                <td style="width: 25%;" class="text-left">OTROS</td>
                <td style="width: 25%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="otros" id="otros" style="width: 100%"></td>
                <td style="width: 25%;">DRENAJES</td>
                <td style="width: 25%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="drenajes" id="drenajes" style="width: 100%"></td>
            </tr>
            <tr>
                <td rowspan="2" style="width: 25%;" class="text-left"><b>TOTAL</b></td>
                <td rowspan="2" style="width: 25%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="totals_texto" id="totals_texto" style="width: 100%; height: 55px;" readonly></td>
                <td style="width: 25%;">OTRAS</td>
                <td style="width: 25%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="eliminacion_otras" id="eliminacion_otras" style="width: 100%"></td>
            </tr>
            <tr>
                <td style="width: 25%;" class="text-left"><b>TOTAL</b></td>
                <td style="width: 25%;"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="total_eliminacion" id="total_eliminacion" style="width: 100%" readonly></td>
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
                <td><input onchange="handlePublica(this)" type="text" name="evolucion" id="evolucion" style="width: 100%"></td>
                <td><input onchange="handlePublica(this)" type="text" name="prescripcion" id="prescripcion" style="width: 100%"></td>
                <td><input onchange="handlePublica(this)" type="text" name="administracion_farmacos" id="administracion_farmacos" style="width: 100%"></td>
            </tr>
        </tbody>
    </table>
    <br>
    <table border="1" style="width: 100%">
        <thead>
            <tr>
                <td colspan="14" class="bg-blue"><b>H. CONDICIÓN DE EGRESO</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 20%;"><b>CONSTANTES VITALES DE ENTREGA</b></td>
                <td style="font-size: 18px">TA @for($count=0; $count<=10;$count++) &nbsp; @endfor <input onchange="handlePublica(this)" style="height: 25px;" name="constantes_vitales" id="ta" type="radio">
                </td>
                <td style="font-size: 18px">FC @for($count=0; $count<=10;$count++) &nbsp; @endfor <input onchange="handlePublica(this)" style="height: 25px;" name="constantes_vitales" id="fc" type="radio">
                </td>
                <td style="font-size: 18px">FR @for($count=0; $count<=10;$count++) &nbsp; @endfor <input onchange="handlePublica(this)" style="height: 25px;" name="constantes_vitales" id="fr" type="radio">
                </td>
                <td style="font-size: 18px">SAT. O2 @for($count=0; $count<=10;$count++) &nbsp; @endfor <input onchange="handlePublica(this)" style="height: 25px;" name="constantes_vitales" id="sat" type="radio">
                </td>
                <td style="font-size: 18px">T° @for($count=0; $count<=10;$count++) &nbsp; @endfor <input onchange="handlePublica(this)" style="height: 25px;" name="constantes_vitales" id="t" type="radio">
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
                <td><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_aldrete_egreso" id="valor_aldrete_egreso" style="width: 100%"></td>
                <td colspan="3" class="text-center"><b>ENTREGA</b></td>
            </tr>
            <tr>
                <td class="text-right">BROMAGE</td>
                <td><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_bromage_egreso" id="valor_bromage_egreso" style="width: 100%"></td>
                <td class="text-left"><b>NOMBRE</b></td>
                <td colspan="2"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="nom_texto" id="nom_texto" style="width: 100%"></td>
            </tr>
            <tr>
                <td class="text-left" rowspan="2"><b>FECHA:</b></td>
                <td class="text-right">RAMSAY</td>
                <td><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_ramsay_egreso" id="valor_ramsay_egreso" style="width: 100%"></td>
                <td class="text-left"><b>FIRMA</b></td>
                <td colspan="2"><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="firm_texto" id="firm_texto" style="width: 100%"></td>
            </tr>
            <tr>
                <td class="text-right">EVA</td>
                <td><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_eva_egreso" id="valor_eva_egreso" style="width: 100%"></td>
                <td colspan="3" class="text-center"><b>RECIBE</b></td>
            </tr>
            <tr>
                <td class="text-left" rowspan="2"><b>HORA:</b> </td>
                <td class="text-right">STEWARD</td>
                <td><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_steward_egreso" id="valor_steward_egreso" style="width: 100%"></td>
                <td class="text-left"><b>NOMBRE</b></td>
                <td colspan="2"><input onchange="handlePublica(this)" type="text" name="nb_texto" id="nb_texto" style="width: 100%"></td>
            </tr>
            <tr>
                <td class="text-right">OTRO:</td>
                <td><input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" onchange="handlePublica(this)" type="text" name="valor_otro_egreso" id="valor_otro_egreso" style="width: 100%"></td>
                <td class="text-left"><b>FIRMA</b></td>
                <td colspan="2"><input onchange="handlePublica(this)" type="text" name="firmatexto" id="firmatexto" style="width: 100%"></td>
            </tr>
        </tbody>
    </table>
    <br>
    <table border="1" style="width: 100%">
        <tr>
            <td rowspan="2" style="width: min-content;"><b>CONDICIONES AL SALIR</b></td>
            <td style="width: min-content;">EXTUBADO</td>
            <td class="text-center"><input onchange="handlePublica(this)" type="radio" name="condicion_salir" id="extubado" value="1"></td>

            <td style="width: min-content;" rowspan="2"><b>CONDUCIDO A:</b></td>

            <td style="width: min-content;" rowspan="2">HOSPITALIZACIÓN</td>
            <td class="text-center" rowspan="2"><input onchange="handlePublica(this)" type="radio" name="conducido_a" id="hospita" value="1"></td>

            <td style="width: min-content;" rowspan="2">UNIDAD CUIDADOS INTENSIVOS</td>
            <td class="text-center" rowspan="2"><input onchange="handlePublica(this)" type="radio" name="conducido_a" id="uci1" value="2"></td>

            <td style="width: min-content;" rowspan="2">CRÍTICOS DE EMERGENCIA</td>
            <td class="text-center" rowspan="2"><input onchange="handlePublica(this)" type="radio" name="conducido_a" id="cde" value="3"></td>

            <td style="width: min-content;" rowspan="2">DOMICILIO</td>
            <td class="text-center" rowspan="2"><input onchange="handlePublica(this)" type="radio" name="conducido_a" id="domi" value="4"></td>

            <td style="width: min-content;" rowspan="2">MORGUE</td>
            <td class="text-center" rowspan="2"><input onchange="handlePublica(this)" type="radio" name="conducido_a" id="morgue" value="5"></td>
        </tr>
        <tr>
            <td style="width: min-content;">INTUBADO</td>
            <td class="text-center"><input onchange="handlePublica(this)" type="radio" name="condicion_salir" id="intubado" value="2"></td>
        </tr>
    </table>
    <br>
    <div class="anesthesia-layout-end" hidden></div>
    <script type="application/json" class="anesthesia-layout-config">
    {"groups":[
        {"match":"B. PROCEDIMIENTO QUIRÚRGICO ANESTÉSICO","id":"post-procedimiento","title":"Procedimiento quirúrgico anestésico","description":"Especialidad, diagnóstico, cirugía y técnica utilizada.","icon":"fa-clipboard","open":true},
        {"match":"C. ENTREGA - RECEPCIÓN DEL PACIENTE","id":"post-recepcion","title":"Entrega y recepción del paciente","description":"Responsables y escalas clínicas al ingreso.","icon":"fa-exchange","open":true},
        {"match":"D. MONITORIZACIÓN","id":"post-monitorizacion","title":"Monitorización postanestésica","description":"Controles seriados de las escalas e índices clínicos.","icon":"fa-line-chart","open":true},
        {"match":"E. INGRESOS","id":"post-balance","title":"Ingresos y eliminación","description":"Balance de líquidos, pérdidas y totales calculados.","icon":"fa-balance-scale","open":true},
        {"match":"G. EVOLUCIÓN POSTANESTÉSICA","id":"post-evolucion","title":"Evolución postanestésica","description":"Evolución, prescripción y administración de fármacos.","icon":"fa-file-text-o","open":true},
        {"match":"H. CONDICIÓN DE EGRESO","id":"post-egreso","title":"Condición de egreso","description":"Constantes, escalas, responsables y destino del paciente.","icon":"fa-sign-out","open":true}
    ]}
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
                input.name === 'valor_eva_egreso' || input.name === 'valor_steward_egreso' || input.name === 'valor_otro_egreso' || input.name === 'fecha_egreso' || input.name === 'prescripcion' || input.name === 'administracion_farmacos'
            ) {

                nombre = input.name;
                valor = input.value;
            } else {
                nombre = input.name;
                valor = input.checked ? 1 : 0;
            }

            window.setAnesthesiaRecordStatus('saving');
            $.ajax({
                url: "{{ route('formato.postanestesico_guardar') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                data: {
                    [nombre]: valor,

                    id_hc: $("#id_hc").val(),


                },
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        window.setAnesthesiaRecordStatus('saved');
                        sumaTotales(input);
                    }
                },
                error: function(error) {
                    window.setAnesthesiaRecordStatus('error');
                    console.error(error);
                }
            });
        }

        function sumaTotales(input) {

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
        }
    </script>
</section>
@endsection
