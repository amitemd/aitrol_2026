<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d");
?>

@include('hospital.emergencia.partials.clinical-step-styles')

<div class="card clinical-step-card">
    <div class="card-header clinical-step-primary-header">
        <h4 class="card-title clinical-step-primary-title">{{trans('pasos.EnfermedadActualyRevisiondeSistemas')}}</h4>
    </div>
    <div class="card-body clinical-step-body">
        <form id="form_paso5">
            @php 
                $form008 = $solicitud->form008->first(); 
            @endphp
            <input type="hidden" name="solicitud_id" value="{{$solicitud->id}}"> 
            <div class="clinical-form-grid">
                {{-- <div class="col-md-12">
                    <b> {{trans('pasos.VíaAéreaLibre')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="aerea_libre" id="aerea_libre" value="{{$form008->aerea_libre}}">
                </div> 
                <div class="col-md-12">
                    <b> {{trans('pasos.VíaAéreaObstruida')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="aerea_obstruida" id="aerea_obstruida" value="{{$form008->aerea_obstruida}}">
                </div> 
                <div class="col-md-12">
                    <b> {{trans('pasos.CondiciónEstable')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="condicion_estable" id="condicion_estable" value="{{$form008->condicion_estable}}">
                </div> 
                <div class="col-md-12">
                    <b> {{trans('pasos.CondiciónInestable')}}</b>
                </div>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="condicion_inestable" id="condicion_inestable" value="{{$form008->condicion_inestable}}">
                </div> --}}
                <div class="clinical-field clinical-field--full">
                    <label for="observacion_quintop">{{trans('pasos.Observacion')}}</label>
                    <textarea class="form-control" name="observacion_quintop" id="observacion_quintop" placeholder="{{trans('pasos.Observacion')}}">{{$form008->observacion_quintop}}</textarea>
                </div> 
                <div class="clinical-form-actions">
                    <button class="btn btn-primary clinical-save-button" onclick="guardar_paso5();" type="button">
                        <i class="fa fa-save"></i> Guardar
                    </button>
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
    function guardar_paso5() {

        $.ajax({
            type: "post",
            url: "{{route('hospital.quintopaso_store')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form_paso5").serialize(),
            
            success: function(datahtml, data) {
                $("#content").html(datahtml);
                return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`); 
            },
            error: function() {
                alert("{{trans('pasos.ErrorCargar')}}");
            }
        });
           
    }
</script>
