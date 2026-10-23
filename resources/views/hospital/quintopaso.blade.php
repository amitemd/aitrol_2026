<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d");
?>

<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="d-flex align-items-center col-md-12">
                   <span class="sradio">5</span>
                    <h4 class="card-title ml-25 colorbasic">
                        {{trans('pasos.EnfermedadActualyRevisiondeSistemas')}}
                    </h4>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row" style="padding-top: 10px;">
            <div class="col-md-4">
                <label >{{trans('pasos.VíaAéreaLibre')}}</label>
                <input type="checkbox" name="aerea_libre"><br>
            </div>
            
            <div class="col-md-4">
                <label >{trans('pasos.VíaAéreaObstruida')}}</label>
                <input type="checkbox" name="aerea_obstruida"><br>
            </div>
            <div class="col-md-4">
                <label >{trans('pasos.CondiciónEstable')}}</label>
                <input type="checkbox" name="condicion_estable"><br>
            </div>
            <div class="col-md-4">
                <label >{trans('pasos.CondiciónInestable')}}</label>
                <input type="checkbox" name="condicion_inestable"><br>
            </div>
            
            
        </div>
    </div>

</div>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>