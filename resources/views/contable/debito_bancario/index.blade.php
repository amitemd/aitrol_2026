@extends('contable.debito_bancario.base')
@section('action-content')
<!-- Ventana modal editar -->

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Banco</a></li>
      <li class="breadcrumb-item active">D&eacute;bito Bancario Acreedores</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <h3 class="box-title">Listado de D&eacute;bitos Bancarios Acreedores</h3>
      </div>
      <div class="col-md-1 text-right">
        <button type="button" onclick="location.href='{{route('debitobancario.crear')}}'" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>Crear D&eacute;bito Bancario
        </button>
      </div>
    </div>

    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR DE D&Eacute;BITOS BANCARIOS</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('debitobancario.buscar') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_asiento">Asiento: </label>
        </div>
        <div class="form-group col-md-2 col-xs-5 container-4">
          <input class="form-control" type="text" id="id_asiento" name="id_asiento" value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif" placeholder="Ingrese asiento..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">Fecha: </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <div class="col-xs-12">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" name="fecha" class="form-control" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha']}}@endif">
            </div>
          </div>
        </div>
        <div class="form-group col-md-1 col-xs-12">
          <label>Proveedor</label>
        </div>
        <div class="form-group col-md-2">
          <select class="form-control select2" style="width: 100%;" name="id_proveedor" id="id_proveedor">
            <option value="">Seleccione...</option>
            @foreach($proveedores as $value)
            <option @if(isset($searchingVals)) {{ $value->id == $searchingVals['id_acreedor'] ? 'selected' : ''}} @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_concepto">Concepto: </label>
        </div>
        <div class="form-group col-md-2 col-xs-5 container-4">
          <input class="form-control" type="text" id="buscar_concepto" name="buscar_concepto" value="@if(isset($searchingVals)){{$searchingVals['concepto']}}@endif" placeholder="Ingrese el concepto..." />
        </div>
        <div class="col-xs-offset-2 col-xs-2" style="text-align: right;">
          <button type="submit" id="buscarAsientos" class="btn btn-primary btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
        </div>
        <div class="text-center md:w-auto w-full">
          <input type="hidden" name="excel" id="excel">
          <button type="submit" onclick="document.getElementById('excel').value=1" class="bg-green btn btn-primary btn-xl" type="button">
            <span class="glyphicon glyphicon-download-alt"></span>
            Excel
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">D&Eacute;BITOS BANCARIOS</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="form-row">
          <div id="resultados">
          </div>
          <div id="contenedor">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr class="well-dark">
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Id</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Asiento</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Concepto</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Creado por</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Anulado por</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Accion</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($registros as $value)
                      <tr class="well">
                        <td>{{ $value->id }}</td>
                        <td>{{ $value->id_asiento }}</td>
                        <td>{{ $value->fecha }}</td>
                        <td>{{ $value->acreedor->nombrecomercial }}</td>
                        <td class="text-right">{{ number_format($value->valor, 2) }}</td>
                        <td>{{$value->concepto}}</td>
                        <td>@if($value->estado == '1') Activo @elseif($value->estado =='0') Anulada @else Activo @endif</td>
                        <td>{{$value->usuario->nombre1}} {{$value->usuario->apellido1}}</td>
                        <td>@if($value->estado==0) {{$value->usuariomod->nombre1}} {{$value->usuariomod->apellido1}} @endif</td>
                        <td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{ route('debitobancario.revisar', ['id' => $value->id]) }}" class="btn btn-success btn-gray">
                            <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>
                          </a>
                          @if($value->estado == '1')
                          <a href="javascript:anular({{$value->id}});" class="btn btn-danger btn-gray"><i class="fa fa-trash"> </i> </a>

                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-5">
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando registros {{count($registros)}} / {{count($registros)}} de {{$registros->total()}} registros</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $registros->appends(Request::only(['id_asiento', 'fecha', 'concepto', 'id_proveedor', 'buscar_concepto']))->links() }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    <!-- /.box-body -->
  </div>
</section>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
  $(document).ready(function() {

    $('#example2').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false
    });
    $('#fecha').datetimepicker({
      format: 'YYYY-MM-DD',
    });

    $("#buscarAsiento").click(function() {
      buscar_nota();
    })
  });

  function anular(id) {
    /*if (confirm('¿Desea Anular Factura  ?')) {
      $.ajax({
          type: 'get',
          url:"{{ url('contable/compras/factura/')}}/"+id,
          datatype: 'json',
          data: $("#fecha_enviar").serialize(),
          success: function(data){
            swal("Correcto!","Anulación Correcta","success");
            location.href ="{{route('compras_index')}}";
          },
          error: function(data){
            console.log(data);
          }
        });
    }else{
      compras.verificar_anulacion
       location.href ="{{route('compras_index')}}";
    }*/

    Swal.fire({
      title: '¿Desea Anular esta comprobante?',
      text: "No puedes revertir esta acccion!",
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        var acumulate = "";

        $.ajax({
          type: 'get',
          url: "{{ route('compras.verificar_anulacion')}}",
          datatype: 'json',
          data: {
            'verificar': '2',
            'id_compra': id
          },
          success: function(data) {
            //console.log(data+" dsada "+id);
            console.log(data);

            if (data.respuesta == "si") {
              let enlace = `<a target="_blank" href="{{ url('contable/cruce/valores/buscar?id=${data.ids[0]}')}}"><b>${data.tablas[0]}</b></a>`;
              let texto = `Existen algunos ${enlace} generados con esta factura <br> Desea continuar ?`;
              // alertas("error", "Error", texto);
              Swal.fire({
                title: "Tiene observaciones",
                html: `${texto}`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, continuar"
              }).then((result) => {
                if (result.isConfirmed) {
                  test(id);
                }
              });
            } else {
              test(id);
            }


          },
          error: function(data) {
            console.log(data);
          }
        });

      }
    })


  }
  async function test(id) {
    try {
      const {
        value: text
      } = await Swal.fire({
        input: 'textarea',
        inputPlaceholder: 'Ingrese motivo de anulación...',
        inputAttributes: {
          'aria-label': 'Ingrese motivo de anulación'
        },
        showCancelButton: true
      })

      if (text) {
        $.ajax({
          type: 'get',
          url: "{{ url('contable/Banco/debitobancario/anulacion/')}}/" + id,
          datatype: 'json',
          data: {
            'concepto': text
          },
          success: function(data) {
            Swal.fire("Correcto!", "Anulación Correcta", "success");
            location.href = "{{route('debitobancario.index')}}";
          },
          error: function(data) {
            console.log(data);
          }
        });

      }

    } catch (err) {
      console.log(err);
    }
  }

  function autocompletarceros() {
    var variable = $("#buscar_secuencia").val();
    var secuencia = 0;
    if ((variable.length) >= 1) {
      var longitud = parseInt(variable.length);
      if (longitud > 10) {
        alert('Máximo 10 caracteres');
        $("#buscar_secuencia").val('');
      } else {
        switch (longitud) {

          case 1:
            secuencia = '000000000';
            break;
          case 2:
            secuencia = '00000000';
            break;
          case 3:
            secuencia = '0000000';
            break;
          case 4:
            secuencia = '000000';
            break;
          case 5:
            secuencia = '00000';
            break;
          case 6:
            secuencia = '0000';
            break;
          case 7:
            secuencia = '000';
            break;
          case 8:
            secuencia = '00';
            break;
          case 9:
            secuencia = '0';
        }
        $('#buscar_secuencia').val(secuencia + variable);
        obtener_tabla()

      }


    } else {
      //aqui devuelvo los originales
      $("#resultados").hide();
      $("#contenedor").show();

    }
  }

  function buscar_nota() {
    var asiento = $("#buscar_asiento").val();
    var fecha = $("#fecha").val();
    var concepto = $("#concepto").val();

    if ((asiento != '' && asiento != undefined) || (fecha != '' && fecha != undefined) || (concepto != '' && concepto != undefined)) {
      $.ajax({
        type: 'get',
        url: "{{route('notacredito.buscar')}}",
        datatype: 'html',
        data: {
          'buscar_asiento': asiento,
          'fecha': fecha,
          'concepto': concepto,
        },
        success: function(datahtml) {

          $("#resultados").html(datahtml);
          $("#resultados").show();
          $("#contenedor").hide();

        },
        error: function(data) {
          console.log(data);

        }
      });
    } else {
      alert("campos vacios")
      $("#resultados").hide();
      $("#contenedor").show();
    }

  }
  $('.select2').select2({
    tags: false
  });

  function alertas(icon, title, text) {
    Swal.fire({
      icon: `${icon}`,
      title: `${title}`,
      html: `${text}`
    })
  }
</script>
@endsection