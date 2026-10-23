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

<div class="modal-header" style="color: white; padding-top: 5px; padding-bottom: 1px; background-image: linear-gradient(to right, #004AC1,#004AC1,#004AC1);">
     <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
    <h4 class="modal-title">DESCUENTOS</h4>
  </div>
  <div class="modal-body">
    <form   enctype="multipart/form-data" method="post" action="{{route('hospital_admin.darbaja')}}">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="tipo" id="tipo" value="{{$tipo}}">
            <input type="hidden" name="id_producto" id="id_producto" value="{{$id_producto}}">
            <input type="hidden" name="serie" id="serie" value="{{$serie}}">
            <input type="hidden" name="id_bodega" id="id_bodega" value="{{$bodega}}">
            <input type="hidden" name="id_pedido" id="id_pedido" value="{{$pedido}}">
            <input type="hidden" name="f_venci" id="f_venci" value="{{$f_venci}}">
            <input type="hidden" name="lote" id="lote" value="{{$lote}}">
        
          <div class="col-md-12">
                    <div class="row">
                      <div for="codigo" class="col-md-4" style="padding-top: 20px">INGRESE LA CANTIDAD A DESCONTAR</div>
                      <div class="col-md-8"><input onchange="return validarRango(this);" id="cant_baja" type="number" name="cant_baja" required maxlength="200" style=" border: 1px solid #BFC9CA; margin-top: 20px;"></div>

                    </div>
          </div>
                    <div class="col-md-12">
                    <div class="row">
                      <div for="codigo" class="col-md-4" style="padding-top: 20px">INGRESE EL MOTIVO</div>
                      <div  class="col-md-8"><input id="motivo" type="text" name="motivo" required maxlength="200" style=" border: 1px solid #BFC9CA; margin-top: 20px;"></div>

                    </div>
          </div>
                    
        <div class="col-md-12" style="text-align: center; margin-top: 20px;">
          <button type="submit" class="btn btn-primary active" >AGREGAR
          </button>
      </div>
    </form>
  </div>
  <div class="modal-footer"> 
  </div>
</div>
