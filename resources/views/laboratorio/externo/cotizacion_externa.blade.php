<style type="text/css">
  .alerta_correcto{
    position: fixed;
    z-index: 9999;
    bottom:  58%;
    left: 41%;
    
  }
  .alerta_ok{
    position: fixed;
    z-index: 9999;
    bottom:  58%;
    left: 41%;
    
  }
  .xcabecera{
    padding: 10px;
    color: #fff;
    border-bottom: 1px solid rgb(145, 121, 26);
    background: rgb(239,172,21);
    background: -moz-linear-gradient(top,  rgba(239,172,21,1) 0%, rgba(235,165,18,1) 31%, rgba(237,162,19,1) 32%, rgba(235,160,17,1) 39%, rgba(235,154,19,1) 51%, rgba(232,150,15,1) 58%, rgba(234,145,19,1) 64%, rgba(234,142,15,1) 66%, rgba(231,134,17,1) 78%, rgba(233,132,14,1) 81%, rgba(230,128,17,1) 87%, rgba(229,123,13,1) 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(239,172,21,1)), color-stop(31%,rgba(235,165,18,1)), color-stop(32%,rgba(237,162,19,1)), color-stop(39%,rgba(235,160,17,1)), color-stop(51%,rgba(235,154,19,1)), color-stop(58%,rgba(232,150,15,1)), color-stop(64%,rgba(234,145,19,1)), color-stop(66%,rgba(234,142,15,1)), color-stop(78%,rgba(231,134,17,1)), color-stop(81%,rgba(233,132,14,1)), color-stop(87%,rgba(230,128,17,1)), color-stop(100%,rgba(229,123,13,1)));
    background: -webkit-linear-gradient(top,  rgba(239,172,21,1) 0%,rgba(235,165,18,1) 31%,rgba(237,162,19,1) 32%,rgba(235,160,17,1) 39%,rgba(235,154,19,1) 51%,rgba(232,150,15,1) 58%,rgba(234,145,19,1) 64%,rgba(234,142,15,1) 66%,rgba(231,134,17,1) 78%,rgba(233,132,14,1) 81%,rgba(230,128,17,1) 87%,rgba(229,123,13,1) 100%);
    background: -o-linear-gradient(top,  rgba(239,172,21,1) 0%,rgba(235,165,18,1) 31%,rgba(237,162,19,1) 32%,rgba(235,160,17,1) 39%,rgba(235,154,19,1) 51%,rgba(232,150,15,1) 58%,rgba(234,145,19,1) 64%,rgba(234,142,15,1) 66%,rgba(231,134,17,1) 78%,rgba(233,132,14,1) 81%,rgba(230,128,17,1) 87%,rgba(229,123,13,1) 100%);
    background: -ms-linear-gradient(top,  rgba(239,172,21,1) 0%,rgba(235,165,18,1) 31%,rgba(237,162,19,1) 32%,rgba(235,160,17,1) 39%,rgba(235,154,19,1) 51%,rgba(232,150,15,1) 58%,rgba(234,145,19,1) 64%,rgba(234,142,15,1) 66%,rgba(231,134,17,1) 78%,rgba(233,132,14,1) 81%,rgba(230,128,17,1) 87%,rgba(229,123,13,1) 100%);
    background: linear-gradient(to bottom,  rgba(239,172,21,1) 0%,rgba(235,165,18,1) 31%,rgba(237,162,19,1) 32%,rgba(235,160,17,1) 39%,rgba(235,154,19,1) 51%,rgba(232,150,15,1) 58%,rgba(234,145,19,1) 64%,rgba(234,142,15,1) 66%,rgba(231,134,17,1) 78%,rgba(233,132,14,1) 81%,rgba(230,128,17,1) 87%,rgba(229,123,13,1) 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#efac15', endColorstr='#e57b0d',GradientType=0 );  
  }
</style>

<div class="alert alert-danger alerta_correcto alert-dismissable col-6" role="alert" style="display: none;font-size: 14px;">
    <b>Complete los datos: <span id="err"></span></b>
</div>
<div class="alert alert-success alerta_ok alert-dismissable col-6" role="alert" style="display: none;font-size: 14px;">
    <b>Se realizó pago con éxito!!</b>
</div>
<div class="modal-header"> 
  <div class="row">
    <div class="col-md-3 col-sm-12"><b>Paciente</b></div>
    <div class="col-md-9 col-sm-12"><b>{{$orden->id_paciente}} - {{$orden->paciente->nombre1}} @if($orden->pnombre2=='N/A'||$orden->paciente->nombre2=='(N/A)') @else{{ $orden->paciente->nombre2 }} @endif {{$orden->paciente->apellido1}} @if($orden->paciente->apellido2=='N/A'||$orden->paciente->apellido2=='(N/A)') @else{{ $orden->paciente->apellido2 }} @endif
    </b></div>
    <div class="col-md-3 col-sm-6"><b>Orden No.</b></div>
    <div class="col-md-3 col-sm-6"><b>{{$orden->id}}</b></div>
    <div class="col-md-3 col-sm-6"><b>Edad</b></div>
    <div class="col-md-2 col-sm-5"><b>{{$age}} años</b></div>
    <div class="col-md-1 col-sm-1">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button> 
    </div>  
  </div>
</div>
<div class="modal-body" style="font-size: 13px;height: {{$detalles->count()*100}}px">
  <div class="col-md-1 col-sm-2" style="font-weight: bold" >No.</div>
  <div class="col-md-7 col-sm-6" style="font-weight: bold" >Nombre</div>
  <div class="col-md-4 col-sm-4" style="font-weight: bold;text-align: right;" >Valor</div>
  @php $xcant=0; @endphp
  @foreach($detalles as $detalle)
    @php
      $xcant++;
    @endphp
    <div class="col-md-1 col-sm-2" >{{$xcant}}</div>
    <div class="col-md-7 col-sm-6" >{{$detalle->examen->nombre}}</div>
    <div class="col-md-4 col-sm-4" style="text-align: right;">{{number_format(round($detalle->valor,2),2)}}</div>
  @endforeach 
  <div class="col-md-12 col-sm-12" style="font-weight: bold" >&nbsp;</div>
  <div class="col-md-1 col-sm-2" style="font-weight: bold" >&nbsp;</div>
  <div class="col-md-7 col-sm-6" style="font-weight: bold" >SubTotal</div>
  <div class="col-md-4 col-sm-4" style="font-weight: bold;text-align: right;" >$ {{number_format(round($orden->valor,2),2)}}</div>
  <div class="col-md-1 col-sm-2" style="font-weight: bold" >&nbsp;</div>
  <div class="col-md-7 col-sm-6" style="font-weight: bold" >Descuento</div>
  <div class="col-md-4 col-sm-4" style="font-weight: bold;text-align: right;" >(-)$ {{number_format($orden->descuento_valor,2)}}</div>
  <div class="col-md-1 col-sm-2" style="font-weight: bold" >&nbsp;</div>
  <div class="col-md-7 col-sm-6" style="font-weight: bold" >Fee Administrativo</div>
  <div class="col-md-4 col-sm-4" style="font-weight: bold;text-align: right;" >$ {{number_format($orden->recargo_valor,2)}}</div>
  <div class="col-md-1 col-sm-2" style="font-weight: bold" >&nbsp;</div>
  <div class="col-md-7 col-sm-6" style="font-weight: bold" >Total</div>
  <div class="col-md-4 col-sm-4" style="font-weight: bold;text-align: right;" >$ {{number_format($orden->total_valor,2)}}</div>
  <div class="col-md-12 col-sm-22" style="font-weight: bold" >
    <form id="guardar_mail" method="POST">
      {{ csrf_field() }}
      <div class="form-group col-md-8">
        <input type="hidden" name="id_orden" value="{{$orden->id}}">
        <label for="email" class="control-label">Ingrese el email</label>
        <input id="email" type="text" class="form-control input-sm" name="email" maxlength="70" value="{{ $email }}" required autofocus onchange="">
      </div>  
    </form>  
  </div>
  
</div>
<div class="modal-footer">
  <button type="button" id="cerrar_modal" class="btn btn-secondary" data-dismiss="modal">Close</button>
  <button type="button" id="bpago" onclick="pagar_orden();" class="btn btn-primary"><i class="glyphicon glyphicon-credit-card"> </i> Pagar</button>
  <div id="imagen_espera2" style="display: none;">
    <img src="{{asset('/images/espera.gif')}}" style="width: 10%;"> En proceso ...
  </div>
</div>

       
      