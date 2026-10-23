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
      <li class="breadcrumb-item active">Transferencia Bancaria</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <h3 class="box-title">Listado de Transferencias Bancarias</h3>
      </div>
      <div class="col-md-1 text-right">
        <button type="button" onclick="location.href='{{route('transferenciabancaria.create')}}'" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>Crear Transferencia Bancaria
        </button>
      </div>
    </div>

    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR DE TRANSACCIONES BANCARIAS</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('transferenciabancaria.buscar') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-1">
          <label class="texto" for="buscar_asiento">Asiento: </label>
        </div>
        <div class="form-group col-md-2 col-xs-2">
          <input class="form-control" type="text" id="buscar_asiento" name="buscar_asiento" value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif" placeholder="Ingrese asiento..." />
        </div>
        <div class="form-group col-md-2">
          <label>N° Cheque:</label>
        </div>
        <div class="form-group col-md-2">
          <input class="form-control" type="text" id="numcheque" name="numcheque" value="@if(isset($searchingVals)){{$searchingVals['numcheque']}}@endif" placeholder="Ingrese numero de cheque..." />
        </div>
        <div class="form-group col-md-2">
          <label>Fecha Cheque:</label>
        </div>
        <div class="form-group col-md-3 col-xs-2">
          <div class="col-xs-12">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" name="fecha_cheque" class="form-control" id="fecha_cheque" value="@if(isset($searchingVals)){{$searchingVals['fecha_cheque']}}@endif">
            </div>
          </div>
        </div>

        <div class="form-group col-md-1">
          <label>Fecha:</label>
        </div>
        <div class="form-group col-md-2 col-xs-2 container-4">
          <div class="col-xs-12">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" name="fecha_asiento" class="form-control" id="fecha_asiento" value="@if(isset($searchingVals)){{$searchingVals['fecha_asiento']}}@endif">
            </div>
          </div>
        </div>
        <!--
        <div class="form-group col-md-2 col-xs-1">
          <label class="texto" for="buscar_asiento">Cuenta Destino: </label>
        </div>
        <div class="form-group col-md-2 col-xs-2">
         <select class="form-control select2" name="id_cuenta_destino" id="id_cuenta_destino" style="width: 100%;">
                <option value="">Seleccione...</option>
                @foreach($cuentas as $c)
                    <option @if(isset($searchingVals)) @if($searchingVals['id_cuenta_destino']==$c->id) @endif @endif value="{{$c->id}}">{{$c->nombre}}</option>
                @endforeach
         </select>
        </div>-->
        <div class="form-group col-md-2 col-xs-1">
          <label class="texto" for="buscar_asiento">Concepto </label>
        </div>
        <div class="form-group col-md-2 col-xs-2">
          <input type="text" class="form-control" name="concepto" id="concepto" autocomplete="off" value="@if(isset($searchingVals)){{$searchingVals['concepto']}}@endif">
        </div>
        <div class="col-xs-1">
          <button type="submit" id="buscarAsiento" class="btn btn-success btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
        </div>
      </form>
      <form method="GET" id="4" action="{{ route('transferenciabancaria.exportar_excel') }}">
        <input type="hidden" name="buscar_asiento2" id="buscar_asiento2" value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif" placeholder="Ingrese asiento..." />
        <input type="hidden" name="fecha_asiento2" id="fecha_asiento2" value="@if(isset($searchingVals)){{$searchingVals['fecha_asiento']}}@endif">
        <div class="col-xs-2">
          <button type="submit" id="buscarAsiento0" class="btn btn-success btn-gray">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar Excel
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">TRANSACCIONES BANCARIAS</label>
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
                  <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr class="well-dark">
                        <th width="5%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">ID</th>
                        <th width="5%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Asiento</th>
                        <th width="5%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"># Cheque</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha Cheque</th>
                        <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Creado por</th>
                        <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Accion</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($registros as $value)
                      <tr class="well">
                        <td>{{ $value->id }}</td>
                        <td>{{ $value->id_asiento }}</td>
                        <td> {{ $value->numcheque}} </td>
                        <td> {{ date('d/m/Y', strtotime($value->fecha_asiento))}} </td>
                        <td>{{ date('d/m/Y', strtotime($value->fecha_cheque)) }}</td>
                        <td>{{ $value->concepto }}</td>

                        <td class="text-right">{{ number_format($value->valor_destino, 2) }}</td>
                        <td>@if($value->estado == '1') Activo @elseif($value->estado =='0') Anulada @else Activo @endif</td>
                        <td>{{$value->usuario->nombre1}} {{$value->usuario->apellido1}}</td>
                        <td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{ route('transferenciabancaria.show', ['id' => $value->id]) }}" class="btn btn-success btn-gray">
                            <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>
                          </a>
                          @if($value->estado == '1')
                          <a onclick="guardar({{$value->id}}, {{$value->id_asiento}});" class="btn btn-danger btn-gray">
                            <i class="glyphicon glyphicon-ban-circle" aria-hidden="true"></i>
                          </a>
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
                    {{ $registros->appends(Request::only(['id_asiento', 'fecha', 'concepto','fecha_cheque','numcheque','id_cuenta_destino','concepto']))->links() }}
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">

  function guardar(ids, id_asientos) {
    Swal.fire({
        title: '¿Desea Anular esta Transferencia Bancaria?',
        text: "No puedes revertir esta acccion!",
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        var id = ids;
        var id_asiento = id_asientos;
        test(id, id_asiento);
      }
    })
  }

  async function test(id, id_asiento) {
    try {
      let mensaje = "";
      @php
        $id_empresa = Session::get('id_empresa');
      @endphp
      @if($id_empresa != "0992704152001")
        mensaje =  'Asiento anulado: '+id_asiento;
      @endif
     
      const { value: text } = await Swal.fire({
                      title: 'Ingrese una Observacion',
                      input: 'textarea',
                      inputPlaceholder: 'Ingrese motivo de anulación...',
                      showCancelButton: true,
                      inputValue: mensaje,
                      inputValidator: (value) => {  
                        if (!value) {
                          return 'Ingrese una Observacion !!'
                        }
                      }
                    })
                    if (text) {
                        $.ajax({
                          type: 'get',
                          url:"{{route('transferenciabancaria.anular')}}",
                          datatype: 'json',
                          data: {'observacion':text,
                                 'id': id,
                                },
                          success: function(data){

                            if (data.status == "error") {
                              Swal.fire({
                              title: data.status.toUpperCase(),
                              icon: data.status,
                              html: data.msj,
                              });
                              
                                  
                          }
                           
                        if(data.status == 'success') { 
                          swal("Correcto!", "Anulado", "success");
                            location.reload();

                        }
                          },
                          error: function(data){
                            console.log(data);
                          }
                        }); 
                    }
    } catch(err) {
      console.log(err);
    }
  }

  $(document).ready(function() {

    $('#example2').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false
    });
    $('#fecha_asiento').datetimepicker({
      format: 'DD/MM/YYYY',
    });
    $('#fecha_cheque').datetimepicker({
      format: 'DD/MM/YYYY',
    });

    $("#buscarAsiento").click(function() {
      buscar_nota();
    })
    $('.select2').select2({
      tags: false
    });
  });


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
    var fecha = $("#fecha_asiento=").val();
    // var concepto= $("#concepto").val();

    if ((asiento != '' && asiento != undefined) || (fecha != '' && fecha != undefined) /*|| (concepto!='' && concepto!= undefined) */ ) {
      $.ajax({
        type: 'get',
        url: "{{route('transferenciabancaria.buscar')}}",
        datatype: 'html',
        data: {
          'buscar_asiento': asiento,
          'fecha_asiento': fecha,
          // 'concepto':concepto,
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
</script>
@endsection