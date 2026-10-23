<div class="modal-header">
  
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
 
  <h4>Dar de Baja a Producto</h4> 
  

</div>
<div class="modal-body">
  <div class="row">
    <div class="col-md-6"><b> Empresa:  {{$empresa->razonsocial}}  </b></div>
    <div class="col-md-6"><b> Codigo de Producto:  {{$producto->codigo}}  </b></div>
    <div class="col-md-6"><b> Nombre del Producto:  {{$producto->nombre}}  </b></div>
    <div class="col-md-6"><b> Bodega: {{$bodega->nombre}} </b></div>
    <div class="col-md-6"><b> Serie: {{$serie}}</b></div>
    <div class="col-md-12">
      <form class="form-vertical" role="form" method="POST" action="{{ route('producto_dar_baja_guardar') }}">
          {{ csrf_field() }}
          <div class="box-body col-md-12">
              <div class="form-group col-md-9">
                  <label for="codigo" class="col-md-12 control-label" >Ingrese El Motivo de la baja</label>
                  <div class="col-md-12">
                      <input id="observacion" type="text" class="form-control" name="observacion" value="" style="text-transform:uppercase;"  maxlength="100"  required autofocus >
                  </div>
                  
              </div>
              <div class="form-group col-md-3">
                  <label for="codigo" class="col-md-12 control-label" >Cantidad</label>
                  <div class="col-md-12">
                      <input id="cantidad" type="number" class="form-control" name="cantidad" value="" style="text-transform:uppercase;"  required autofocus >
                  </div>
                  
              </div>
              <div class="col-md-12">
                  <button class="btn btn-danger" type="button" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;" onclick="dardebaja()"> 
                     Dar de Baja
                  </button>
              </div>
             
          </div>
      </form>
    </div>
  </div>    
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>	 


<script type="text/javascript">

    function dardebaja(id){

        //alert(id);
        
        var serie       = '{{$serie}}';
        var id_bodega   = '{{$id_bodega}}';
        var cantidad    = $('#cantidad').val();
        var observacion = $('#observacion').val();

        var mensaje = '';
        if(serie == ''){
          var mensaje = 'Ingrese la serie \n';
        }
        if(id_bodega == ''){
          var mensaje = mensaje + 'Ingrese la bodega \n';
        }
        if(cantidad == ''){
          var mensaje = mensaje + 'Ingrese la cantidad \n';
        }
        if(observacion == ''){
          var mensaje = 'Ingrese la observacion \n';
        }

        if(mensaje != ''){
          Swal.fire({
              icon: 'error',
              title: 'Error',
              html: mensaje
          })  
        }else{
          var confirmar = confirm("Esta seguro, desea dar de Baja al producto");
          if(confirmar){
            $.ajax({
              type: 'post',
              url:"{{ route('insumos.producto.vt_dar_baja_producto')}}",
              headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
              datatype: 'json',
              data: {
                  "serie":serie,
                  "id_bodega":id_bodega,
                  "cantidad_baja":cantidad,
                  "observacion":observacion,
                  
              },
              success: function(data){
                  console.log(data);
                  $( "#boton_buscar" ).click();
                  if(data.estado == "Ok"){
                      Swal.fire({
                          icon: 'success',
                          title: 'Listo',
                          html: 'Dado de baja'
                      })
                      location.reload();
                      
                  }
                  else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: data.mensaje
                    })  
                  }
              },
              error: function(data){
                  Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: data.mensaje
                    }) 
              }
            });
          }  

        }

       
    }
</script>
   
