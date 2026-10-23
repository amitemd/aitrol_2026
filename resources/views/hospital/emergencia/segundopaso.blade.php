<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d H:m:i");
?>
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="d-flex align-items-center col-md-12">
                    <h4 class="card-title ml-25 colorbasic">
                        {{ trans('pasos.atencion') }}
                    </h4>
                </div>
            </div>


        </div>
    </div>
    <div class="card-body">
        <form id="form_paso2">
            <input type="hidden" name="solicitud_id" value="{{$solicitud->id}}">
            <div class="row" style="padding-top: 10px;">

                <div class="col-md-4">
                    <label class="col-form-label-sm"><b>{{ trans('pasos.Fecha') }}</b></label>

                    <div class="input-group date">
                        <input type="text" data-input="true" class="form-control input-sm flatpickr-date-time active" name="fecha" id="fecha" value="@if($solicitud->fecha_ingreso==null){{$fecha}}@else{{$solicitud->fecha_ingreso}}@endif" autocomplete="off">
                        <div class="input-group-addon">
                            <i class="glyphicon glyphicon-remove-circle"></i>
                        </div>
                    </div>
                </div>
                @php $paciente = $solicitud->paciente; @endphp

                @php $form008 = $solicitud->form008->first(); @endphp

                <div class="col-md-4">
                    <label class="col-form-label-sm"><b>{{ trans('pasos.CondicionLlegada') }}</b></label>

                    <select class="form-control" name="condicion_llegada" id="condicion_llegada">
                        <option value="">Seleccione ...</option>
                        <option value="1" @if($form008->condicion=='1') selected @endif>Estable</option>
                        <option value="2" @if($form008->condicion=='2') selected @endif>Inestable</option>
                        <option value="3" @if($form008->condicion=='3') selected @endif>Fallecido</option>
                    </select>
                </div>
                <div class="col-md-12">
                    &nbsp;
                </div>
                <div class="col-md-12">
                    <label class="col-form-label-sm"><b>{{ trans('pasos.MotivoAtencion') }}</b></label>
                </div>
                <div class="col-md-6">
                    <input class="form-control" type="text" name="motivo" id="motivo" value="{{$form008->motivo}}">
                </div>
                <div class="col-md-12">
                    &nbsp;
                </div>

                <div class="col-md-12" style="text-align: center; margin-top: 10px;">
                    <button class="btn btn-primary" onclick="guardar_paso2();" type="button"> <i class="fa fa-save"></i> </button>
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
                alert('error al cargar');
            }
        });

    }
</script>