<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;">MUESTRAS TOMADAS: {{$orden->paciente->apellido1}} {{$orden->paciente->apellido2}} {{$orden->paciente->nombre1}} {{$orden->paciente->nombre2}}</h4>
</div>

<div class="modal-body">
  <div class="row" style="padding: 10px;">
     <div class="col-md-2">
       <b>Cantidad</b>
     </div>
     <div class="col-md-5">
       <b>Fecha Muestra</b>
     </div>
     <div class="col-md-5">
       <b>Usuario</b>
     </div>
     @php $cont = 1; @endphp 
     @foreach($muestras as $muestra)
     <div class="col-md-2">
       <b>{{$cont}}:</b>
     </div>
     <div class="col-md-5">
       {{$muestra->toma_muestra}}
     </div>
     <div class="col-md-5">
       {{$muestra->user_crea->apellido1}} {{$muestra->user_crea->nombre1}}
     </div>
     @php $cont++; @endphp 
     @endforeach
        
  </div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>

