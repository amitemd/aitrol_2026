<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d");
?>

<div class="card">
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

            <div class="row" style="padding-top: 10px;">
                @php

                $form008 = $solicitud->form008->first();
                if($form008 != null) {
                $hc = $form008->agenda->historia_clinica;
                } else {
                $hc = null;
                }

                $triaje = $solicitud->manchester->first();

                @endphp

                <input type="hidden" name="solicitud_id" value="{{$solicitud->id}}">

                <div class="col-md-3">
                    <label style="font-size: 10px;"><b>{{trans('pasos.SinConstantesVitales')}}</b></label>
                    <input type="text" name="sin_constantes" class="form-control input-sm" value="@if($form008->sin_constantes != null){{$form008->sin_constantes}} @endif">
                </div>

                <div class="col-md-3">
                    <label style="font-size: 10px;"><b>{{trans('pasos.PresiónArterial')}}</b></label>
                    <input type="text" name="presion_arterial" class="form-control input-sm" value="@if($hc->presion==0)@if($triaje != null) {{$triaje->presion_sistolica}} / {{$triaje->presion_diastolica}} @endif @else{{$hc->presion}}@endif">
                </div>

                <div class="col-md-2">
                    <label style="font-size: 10px;"><b>{{trans('pasos.F.CardiacaMin')}}</b></label>
                    <input type="text" name="frec_cardiaca" class="form-control input-sm" value="@if($hc->pulso==null)@if($triaje != null){{$triaje->frec_cardiaca}} @endif @else {{$hc->pulso}} @endif">
                </div>
                <div class="col-md-2">
                    <label style="font-size: 10px;"><b>{{trans('pasos.F.RespMin')}}</b></label>
                    <input type="text" name="frec_respiratoria" class="form-control input-sm" value="@if($form008->frec_respiratoria == null)@if($triaje != null){{$triaje->frec_resp}}@endif @else{{$form008->frec_respiratoria}} @endif">
                </div>

                <div class="col-md-2">
                    <label style="font-size: 10px;"><b>{{trans('pasos.Pulsioximetria')}}</b></label>
                    <input type="text" name="pulsioximetria" class="form-control input-sm" value="@if($form008->pulsioximetria != null){{$form008->pulsioximetria}} @endif">
                </div>

                <div class="col-md-2">
                    <label style="font-size: 10px;"><b>{{trans('pasos.PerimetroCefalico')}}</b></label>
                    <input type="text" name="perimetro_cefalico" class="form-control input-sm" value="@if($form008->perimetro_cefalico != null){{$form008->perimetro_cefalico}} @endif">
                </div>
                <div class="col-md-2">
                    <label style="font-size: 10px;"><b>{{trans('pasos.PesoKg')}}</b></label>
                    <input type="text" name="peso" class="form-control input-sm" value="@if($hc->peso == 0)@if($triaje != null){{$triaje->peso}}@endif @else{{$hc->peso}} @endif">
                </div>
                <div class="col-md-2">
                    <label style="font-size: 10px;"><b>{{trans('pasos.TallaM')}}</b></label>
                    <input type="text" name="talla" class="form-control input-sm" value="@if($hc->altura == 0)@if($triaje != null){{$triaje->talla}}@endif @else{{ $hc->altura}}@endif ">
                </div>
                <div class="col-md-2">
                    <label style="font-size: 10px;"><b>{{trans('pasos.GlicemiaCapilar')}}</b></label>
                    <input type="text" name="glicemia_capilar" class="form-control input-sm" value="@if($form008->glicemia_capilar != null){{$form008->glicemia_capilar}} @endif">
                </div>
                <div class="col-md-10">
                    &nbsp;
                </div>
                <div class="col-md-10">
                    <br>
                    <label style="font-size:12px;"><b>{{trans('pasos.GLASGOW')}}</b> </label>
                </div>
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
                <div class="col-md-2">
                    <label style="font-size:10px;"><b>{{trans('pasos.Total(15)')}}</b></label>
                    <input type="text" name="total_glas" id="total_glas" class="form-control input-sm" value="{{$form008->total_glas}}">
                </div>
                <div class="col-md-2">
                    <label style="font-size:10px;"><b>{{trans('pasos.Reac.PupilaDer')}}</b></label>
                    <select class="form-control form-control-sm" name="pupila_der" id="pupila_der">
                        <option value="Si">Si</option>
                        <option value="No">No</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label style="font-size:10px;"><b>{{trans('pasos.Reac.PupilaIzq')}}</b></label>

                    <select class="form-control form-control-sm" name="pupila_izq" id="pupila_izq">
                        <option value="Si">Si</option>
                        <option value="No">No</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label style="font-size:10px;"><b>{{trans('pasos.T.llenadoCapilar')}}</b></label>
                    <input type="text" name="llenado_capilar" class="form-control input-sm" value="@if($form008->t_llenado_capilar == null)@if($triaje != null) {{$triaje->llenado_capilar}}@endif @else {{$form008->t_llenado_capilar}} @endif">
                </div>
                {{-- <div class="col-md-2">
                    <label style="font-size:10px;"><b>{{trans('pasos.Satura.Oxigeno')}}</b></label>
                    <input type="text" name="satura_oxigeno" class="form-control input-sm" value=" @if($form008->satura_oxigeno == null)@if($triaje != null){{$triaje->sat_oxigeno}}@endif @else{{$form008->satura_oxigeno}}@endif">
                </div> --}}
                <div class="col-md-12" style="text-align: center; margin-top: 10px;">
                    <button class="btn btn-primary" onclick="guardar_paso6();" type="button"> <i class="fa fa-save"></i> </button>
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
            datatype: 'json',
            data: $("#form_paso6").serialize(),

            success: function(datahtml, data) {
                $("#content").html(datahtml);
                return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`);
            },
            error: function() {
                alert('error al cargar');
            }
        });

    }
</script>