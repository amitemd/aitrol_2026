<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d H:m:i");
?>
<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="d-flex align-items-center col-md-12">
                    <h4 class="card-title ml-25 colorbasic">
                        {{trans('pasos.AccidenteViolenciaIntoxicacion')}}
                    </h4>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form id="form_paso3">
        <div class="row" style="padding-top: 10px;">
            @php $form008 = $solicitud->form008->first(); @endphp
            <input type="hidden" name="solicitud_id" value="{{$solicitud->id}}">
            <div class="col-md-4">
                <label> {{trans('pasos.FechayHora')}}</label>
               <div class="input-group date">
                      <input type="text"  data-input="true" class="form-control input-sm flatpickr-date-time active" name="fecha" id="fecha" value="@if($form008->fecha_evento == null) {{$fecha}} @else {{$form008->fecha_evento}} @endif" autocomplete="off">
                      <div class="input-group-addon">
                        <i class="glyphicon glyphicon-remove-circle"></i>
                      </div>   
                </div>
            </div>
            
            <div class="col-md-4">
                <label >{{trans('pasos.LugardelEvento')}}</label>
                <input type="text" name="lugar_evento" class="form-control input-sm" value="{{$form008->lugar_evento}}">
            </div>
            <div class="col-md-4">
                <label >{{trans('pasos.DireccióndelEvento')}}</label>
                <input type="text" name="direccion_evento" class="form-control input-sm" value="{{$form008->direccion_evento}}">
            </div>
            <div class="col-md-12">
                &nbsp;
            </div>

            <div class="col-md-4">
                <input type="checkbox" name="custodia" value="1" @if($form008->custodia_policial=='1') checked @endif>
                <label >{{trans('pasos.CustodiaPolicial')}}</label>
            </div>

            <div class="col-md-8">
                &nbsp;
            </div>

            <div class="col-md-4">
                <input type="checkbox" name="accidente_transito" value="1"  @if($form008->accidente_transito=='1') checked @endif>
                <label >{{trans('pasos.AccidentedeTransito')}}</label>
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="caida" value="1" @if($form008->caida=='1') checked @endif>
                <label >{{trans('pasos.Caída')}}</label>
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="quemadura" value="1" @if($form008->quemadura=='1') checked @endif>
                <label >{{trans('pasos.Quemadura')}}</label>
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="mordedura" value="1" @if($form008->mordedura=='1') checked @endif>
                <label >{{trans('pasos.Mordedura')}}</label>
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="ahogamiento" value="1" @if($form008->ahogamiento=='1') checked @endif>
                <label >{{trans('pasos.Ahogamiento')}}</label>
                
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="arma_fuego" value="1" @if($form008->violencia_armf=='1') checked @endif>
                <label >{{trans('pasos.ViolemciaXArmadeFuego')}}</label>
                
            </div>    
            <div class="col-md-4">
                <input type="checkbox" name="arma_punzante" value="1" @if($form008->violencia_armcp=='1') checked @endif>
                <label >{{trans('pasos.ViolemciaXArmaC.Punzante')}}</label>
                
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="rina" value="1" @if($form008->violencia_rina=='1') checked @endif>
                <label >{{trans('pasos.ViolenciaXRiña')}}</label>
                
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="violencia_familiar" value="1" @if($form008->violencia_familiar=='1') checked @endif>
                <label >{{trans('pasos.ViolenciaFamiliar')}}</label>
                
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="abuso_fisico" value="1" @if($form008->abuso_fisico=='1') checked @endif>
                <label >{{trans('pasos.AbusoFísico')}}</label>
                
            </div>    
            <div class="col-md-4">
                <input type="checkbox" name="abuso_psicologico" value="1" @if($form008->abuso_psicologico=='1') checked @endif>
                <label >{{trans('pasos.AbusoPsicológico')}}</label>
                
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="abuso_sexual" value="1" @if($form008->abuso_sexual=='1') checked @endif>
                <label >{{trans('pasos.AbusoSexual')}}</label>
                
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="intoxicacion_alcoholica" value="1" @if($form008->intoxicacion_alcoholica=='1') checked @endif>
                <label >{{trans('pasos.IntoxicacionAlcohólica')}}</label>
                
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="intoxicacion_alimentaria" value="1" @if($form008->intoxicacion_alimentaria=='1') checked @endif>
                <label >{{trans('pasos.IntoxicacionAlimentaria')}}</label>
                
            </div>   
            <div class="col-md-4">
                <input type="checkbox" name="intoxicacion_drogas" value="1" @if($form008->intoxicacion_drogas=='1') checked @endif>
                <label >{{trans('pasos.IntoxicacionxDrogas')}}</label>
                
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="intoxicacion_gases" value="1" @if($form008->intoxicacion_gases=='1') checked @endif>
                <label >{{trans('pasos.IntoxicaciondeGases')}}</label>
                
            </div>
           
            <div class="col-md-4">
                <input type="checkbox" name="envenenamiento" value="1" @if($form008->envenenamiento=='1') checked @endif>
                <label >{{trans('pasos.Envenenamiento')}}</label>
                
            </div>    
            <div class="col-md-4">
                <input type="checkbox" name="picadura" value="1" @if($form008->picadura=='1') checked @endif>
                <label >{{trans('pasos.Picadura')}}</label>
                
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="anafilaxia" value="1" @if($form008->anafilaxia=='1') checked @endif>
                <label >{{trans('pasos.Anafilaxia')}}</label>
                
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="cuerpo_extrano" value="1" @if($form008->cuerpo_extrano=='1') checked @endif>
                <label >{{trans('pasos.CuerpoExtrano')}}</label>
                
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="aplastamiento" value="1" @if($form008->aplastamiento=='1') checked @endif>
                <label >{{trans('pasos.Aplastamiento')}}</label>
                
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="otro_accidente" value="1" @if($form008->otro_accidente=='1') checked @endif>
                <label >{{trans('pasos.OtroAccidente')}}</label>
                
            </div>
            <div class="col-md-4">
                <input type="checkbox" name="acc_notificacion" value="1" @if($form008->acc_notificacion=='1') checked @endif>
                <label >{{trans('pasos.Notificacion')}}</label>
                
            </div>
            <div class="col-md-12">
                <label>{{trans('pasos.Observaciones')}}</label>
                <input type="text" name="observacion3" id="observacion3" class="form-control input-sm" value="{{$form008->observacion_p3}}">
            </div>
             <div class="col-md-12">
                &nbsp;
            </div>  
            <div class="col-md-4">
                <input type="checkbox" name="aliento_alcohol" value="1" @if($form008->aliento_etilico=='1') checked @endif>
                <label>{{trans('pasos.AlientoEtílico')}}</label>
               
            </div>  
            {{-- <div class="col-md-3">
                <label>{{trans('pasos.ValorAlcocheck')}}</label>
                <input type="text" name="valor_alcohol" class="form-control input-sm" value="{{$form008->valor_alcocheck}}">
            </div> --}}

            <div class="col-md-12" style="text-align: center; margin-top: 10px;">
                <button class="btn btn-primary" onclick="guardar_paso3();" type="button"> <i class="fa fa-save"></i> </button>
            </div>

        </div>
    </form>
    </div>

</div>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
<script src="{{asset('ho/app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
    function guardar_paso3() {

        $.ajax({
            type: "post",
            url: "{{route('hospital.tercerpaso_store')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form_paso3").serialize(),
            
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