<div class="modal-header">
    <div class="col-md-10"><h3>Actualiza Valor Particular</h3></div>
    <div class="col-md-2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
    </div>
</div>
<div class="modal-body">   
  <div class="box-body">

      @php
          $product = Sis_medico\Ct_productos::where('id',$id_producto)
                                             ->where('estado_tabla','1') 
                                             ->first()
      @endphp
       
    <form class="form-horizontal" id="form">
      {{ csrf_field() }}
      <input  name="id_prod" id="id_prod" type="text" class="hidden" value="@if(!is_null($id_producto)){{$id_producto}}@endif">
      <div class="row">
        <div class="form-group col-md-6">
          <label for="id_seguro" class="col-md-4 control-label">Seguro:</label>
          <div class="col-md-8">
            PARTICULAR
          </div>
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-6">
            <label for="id_producto" class="col-md-4 control-label">Producto:</label>
            <div class="col-md-8">
              {{$product->nombre}} 
            </div>
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-6">
            <label for="precio" class="col-md-4 control-label">Precio:</label>
            <div class="col-md-8">
              <input id="precio" type="text" class="form-control" name="precio" value="{{$product->valor_total_paq}}" onkeypress="return isNumberKey(event)" onblur="checkformat(this);" required autofocus> 
            </div>
        </div>
      </div> 
    </form>
    <br><br>
    <div style="padding-top: 10px;padding-left: 70px" class="form-group col-md-12">
      <center>
				<div class="col-md-6 col-md-offset-2">
					<div class="col-md-7">
						<button type="button" class="btn btn-primary" onclick="update_valor()"><span class="glyphicon glyphicon-floppy-disk"> Guardar</span> </button>
					</div>
				</div>
      </center>
		</div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
  
    
    function isNumberKey(evt)
    {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

     return true;
    }

    function checkformat(entry) { 
        
        var test = entry.value;

        if (!isNaN(test)) {
            entry.value=parseFloat(entry.value).toFixed(2);
        }

        if (isNaN(entry.value) == true){      
            entry.value='0.00';        
        }
        if (test < 0) {

            entry.value = '0.00';
        }
    
    }
  
    //Guarda Nuevo Valor de Total Paquete Particular
    function update_valor(){


      $.ajax({
        type: 'post',
        url:"{{ route('valor_total_paquete.guardar') }}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#form").serialize(),
        success: function(data){
            console.log(data);
        
            if(data =='ok'){
                
                swal({
                    title: "Datos Guardados",
                    icon: "success",
                    type: 'success',
                    buttons: true,
                })

                $("#val_tot_paquete").modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            }

        },
        error: function(data){
          console.log(data);
        }
       });

    }
 
  

  //Guarda Producto Tarifario
  /*function guardar(){


    var formulario = document.forms["form"];

    var seguro_paciente = formulario.id_seguro.value;
    var val_precio = formulario.precio.value;

    var msj = "";

    if(seguro_paciente == ""){
      msj += "Por favor,Seleccione el Seguro<br/>";
    }

    if(val_precio == 0){
      msj += "Por favor, Ingrese un precio<br/>";
    }

    if(msj != ""){
            
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
            return false;
    }
    
    $.ajax({
      type: 'post',
      url:"{{ route('tarifario_paquete.guardar') }}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: $("#form").serialize(),
      success: function(data){
        //console.log(data);
        
            if(data.msj =='ok'){
                
                swal({
                    title: "Ya existe creado un Tarifario con el mismo Producto, Seguro, Nivel",
                    icon: "error",
                    type: 'error',
                    buttons: true,
                })

            }else{

                swal({
                    title: "Datos Guardados",
                    icon: "success",
                    type: 'success',
                    buttons: true,
                })

                carga_tabla_producto_tarifario();

                $("#producto_tarifario").modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                $("#tarifario_paquete").removeData('modal');
              

            }

      },
      error: function(data){
        console.log(data);
      }
    });

  }*/



</script>


