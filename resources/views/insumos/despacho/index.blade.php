

@extends('insumos.ingreso.base')
@section('action-content')
<style type="text/css">
  .ui-corner-all
        {
            -moz-border-radius: 4px 4px 4px 4px;
        }
       
        .ui-widget
        {
            font-family: Verdana,Arial,sans-serif;
            font-size: 15px;
        }
        .ui-menu
        {
            display: block;
            float: left;
            list-style: none outside none;
            margin: 0;
            padding: 2px;
        }
        .ui-autocomplete
        {
             overflow-x: hidden;
              max-height: 200px;
              width:1px;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            float: left;
            display: none;
            min-width: 160px;
            _width: 160px;
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
        .ui-menu .ui-menu-item
        {
            clear: left;
            float: left;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .ui-menu .ui-menu-item a
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            cursor: pointer;
            background-color: #ffffff;
        }
        .ui-menu .ui-menu-item a:hover
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            color: White;
            cursor: pointer;
            background-color: #006699;
        }
        .ui-widget-content a
        {
            color: #222222;
        }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

    <!-- Main content -->
    <section class="content">
      <div class="box">
        <div class="box-header">
          <div class="row">
              <div class="col-sm-8">
                <h3 class="box-title">Entrega / Despacho de Productos</h3>
              </div>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
              <div class="col-sm-6"></div>
              <div class="col-sm-6"></div>
            </div>
            <form method="POST" id="ingreso">
               <div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title">Ingreso de producto</h3>
                  
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  </div>  
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                          <label for="inputid" class="col-sm-3 control-label">Codigo</label>
                          <div class="col-sm-9">
                            <input value="" type="text" class="form-control" name="codigo" id="codigo" placeholder="Codigo"  style="text-transform:uppercase;">
                          </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                          <label for="inputapellido" class="col-sm-3 control-label">Nombre</label>
                          <div class="col-sm-9">
                            <input value="" type="text" class="form-control"  id="nombre" name="nombre" id="inputapellido" placeholder="Nombre" style="text-transform:uppercase;">
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                  <button type="button" id="busqueda" class="btn btn-primary">
                    Agregar
                  </button>
                </div>
              </div>
            </form>

          <form method="POST"  name="frm" id="frm">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">

              <div class="box-body">
                <div class="row">
                    <!-- Numero de Pedido -->
                    <div class="form-group col-xs-6{{ $errors->has('pedido') ? ' has-error' : '' }}">
                        <label for="pedido" class="col-md-4 control-label">Numero de pedido</label>
                        <div class="col-md-8">
                          <input id="pedido" type="text" class="form-control" name="pedido" value="{{ old('pedido') }}" onkeyup="valida()" required autofocus>
                        </div>
                    </div>
                    <!-- Vencimiento -->
                    <div class="form-group col-xs-6{{ $errors->has('vencimiento') ? ' has-error' : '' }}">
                        <label for="vencimiento" class="col-md-4 control-label">Fecha de Vencimiento</label>
                        <div class="col-md-7">
                          <div class="input-group date">
                            <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" value="" name="vencimiento" class="form-control" id="vencimiento"  placeholder="AAAA/MM/DD">
                          </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="form-group  col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                        <label for="observaciones" class="col-sm-3 control-label">Observaciones</label>
                        <div class="col-sm-9">
                          <input id="observaciones" type="text" class="form-control" name="observaciones" value="{{ old('observaciones') }}" autofocus>
                        </div>
                    </div>
                </div>
              </div>

              <div class="row">
                <div class="table-responsive col-md-12">
                  <input name='contador' type="hidden" value="0" id="contador">

                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr role="row">
                         <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Codigo</th>
                        <th width="40%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Nombre</th>
                        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Cantidad</th>
                        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Serie</th>
                        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Precio de compra</th>
                        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bodega</th>
                        <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Acción</th>

                      </tr>
                    </thead>
                    <tbody id="crear">
                         
                    </tbody>
                    <tfoot>
                      
                    </tfoot>
                  </table>
                  <div class="box-footer">
                    <button type="button" id="envio" class="btn btn-primary">
                      Guardar
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
        <!-- /.box-body -->
      </div>
    </section>
    <!-- /.content -->
  </div>

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">

  $(document).ready(function()
{
  $('#fecha').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });
  $('#vencimiento').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });
  src = "{{route('producto.codigo')}}";

  $("#codigo").autocomplete({
      source: function( request, response ) {
        $.ajax( {
          url: src,
          dataType: "json",
          data: {
            term: request.term
          },
          success: function( data ) {
            response(data);
          }
        } );
      },
      minLength: 1,
    } );

  $("#codigo").change( function(){
      $.ajax({
        type: 'post',
        url:'{{route('producto.codigo2')}}',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        type: 'POST',
        datatype: 'json',
        data: $("#codigo"),
        success: function(data){
            $('#nombre').val(data);
        },
        error: function(data){
            console.log(data);
        }
    })
  });
  var bodegas

  $('#busqueda').click(function(event){

    $.ajax({
        type: 'post',
        url:"{{route('ingreso.formulario')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
        datatype: 'json',
        data: $("#ingreso").serialize(),
        success: function(data){
          id= document.getElementById('contador').value;
          var midiv = document.createElement("tr");
            midiv.setAttribute("id","dato"+id);
            if(data[0].despacho == 0)
            {
              var f = new Date();
              var dia = ('0' + (f.getDate())).slice(-2);
              var mes = ('0' + (f.getMonth()+1)).slice(-2);
              var segundos = ('0' + (f.getMilliseconds())).slice(-1);
              var id2 = id.slice(-1);

              var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;
              
              midiv.innerHTML = "<input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' > <td>  <input name='id"+id+"' type='hidden' value='"+data[0].id+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> <td> <input type='text' value='1' class='input-number' onkeypress='return valida(event)'  name='cantidad"+id+"' ></td> <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td> <td>"+data[0].precio_compra+"</td> <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> <td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td>";
            }
            if(data[0].despacho == 1)
            {
              var f = new Date();
              var dia = ('0' + (f.getDate())).slice(-2);
              var mes = ('0' + (f.getMonth()+1)).slice(-2);
              var segundos = ('0' + (f.getMilliseconds())).slice(-1);
              var id2 = id.slice(-1);

              var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;
              
              midiv.innerHTML = "<input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' > <td>  <input name='id"+id+"' type='hidden' value='"+data[0].id+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> <td> <input type='hidden' value='1' name='cantidad"+id+"' > 1 </td> <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td> <td>"+data[0].precio_compra+"</td> <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td><td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td>";
            }
            
            document.getElementById('crear').appendChild(midiv);
            window.onbeforeunload = confirmarSalida;
            id = parseInt(id);
            id = id+1;
            document.getElementById('contador').value = id;
        },
        error: function(data){
            console.log(data);
        }
    })
  });

  $('#envio').click(function(event){

    var formulario = document.forms["frm"];
    var pedido = formulario.pedido.value;
    var fecha = formulario.fecha.value;
    var vencimiento = formulario.vencimiento.value;
    var id_proveedor = formulario.id_proveedor.value;
    var contador = formulario.contador.value;
    var msj = "";
    if(pedido == "")
        msj += "Por favor, ingrese el numero del Pedido\n";
    if(fecha == "")
        msj += "Por favor, ingrese la fecha de la Orden\n";
    if(vencimiento == "")
        msj += "Por favor, ingrese la fecha de vencimiento\n";
    if(id_proveedor == "")
        msj += "Por favor, ingrese el proveedor\n";
    if(contador == 0)
        msj += "Por favor, ingrese al menos un producto\n";
    if(msj == "")
    {
        $.ajax({
        type: 'post',
        url:"{{route('ingreso.guardar')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
        datatype: 'json',
        data: $("#frm").serialize(),
        success: function(data){
          var dato_url = "{{route('producto.index')}}";
          window.onbeforeunload = beforeVoid;
          alert('Datos Ingresados Correctamente')
          location.href ="{{ route('producto.index')}}";
        },
        error: function(data){
          alert('Error al Registrar Datos')
            console.log(data);
        }
        })
    }
    else
        alert(msj);

    
  });
  src2 = "{{route('producto.nombre')}}";

  $("#nombre").autocomplete({
      source: function( request, response ) {
        $.ajax( {
          url: src2,
          dataType: "json",
          data: {
            term: request.term
          },
          success: function( data ) {
            response(data);
          }
        } );
      },
      minLength: 3,
    } );

  $("#nombre").change( function(){
      $.ajax({
        type: 'post',
        url:"{{route('producto.nombre2')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        type: 'POST',
        datatype: 'json',
        data: $("#nombre"),
        success: function(data){
            $('#codigo').val(data);
        },
        error: function(data){
            console.log(data);
        }
    })
  });
});


    function confirmarSalida()
    {
        return "Va a abandonar esta página. Cualquier cambio no guardado se perderá";
    }
    function beforeVoid()
    {}

    function valida(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla==8){
            return true;
        }
            
        // Patron de entrada, en este caso solo acepta numeros
        patron =/[0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function eliminardato(valor)
    {
      var nombre1 = "dato"+valor;
      var nombre2 = 'visibilidad'+valor;
      document.getElementById(nombre1).style.display='none';
      document.getElementById(nombre2).value = 0;

    }
</script>
@endsection