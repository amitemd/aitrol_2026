@extends('preanestesico.transanestesico')
@section('action-content')


@php
$fecha=date('Y-m-d');
$hora = date('H:i:s');
@endphp


<?php
$fechaNacimientoValor = trim((string) optional($transanestesico->historia->paciente)->fecha_nacimiento);
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
$pacienteTrans = $transanestesico->historia->paciente;
$nombrePacienteTrans = trim($pacienteTrans->apellido1 . ' ' . $pacienteTrans->apellido2 . ' ' . $pacienteTrans->nombre1 . ' ' . $pacienteTrans->nombre2);
$sexoPacienteTrans = $pacienteTrans->sexo == 1 ? 'Masculino' : 'Femenino';
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

    .bg-light-green {
        background-color: #7FE290;
    }
</style>
<section class="content anesthesia-record-v3 transanestesico-v3" data-anesthesia-record>


    <input type="hidden" value="{{$transanestesico->id}}" name="id" id="id" />
    <input type="hidden" value="{{$pre_anestesico->id}}" name="id_pre" id="id_pre" />
    <input type="hidden" value="{{$transanestesico->id_hc}}" name="id_hc" id="id_hc" />
    <input type="hidden" value="{{$transanestesico->id_hc_procedimientos}}" name="id_hc_procedimientos" id="id_hc_procedimientos" />

    <div class="anesthesia-record-hero">
        <div class="anesthesia-record-hero__title">
            <span class="anesthesia-record-hero__icon"><i class="fas fa-procedures" aria-hidden="true"></i></span>
            <div><h1>Registro transanestésico</h1><p>Seguimiento anestésico y registro intraoperatorio del paciente.</p></div>
        </div>
        <div class="anesthesia-record-hero__meta">
            <div class="anesthesia-save-status is-saved" data-anesthesia-save-status role="status" aria-live="polite"><i class="fa fa-check-circle" aria-hidden="true"></i><span>Guardado</span></div>
            <div class="anesthesia-record-chip">Paciente<strong>{{ strtoupper($nombrePacienteTrans) }}</strong></div>
            <div class="anesthesia-record-chip">Identificación<strong>{{ $pacienteTrans->id }}</strong></div>
            <div class="anesthesia-record-chip">Edad / sexo<strong>{{ $edad }} {{ $unidadEdad }} · {{ $sexoPacienteTrans }}</strong></div>
        </div>
    </div>

    <nav class="anesthesia-record-nav" aria-label="Secciones del registro transanestésico">
        <button type="button" data-scroll-target="trans-region"><i class="fa fa-map-marker" aria-hidden="true"></i> Región operatoria</button>
        <button type="button" data-scroll-target="trans-registro"><i class="fa fa-area-chart" aria-hidden="true"></i> Registro</button>
        <button type="button" data-scroll-target="trans-medicacion"><i class="fa fa-medkit" aria-hidden="true"></i> Medicación</button>
        <button type="button" data-scroll-target="trans-tecnica"><i class="fa fa-stethoscope" aria-hidden="true"></i> Técnica</button>
        <button type="button" data-scroll-target="trans-balance"><i class="fa fa-balance-scale" aria-hidden="true"></i> Balance y controles</button>
        <button type="button" data-scroll-target="trans-incidentes"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Incidentes</button>
        <button type="button" data-scroll-target="trans-egreso"><i class="fa fa-sign-out" aria-hidden="true"></i> Egreso</button>
    </nav>

    <table class="anesthesia-patient-source" border="1" cellpadding="0" cellspacing="0" style="width: 100%">
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
                <td colspan="5" class="bg-purple" style="font-size: 10;"><b>REGIÓN OPERATORIA</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">
                    CABEZA &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="cabeza" id="cabeza" required="required">
                </td>
                <td class="text-center">
                    ORGANOS &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="organos" id="organos" required="required">
                </td>
                <td class="text-center">
                    CUELLO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="cuello" id="cuello" required="required">
                </td>
                <td class="text-center">
                    COLUMNA &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="columna" id="columna" required="required">
                </td>
                <td class="text-center">
                    TORÁX &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="torax" id="torax" required="required">
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    ABDOMEN &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="abdomen" id="abdomen" required="required">
                </td>
                <td class="text-center">
                    PELVIS &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="pelvis" id="pelvis" required="required">
                </td>
                <td class="text-center">
                    EXTREMIDADES SUPERIORES &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="e_superiores" id="e_superiores" required="required">
                </td>

                <td class="text-center">
                    EXTREMIDADES INFERIORES &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="e_inferiores" id="e_inferiores" required="required">
                </td>

                <td class="text-center">
                    PERINEAL &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" name="perineal" id="perineal" required="required">
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
                    <div id="vistaImagen" aria-label="Editor del registro transanestésico"></div>
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
                <td colspan="3">
                    <div class="alert alert-info">Seleccione una orden activa. La administración quedará vinculada al episodio y registrará los cinco correctos.</div>
                    <div class="row">
                        <div class="col-md-4"><label>Orden médica</label><select id="drug_recipe_detail" class="form-control"><option value="">Seleccione</option>@foreach($medicationOrders as $order)<option value="{{$order->id}}" data-dose="{{$order->dosis}}">{{$order->nombre}} — {{$order->dosis}}</option>@endforeach</select></div>
                        <div class="col-md-2"><label>Dosis aplicada</label><input id="drug_dose" class="form-control"></div>
                        <div class="col-md-2"><label>Vía</label><select id="drug_route" class="form-control"><option value="">Seleccione</option><option value="INTRAVENOUS">Intravenosa</option><option value="INTRAMUSCULAR">Intramuscular</option><option value="INHALATION">Inhalatoria</option><option value="OTHER">Otra</option></select></div>
                        <div class="col-md-2"><label>Hora programada</label><input id="drug_scheduled" type="datetime-local" class="form-control" value="{{date('Y-m-d\TH:i')}}"></div>
                        <div class="col-md-2"><label>Hora real</label><input id="drug_administered" type="datetime-local" class="form-control" value="{{date('Y-m-d\TH:i')}}"></div>
                    </div>
                    <div class="row" style="margin-top:8px;">
                        <div class="col-md-3"><label>Serie</label><input id="drug_series" class="form-control"></div>
                        <div class="col-md-5"><label>Resultado u observación</label><input id="drug_result" class="form-control"></div>
                        <div class="col-md-4" style="padding-top:24px;">
                            <label><input id="drug_patient_verified" type="checkbox"> Paciente verificado</label>&nbsp;
                            <label><input id="drug_medication_verified" type="checkbox"> Orden verificada</label>
                            <button class="btn btn-primary bg-green" id="boton_enviar" onclick="guardar();" type="button"><i class="fa fa-shield"></i> Registrar eMAR</button>
                        </div>
                    </div>
                </td>
            </tr>
            @php
            $contador = 1;
            @endphp
            @foreach ($drogas as $rc)
            <tr>
                <td>{{ $contador++ }}</td>
                <td>
                    <input style="width: 98%;" type="text" value="{{ $rc->drogas }}" disabled>
                    <small class="text-warning">Registro histórico previo a eMAR</small>
                </td>
                <td>
                    <a onclick="eliminar({{ $rc->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
                <td>
                    <hr>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

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
                <td class="">
                    ABIERTO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" name="sistema" value="abierto" id="abierto" required="required">
                </td>
                <td class="">
                    SEMICERRADO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" name="sistema" value="semicerrado" id="semicerrado" required="required">
                </td>
                <td class="">
                    CERRADO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" name="sistema" value="cerrado" id="cerrado" required="required">
                </td>

                <td class="">
                    CIRCUITO CIRCULAR &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="circuito_circular" name="aparato" id="circuito_circular" required="required">
                </td>
                <td class="">
                    UNIDIRECCIONAL &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="unidireccional" name="aparato" id="unidireccional" required="required">
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-center bg-light-green" style="width: 60%;"><b>MANEJO DE VÍA AÉREA</b></td>
            </tr>
            <tr>
                <td colspan="2">
                    MÁSCARA FACIAL &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="mascara" name="manejo" id="mascara" required="required">
                </td>
                <td colspan="2">
                    SUPRAGLÓTICA &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="supraglotica" name="manejo" id="supraglotica" required="required">
                </td>
                <td class="">
                    TRAQUEÓTOMO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="traqueotomo" name="manejo" id="traqueotomo" required="required">
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-center bg-light-green" style="width: 60%;"><b>INTUBACIÓN</b></td>
            </tr>
            <tr>
                <td>
                    NASAL &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="nasal" name="intubacion" id="nasal" required="required">
                </td>
                <td>
                    ORAL &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="oral" name="intubacion" id="oral" required="required">
                </td>
                <td>
                    SUBMENTONEANA &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="submentoneana" name="intubacion" id="submentoneana" required="required">
                </td>
                <td>
                    VISIÓN DIRECTA &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="vision" name="intubacion" id="vision" required="required">
                </td>
                <td>
                    A CIEGAS &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="a_ciegas" name="intubacion" id="a_ciegas" required="required">
                </td>
            </tr>
        </tbody>
    </table>
    <table border="1" style="width: 100%">
        <tbody>
            <tr>
                <td colspan="5" class="text-center bg-light-green" style="width: 60%;"><b>TIPO DE TUBO</b></td>
            </tr>
            <tr>
                <td>
                    CONVENCIONAL &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="convencional" name="tipo_tubo" id="convencional" required="required">
                </td>
                <td>
                    PREFORMADO ORAL &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="p_oral" name="tipo_tubo" id="p_oral" required="required">
                </td>
                <td>
                    PREFORMADO NASAL &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="p_nasal" name="tipo_tubo" id="p_nasal" required="required">
                </td>
                <td>
                    REFORZADO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="reforsado" name="tipo_tubo" id="reforsado" required="required">
                </td>
                <td>
                    DOBLE LUMEN &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="doble" name="tipo_tubo" id="doble" required="required">
                </td>
            </tr>
            <tr>
                <td>
                    DÍAMETRO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="diametro" name="tubo" id="diametro" required="required">
                </td>
                <td colspan="2">
                    BALON &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; SI &nbsp;&nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="SI" name="balon" id="diametro" required="required">
                    &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; NO &nbsp;&nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="NO" name="balon" id="diametro" required="required">
                </td>
                <td colspan="2">
                    TAPONAMIENTO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; SI &nbsp;&nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="SI" name="taponamiento" id="diametro" required="required">
                    &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; NO &nbsp;&nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="NO" name="taponamiento" id="diametro" required="required">
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
                    &nbsp; &nbsp;&nbsp; I &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="1" name="cormack" id="diametro" required="required">
                    &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; II &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="2" name="cormack" id="diametro" required="required">
                    &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; III &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="3" name="cormack" id="diametro" required="required">
                    &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; IV &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="4" name="cormack" id="diametro" required="required">
                </td>
                <td>
                    NÚMERO DE INTENTOS
                </td>
                <td colspan="2"><input onchange="handlePublica(this)" style="width: 100%;" class="flat-orange" type="text" name="intentos" id="doble" required="required"></td>
            </tr>
            <tr>
                <td colspan="3" class="text-center" style="width: 60%;"><b>INDUCCION</b></td>
                <td colspan="2" class="text-center"><b>MANTENIMIENTO</b></td>
            </tr>
            <tr>
                <td>
                    INHALATORIA &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" name="induccion" value="inhalatoria" id="inhalatoria" required="required">
                </td>
                <td class="text-center">
                    INTRAVENOSA &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" name="induccion" value="intravenosa" id="intravenosa" required="required">
                </td>
                <td>
                    INHALATORIA &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" name="induccion" value="inhalatoria" id="inhalatoria" required="required">
                </td>
                <td>
                    INTRAVENOSA &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="intravenosa" name="mantenimiento" id="intravenosa" required="required">
                </td>
                <td>
                    BALANCEADA &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="radio" value="balanceada" name="mantenimiento" id="balanceada" required="required">
                </td>
            </tr>
        </tbody>
    </table>


    <table border="1" style="width: 100%">
        <tbody>
            <tr>
                <td colspan="2" class="text-center bg-light-green" style="width: 35%;"><b>REGIONAL</b></td>
                <td colspan="4" class="text-center bg-light-green" style="width: 65%;">TRONCULAR</td>
            </tr>
            <tr>
                <td class="bg-light-green" style="width: 17%;">ASEPSIA CON</td>
                <td style="width: 18%;"><input type="text" onchange="handlePublica(this)" style="width: 100%;" name="asepsia" id="asepsia"></td>
                <td class="bg-light-green">BLOQUEO DE NERVIO</td>
                <td style="width: 15%;"><input type="text" onchange="handlePublica(this)" style="width: 100%;" name="bloqueo_de_nervio" id="bloqueo_de_nervio"></td>
                <td class="bg-light-green">NÚMERO DE INTENTOS</td>
                <td style="width: 15%;"><input type="text" onchange="handlePublica(this)" style="width: 100%;" name="numero_intentos1" id="numero_intentos1"></td>
            </tr>
            <tr>
                <td class="bg-light-green">HABÓN CON</td>
                <td><input type="text" onchange="handlePublica(this)" style="width: 100%;" name="habon" id="habon"></td>
                <td class="bg-light-green">BLOQUEO DE PLEXO</td>
                <td><input type="text" onchange="handlePublica(this)" style="width: 100%;" name="bloqueo_plexo" id="bloqueo_plexo"></td>
                <td class="bg-light-green">NÚMERO DE INTENTOS</td>
                <td><input type="text" onchange="handlePublica(this)" style="width: 100%;" name="numero_intentos2" id="numero_intentos2"></td>
            </tr>
            <tr>
                <td class="bg-light-green">LOCAL ASISTIDA</td>
                <td><input type="text" style="width: 100%;" onchange="handlePublica(this)" style="width: 100%;" name="local" id="local"></td>
                <td class="bg-light-green">ANESTÉSICO LOCAL</td>
                <td><input type="text" style="width: 100%;" onchange="handlePublica(this)" style="width: 100%;" name="anestesico" id="anestesico"></td>
                <td class="bg-light-green">COADYUVANTE</td>
                <td><input type="text" style="width: 100%;" onchange="handlePublica(this)" style="width: 100%;" name="coadyuvante" id="coadyuvante"></td>
            </tr>
            <tr>
                <td class="bg-light-green">INTRAVENOSA</td>
                <td><input type="text" style="width: 100%;" onchange="handlePublica(this)" style="width: 100%;" name="intravenosa" id="intravenosa"></td>
                <td class="bg-light-green">TIPO DE AGUJA</td>
                <td><input type="text" style="width: 100%;" onchange="handlePublica(this)" style="width: 100%;" name="tipo_aguja" id="tipo_aguja"></td>
                <td class="bg-light-green">EQUIPO</td>
                <td><input type="text" style="width: 100%;" onchange="handlePublica(this)" style="width: 100%;" name="equipo" id="equipo"></td>
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
                <td><input onchange="handlePublica(this)" type="text" style="width: 100%;" id="raquidea" name="raquidea"></td>
                <td colspan="3" class="text-center">EPIDURAL</td>
                <td><input onchange="handlePublica(this)" type="text" style="width: 100%;" id="epidural" name="epidural"></td>
                <td>CAUDAL</td>
                <td><input onchange="handlePublica(this)" type="text" style="width: 100%;" id="caudal" name="caudal"></td>
                <td>CATETER</td>
                <td><input onchange="handlePublica(this)" type="text" style="width: 100%;" id="cateter" name="cateter"></td>
            </tr>
            <tr>
                <td colspan="2">TIPO DE AGUA</td>
                <td><input onchange="handlePublica(this)" type="text" style="width: 100%;" id="aguja_neu" name="aguja_neu"></td>
                <td colspan="2">NUMERO DE AGUJA</td>
                <td><input onchange="handlePublica(this)" type="text" style="width: 100%;" id="num_aguja" name="num_aguja"></td>
                <td colspan="3">N° DE INTENTOS</td>
                <td><input onchange="handlePublica(this)" type="text" style="width: 100%;" id="numero_intentos3" name="numero_intentos3"></td>
            </tr>
            <tr>
                <td rowspan="2">BORBOTAJE</td>
                <td rowspan="2">SI</td>
                <td rowspan="2" style="width: 5%;" class="text-center"><input type="radio" onchange="handlePublica(this)" style="width: 100%; height: 20px;" value="SI" name="borbotaje"></td>
                <td rowspan="2">NO</td>
                <td rowspan="2" style="width: 5%;"><input type="radio" onchange="handlePublica(this)" style="width: 100%; height: 20px;" value="NO" name="borbotaje"></td>
                <td rowspan="2" class="text-center">ACCESO</td>
                <td>MEDIAL</td>
                <td>LATERAL</td>
                <td rowspan="2">SITIO DE PUNCIÓN</td>
                <td rowspan="2"><input onchange="handlePublica(this)" type="text" style="width: 100%; height: 60px;" id="puncion" name="puncion"></td>
            </tr>
            <tr>
                <td style="height: 25px;"><input type="radio" onchange="handlePublica(this)" style="width: 100%; height: 20px;" value="medial" name="acceso"></td>
                <td><input type="radio" onchange="handlePublica(this)" style="width: 100%; height: 20px;" value="lateral" name="acceso"></td>
            </tr>
            <tr>
                <td class="bg-light-green" colspan="2">DERMATOMA</td>
                <td colspan="2"><input onchange="handlePublica(this)" type="text" style="width: 100%;" name="dermatoma" id="dermatoma"></td>
                <td class="bg-light-green" colspan="2">POSICIÓN</td>
                <td colspan="4"><input onchange="handlePublica(this)" type="text" style="width: 100%;" name="posicion" id="posicion"></td>
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
                    <input onchange="handlePublica(this)" style="width: 100%; height: 45px;" type="text" name="analgesia" id="analgesia">
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
                <td> &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="radio" value="1" name="escala" id="1" required="required"></td>
                <td class="text-center"><b>2</b></td>
                <td> &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="radio" value="2" name="escala" id="1" required="required"></td>
                <td class="text-center"><b>3</b></td>
                <td> &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="radio" value="3" name="escala" id="1" required="required"></td>
                <td class="text-center"><b>4</b></td>
                <td> &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="radio" value="4" name="escala" id="1" required="required"></td>
                <td class="text-center"><b>5</b></td>
                <td> &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="radio" value="5" name="escala" id="1" required="required"></td>
                <td class="text-center"><b>6</b></td>
                <td> &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="radio" value="6" name="escala" id="1" required="required"></td>
            </tr>
        </tbody>
    </table>
    <table border="1" style="width: 100%">
        <tbody>
            <tr>
                <td colspan="3" class="bg-purple" style="font-size: 8;"><b>G. ACCESOS VASCULARES</b></td>
                <td colspan="4" class="bg-purple" style="font-size: 8;"><b>H. REPOSICÍON VOLÉMICA</b></td>
                <td colspan="2" class="bg-purple" style="font-size: 8;"><b>I. PÉRDIDAS</b></td>
            </tr>
            <tr>
                <td class="text-center bg-light-green" style="width: 15%;">TIPO</td>
                <td class="text-center bg-light-green" style="width: 7%;">CALIBRE</td>
                <td class="text-center bg-light-green" style="width: 7%;">SITIO</td>
                <td class="text-center bg-light-green" style="width: 15%;">DESTROSA 5%</td>
                <td style="width: 5%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="text" id="dextrosa" name="dextrosa"></td>
                <td class="text-center bg-light-green" style="width: 15%;">SANGRE</td>
                <td style="width: 5%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="text" id="sangre_derivados" name="sangre_derivados"></td>
                <td class="text-center bg-light-green" style="width: 15%;">SANGRADO</td>
                <td style="width: 5%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="text" id="sangrado" name="sangrado"></td>
            </tr>
            <tr>
                <td class="text-center">IV PERIFÉRICO 1</td>
                <td style="text-align: center; vertical-align: middle;">
                    <input type="text" id="calibre" onchange="handlePublica(this)" name="c1" required="required">
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <input type="text" id="sitio" onchange="handlePublica(this)" name="s1" required="required">
                </td>
                <td class="text-center bg-light-green">DESTROSA 10%</td>
                <td style="text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="text" id="destrosa10" name="destrosa10"></td>
                <td class="text-center bg-light-green">PLASMA</td>
                <td style="text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="text" id="plasma" name="plasma"></td>
                <td class="text-center bg-light-green">DIURESIS</td>
                <td style="text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="text" id="diuresis" name="diuresis"></td>
            </tr>
            <tr>
                <td class="text-center">IV PERIFÉRICO 2</td>
                <td style="text-align: center; vertical-align: middle;">
                    <input type="text" id="calibre" onchange="handlePublica(this)" name="c2">
                </td>

                <td style="text-align: center; vertical-align: middle;">
                    <input type="text" id="sitio" onchange="handlePublica(this)" name="s2">
                </td>
                <td class="text-center bg-light-green">DESTROSA 50%</td>
                <td style="text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="text" id="destrosa5" name="destrosa5"></td>
                <td class="text-center bg-light-green">PLAQUETAS</td>
                <td style="text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="text" id="plaquetas" name="plaquetas"></td>
                <td class="text-center bg-light-green">OTROS</td>
                <td><input onchange="handlePublica(this)" type="text" id="otros_perdidas" name="otros_perdidas"></td>
            </tr>
            <tr>
                <td class="text-center">IV PERIFÉRICO 3</td>
                <td style="text-align: center; vertical-align: middle;">
                    <input type="text" id="calibre" onchange="handlePublica(this)" name="c3">
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <input type="text" id="sitio" onchange="handlePublica(this)" name="s3">
                </td>
                <td class="text-center bg-light-green">DESTROSA EN SS</td>
                <td style="text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="text" id="destrosass" name="destrosass"></td>
                <td class="text-center bg-light-green">CRIOPRECIPITADOS</td>
                <td style="text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="text" id="crioprecipitados" name="crioprecipitados"></td>
                <td rowspan="3" class="text-center bg-light-green">TOTAL</td>
                <td rowspan="3"><input onchange="handlePublica(this)" style="height: 130px;" type="text" id="total_perdidas" name="total_perdidas"></td>
            </tr>
            <tr>
                <td class="text-center">IV CENTRAL</td>
                <td style="text-align: center; vertical-align: middle;">
                    <input type="text" id="calibre" onchange="handlePublica(this)" name="c4">
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <input type="text" id="sitio" onchange="handlePublica(this)" name="s4">
                </td>
                <td class="text-center bg-light-green">SS 0.9%</td>
                <td style="text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="text" id="ss_9" name="ss_9"></td>
                <td rowspan="2" class="text-center bg-light-green">OTROS</td>
                <td rowspan="2"><input onchange="handlePublica(this)" style="height: 80px;" type="text" id="otros_reposicion" name="otros_reposicion"></td>

            </tr>
            <tr>
                <td class="text-center">INTRA ALTERIAL</td>
                <td style="text-align: center; vertical-align: middle;">
                    <input type="text" id="calibre" onchange="handlePublica(this)" name="c5">
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <input type="text" id="sitio" onchange="handlePublica(this)" name="s5">
                </td>
                <td class="text-center bg-light-green">LACTO RINGER</td>
                <td style="text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="text" id="lactato_ringer" name="lactato_ringer"></td>
            </tr>
            <tr>
                <td class="text-center">OTRO</td>
                <td style="text-align: center; vertical-align: middle;">
                    <input type="text" id="calibre" onchange="handlePublica(this)" name="s6">
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <input type="text" id="sitio" onchange="handlePublica(this)" name="c6">
                </td>
                <td class="text-center bg-light-green">EXPANSORES</td>
                <td style="text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="text" id="expansores" name="expansores"></td>
                <td class="text-center bg-light-green">TOTAL</td>
                <td><input onchange="handlePublica(this)" type="text" id="total_reposicion" name="total_reposicion"></td>
                <td class="text-center bg-light-green">BALANCE</td>
                <td><input onchange="handlePublica(this)" type="text" id="balance" name="balance"></td>
            </tr>
        </tbody>
    </table>
    <table border="1" style="width: 100%">
        <tbody>
            <tr>
                <td colspan="9" class="bg-purple" style="font-size: 8;"><b>J. DATOS DEL RECIENTE NACIDO</b></td>
            </tr>
            <tr>
                <td class="text-center bg-light-green"><b>APGAR</b></td>
                <td class="text-center">FETO MUERTO</td>
                <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" name="apgar" id="feto" value="feto"></td>
                <td class="text-center">1 MINUTO</td>
                <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" name="apgar" id="minuto1" value="1minuto"></td>
                <td class="text-center">5 MINUTOS</td>
                <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" name="apgar" id="minuto5" value="5minuto"></td>
                <td class="text-center">10 MINUTOS</td>
                <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" name="apgar" id="minuto10" value="10minuto"></td>
            </tr>
            <tr>
                <td class="text-center bg-light-green">DURACIÓN ANESTESIA</td>
                <td colspan="8"><input onchange="handlePublica(this)" style="width: 100%;" type="text" id="duracion_anestesia" name="duracion_anestesia"></td>
            </tr>
            <tr>
                <td class="text-center bg-light-green">DRACIÓN DE CIRUGIA</td>
                <td colspan="8"><input onchange="handlePublica(this)" style="width: 100%;" type="text" name="duracion_operacion" id="duracion_cirugia"></td>
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
                <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" name="tecnicas_especiales" id="hemodilucion" value="hemodilucion"></td>
                <td class="text-center">AUTOTRANSFUSIÓN</td>
                <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" name="tecnicas_especiales" id="autotransfusion" value="autotransfusion"></td>
                <td class="text-center">HIPOTENSÍON</td>
                <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" name="tecnicas_especiales" id="hipotension" value="hipotension"></td>
                <td class="text-center">HIPOTERMIA</td>
                <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" name="tecnicas_especiales" id="hipotermia" value="hipotermia"></td>
                <td class="text-center">CIRCULACIÓN EXTRACOPÓREA</td>
                <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" name="tecnicas_especiales" id="circulacion_extracoperea" value="circulacion_extracoperea"></td>
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
                <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" name="mantenimiento_temperatura" id="hemodilucion" value="1"></td>
                <td style="width: 15%;">CALENTAMIENTO DE FLUIDOS</td>
                <td class="text-center" style="width: 3%;"><input onchange="handlePublica(this)" type="radio" name="mantenimiento_temperatura" id="hemodilucion" value="2"></td>
                <td class="text-center" style="width: 3%;">OTROS</td>
                <td style="width: 30%;"><input onchange="handlePublica(this)" style="width: 100%;" type="text" name="otros_temperatura" id="temperatura"></td>
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
                <td style="text-align: center; vertical-align: middle; width: 3%;"><input onchange="handlePublica(this)" type="radio" name="indice" id="actividad_electrica" value="1"></td>
                <td style="width: 15%;">ARRITMIA</td>
                <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="arritmia" value="2"></td>
                <td>ASISTOLIA</td>
                <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="asistolia" value="3"></td>
                <td>TROMBOEMBOLIA</td>
                <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="tromboembolia" value="4"></td>
            </tr>
            <tr>
                <td>HIPERTERMIA MALIGNA</td>
                <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="hipertermia" value="5"></td>
                <td style="width: 15%;">ANAFILAXIA</td>
                <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="anafilaxia" value="6"></td>
                <td>ISQUEMIA MIOCÁRDICA</td>
                <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="isquemia" value="7"></td>
                <td>NEUMOTÓRAX</td>
                <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="neumotorax" value="8"></td>
            </tr>
            <tr>
                <td>BRONCOESPASMO</td>
                <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="broncoespasmo" value="9"></td>
                <td style="width: 15%;">DESPERTAR PROLONGADO</td>
                <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="despertar_prolongado" value="10"></td>
                <td>EMBOLIA AÉREA VENOSA</td>
                <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="embolea" value="11"></td>
                <td>LARINGOESPASMO</td>
                <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="laringoespasmo" value="12"></td>
            </tr>
            <tr>
                <td>DIFICULTAD DE LA TÉCNICA</td>
                <td style="width: 3%; text-align: center; vertical-align: middle;"><input onchange="handlePublica(this)" type="radio" name="indice" id="dificultad" value="13"></td>
                <td style="width: 15%;">OTROS</td>
                <td colspan="5"><input style="width: 100%;" onchange="handlePublica(this)" type="text" name="otros_indice" id="otros_indice"></td>
            </tr>
        </tbody>
    </table>
    <br>
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="font-size: 14px;" class="bg-purple"><b>OBSERVACIONES</b></td>
            </tr>
            <tr>
                <td>
                    <input onchange="handlePublica(this)" style="width: 100%; height: 45px;" type="text" name="observaciones" id="observaciones">
                </td>
            </tr>
        </tbody>
    </table>
    <br>
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

            <div class="form-group col-md-2">
                <button type="button" class="btn btn-primary" onclick="agregarExamen()">Agregar</button>
            </div>

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
                    <input style="width: 98%;" type="text" value="{{ $ex->nombre }}" disabled>
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


    <table border="1" style="width: 100%">
        <tbody>
            <tr>
                <td colspan="12" class="bg-purple" style="font-size: 8;"><b>R. CONDICIONES DE EGRESO</b></td>
            </tr>
            <tr>
                <td class="text-center" style="font-size: 5; width: 12%;" rowspan="2">CONDICIONES AL SALIR</td>
                <td class="text-center" style="font-size: 5;">EXTUBADO</td>
                <td class="text-center" style="font-size: 5; width: 5%;"><input onchange="handlePublica(this)" type="radio" name="condiciones_al_salir" id="exturbado" value="1"></td>
                <td class="text-center" style="font-size: 5;" rowspan="2">CONDUCIDO A:</td>
                <td class="text-center" style="font-size: 5;" rowspan="2">UNIDAD DE CUIDADOS POST ANESTÉSICO</td>
                <td class="text-center" style="font-size: 5; width: 5%;" rowspan="2"><input onchange="handlePublica(this)" type="radio" name="conducido" id="post_anestesico" value="1"></td>
                <td class="text-center" style="font-size: 5;" rowspan="2">UNIDAD DE CUIDADOS INTENSIVOS</td>
                <td class="text-center" style="font-size: 5; width: 5%;" rowspan="2"><input onchange="handlePublica(this)" type="radio" name="conducido" id="c_intensivos" value="2"></td>
                <td class="text-center" style="font-size: 5;" rowspan="2">CRÍTICOS DE EMERGENCIA</td>
                <td class="text-center" style="font-size: 5; width: 5%;" rowspan="2"><input onchange="handlePublica(this)" type="radio" name="conducido" id="critico" value="3"></td>
                <td class="text-center" style="font-size: 5; "  rowspan="2">MORGUE</td>
                <td class="text-center" style="font-size: 5; width: 5%" rowspan="2"><input onchange="handlePublica(this)" type="radio" name="conducido" id="morgue" value="4"></td>
            </tr>
            <tr>
                <td style="font-size: 5;" class="text-center">INTUBADO</td>
                <td class="text-center"><input onchange="handlePublica(this)" type="radio" name="condiciones_al_salir" id="intubado" value="2"></td>
            </tr>
            <tr>
                <td style="font-size: 5;">CONSTANTES VITALES DE ENTREGA</td>
                <td colspan="2"> <b>TA</b>&nbsp;<input style="width: 200px;" onchange="handlePublica(this)" type="text" name="ta" id="ta" value=""></td>
                <td colspan="2"> <b>FC</b>&nbsp;<input style="width: 200px;" onchange="handlePublica(this)" type="text" name="fc" id="fc" value=""></td>
                <td colspan="2"> <b>FR</b>&nbsp;<input style="width: 200px;" onchange="handlePublica(this)" type="text" name="fr" id="fr" value=""></td>
                <td colspan="3"> <b>SAT.O2</b>&nbsp;<input style="width: 200px;" onchange="handlePublica(this)" type="text" name="sat" id="sat" value=""></td>
                <td colspan="2"> <b>T°</b>&nbsp;<input style="width: 200px;" onchange="handlePublica(this)" type="text" name="t" id="t" value=""></td>
            </tr>
        </tbody>
    </table>

 


    <div class="anesthesia-layout-end" hidden></div>
    <script type="application/json" class="anesthesia-layout-config">
    {"groups":[
        {"match":"REGIÓN OPERATORIA","id":"trans-region","title":"Región operatoria","description":"Área anatómica relacionada con el procedimiento.","icon":"fa-map-marker","open":true},
        {"match":"REGISTRO TRANSANESTÉSICO","id":"trans-registro","title":"Registro transanestésico","description":"Gráfico y seguimiento durante el procedimiento.","icon":"fa-area-chart","open":true},
        {"match":"DROGAS ADMINISTRADAS","id":"trans-medicacion","title":"Medicamentos administrados","description":"Registro eMAR y administraciones realizadas.","icon":"fa-medkit","open":true},
        {"match":"TÉCNICA ANESTÉSICA","id":"trans-tecnica","title":"Técnica anestésica","description":"Vía aérea, intubación, anestesia general, regional y sedoanalgesia.","icon":"fa-stethoscope","open":true},
        {"match":"G. ACCESOS VASCULARES","id":"trans-balance","title":"Balance y controles intraoperatorios","description":"Accesos, reposición, pérdidas, recién nacido y técnicas especiales.","icon":"fa-balance-scale","open":true},
        {"match":"N. INCIDENTES","id":"trans-incidentes","title":"Incidentes y observaciones","description":"Eventos, observaciones y datos complementarios.","icon":"fa-exclamation-triangle","open":true},
        {"match":"R. CONDICIONES DE EGRESO","id":"trans-egreso","title":"Condiciones de egreso","description":"Estado final, destino y constantes vitales.","icon":"fa-sign-out","open":true}
    ]}
    </script>
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
        var detailId = $("#drug_recipe_detail").val();
        if (!detailId) {
            alert("Seleccione una orden médica activa.");
            return;
        }
        window.setAnesthesiaRecordStatus('saving');
        $.ajax({
            url: "{{route('formato.transanestesico_store')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            data: {
                recipe_detail_id: detailId,
                id_anestesiologia: $("#id").val(),
                dose: $("#drug_dose").val(),
                route: $("#drug_route").val(),
                scheduled_at: $("#drug_scheduled").val(),
                administered_at: $("#drug_administered").val(),
                status: 'ADMINISTERED',
                series: $("#drug_series").val(),
                clinical_result: $("#drug_result").val(),
                patient_verified: $("#drug_patient_verified").is(':checked') ? 1 : 0,
                medication_verified: $("#drug_medication_verified").is(':checked') ? 1 : 0
            },
            success: function(response) {
                console.log(response);

                if (response.success) {
                    window.setAnesthesiaRecordStatus('saved');
                    window.location.reload();
                }
            },
            error: function(error) {
                window.setAnesthesiaRecordStatus('error');
                var response = error.responseJSON || {};
                alert(response.msj || 'No fue posible registrar la administración anestésica.');
            }
        });
    }

    $('#drug_recipe_detail').on('change', function() {
        $('#drug_dose').val($(this).find(':selected').data('dose') || '');
    });

    function handlePublica(input) {
        let nombre, valor;

        if (input.name === 'sistema' || input.name === 'aparato' || input.name === 'manejo' || input.name === 'intubacion' || input.name === 'tipo_tubo' ||
            input.name === 'balon' || input.name === 'taponamiento' || input.name === 'cormack' || input.name === 'induccion' || input.name === 'mantenimiento' || input.name === 'intentos' ||
            input.name === 'asepsia' || input.name === 'bloqueo_de_nervio' || input.name === 'numero_intentos1' || input.name === 'habon' || input.name === 'bloqueo_plexo' ||
            input.name === 'numero_intentos2' || input.name === 'local' || input.name === 'anestesico' || input.name === 'coadyuvante' || input.name === 'intravenosa' ||
            input.name === 'tipo_aguja' || input.name === 'equipo' || input.name === 'borbotaje' || input.name === 'acceso' || input.name === 'raquidea' ||
            input.name === 'epidural' || input.name === 'caudal' || input.name === 'cateter' || input.name === 'aguja_neu' || input.name === 'num_aguja' || input.name === 'numero_intentos3' ||
            input.name === 'dermatoma' || input.name === 'posicion' || input.name === 'escala' || input.name === 'tecnicas_especiales' || input.name === 'duracion_anestesia' ||
            input.name === 'duracion_operacion' || input.name === 'sangre_derivados' || input.name === 'dextrosa' || input.name === 'lactato_ringer' || input.name === 'expansores' ||
            input.name === 'mantenimiento_temperatura' || input.name === 'apgar' || input.name === 'otros_temperatura' || input.name === 's1' || input.name === 's2' || input.name === 's3' ||
            input.name === 's4' || input.name === 's5' || input.name === 's6' || input.name === 'c1' || input.name === 'c2' || input.name === 'c3' || input.name === 'c4' || input.name === 'c5' ||
            input.name === 'c6' || input.name === 'destrosa10' || input.name === 'destrosa5' || input.name === 'destrosass' || input.name === 'ss_9' || input.name === 'plasma' ||
            input.name === 'crioprecipitados' || input.name === 'otros_reposicion' || input.name === 'otros_perdidas' || input.name === 'total_perdidas' || input.name === 'total_reposicion' ||
            input.name === 'balance' || input.name === 'otros_indice' || input.name === 'indice' || input.name === 'observaciones' || input.name === 'analgesia'  || input.name === 'conducido'||
	     input.name === 'condiciones_al_salir'  || input.name === 'puncion' || input.name === 'diuresis'  || input.name === 'sangrado' || input.name === 'plaquetas' || input.name === 'ta'
         || input.name === 'fc' || input.name === 'fr' || input.name === 'sat' || input.name === 't'
        ) {

            nombre = input.name;
            valor = input.value;
        } else {
            nombre = input.name;
            valor = input.checked ? 1 : 0;
        }
        window.setAnesthesiaRecordStatus('saving');
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
                    window.setAnesthesiaRecordStatus('saved');
                }
            },
            error: function(error) {
                window.setAnesthesiaRecordStatus('error');
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
</script>
@endsection
