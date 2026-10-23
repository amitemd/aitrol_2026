
<script type="text/javascript">
	   function validarRango(elemento){
        var numero = parseInt(elemento.value,10);
          //Validamos que se cumpla el rango
        if(numero<1 || numero>{{$cantidad}}){
            alert("La cantidad debe ser menor o igual a "+{{$cantidad}}+" producto(s)");
            $('#cantidad').val("");
            return false;
        }
        return true;
    }
</script>

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;"><b>TOTAL DE PRODUCTOS EN @if($tipo == '1') BODEGA @elseif($tipo == '2') TRANSITO @endif</b></h4>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;"><b>Cantidad Total: {{$cantidad}} </b> </h4>
</div>

<div class="modal-body">
	<div class="row" style="padding: 10px;">
    	<div class="form-group col-xs-12">
        	<form method="POST" action="{{ route('producto.borrar') }}" >
        		{{ csrf_field() }}
        	  <input type="hidden" name="tipo" id="tipo" value="{{$tipo}}">
            <input type="hidden" name="id_producto" id="id_producto" value="{{$id_producto}}">
            <input type="hidden" name="serie" id="serie" value="{{$serie}}">
            <input type="hidden" name="id_bodega" id="id_bodega" value="{{$bodega}}">
            <input type="hidden" name="id_pedido" id="id_pedido" value="{{$pedido}}">
            <input type="hidden" name="f_venci" id="f_venci" value="{{$f_venci}}">
            <input type="hidden" name="lote" id="lote" value="{{$lote}}">

        		<label for="codigo" class="col-md-6 control-label" style="text-align: right;">Ingrese la cantidad a descontar: </label>
	            <div class="col-md-3">
	                <input id="cantidad" type="number" onchange="return validarRango(this);" class="form-control" name="cantidad" placeholder="Ingrese cantidad" maxlength="25"  required autofocus >
	            </div>
	            <label for="codigo" class="col-md-6 control-label" style="text-align: right; margin-top: 10px">Ingrese el motivo: </label>
	            <div class="col-md-3">
	                <input style="margin-top: 10px" id="motivo" type="text"  class="form-control" name="motivo" value="" maxlength="100"  required autofocus >
	            </div>
	            <div class="col-md-12" style="text-align: center; margin: 10px">
	                <button class="btn btn-primary" type="submit" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;"> 
	                   &nbsp;&nbsp;Aceptar
	                </button>
	            </div>
	        </form>
        </div>
    </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>	 


