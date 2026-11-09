@extends('layouts.app-template-h')
@section('content')

<?php
date_default_timezone_set('America/Guayaquil');
$fecha_actual = date("Y-m-d H:i:s");
?>

<style>
    .emergency-triage-page {
        --triage-navy: #07527c;
        --triage-blue: #087fba;
        --triage-teal: #12a5a0;
        --triage-ink: #073b5c;
        --triage-muted: #607d92;
        --triage-line: #cfe2ed;
        --triage-soft: #eef8fb;
        padding: 24px 28px 42px;
    }

    .emergency-triage-page .triage-hero {
        align-items: center;
        background: linear-gradient(118deg, var(--triage-navy) 0%, var(--triage-blue) 52%, var(--triage-teal) 100%);
        border-radius: 18px;
        box-shadow: 0 16px 34px rgba(7, 82, 124, .16);
        color: #fff;
        display: flex;
        justify-content: space-between;
        margin-bottom: 18px;
        overflow: hidden;
        padding: 24px 28px;
        position: relative;
    }

    .emergency-triage-page .triage-hero:after {
        border: 1px solid rgba(255, 255, 255, .16);
        border-radius: 50%;
        content: '';
        height: 190px;
        position: absolute;
        right: -42px;
        top: -86px;
        width: 190px;
    }

    .emergency-triage-page .triage-hero-copy {
        align-items: center;
        display: flex;
        gap: 18px;
        min-width: 0;
        position: relative;
        z-index: 1;
    }

    .emergency-triage-page .triage-hero-icon {
        align-items: center;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .28);
        border-radius: 14px;
        display: flex;
        flex: 0 0 58px;
        font-size: 25px;
        height: 58px;
        justify-content: center;
    }

    .emergency-triage-page .triage-eyebrow {
        color: #aef2ef;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .09em;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .emergency-triage-page .triage-hero h1 {
        color: #fff;
        font-size: 25px;
        font-weight: 800;
        line-height: 1.2;
        margin: 0 0 4px;
    }

    .emergency-triage-page .triage-hero p {
        color: rgba(255, 255, 255, .88);
        font-size: 13px;
        margin: 0;
    }

    .emergency-triage-page .triage-back {
        align-items: center;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .42);
        border-radius: 10px;
        color: #fff;
        display: inline-flex;
        font-size: 13px;
        font-weight: 700;
        gap: 8px;
        padding: 10px 16px;
        position: relative;
        z-index: 1;
    }

    .emergency-triage-page .triage-back:hover,
    .emergency-triage-page .triage-back:focus {
        background: #fff;
        color: var(--triage-navy);
        text-decoration: none;
    }

    .emergency-triage-page .triage-panel {
        background: #fff;
        border: 1px solid var(--triage-line);
        border-radius: 16px;
        box-shadow: 0 12px 28px rgba(28, 84, 113, .08);
        overflow: hidden;
    }

    .emergency-triage-page .patient-summary {
        align-items: center;
        background: linear-gradient(90deg, #f4fbfd, #ecf8fb);
        border-bottom: 1px solid var(--triage-line);
        display: flex;
        gap: 14px;
        padding: 18px 22px;
    }

    .emergency-triage-page .patient-avatar {
        align-items: center;
        background: #dff2f8;
        border-radius: 12px;
        color: var(--triage-blue);
        display: flex;
        flex: 0 0 46px;
        font-size: 20px;
        height: 46px;
        justify-content: center;
    }

    .emergency-triage-page .patient-summary-main {
        min-width: 0;
    }

    .emergency-triage-page .patient-kicker {
        color: var(--triage-muted);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .emergency-triage-page .patient-name {
        color: var(--triage-ink);
        font-size: 17px;
        font-weight: 800;
        margin-top: 2px;
    }

    .emergency-triage-page .patient-id {
        background: #fff;
        border: 1px solid var(--triage-line);
        border-radius: 999px;
        color: var(--triage-ink);
        font-size: 12px;
        font-weight: 700;
        margin-left: auto;
        padding: 8px 13px;
        white-space: nowrap;
    }

    .emergency-triage-page .patient-data-inputs {
        height: 0;
        overflow: hidden;
        position: absolute;
        width: 0;
    }

    .emergency-triage-page .triage-section {
        border-bottom: 1px solid #e4eef4;
        padding: 22px;
    }

    .emergency-triage-page .triage-section:last-of-type {
        border-bottom: 0;
    }

    .emergency-triage-page .section-heading {
        align-items: center;
        display: flex;
        gap: 11px;
        margin-bottom: 17px;
    }

    .emergency-triage-page .section-icon {
        align-items: center;
        background: #e4f5f9;
        border-radius: 10px;
        color: var(--triage-blue);
        display: flex;
        flex: 0 0 38px;
        font-size: 15px;
        height: 38px;
        justify-content: center;
    }

    .emergency-triage-page .section-heading h2 {
        color: var(--triage-ink);
        font-size: 16px;
        font-weight: 800;
        line-height: 1.2;
        margin: 0 0 3px;
    }

    .emergency-triage-page .section-heading p {
        color: var(--triage-muted);
        font-size: 11px;
        margin: 0;
    }

    .emergency-triage-page .triage-grid {
        display: grid;
        gap: 15px;
        grid-template-columns: repeat(12, minmax(0, 1fr));
    }

    .emergency-triage-page .field-span-2 { grid-column: span 2; }
    .emergency-triage-page .field-span-3 { grid-column: span 3; }
    .emergency-triage-page .field-span-4 { grid-column: span 4; }
    .emergency-triage-page .field-span-6 { grid-column: span 6; }
    .emergency-triage-page .field-span-8 { grid-column: span 8; }
    .emergency-triage-page .field-span-9 { grid-column: span 9; }
    .emergency-triage-page .field-span-12 { grid-column: span 12; }

    .emergency-triage-page .triage-field {
        margin: 0;
        min-width: 0;
    }

    .emergency-triage-page .triage-field label {
        color: #244a62;
        display: block;
        font-size: 11px;
        font-weight: 800;
        margin: 0 0 7px;
    }

    .emergency-triage-page .triage-field .form-control {
        background-color: #fff;
        border: 1px solid #c9dce7;
        border-radius: 9px;
        box-shadow: none;
        color: #173f59;
        font-size: 13px;
        height: 42px;
        padding: 8px 12px;
        width: 100%;
    }

    .emergency-triage-page .triage-field .form-control:focus {
        border-color: #18abc0;
        box-shadow: 0 0 0 3px rgba(24, 171, 192, .12);
    }

    .emergency-triage-page .field-with-unit {
        position: relative;
    }

    .emergency-triage-page .field-with-unit .form-control {
        padding-right: 46px;
    }

    .emergency-triage-page .field-unit {
        color: #6f8899;
        font-size: 11px;
        font-weight: 800;
        pointer-events: none;
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
    }

    .emergency-triage-page .priority-box {
        background: #f5fbfd;
        border: 1px solid #cfe7ef;
        border-radius: 12px;
        padding: 14px;
    }

    .emergency-triage-page .priority-select {
        border-left: 5px solid #8aa5b5;
        font-weight: 700;
        transition: background-color .18s ease, border-color .18s ease, color .18s ease;
    }

    .emergency-triage-page .priority-select.priority-level-1 {
        background-color: #fff1f0;
        border-color: #c62828;
        color: #8e1717;
    }

    .emergency-triage-page .priority-select.priority-level-2 {
        background-color: #fff4e8;
        border-color: #dc6814;
        color: #8a3d08;
    }

    .emergency-triage-page .priority-select.priority-level-3 {
        background-color: #fff9db;
        border-color: #d1a400;
        color: #674f00;
    }

    .emergency-triage-page .priority-select.priority-level-4 {
        background-color: #edf9f0;
        border-color: #2e8b57;
        color: #17643a;
    }

    .emergency-triage-page .priority-select.priority-level-5 {
        background-color: #eef4ff;
        border-color: #3569b8;
        color: #234d8c;
    }

    .emergency-triage-page .priority-select option {
        font-weight: 700;
        padding: 8px 12px;
    }

    .emergency-triage-page .priority-select option[value="0"] {
        background: #fff;
        color: #526d7d;
    }

    .emergency-triage-page .priority-select option.priority-level-1 { background: #c62828; color: #fff; }
    .emergency-triage-page .priority-select option.priority-level-2 { background: #dc6814; color: #fff; }
    .emergency-triage-page .priority-select option.priority-level-3 { background: #f4cf3a; color: #3f3200; }
    .emergency-triage-page .priority-select option.priority-level-4 { background: #2e8b57; color: #fff; }
    .emergency-triage-page .priority-select option.priority-level-5 { background: #3569b8; color: #fff; }

    .emergency-triage-page .priority-help {
        color: #607d92;
        display: block;
        font-size: 10px;
        line-height: 1.4;
        margin-top: 8px;
    }

    .emergency-triage-page .triage-actions {
        align-items: center;
        background: #f8fbfd;
        border-top: 1px solid var(--triage-line);
        display: flex;
        justify-content: flex-end;
        padding: 16px 22px;
    }

    .emergency-triage-page .btn-update-triage {
        align-items: center;
        background: #087db9;
        border: 0;
        border-radius: 10px;
        box-shadow: 0 8px 18px rgba(8, 125, 185, .2);
        color: #fff;
        display: inline-flex;
        font-size: 13px;
        font-weight: 800;
        gap: 8px;
        justify-content: center;
        min-width: 160px;
        padding: 11px 18px;
    }

    .emergency-triage-page .btn-update-triage:hover,
    .emergency-triage-page .btn-update-triage:focus {
        background: #066b9f;
        color: #fff;
    }

    @media (max-width: 1199px) {
        .emergency-triage-page .field-span-2,
        .emergency-triage-page .field-span-3 { grid-column: span 4; }
        .emergency-triage-page .field-span-4 { grid-column: span 6; }
    }

    @media (max-width: 767px) {
        .emergency-triage-page { padding: 16px 12px 30px; }
        .emergency-triage-page .triage-hero { align-items: flex-start; flex-direction: column; gap: 18px; padding: 20px; }
        .emergency-triage-page .triage-hero h1 { font-size: 21px; }
        .emergency-triage-page .triage-back { width: 100%; justify-content: center; }
        .emergency-triage-page .patient-summary { align-items: flex-start; flex-wrap: wrap; }
        .emergency-triage-page .patient-id { margin-left: 60px; }
        .emergency-triage-page .field-span-2,
        .emergency-triage-page .field-span-3,
        .emergency-triage-page .field-span-4,
        .emergency-triage-page .field-span-6,
        .emergency-triage-page .field-span-8,
        .emergency-triage-page .field-span-9,
        .emergency-triage-page .field-span-12 { grid-column: span 12; }
        .emergency-triage-page .triage-actions { padding: 15px; }
        .emergency-triage-page .btn-update-triage { width: 100%; }
    }
</style>

<div class="content emergency-triage-page">
    <div class="triage-hero">
        <div class="triage-hero-copy">
            <div class="triage-hero-icon"><i class="fas fa-ambulance"></i></div>
            <div>
                <div class="triage-eyebrow">Atención prioritaria</div>
                <h1>{{trans('emergencia.Registrodeemergencia')}}</h1>
                <p>Actualiza la valoración clínica, los signos vitales y la prioridad de atención del paciente.</p>
            </div>
        </div>
        <button type="button" onclick="location.href='{{route('hospital.emergencia')}}'" class="triage-back">
            <i class="fas fa-arrow-left"></i> {{trans('emergencia.Regresar')}}
        </button>
    </div>

    <form id="form_manchester">
        {{ csrf_field() }}
        <input type="hidden" name="id_manchester" value="{{$id}}">
        <input type="hidden" name="version" value="{{ $version }}">

        <div class="triage-panel">
            <div class="patient-summary">
                <div class="patient-avatar"><i class="fas fa-user-injured"></i></div>
                <div class="patient-summary-main">
                    <div class="patient-kicker">Paciente en evaluación</div>
                    <div class="patient-name">{{$datos->apellido1}} {{$datos->apellido2}} {{$datos->nombre1}} {{$datos->nombre2}}</div>
                </div>
                <div class="patient-id"><i class="far fa-id-card"></i>&nbsp; {{ $datos->id_paciente}}</div>

                <div class="patient-data-inputs" aria-hidden="true">
                    <input type="text" class="nombre" name="nombre" id="id_paciente" value="{{$datos->apellido1}} {{$datos->apellido2}} {{$datos->nombre1}} {{$datos->nombre2}}" readonly>
                    <input type="text" id="paciente" name="paciente" value="{{ $datos->id_paciente}}" readonly>
                </div>
            </div>

            <section class="triage-section">
                <div class="section-heading">
                    <div class="section-icon"><i class="fas fa-notes-medical"></i></div>
                    <div>
                        <h2>Motivo y clasificación de ingreso</h2>
                        <p>Registra la causa de atención y el contexto inicial de la emergencia.</p>
                    </div>
                </div>
                <div class="triage-grid">
                    <div class="triage-field field-span-8">
                        <label for="mot_consulta">{{trans('emergencia.MotivodeConsulta')}}</label>
                        <input type="text" class="form-control validar" name="mot_consulta" id="mot_consulta" value="{{ $datos->motivo_consulta}} ">
                    </div>
                    <div class="triage-field field-span-2">
                        <label for="tipos_emergencia">{{trans('emergencia.TipodeEmergencia')}}</label>
                        <select class="form-control validar" name="tipos_emergencia" id="tipos_emergencia" required>
                            <option value="0">{{trans('emergencia.Seleccione')}}</option>
                            @foreach($tipos_emergencia as $value)
                            <option @if($value->id == $datos->tipo_emergencia) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="triage-field field-span-2">
                        <label for="embarazo_p">{{trans('emergencia.EmbarazoPuerperio:')}}</label>
                        <select class="form-control" name="embarazo_p" id="embarazo_p">
                            <option value="">{{trans('emergencia.Seleccione')}}</option>
                            <option @if($datos->embarazo_puerperio == '1') selected @endif value="1">Sí</option>
                            <option @if($datos->embarazo_puerperio == '0') selected @endif value="0">No</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="triage-section">
                <div class="section-heading">
                    <div class="section-icon"><i class="fas fa-heartbeat"></i></div>
                    <div>
                        <h2>Signos vitales y medidas</h2>
                        <p>Ingresa los valores actuales para facilitar la evaluación y el seguimiento clínico.</p>
                    </div>
                </div>
                <div class="triage-grid">
                    <div class="triage-field field-span-2">
                        <label for="presion_art_sis">{{trans('emergencia.PresionArterialSistolica')}}</label>
                        <div class="field-with-unit"><input type="text" class="form-control" id="presion_art_sis" name="presion_art_sis" value="{{ $datos->presion_sistolica}}"><span class="field-unit">mmHg</span></div>
                    </div>
                    <div class="triage-field field-span-2">
                        <label for="presion_art_dias">{{trans('emergencia.PresionArterialDiastolica')}}</label>
                        <div class="field-with-unit"><input type="text" class="form-control" id="presion_art_dias" name="presion_art_dias" value="{{ $datos->presion_diastolica}}"><span class="field-unit">mmHg</span></div>
                    </div>
                    <div class="triage-field field-span-2">
                        <label for="frec_cardiaca">{{trans('emergencia.FrecuenciaCardiaca')}}</label>
                        <div class="field-with-unit"><input type="text" class="form-control" name="frec_cardiaca" id="frec_cardiaca" value="{{ $datos->frec_cardiaca}}"><span class="field-unit">lpm</span></div>
                    </div>
                    <div class="triage-field field-span-2">
                        <label for="frec_resp">{{trans('emergencia.FrecuenciaRespiratoria')}}</label>
                        <div class="field-with-unit"><input type="text" class="form-control" id="frec_resp" name="frec_resp" value="{{ $datos->frec_resp}}"><span class="field-unit">rpm</span></div>
                    </div>
                    <div class="triage-field field-span-2">
                        <label for="temperatura">{{trans('emergencia.TemperaturaC')}}</label>
                        <div class="field-with-unit"><input type="text" class="form-control" id="temperatura" name="temperatura" value="{{ $datos->temp}}"><span class="field-unit">°C</span></div>
                    </div>
                    <div class="triage-field field-span-2">
                        <label for="satura_oxigeno">{{trans('emergencia.SaturaciondeOxigeno')}}</label>
                        <div class="field-with-unit"><input type="text" class="form-control" id="satura_oxigeno" name="satura_oxigeno" value="{{ $datos->sat_oxigeno}}"><span class="field-unit">%</span></div>
                    </div>
                    <div class="triage-field field-span-3">
                        <label for="talla">{{trans('emergencia.Tallacm')}}</label>
                        <div class="field-with-unit"><input type="text" class="form-control" id="talla" name="talla" value="{{ $datos->talla}}"><span class="field-unit">cm</span></div>
                    </div>
                    <div class="triage-field field-span-3">
                        <label for="peso">{{trans('emergencia.PesoKg')}}</label>
                        <div class="field-with-unit"><input type="text" class="form-control" id="peso" name="peso" value="{{ $datos->peso}}"><span class="field-unit">kg</span></div>
                    </div>
                    <div class="triage-field field-span-3">
                        <label for="total_capilar">{{trans('emergencia.TotalLlenadoCapilar')}}</label>
                        <div class="field-with-unit"><input type="text" class="form-control" id="total_capilar" name="total_capilar" value="{{ $datos->llenado_capilar}}"><span class="field-unit">seg</span></div>
                    </div>
                    <div class="triage-field field-span-3">
                        <label for="reac_pupilar">{{trans('emergencia.ReaccionPupilar')}}</label>
                        <select class="form-control" id="reac_pupilar" name="reac_pupilar">
                            <option @if($datos->reaccion_pupilar == 'Si') selected @endif value="Si">Sí</option>
                            <option @if($datos->reaccion_pupilar == 'No') selected @endif value="No">No</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="triage-section">
                <div class="section-heading">
                    <div class="section-icon"><i class="fas fa-brain"></i></div>
                    <div>
                        <h2>Valoración neurológica</h2>
                        <p>Completa las respuestas de Glasgow y el estado actual de conciencia.</p>
                    </div>
                </div>
                <div class="triage-grid">
                    <div class="triage-field field-span-3">
                        <label for="resp_ocular">{{trans('emergencia.RespuestaOcular')}}</label>
                        <select class="form-control validar" id="resp_ocular" name="resp_ocular">
                            <option value="">{{trans('emergencia.Seleccione')}}</option>
                            @foreach($ocular as $oc)
                            <option @if($datos->resp_ocular == $oc->prioridad) selected @endif value="{{$oc->prioridad}}">{{$oc->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="triage-field field-span-3">
                        <label for="resp_verbal">{{trans('emergencia.RespuestaVerbal')}}</label>
                        <select class="form-control validar" id="resp_verbal" name="resp_verbal">
                            <option value="">{{trans('emergencia.Seleccione')}}</option>
                            @foreach($verbal as $verb)
                            <option @if($datos->resp_verbal == $verb->prioridad) selected @endif value="{{$verb->prioridad}}">{{$verb->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="triage-field field-span-3">
                        <label for="resp_motora">{{trans('emergencia.RespuestaMotora')}}</label>
                        <select class="form-control validar" id="resp_motora" name="resp_motora">
                            <option value="">{{trans('emergencia.Seleccione')}}</option>
                            @foreach($motora as $mot)
                            <option @if($mot->prioridad == $datos->resp_motora) selected @endif value="{{$mot->prioridad}}">{{$mot->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="triage-field field-span-3">
                        <label for="est_conciencia">{{trans('emergencia.EstadodeConciencia')}}</label>
                        <select class="form-control validar" name="est_conciencia" id="est_conciencia">
                            <option value="">{{trans('emergencia.Seleccione')}}</option>
                            <option @if($datos->estado_conciencia == '1') selected @endif value="1">Consciente</option>
                            <option @if($datos->estado_conciencia == '0') selected @endif value="0">Inconsciente</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="triage-section">
                <div class="section-heading">
                    <div class="section-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div>
                        <h2>Prioridad clínica</h2>
                        <p>Define el nivel de atención conforme a la evaluación registrada.</p>
                    </div>
                </div>
                <div class="triage-grid">
                    <div class="triage-field field-span-4 priority-box">
                        <label for="prioridad">{{trans('emergencia.Prioridad')}}</label>
                        <select class="form-control validar priority-select" name="prioridad" id="prioridad" required>
                            <option value="0">{{trans('emergencia.Seleccione')}}</option>
                            @foreach($prioridad as $value)
                            <option class="priority-level-{{$value->prioridad}}" @if($value->id == $datos->prioridad) selected @endif data-level="{{$value->prioridad}}" value="{{$value->id}}">{{$value->nombre}}</option>
                            @endforeach
                        </select>
                        <span class="priority-help">El color identifica visualmente el nivel de urgencia seleccionado.</span>
                    </div>
                </div>
            </section>

            <div class="triage-actions">
                <button type="button" onclick="update_manchester();" class="btn-update-triage">
                    <i class="far fa-save"></i> {{trans('emergencia.Actualizar')}}
                </button>
            </div>
        </div>
    </form>
</div>

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script type="text/javascript">
    function updatePriorityAppearance() {
        var priority = document.getElementById('prioridad');
        if (!priority) return;

        priority.className = priority.className.replace(/\s*priority-level-\d/g, '');
        if (priority.value !== '0') {
            var option = priority.options[priority.selectedIndex];
            if (option && option.dataset.level) priority.classList.add('priority-level-' + option.dataset.level);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var priority = document.getElementById('prioridad');
        if (!priority) return;
        updatePriorityAppearance();
        priority.addEventListener('change', updatePriorityAppearance);
    });

    function update_manchester() {
        if (!validarDatos()) {
            $.ajax({
                type: 'post',
                url: "{{ route('manchester.update') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_manchester").serialize(),
                success: function(data) {
                    alertas(data.respuesta, `${data.respuesta.toUpperCase()}...`, data.msj)
                    if (data.respuesta == 'success') {
                        setTimeout(function() {
                            window.location.href = "{{route('hospital.emergencia')}}";
                        }, 1500)
                    }
                },
                error: function(data) {
                    var error = data.responseJSON || {};
                    alert(error.msj || error.message || 'No se pudo registrar el triaje. Recargue y revise los datos.');
                }
            });
        }
    }
</script>

@endsection
