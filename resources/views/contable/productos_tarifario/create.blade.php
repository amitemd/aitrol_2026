@extends('contable.productos_tarifario.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
  <div class="box">
    <div class="box-header">
        
    </div>
      <div class="box-body">
        <form class="form-horizontal" id="form">
            {{ csrf_field() }}
        <div class="row">
            
            <div class="form-group col-md-6">
                <label for="id_seguro" class="col-md-4 control-label">Seguro:</label>
                <div class="col-md-8">
                    <select id="id_seguro" class="form-control input-sm" name="id_seguro" required onchange="cargar_nivel();">
                      <option value="">Seleccione...</option>
                      @foreach($seguros as $value)
                      <option value="{{$value->id}}">{{$value->nombre}}</option>
                      @endforeach
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
                <div id="div_nivel" style="margin-bottom: 0px;" class="form-group col-md-6 {{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="id_producto" class="col-md-4 control-label">Producto:</label>
                <div class="col-md-8">
                    <select id="id_producto" class="form-control input-sm select2_productos" name="id_producto" required onchange="cargar_precio();">
                    <option value=""> Seleccione...</option>
                    @foreach($productos as $producto)
                      <option value="{{$producto->id}}">{{$producto->nombre}}</option>
                    @endforeach
                    </select>
                </div>
            </div>
        </div> 

        <div class="row">
                <div id="div_precio" style="margin-bottom: 0px;" class="form-group col-md-6 {{ $errors->has('id_producto') ? ' has-error' : '' }}">
                </div>
        </div>

            
        </div>
        </form>
        <div class="row">
            <div class="form-group col-md-2 ">
                <div class="col-md-7">
                    <button type="button" class="btn btn-primary" onclick="guardar()"><span class="glyphicon glyphicon-floppy-disk"> Guardar</span> </button>
                </div>
            </div>
        </div>
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
    $('#fecha').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD',            
    });
    $('.select2_productos').select2({
        tags: false
    });
    function guardar(){
        //alert("ingreso");
        $.ajax({
          type: 'post',
          url:"{{ route('productos.guardar') }}",
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
                    location.href ="{{route('productos.productos_tarifario')}}";
                });
            };
          },
          error: function(data){
             console.log(data);
             //swal("Complete todos los campos");
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
</script>

@endsection