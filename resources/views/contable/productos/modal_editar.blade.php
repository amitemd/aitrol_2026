<style type="text/css">
    .modal-body .form-group {
        margin-bottom: 0px;
    }
</style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
      <h4 class="modal-title" id="myModalLabel" style="text-align: center;">EDITAR PRODUCTO TARIFARIO PAQUETE</h4>
    </div>
    <div class="modal-body">  
        @php
         $seg = Sis_medico\Seguro::where('id',$prod_tari_paq->id_seguro)->where('inactivo','1')->first();
         $prod = Sis_medico\Ct_productos::where('id',$prod_tari_paq->id_paquete)->where('estado_tabla','1')->first();
         $inf_nivel = Sis_medico\Nivel::where('id',$prod_tari_paq->id_nivel)->where('estado',1)->first();
        @endphp
        <div class="row" style="padding-left: 60px"  >
            <form id="form" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id_seguro"  id="id_seguro"  value="{{$prod_tari_paq->id_seguro}}">
                <input type="hidden" name="id_nivel"  id="id_nivel"  value="{{$prod_tari_paq->id_nivel}}">
                <input type="hidden" name="id_producto"  id="id_producto"  value="{{$prod_tari_paq->id_producto}}">
                <input type="hidden" name="id_tar_paq"  id="id_tar_paq"  value="{{$id_prod_tar_paq}}">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="id_seguro" class="col-md-4 control-label">Seguro:</label>
                        <div class="col-md-8">
                            {{$seg->nombre}} 
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="id_nivel" class="col-md-4 control-label">Nivel:</label>
                        <div class="col-md-8">
                          @if(!is_null($inf_nivel)){{$inf_nivel->nombre}}@endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="id_producto" class="col-md-4 control-label">Producto:</label>
                        <div class="col-md-8">
                            {{$prod->nombre}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="precio" class="col-md-4 control-label">Precio:</label>
                        <div class="col-md-8">
                            <input id="precio"  type="text" class="form-control"  name="precio" value="{{$prod_tari_paq->precio}}" onkeypress="return isNumberKey(event)" onblur="checkformat(this);">
                        </div>
                    </div>
                </div>
                <br>
            </form>
            <div style="padding-top: 10px;padding-left: 70px" class="form-group col-md-12">
                <center>    
                    <div class="col-md-6 col-md-offset-2">
                        <div class="col-md-7">
                            <button type="button" class="btn btn-primary" onclick="guardar();"><span class="glyphicon glyphicon-floppy-disk"> Actualizar</span> </button>
                        </div>
                    </div>
                </center>
            </div>
            <br><br>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
            </div>
        </div>
    </div>
 
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">

    //Carga la Tabla de Producto Tarifario 
    /*function carga_tabla_producto_tarifario()
    {
            
        var id_prod = $("#id_producto").val();

        $.ajax({
            type:"GET",
            url:"{{route('recarga_prod_tarifario.index')}}/"+id_prod,
            data: "",
            datatype: "html",
            success:function(data){
                    $('#recarga_prod_tarif').html(data);
            },
            error:function(){
                alert('error al cargar');
            }
        });

    }*/
    
    
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


    function guardar(){
        $.ajax({
          type: 'post',
          url:"{{ route('producto_tarifario_paquete.update',['id_prod_tar_paq' => $id_prod_tar_paq]) }}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#form").serialize(),
          success: function(data){
            //console.log(data);
            if(data == "ok"){
                swal({
                    title: "Datos Guardados",
                    icon: "success",
                    type: 'success',
                    buttons: true,
                })

                carga_tabla_producto_tarifario();

                $("#edit_prod_tar_paq").modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                $("#tarifario_paquete").removeData('modal');
              
            };

          },
          error: function(data){
             console.log(data);
  
          }
        });

    } 

    /*function goBack() {
      window.history.back();
    }*/
    
    /*function cargar_nivel(){
        //console.log('nivel');
        var xseguro = $('#id_seguro').val();
        var js_seguro = document.getElementById('id_seguro').value;
        if(js_seguro =='1'){
            $('#div_nivel').addClass('oculto');
        }else{
            $('#div_nivel').removeClass('oculto');
        }
        
        $.ajax({
            type: 'post',
            url:"{{route('productos.nivel')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form").serialize(),
            success: function(data){
                if(data!='no'){
                    $('#div_nivel').empty().html(data);
                    $('#div_nivel').removeClass('oculto');
                }else{
                    $('#div_nivel').addClass('oculto');
                    $('#div_nivel').empty().html('');    
                }
                                                                                     
            },
            error: function(data){
                    
                }
        });
    }*/

    /*$(document).ready(function() {
        cargar_nivel();
    })*/

    /*function cargar_precio(){

        var xseguro = $('#id_seguro').val();
        var js_seguro = document.getElementById('id_producto').value;
        if(js_seguro =='1'){
            $('#div_precio').addClass('oculto');
        }else{
            $('#div_precio').removeClass('oculto');
        }
        
        $.ajax({
            type: 'post',
            url:"{{route('productos.precios')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form").serialize(),
            success: function(data){
                if(data!='no'){
                    $('#div_precio').empty().html(data);
                    $('#div_precio').removeClass('oculto');
                }else{
                    $('#div_precio').addClass('oculto');
                    $('#div_precio').empty().html('');    
                }
                                                                                     
            },
            error: function(data){
                    
                }
        });
    }*/
    /*$(document).ready(function() {
        cargar_precio();
    })*/
</script>