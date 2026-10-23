@extends('contable.compras_pedidos.base')
@section('action-content')
<style type="text/css">
    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 460px;
        _width: 460px !important;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Compra - Interna</a></li>
            <li class="breadcrumb-item"><a href=""></a>Crear Producto Saldo Inicial</li>
            <li class="breadcrumb-item active" aria-current="page">Nueva Saldo Inicial</li>
        </ol>
    </nav>

    <div>
      
    <form id="formulario" class="form-vertical">
      {{ csrf_field() }}
      <div class="box">
        <div class="box-header color_cab">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-9">
                <h5><b>Saldo Iniciales de Productos</b></h5>
              </div>
              <div class="col-md-1 text-right">
                  <a href="{{route('contable.compraspedido.indexInicial')}}" class="btn btn-default btn-gray" >
                      <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                  </a>
              </div>
            </div> 
          </div>          
        </div>
        <div class="separator"></div>
        <div class="box-body dobra">
          <!--NOMBRE DE LA BODEGA-->
          <div class="form-group  col-xs-6">
              <label for="producto" class="col-md-4 texto">Producto:</label>
              <div class="col-md-7">
                  <select id="producto" name="id_producto" class="form-control select2_cuentas" style="width:100%">
                        <option> </option>
                        @foreach($productos as $value)
                              <option value="{{$value->id}}" >{{$value->codigo}} | {{$value->nombre}}</option>
                        @endforeach

                  </select>
              </div>
          </div>
          <div class="form-group col-md-6">
              <label for="costo" class="col-md-4 texto"> Costo del produco</label>
              <div class="col-md-7">
                  <input onblur="this.value=parseFloat(this.value).toFixed(2);" id="costo" onKeypress="if (event.keyCode < 45 || event.keyCode > 57 || event.key == '-') event.returnValue = false;" name="costo" class="form-control" type="text" placeholder="Costo..">
              </div>
          </div>
          <div class="form-group col-md-6">
              <label for="venta" class="col-md-4 texto">Costo de venta</label>
              <div class="col-md-7">
                  <input onblur="this.value=parseFloat(this.value).toFixed(2);" id="venta" type="text" class="form-control" name="costo_venta" onKeypress="if (event.keyCode < 45 || event.keyCode > 57 || event.key == '-') event.returnValue = false;">
              </div>
          </div>
          <div class=" form-group col-md-6">
          <label for="venta" class="col-md-4 texto">Año</label>
              <div class="col-md-7">
                 <input id="fecha" name="fecha" type="text" class="form-control" autocomplete="off" >
              </div>
          </div> 

          <div class=" form-group col-md-6">
              <label for="venta" class="col-md-4 texto">Stock</label>
              <div class="col-md-7">
                 <input id="stock" name="stock" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57 || event.key == '-') event.returnValue = false;">
              </div>
          </div> 

          <div class="form-group  col-xs-6">
              <label for="bodegas" class="col-md-4 texto">Bodega:</label>
              <div class="col-md-7">
                  <select id="bodegas" name="bodega" class="form-control select2_bodega" style="width:100%">
                        <option> </option>
                        @foreach($bodegas as $value)
                              <option value="{{$value->id}}" >{{$value->nombre}}</option>
                        @endforeach
                  </select>
              </div>
          </div>
         
          <div class="form-group col-xs-10 text-center">
            <div class="col-md-6 col-md-offset-4">
                <button type="button" id="btn_add" class="btn btn-default btn-gray">
                  <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                </button>
            </div>
          </div>
        </div>  
      </div>
    </form>
      
    </div>


</section>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script>
      $('.select2_cuentas').select2({
          tags: false
      });

      $('.select2_bodega').select2({
          tags: false
      });
      
    $(function () {

      $('#fecha').datetimepicker({
          format: 'YYYY'
      });

    });
   
</script>

<script>
  document.getElementById('btn_add').addEventListener("click", validar)

  function validar(){
       // alert("Hola")
        let producto = document.getElementById('producto').value;
        let costo = document.getElementById('costo').value;
        let venta = document.getElementById('venta').value;
        let fecha = document.getElementById('fecha').value;

        if(producto == '' ){
          alertas("Error!", `Debe Seleccionar un Producto`, "error");
        }else if(costo == ''){
          alertas("Error!", `Costo del producto vacio`, "error");
        }else if(venta == ''){
          alertas("Error!", `Costo de venta del producto vacio`, "error");
        }else if(fecha == ''){
          alertas("Error!", `Fecha Vacio`, "error");
        }else{
          $.ajax({
                type: "post",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                url: "{{route('contable.storeProdInicial')}}",
                data: $("#formulario").serialize(),
                success: function(data){
                  console.log("hola");
                    console.log(data);
                    if(data.respuesta == 'existe'){
                      alertas('Error...', data.msj, 'error');
                    }else if(data.respuesta == 'si'){
                      alertas('Exito...', data.msj, 'success');
                    }else{
                      alertas('Error...', data.msj, 'error');
                    }
                },error:  function(){
                  alert('error al cargar');
                }
            });     
          }
        

  }



  function alertas(title,text,icon){
    Swal.fire({
      icon: `${icon}`,
      title: `${title}`,
      text: `${text}`,
    })
  }

</script>
@endsection