@extends('contable.debito_bancario.base')
@section('action-content')
<!-- Ventana modal editar -->

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.banco')}}</a></li>
      <li class="breadcrumb-item active">{{trans('contableM.DepositoBancario')}}</li>
    </ol>
  </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
              <h3 class="box-title">Listado de Dépositos Bancarios</h3>
            </div>
            <div class="col-md-1 text-right">
              <button type="button" onclick="location.href='{{route('depositobancario.create')}}'" class="btn btn-success btn-gray">
                   <i aria-hidden="true"></i>Crear Déposito Bancario
        </button>
      </div>
    </div>

    <div class="row head-title">
      <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR DE DEPOSITOS BANCARIOS</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('depositobancario.buscar') }}">
        {{ csrf_field() }}
          <div class="form-group col-md-2 col-xs-2">
            <label class="texto" for="numero">ID  : </label>
          </div>
          <div class="form-group col-md-2 col-xs-2 container-2">
            <input class="form-control" type="text" id="numero" name="numero" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese id..." />
          </div>
          <div class="form-group col-md-2 col-xs-2">
            <label class="texto" for="numero">Id Asiento  : </label>
          </div>
          <div class="form-group col-md-2 col-xs-2 container-2">
            <input class="form-control" type="text" id="id_asiento" name="id_asiento" value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif" placeholder="Ingrese id..." />
          </div>
          <div class="form-group col-md-2 col-xs-2">
              <label> Cuenta Destino</label>
          </div>
          <div class="form-group col-md-2 col-xs-2">
              <select class="form-control select2" name="id_cuenta_destino" id="id_cuenta_destino">
                    <option value="">Seleccione...</option>
                    @foreach($cuentas as $x)
                        <option @if(isset($searchingVals)) @if($searchingVals['id_cuenta_destino']==$x->id) @endif @endif  value="{{$x->id}}">{{$x->nombre}}</option>
                    @endforeach
              </select>
          </div>
          <div class="form-group col-md-2">
              <label> Concepto </label>
          </div>
          <div class="form-group col-md-2">
              <input type="text" class="form-control" name="concepto" id="concepto" placeholder="Ingrese concepto">
          </div>
          <div class="form-group col-md-2 col-xs-2">
            <label class="texto" for="fecha">{{trans('contableM.fecha')}}: </label>
          </div>
          <div class="form-group col-md-3 col-xs-10 container-4">
              <div class="col-xs-12">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" name="fecha_asiento" class="form-control" id="fecha_asiento" value="@if(isset($searchingVals)){{$searchingVals['fecha_asiento']}}@endif">
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group col-md-4">
              <label class="texto" for="referencia_deposito">Refencia deposito</label>
            </div>
            <div class="form-group col-md-8 container-2">
              <input class="form-control" type="text" id="referencia_deposito" name="nota" value="@if(isset($searchingVals)){{$searchingVals['nota']}}@endif" placeholder="Ingrese la referencia_deposito..." />
            </div>
          </div>
          <div class="col-xs-1">
            <button type="submit" id="buscarAsiento" class="btn btn-success btn-gray">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
            </button>
          </div>
        </form>
        <form method="GET" id="4" action="{{ route('depositobancario.exportar_excel') }}">
      <input type= "hidden" name="buscar_asiento2"  id= "buscar_asiento2"value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif" placeholder="Ingrese asiento..." />
      <input type= "hidden" name="fecha_asiento2" id= "fecha_asiento2" value="@if(isset($searchingVals)){{$searchingVals['fecha_asiento']}}@endif">
      <input type= "hidden" name="concepto2" id= "concepto2" value="@if(isset($searchingVals)){{$searchingVals['concepto']}}@endif">
      <div class="col-xs-2">
          <button type="submit" id="buscarAsiento0" class="btn btn-success btn-gray">
                    <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar Excel
          </button>
       </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">{{trans('contableM.DEPOSITOSBANCARIOS')}}</label>
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
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.id')}}</th>
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.asiento')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fecha')}}</th>
                        <th width="35%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.detalle')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Referencia Deposito</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.valor')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($registros as $value)
                      <tr class="well">
                        <td>{{ $value->id }}</td>
                        <td>{{ $value->id_asiento }}</td>
                        <td>{{ date('d/m/Y', strtotime($value->fecha_asiento)) }}</td>
                        <td>{{ $value->concepto }}</td>

                        <td>{{ $value->nota }}</td>
                        <td class="text-right"> {{number_format($value->total_deposito,2)}}</td>
                        <td>@if($value->estado == '1') {{trans('contableM.activo')}} @elseif($value->estado =='0') {{trans('contableM.anulada')}} @else {{trans('contableM.activo')}} @endif</td>
                        <td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{ route('depositobancario.show', ['id' => $value->id]) }}" class="btn btn-success btn-gray">
                            <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>
                          </a>
                          @if($value->estado == '1')
                          <a href="javascript:anular({{$value->id}});" class="btn btn-danger btn-gray">
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
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{trans('agenda.registros')}} {{count($registros)}} / {{count($registros)}} {{trans('laboratorio.de')}} {{$registros->total()}} {{trans('contableM.registros')}}</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $registros->appends(Request::only(['id_asiento', 'fecha', 'concepto','id_cuenta_destino']))->links() }}
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
    $('#fecha_asiento').datetimepicker({
      format: 'DD/MM/YYYY',
    });

    $("#buscarAsiento").click(function() {
      buscar_nota();
    });
    $('.select2').select2({
      tags: false
    });


  });

  function anular(id) {

    Swal.fire({
        title: '¿Desea Anular este comprobante?',
        text: `{{trans('contableM.norevertiraccion')}}!`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        var acumulate="";

          test(id);

      }
    })
}
async function test(id) {
  try {
    const { value: text } = await Swal.fire({
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
                        url:"{{ url('contable/Banco/depositobancario/anular/')}}/"+id,
                        datatype: 'json',
          data: {
            'observacion': text
          },
                        success: function(data){

            if (data.status == "error") {
              Swal.fire({
                title: data.status,
                icon: data.status,
                html: data.msj,
              });
            }
            if (data.status == 'success') {
              Swal.fire(data.status, data.msj, data.status);
              location.href = "{{route('depositobancario.index')}}";

            }

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
</script>
@endsection
