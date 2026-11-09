<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d H:m:i");
?>
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
@include('hospital.emergencia.partials.clinical-step-styles')

<div class="card clinical-step-card">
    <div class="card-header clinical-step-primary-header">
        <h4 class="card-title clinical-step-primary-title">{{ trans('pasos.atencion') }}</h4>
    </div>
    <div class="card-body clinical-step-body">
        <form id="form_paso2">
            <input type="hidden" name="solicitud_id" value="{{$solicitud->id}}">
            <div class="clinical-form-grid clinical-form-grid--three">
                <div class="clinical-field">
                    <label for="fecha">{{ trans('pasos.Fecha') }}</label>
                    <div class="input-group date">
                        <input type="text" data-input="true" class="form-control input-sm flatpickr-date-time active" name="fecha" id="fecha" value="@if($solicitud->fecha_ingreso==null){{$fecha}}@else{{$solicitud->fecha_ingreso}}@endif" autocomplete="off">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                    </div>
                </div>
                @php $paciente = $solicitud->paciente; @endphp
                @php $form008 = $solicitud->form008->first(); @endphp

                <div class="clinical-field">
                    <label for="condicion_llegada">{{ trans('pasos.CondicionLlegada') }}</label>
                    <select class="form-control" name="condicion_llegada" id="condicion_llegada">
                        <option value="">{{trans('paso2.Seleccione')}}</option>
                        <option value="1" @if($form008->condicion=='1') selected @endif>{{trans('paso2.Estable')}}</option>
                        <option value="2" @if($form008->condicion=='2') selected @endif>{{trans('paso2.Inestable')}}</option>
                        <option value="3" @if($form008->condicion=='3') selected @endif>{{trans('paso2.Fallecido')}}</option>
                    </select>
                </div>

                <div class="clinical-field clinical-field--full">
                    <label for="motivo">{{ trans('pasos.MotivoAtencion') }}</label>
                    <input class="form-control" type="text" name="motivo" id="motivo" value="{{$form008->motivo}}">
                </div>

                <div class="clinical-form-actions">
                    <button class="btn btn-primary clinical-save-button" onclick="guardar_paso2();" type="button">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
<script src="{{asset('ho/app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
<script type="text/javascript">
    function guardar_paso2() {

        $.ajax({
            type: "post",
            url: "{{route('hospital.segundopaso_store')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#form_paso2").serialize(),

            success: function(datahtml, data) {
                $("#content").html(datahtml);
                return Swal.fire('Guardado Correctamente');
            },
            error: function() {
                alert("{{trans('pasos.ErrorCargar')}}");
            }
        });

    }
</script>
