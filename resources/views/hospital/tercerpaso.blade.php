<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d");
?>

<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="d-flex align-items-center col-md-12">
                   <span class="sradio">3</span>
                    <h4 class="card-title ml-25 colorbasic">
                       {{trans('pasos.Accidente,Violencia,Intoxicación,EnvenenamientooQuemadura')}}
                    </h4>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row" style="padding-top: 10px;">
            
            <div class="col-md-4">
                <label>{{trans('pasos.FechayHoradelEvento')}}</label>
                <div class="input-group date">
                    <input type="text"  data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha" id="fecha" value="" autocomplete="off">
                    <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle"></i>
                    </div>   
                </div>
            </div>
            <div class="col-md-4">
                <label >{{trans('pasos.LugardelEvento')}}</label>
                <input type="text" name="lugar_evento" class="form-control input-sm">
            </div>
            <div class="col-md-4">
                <label >{{trans('pasos.DireccióndelEvento')}}</label>
                <input type="text" name="direccion_evento" class="form-control input-sm">
            </div>
            <div class="col-md-12">
                &nbsp;
            </div>

            <div class="col-md-4">
                <label >{{trans('pasos.CustodiaPolicial')}}</label>
                <input type="checkbox" name="custodia">
            </div>

            <div class="col-md-8">
                &nbsp;
            </div>

            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.AccidentedeTransito')}}</label>
                <input type="checkbox" name="accidente_transito" value="1"><br>
            </div>
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.Caída')}}</label>
                <input type="checkbox" name="caida" value="1"><br>
            </div>
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.Quemadura')}}</label>
                <input type="checkbox" name="quemadura" value="1"><br>
            </div>
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.Mordedura')}}</label>
                <input type="checkbox" name="mordedura" value="1"><br>
            </div>
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.Ahogamiento')}}</label>
                <input type="checkbox" name="ahogamiento" value="1"><br>
            </div>
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.ViolemciaXArmadeFuego')}}</label>
                <input type="checkbox" name="arma_fuego" value="1"><br>
            </div>    
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.ViolemciaXArmaC.Punzante')}}</label>
                <input type="checkbox" name="arma_punzante" value="1"><br>
            </div>
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.ViolenciaXRiña')}}</label>
                <input type="checkbox" name="riña" value="1"><br>
            </div>
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.ViolenciaFamiliar')}}</label>
                <input type="checkbox" name="violencia_familiar" value="1"><br>
            </div>
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.AbusoFísico')}}</label>
                <input type="checkbox" name="abuso_fisico" value="1"><br>
            </div>    
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.AbusoPsicológico')}}</label>
                <input type="checkbox" name="abuso_psicologico" value="1"><br>
            </div>
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.AbusoSexual')}}</label>
                <input type="checkbox" name="abuso_sexual" value="1"><br>
            </div>
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.IntoxicacionAlcohólica')}}</label>
                <input type="checkbox" name="intoxicacion_alcoholica" value="1"><br>
            </div>
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.IntoxicacionAlimentaria')}}</label>
                <input type="checkbox" name="intoxicacion_alimentaria" value="1"><br>
            </div>   
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.IntoxicacionxDrogas')}}</label>
                <input type="checkbox" name="intoxicacion_drogas" value="1"><br>
            </div>
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.IntoxicaciondeGases')}}</label>
                <input type="checkbox" name="intoxicacion_gases" value="1"><br>
            </div>
           
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.Envenenamiento')}}</label>
                <input type="checkbox" name="envenenamiento" value="1"><br>
            </div>    
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.Picadura')}}</label>
                <input type="checkbox" name="picadura" value="1"><br>
            </div>
            <div class="col-md-4" style="padding: 0px;">
                <label >{{trans('pasos.Anafilaxia')}}</label>
                <input type="checkbox" name="anafilaxia" value="1"><br>
            </div>
            <div class="col-md-12">
                <label>{{trans('pasos.Observaciones')}}</label>
                <textarea cols="3" name="observacion" id="observacion" class="form-control"></textarea>
            </div>
             <div class="col-md-12">
                &nbsp;
            </div>  
            <div class="col-md-4">
                <label>{{trans('pasos.AlientoEtílico')}}</label>
                <input type="checkbox" name="aliento_alcohol" value="1">
            </div>  
            <div class="col-md-4">
                <label>{{trans('pasos.ValorAlcocheck')}}</label>
                <input type="text" name="valor_alcohol" class="form-control input-sm">
            </div> 

        </div>
    </div>

</div>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>