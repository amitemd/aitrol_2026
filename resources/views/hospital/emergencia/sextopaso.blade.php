<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d");
?>

@include('hospital.emergencia.partials.clinical-step-styles')
<style>
    #form_paso6 .vitals-measure-grid,
    #form_paso6 .glasgow-main-grid,
    #form_paso6 .glasgow-detail-grid {
        display: grid;
        gap: 16px;
        align-items: end;
    }
    #form_paso6 .vitals-measure-grid { grid-template-columns: repeat(5, minmax(0, 1fr)); }
    #form_paso6 .glasgow-main-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    #form_paso6 .glasgow-detail-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    #form_paso6 .vitals-measure-grid > [class*="col-md-"],
    #form_paso6 .glasgow-main-grid > [class*="col-md-"],
    #form_paso6 .glasgow-detail-grid > [class*="col-md-"] {
        width: auto;
        max-width: none;
        padding: 0;
    }
    #form_paso6 .vitals-field,
    #form_paso6 .vitals-measure-grid > [class*="col-md-"],
    #form_paso6 .glasgow-main-grid > [class*="col-md-"],
    #form_paso6 .glasgow-detail-grid > [class*="col-md-"] { min-width: 0; }
    #form_paso6 .vitals-field label,
    #form_paso6 .vitals-measure-grid label,
    #form_paso6 .glasgow-main-grid label,
    #form_paso6 .glasgow-detail-grid label {
        display: block;
        min-height: 38px;
        margin-bottom: 6px;
        color: #284c63;
        font-size: 12px !important;
        font-weight: 750;
        line-height: 1.3;
    }
    #form_paso6 .glasgow-title {
        margin: 22px 0 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e2edf2;
        color: #0b3a5b;
        font-size: 14px;
        font-weight: 800;
    }
    #form_paso6 .glasgow-detail-grid { margin-top: 16px; }
    #form_paso6 .clinical-form-actions { margin-top: 16px; }
    @media (max-width: 1100px) {
        #form_paso6 .vitals-measure-grid,
        #form_paso6 .glasgow-detail-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 750px) {
        #form_paso6 .vitals-measure-grid,
        #form_paso6 .glasgow-main-grid,
        #form_paso6 .glasgow-detail-grid { grid-template-columns: 1fr; }
        #form_paso6 .vitals-field label,
        #form_paso6 .vitals-measure-grid label,
        #form_paso6 .glasgow-main-grid label,
        #form_paso6 .glasgow-detail-grid label { min-height: 0; }
    }
</style>

<div class="card clinical-step-card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="d-flex align-items-center col-md-12">
                    <h4 class="card-title ml-25 colorbasic">
                        {{trans('pasos.SignosVitalesMedicionesyValores')}}
                    </h4>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form id="form_paso6">

            <div style="padding-top: 10px;">
                @php

                $form008 = $solicitud->form008->first();
                if($form008 != null) {
                $hc = $form008->agenda->historia_clinica;
                } else {
                $hc = null;
                }

                @endphp

                <input type="hidden" name="solicitud_id" value="{{$solicitud->id}}">

                <div class="vitals-measure-grid">
                <div class="col-md-3">
                    <label style="font-size: 10px;"><b>{{trans('pasos.SinConstantesVitales')}}</b></label>
                    <input type="text" name="sin_constantes" class="form-control input-sm" value="@if($form008->sin_constantes != null){{$form008->sin_constantes}} @endif">
                </div>

                <div class="col-md-3">
                    <label style="font-size: 10px;"><b>{{trans('pasos.PresiónArterial')}}</b></label>
                    <input type="text" name="presion_arterial" class="form-control input-sm" value="{{$vitalValues['pressure']}}">
                </div>

                <div class="col-md-2">
                    <label style="font-size: 10px;"><b>{{trans('pasos.F_CardiacaMin')}}</b></label>
                    <input type="text" name="frec_cardiaca" class="form-control input-sm" value="{{$vitalValues['pulse']}}">
                </div>
                <div class="col-md-2">
                    <label style="font-size: 10px;"><b>{{trans('pasos.F_RespMin')}}</b></label>
                    <input type="text" name="frec_respiratoria" class="form-control input-sm" value="{{$vitalValues['respiratory']}}">
                </div>

                <div class="col-md-2">
                    <label style="font-size: 10px;"><b>{{trans('pasos.Pulsioximetria')}}</b></label>
                    <input type="text" name="pulsioximetria" class="form-control input-sm" value="{{$vitalValues['oxygen']}}">
                </div>

                <div class="col-md-2">
                    <label style="font-size: 10px;"><b>{{trans('boxesh.Temperatura')}} (°C)</b></label>
                    <input type="text" name="temp_bucal" class="form-control input-sm" value="{{$vitalValues['temperature']}}">
                </div>

                <div class="col-md-2">
                    <label style="font-size: 10px;"><b>{{trans('pasos.PerimetroCefalico')}}</b></label>
                    <input type="text" name="perimetro_cefalico" class="form-control input-sm" value="@if($form008->perimetro_cefalico != null){{$form008->perimetro_cefalico}} @endif">
                </div>
                <div class="col-md-2">
                    <label style="font-size: 10px;"><b>{{trans('pasos.PesoKg')}}</b></label>
                    <input type="text" name="peso" class="form-control input-sm" value="{{$vitalValues['weight']}}">
                </div>
                <div class="col-md-2">
                    <label style="font-size: 10px;"><b>{{trans('pasos.TallaM')}}</b></label>
                    <input type="text" name="talla" class="form-control input-sm" value="{{$vitalValues['height']}}">
                </div>
                <div class="col-md-2">
                    <label style="font-size: 10px;"><b>{{trans('pasos.GlicemiaCapilar')}}</b></label>
                    <input type="text" name="glicemia_capilar" class="form-control input-sm" value="@if($form008->glicemia_capilar != null){{$form008->glicemia_capilar}} @endif">
                </div>
                </div>

                <div class="glasgow-title">{{trans('pasos.GLASGOW')}}</div>
                <div class="glasgow-main-grid">
                <div class="col-md-3">
                    <label style="font-size:10px;"><b>{{trans('pasos.Ocular(4)')}}</b></label>
                    <select id="ocular" name="ocular" class="form-control form-control-sm" onchange="calcular_glas()">
                        <option style="font-size: 10px;" value="">{{trans('pasos.Seleccione..')}}</option>
                        @foreach($ocular as $oc)
                        <option style="font-size: 10px;" @if($form008->ocular == null) @if($triaje != null)@if($triaje->resp_ocular == $oc->prioridad) selected @endif @endif @else @if($form008->ocular == $oc->prioridad) selected @endif @endif value="{{$oc->prioridad}}"> {{$oc->nombre}} </option>
                        @endforeach

                    </select>
                </div>
                <div class="col-md-4">
                    <label style="font-size:10px;"><b>{{trans('pasos.Verbal(5)')}}</b></label>
                    <select id="verbal" name="verbal" class="form-control form-control-sm" onchange="calcular_glas()">
                        <option style="font-size: 10px;" value="">{{trans('pasos.Seleccione..')}}</option>
                        @foreach($verbal as $verb)
                        <option style="font-size: 10px;" @if($form008->verbal == null) @if($triaje != null)@if($triaje->resp_verbal == $verb->prioridad) selected @endif @endif @else @if($form008->verbal == $verb->prioridad) selected @endif @endif value="{{$verb->prioridad}}">{{$verb->nombre}}</option>
                        @endforeach

                    </select>
                </div>
                <div class="col-md-4">
                    <label style="font-size:10px;"><b>{{trans('pasos.Motora(6)')}}</b></label>
                    <select id="motora" name="motora" class="form-control form-control-sm" onchange="calcular_glas()">
                        <option style="font-size: 10px;" value="">{{trans('pasos.Seleccione..')}}</option>
                        @foreach($motora as $mot)
                        <option style="font-size: 10px;" @if($form008->motora == null) @if($triaje != null)@if($triaje->resp_motora == $mot->prioridad) selected @endif @endif @else @if($form008->motora == $mot->prioridad) selected @endif @endif value="{{$mot->prioridad}}">{{$mot->nombre}}</option>
                        @endforeach

                    </select>
                </div>

                </div>
                <div class="glasgow-detail-grid">
                <div class="col-md-2">
                    <label style="font-size:10px;"><b>{{trans('pasos.Total(15)')}}</b></label>
                    <input type="text" name="total_glas" id="total_glas" class="form-control input-sm" value="@if($form008->total_glas !== null && $form008->total_glas !== ''){{$form008->total_glas}}@elseif($triaje != null){{(int) $triaje->resp_ocular + (int) $triaje->resp_verbal + (int) $triaje->resp_motora}}@endif" readonly>
                </div>
                <div class="col-md-2">
                    <label style="font-size:10px;"><b>{{trans('pasos.Reac_PupilaDer')}}</b></label>
                    <select class="form-control form-control-sm" name="pupila_der" id="pupila_der">
                        <option value="Si">{{trans('emergencia.SI')}}</option>
                        <option value="No">{{trans('emergencia.NO')}}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label style="font-size:10px;"><b>{{trans('pasos.Reac_PupilaIzq')}}</b></label>

                    <select class="form-control form-control-sm" name="pupila_izq" id="pupila_izq">
                        <option value="Si">{{trans('emergencia.SI')}}</option>
                        <option value="No">{{trans('emergencia.NO')}}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label style="font-size:10px;"><b>{{trans('pasos.T_llenadoCapilar')}}</b></label>
                    <input type="text" name="llenado_capilar" class="form-control input-sm" value="@if($form008->t_llenado_capilar == null)@if($triaje != null) {{$triaje->llenado_capilar}}@endif @else {{$form008->t_llenado_capilar}} @endif">
                </div>
                </div>
                {{-- <div class="col-md-2">
                    <label style="font-size:10px;"><b>{{trans('pasos.Satura_Oxigeno')}}</b></label>
                    <input type="text" name="satura_oxigeno" class="form-control input-sm" value=" @if($form008->satura_oxigeno == null)@if($triaje != null){{$triaje->sat_oxigeno}}@endif @else{{$form008->satura_oxigeno}}@endif">
                </div> --}}
                <div class="clinical-form-actions">
                    <button class="btn btn-primary" onclick="guardar_paso6();" type="button"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script type="text/javascript">
    calcular_glas();

    function calcular_glas() {
        var ocular = $('#ocular').val();
        if (ocular == '') {
            ocular = 0;
        }
        var ocular = parseInt(ocular);
        var verbal = $('#verbal').val();
        if (verbal == '') {
            verbal = 0;
        }
        var verbal = parseInt(verbal);
        var motora = $('#motora').val();
        if (motora == '') {
            motora = 0;
        }
        var motora = parseInt(motora);
        var total = ocular + verbal + motora;

        $('#total_glas').val(total);
    }

    function guardar_paso6() {

        $.ajax({
            type: "post",
            url: "{{route('hospital.sextopaso_store')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            dataType: 'json',
            data: $("#form_paso6").serialize(),

            success: function(response) {
                $("#content").html(response.html);

                $.each(response.vitals || {}, function(key, value) {
                    $('[data-vital-summary="' + key + '"]').text(value);
                });

                return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`);
            },
            error: function() {
                alert("{{trans('pasos.ErrorCargar')}}");
            }
        });

    }
</script>
