@extends('hospital_admin.base')
@section('action-content')

<a type="button" href="{{URL::previous()}}" class="btn btn-sm btn-primary my-2"><i class="far fa-arrow-alt-circle-left"></i> Regresar</a>
<div class="card shadow mb-4">
  <!-- Card Header - Accordion -->
  <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
    <h6 class="m-0 font-weight-bold text-primary">Ingreso de producto</h6>
  </a>
  <!-- Card Content - Collapse -->
  <div class="collapse show" id="collapseCardExample" style="">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
            <form method="POST" id="ingreso">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                      <label for="inputid" class="col-sm-3 control-label">Código</label>
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
              <div class="box-footer">
                <button type="button" id="busqueda" class="btn btn-primary">Agregar</button>
              </div>
            </form>
            <div class="box">
        <form method="POST"  name="frm" id="frm">
          <div class="box-header">
            <div class="row">
                <!-- Fecha -->
                <div class="form-group col-md-6">
                    <label for="fecha" class="col-md-4 control-label">Fecha Pedido</label>
                    <div class="col-md-8">
                      <div class="input-group date">                        
                        <input type="text" value="" name="fecha" class="form-control" id="fecha"  placeholder="AAAA/MM/DD">
                      </div>
                    </div>
                </div>
                <!-- Numero de Pedido -->
                <div class="form-group cl_pedido col-md-6 {{ $errors->has('pedido') ? ' has-error' : '' }}">
                    <label for="pedido" class="col-md-4 control-label">Número de pedido</label>
                    <div class="col-md-8">
                      <input id="pedido" type="text" class="form-control" name="pedido" value="{{ old('pedido') }}" onkeyup="valida(event);" required autofocus>
                    </div>
                    <span class="help-block">
                      <strong id="str_pedido"></strong>
                    </span>
                </div>
                <!-- Vencimiento -->
                <div class="form-group col-md-6{{ $errors->has('vencimiento') ? ' has-error' : '' }}">
                    <label for="vencimiento" class="col-md-4 control-label">Fecha de Vencimiento</label>
                    <div class="col-md-8">
                      <div class="input-group date">
                        <input type="text" value="" name="vencimiento" class="form-control" id="vencimiento"  placeholder="AAAA/MM/DD">
                      </div>
                    </div>
                </div>
                <!-- Proveedor -->
                <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                    <label for="id_proveedor" class="col-md-4 control-label">Proveedor</label>
                    <div class="col-md-8">
                      <select name="id_proveedor" class="form-control" required="" name="id_proveedor">
                          <option value="">Seleccione..</option>
                        @foreach($proveedores as $value)
                          <option value="{{$value->id}}" >{{$value->nombrecomercial}}</option>
                        @endforeach
                      </select>
                    </div>
                </div>
                <!-- MULTIPLE EMPRESA-->
                <div class="form-group col-md-6{{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                    <label for="id_empresa" class="col-md-4 control-label">Empresa</label>
                    <div class="col-md-8">
                      <select id="id_empresa" class="form-control" name="id_empresa">
                          <option value="">Seleccione..</option>
                        @foreach($empresa as $value)
                          <option value="{{$value->id}}" >{{$value->nombrecomercial}}</option>
                        @endforeach
                      </select>
                    </div>
                </div>
                <!-- Observaciones -->
                <div class="form-group  col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                    <label for="observaciones" class="col-md-2 control-label">Observaciones</label>
                    <div class="col-md-10">
                      <input id="observaciones" type="text" class="form-control" name="observaciones" value="{{ old('observaciones') }}" autofocus>
                    </div>
                </div>
            </div>
          </div>
          <div class="general form-group ">
            <label class="col-md-4 control-label"></label>
            <div class="col-md-8">
            </div>
            <span class="help-block">
              <strong id="lote_errores"></strong>
            </span>
          </div>
          <div class="box-body">
            <div class="table-responsive col-md-12">
              <input name='contador' type="hidden" value="0" id="contador">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr role="row">
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Código</th>
                    <th width="25%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Nombre: activate to sort column descending" aria-sort="ascending">Nombre</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Cantidad: activate to sort column descending" aria-sort="sorting">Cantidad</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Serie: activate to sort column descending" aria-sort="sorting">Serie</th>
                    <!--th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Precio de compra</th-->
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Bodega: activate to sort column ascending">Bodega</th>
                    <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Serie: activate to sort column descending" aria-sort="sorting">Lote</th>
                    <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Fecha de Vecimiento: activate to sort column ascending">Fecha de Vecimiento</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Fecha de Vecimiento: activate to sort column ascending">Precio Unitario</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Fecha de Vecimiento: activate to sort column ascending">Precio Final</th>
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Acción</th>

                  </tr>
                </thead>
                <tbody id="crear">

                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="7"></td>
                    <td>Subtotal 12%:</td>
                    <td><input type="hidden" name="subtotal_12" id="subtotal_12"> <input style="width: 55px;" type="text" readonly id="subtotal_12_1"></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td colspan="7"></td>
                    <td>Subtotal 0%:</td>
                    <td><input type="hidden" name="subtotal_0" id="subtotal_0"> <input style="width: 55px;" type="text" readonly id="subtotal_0_1"></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td colspan="7"></td>
                    <td>IVA:</td>
                    <td><input type="hidden" name="iva" id="iva"> <input style="width: 55px;" type="text" readonly id="iva_1"></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td colspan="7"></td>
                    <td>Total:</td>
                    <td><input type="hidden" name="total" id="total"> <input style="width: 55px;" type="text" readonly id="total_1"></td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <div class="box-footer">
            <button type="button" id="envio" class="btn btn-primary">
                  Guardar
            </button>
          </div>
        </form>
      </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">


  function total_calculo(id){
    cantidad = parseInt($('#cantidad'+id).val());
    valor = parseInt($('#precio'+id).val());
    total = cantidad * valor;
    $('#precio_final'+id).text(total);
    suma_totales();
  }
  function suma_totales(){
    contador  =  0;
    subtotal_0 = 0;
    subtotal_12 = 0;
    iva = 0;
    total = 0;
    $("#crear tr").each(function(){
      $(this).find('td')[0];
      visibilidad = $(this).find('#visibilidad'+contador).val();
      if(visibilidad == 1){
        cantidad = parseInt($(this).find('#cantidad'+contador).val());
        valor = parseInt($(this).find('#precio'+contador).val());
        total = cantidad * valor;
        iva = $(this).find('#iva'+contador).val();
        if(iva == 1){
          subtotal_12 = subtotal_12 + total;
        }else{
          subtotal_0 = subtotal_0 + total;
        }

      }
      contador = contador+1;
    });
    iva = subtotal_12 * 0.12;
    total = subtotal_12 + subtotal_0+ iva;
    $('#subtotal_12_1').val(subtotal_12);
    $('#subtotal_0_1').val(subtotal_0);
    $('#iva_1').val(iva);
    $('#total_1').val(total);

    $('#subtotal_12').val(subtotal_12);
    $('#subtotal_0').val(subtotal_0);
    $('#iva').val(iva);
    $('#total').val(total);
  }

  $(document).ready(function()
  {
    $('#fecha').datetimepicker({
              useCurrent: false,
              format: 'YYYY/MM/DD',
              defaultDate: new Date()
                //Important! See issue #1075

          });
    $('#vencimiento').datetimepicker({
              useCurrent: false,
              format: 'YYYY/MM/DD',
              defaultDate: new Date()

                //Important! See issue #1075

          });
    src = "{{route('hospital_admin.codigo')}}";

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
          url:'{{route('hospital_admin.codigo2')}}',
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
          type: 'get',
          url:"{{route('hospital_admin.formulario')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          data: $("#ingreso").serialize(),
          success: function(data){
            //console.log(data);
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

                midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td><input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> <td> <input type='text' value='1' class='input-number' onkeypress='return valida(event)' onkeyup='total_calculo("+id+")' style='width: 60px;' id='cantidad"+id+"' name='cantidad"+id+"' ></td> <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td> "+" <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> <td><div class='form-group  lote_error"+id+"'> <input   type='text' name='lote"+id+"'  required> </div> </td><td> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></td><td><input onkeyup='total_calculo("+id+")' value='0' id='precio"+id+"' type='number' style='width: 60px;' name='precio"+id+"' ></td><td id='precio_final"+id+"' ></td><td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td>";
              }
              if(data[0].despacho == 1)
              {
                var f = new Date();
                var dia = ('0' + (f.getDate())).slice(-2);
                var mes = ('0' + (f.getMonth()+1)).slice(-2);
                var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                var id2 = id.slice(-1);

                var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;

                midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td> <input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> <td> <input type='hidden'  value='1' id='cantidad"+id+"' name='cantidad"+id+"' > 1 </td> <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td>  "+"<td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> <td> <div class='form-group  lote_error"+id+"'><input type='text' name='lote"+id+"' id= required></div></td><td> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></td><td><input type='number' name='precio"+id+"' id='precio"+id+"' style='width: 60px;' value='0' onkeyup='total_calculo("+id+")' ></td><td id='precio_final"+id+"' ></td><td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td>";
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
      var id_empresa = formulario.id_empresa.value;
      var contador = formulario.contador.value;
      var msj = "";
      if(pedido == "")
          msj += "Por favor, ingrese el numero del Pedido\n";
      if(fecha == "")
          msj += "Por favor, ingrese la fecha de la Orden\n";
      if(vencimiento == "")
          msj += "Por favor, ingrese la fecha de vencimiento\n";
      if(id_proveedor == "")
          msj += "Por favor, seleccione el proveedor\n";
        if(id_empresa == "")
          msj += "Por favor, seleccione la empresa\n";
      if(contador == 0)
          msj += "Por favor, ingrese al menos un producto\n";
      if(msj == "")
      {
          $.ajax({
            type: 'get',
            url:"{{route('hospital_admin.guardar')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#frm").serialize(),
            success: function(data){
              var dato_url = "{{ route('hospital_admin.producto')}}";
              window.onbeforeunload = beforeVoid;
              alert('Datos Ingresados Correctamente');
              location.href ="{{route('hospital_admin.codigobarra')}}";
            },
            error: function(data){
                console.log(data);
                var errores = data.responseJSON;
                var contador = document.getElementById('contador').value;
                var error ;
                for(i=0; i<contador; i++) {
                  if(errores['lote'+i] != undefined){
                    error = errores['lote'+i];
                    $('.lote_error'+i).addClass("has-error");
                    $('.general').addClass("has-error");
                    $('#lote_errores').empty().html(errores['lote'+i]);
                  }
                }
                if(data.responseJSON.pedido!=null){
                    $(".cl_pedido").addClass("has-error");
                    $('#str_pedido').empty().html(data.responseJSON.pedido);
                }
                if(data.responseJSON.id_proveedor!=null){
                    $(".cl_id_proveedor").addClass("has-error");
                    $('#str_id_proveedor').empty().html(data.responseJSON.pedido);
                }
            }
          })
      }
      else
          alert(msj);
    });
    src2 = "{{route('hospital_admin.nombre')}}";

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
          url:"{{route('hospital_admin.nombre2')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          type: 'POST',
          datatype: 'json',
          data: $("#nombre"),
          success: function(data){
              $('#codigo').val(data);
          },
          error: function(data){
              //console.log(data);
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
    suma_totales();

  }

  function calcular() {
      // obtenemos todas las filas del tbody
    var filas=document.querySelectorAll("#example2 tbody tr");

    var total=0;

    // recorremos cada una de las filas
    filas.forEach(function(e) {

        // obtenemos las columnas de cada fila
        var columnas=e.querySelectorAll("td");

        // obtenemos los valores de la cantidad y importe
        var cantidad=parseFloat(columnas[1].textContent);
        var importe=parseFloat(columnas[2].textContent);

        // mostramos el total por fila
        columnas[3].textContent=(cantidad*importe).toFixed(2);

        total+=cantidad*importe;
    });

    // mostramos la suma total
    var filas=document.querySelectorAll("#example2 tfoot tr td");
    filas[1].textContent=total.toFixed(2);
  }

</script>
@endsection