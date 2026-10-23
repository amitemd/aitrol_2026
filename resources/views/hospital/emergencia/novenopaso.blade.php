<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">

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
                   {{trans('pasos.EmbarazoParto')}} 
                    </h4>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
    <form id="form_paso9">
      <input type="hidden" name="solicitud_id" value="{{$solicitud->id}}">
            <div class="row" style="padding-top: 10px;">
            @php 
                $form008 = $solicitud->form008->first(); 
                $paciente = $solicitud->paciente; 
            @endphp
                <div class="col-md-3">
                    <label >{{trans('paso2.Gestas')}}</label>
                    <input type="text" name="gestas" id="gestas"  class="form-control input-sm" value="@if($paciente->sexo=='1'){{'NO APLICA'}}@else{{$form008->gestas}}@endif">
                </div>
                
                <div class="col-md-3">
                    <label >{{trans('paso2.Partos')}}</label>
                    <input type="text" name="partos" id="partos" class="form-control input-sm" value="@if($paciente->sexo=='1'){{'NO APLICA'}}@else{{$form008->partos}}@endif">
                </div>
                <div class="col-md-3">
                    <label >{{trans('paso2.Abortos')}}</label>
                    <input type="text" name="abortos" id="abortos" class="form-control input-sm"value="@if($paciente->sexo=='1'){{'NO APLICA'}}@else{{$form008->abortos}}@endif" >
                </div>
                <div class="col-md-3">
                    <label >{{trans('paso2.Cesareas')}}</label>
                    <input type="text" name="cesareas" id="cesareas" class="form-control input-sm" value="@if($paciente->sexo=='1'){{'NO APLICA'}}@else{{$form008->cesareas}}@endif">
                </div>
                <div class="col-md-3">
                    <label >{{trans('paso2.FechaUltimaMenstruacion')}}</label>
                    <input type="text" name="ultima_mens" id="ultima_mens" class="form-control input-sm flatpickr-basic" value="@if($paciente->sexo=='1'){{'NO APLICA'}}@else{{$form008->ultima_menstruacion}}@endif" >
                </div>
                <div class="col-md-3">
                    <label >{{trans('paso2.SemanasGestacion')}}</label>
                    <input type="text" name="semanas_gest" id="semanas_gest" class="form-control input-sm" value="@if($paciente->sexo=='1'){{'NO APLICA'}}@else{{$form008->semanas_gestacion}}@endif">
                </div>

                <div class="col-md-3">
                    <br>
                    <input type="checkbox" name="mov_fetal" value="1" @if($form008->movimiento_fetal=='1') checked @endif id="mov_fetal"> <label>{{trans('paso2.MovimientoFetal')}}</label><br>
                </div>
                <div class="col-md-3">
                    <label >{{trans('paso2.FrecuenciaCFetal')}}</label>
                    <input type="text" name="frecuencia_fetal" id="frecuencia_fetal" class="form-control input-sm" value="@if($paciente->sexo=='1'){{'NO APLICA'}}@else{{$form008->frecuencia_fetal}}@endif">
                </div>
                <div class="col-md-3">
                    <input type="checkbox" name="membranas_rotas" value="1" @if($form008->membranas_rotas=='1') checked @endif id="membranas_rotas"> <label>{{trans('paso2.MembranasRotas')}}</label><br>
                </div>
                <div class="col-md-3">
                    <label >{{trans('paso2.Tiempo')}}</label>
                    <input type="text" name="tiempo" id="tiempo" class="form-control input-sm" value="@if($paciente->sexo=='1'){{'NO APLICA'}}@else{{$form008->tiempo_ruptura}}@endif" >
                </div>
                <div class="col-md-3">
                    <label >{{trans('paso2.AlturaUterina')}}</label>
                    <input type="text" name="altura_uterina" id="altura_uterina" class="form-control input-sm" value="@if($paciente->sexo=='1'){{'NO APLICA'}}@else{{$form008->altura_uterina}}@endif">
                </div>
                <div class="col-md-3">
                    <label >{{trans('paso2.Presentacion')}}</label>
                    <input type="text" name="presentacion" id="presentacion" class="form-control input-sm" value="@if($paciente->sexo=='1'){{'NO APLICA'}}@else{{$form008->presentacion}}@endif">
                </div>
                <div class="col-md-3">
                    <label >{{trans('paso2.Dilatacion')}}</label>
                    <input type="text" name="dilatacion" id="dilatacion" class="form-control input-sm" value="@if($paciente->sexo=='1'){{'NO APLICA'}}@else{{$form008->dilatacion}}@endif">
                </div>
                <div class="col-md-3">
                    <label >{{trans('paso2.Borramiento')}}</label>
                    <input type="text" name="borramiento" id="borramiento" class="form-control input-sm" value="@if($paciente->sexo=='1'){{'NO APLICA'}}@else{{$form008->borramiento}}@endif">
                </div>
                <div class="col-md-3">
                    <label >{{trans('paso2.Plano')}}</label>
                    <input type="text" name="plano" id="plano" class="form-control input-sm" value="@if($paciente->sexo=='1'){{'NO APLICA'}}@else{{$form008->plano}}@endif">
                </div>
                <div class="col-md-3">
                    <br>
                    <input type="checkbox" name="pelvis_util" value="1" @if($form008->pelvis_util=='1') checked @endif id="pelvis_util"> <label>{{ trans('paso2.Pelvisutil') }}</label><br>
                </div>
                <div class="col-md-3">
                    <br>
                  
                    <input type="checkbox" name="sangrado_vaginal" value="1" @if($form008->sangrado_vaginal=='1') checked @endif id="sangrado_vaginal"> <label>{{ trans('Sangrado Vaginal') }}</label><br>
                </div>
                <div class="col-md-3">
                    <label >{{trans('paso2.Contracciones')}}</label>
                    <input type="text" name="contracciones" id="contracciones" class="form-control input-sm" value="@if($paciente->sexo=='1'){{'NO APLICA'}}@else{{$form008->contracciones}}@endif">
                </div>
                <div class="col-md-3">
                    <label >{{trans('pasos.ScoreMama')}}</label>
                    <input type="text" name="score_mama" id="score_mama" class="form-control input-sm" value="@if($paciente->sexo=='1'){{'NO APLICA'}}@else{{$form008->score_mama}}@endif">
                </div>
                <div class="col-md-12" style="text-align: center; margin-top: 10px;">
                    <button class="btn btn-primary" onclick="guardar_paso9();" type="button"> <i class="fa fa-save"></i> </button>
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
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
<script src="{{asset('ho/app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
<script type="text/javascript">
    function guardar_paso9() {

        $.ajax({
            type: "post",
            url: "{{route('hospital.novenopaso_store')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form_paso9").serialize(),
            
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