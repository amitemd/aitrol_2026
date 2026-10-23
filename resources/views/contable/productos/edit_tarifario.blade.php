@extends('contable.productos_tarifario.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
              <h3 class="box-title">Editar</h3>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray" >
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body">
            @php
            
                $seg = Sis_medico\Seguro::where('id',$prod_tari->id_seguro)
                                        ->where('inactivo','1')
                                        ->first();

                $inf_nivel = Sis_medico\Nivel::where('id',$prod_tari->nivel)->where('estado',1)->first();
            
            @endphp
            <form id="form" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id_seguro"  id="id_seguro"  value="{{$prod_tari->id_seguro}}">
                <input type="hidden" name="id_nivel"  id="id_nivel"  value="{{$prod_tari->nivel}}">
                <input type="hidden" name="id_producto"  id="id_producto"  value="{{$prod_tari->id_producto}}">
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
                           {{$prod_tari->nombre}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="precio" class="col-md-4 control-label">Precio:</label>
                        <div class="col-md-8">
                           <input id="precio"  type="text" class="form-control"  name="precio" value="{{$prod_tari->precio_producto}}" onkeypress="return isNumberKey(event)" onblur="checkformat(this);">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-2 ">
                        <div class="col-md-7">
                            <button type="button" class="btn btn-primary" onclick="guardar();"><span class="glyphicon glyphicon-floppy-disk"> Actualizar</span> </button>
                        </div>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
</section>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
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

    function goBack() {
      window.history.back();
    }
    
    $('#fecha').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD',            
    });

    $('.select2_productos').select2({
        tags: true
    });

    function guardar(){
        $.ajax({
          type: 'post',
          url:"{{ route('productos.update',['id_producto' => $id_producto]) }}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#form").serialize(),
          success: function(data){
            console.log(data);
            if(data == "ok"){
                swal({
                    title: "Datos Guardados",
                    icon: "success",
                    type: 'success',
                    buttons: true,
                })
                .then((value) => {
                   location.href ="{{route('productos_servicios_editar', ['id' => $id_producto])}}";
                });
            };
          },
          error: function(data){
             console.log(data);
  
          }
        });

    } 

    function cargar_nivel(){
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
    }

    $(document).ready(function() {
        cargar_nivel();
    })

    function cargar_precio(){
        //console.log('nivel');
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
    }
    $(document).ready(function() {
        cargar_precio();
    })
</script>

@endsection