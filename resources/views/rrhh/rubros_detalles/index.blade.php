@extends('rrhh.rubros_detalles.base')
@section('action-content')
<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-md-12">
          <div class="col-md-8">
            <h3 class="box-title">Listado de Rubros</h3>
          </div>
          <div class="col-md-2 ">
            <a class="btn btn-primary" href="{{ route('rubrosDetalles.create') }}" style="width: 100%;">Nuevo Rubro</a>
          </div>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr>
                  <th>Descripcion</th>
                  <th>Descripcion cuenta</th>
                  <th>Ingreso / Egreso</th>
                  <th>Debe / Haber</th>
                  <th>Editable</th>
                  <th>Marca</th>
                  <th>Estado</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($rubrosDetalles as $value)
                <tr>
                  <td>{{ $value->descripcion }}</td>
                  <td>{{ $value->descripcion_cuenta }}</td>
                  <td>@if($value->ingreso_egreso == 0) Ingreso @else Egreso @endif</td>
                  <td>@if($value->debe_haber == 0) Debe @else Haber @endif</td>
                  <td>@if($value->editable == 1) Si @else No @endif</td>
                  <td>{{ $value->marca }}</td>
                  <td>@if($value->estado == 1 )<div class="bg-green " style="text-align: center;"><span>Activo</span></div>@else<div class="bg-gray" style="text-align: center;"><span>Inactivo</span></div>@endif</td>
                  <td>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{ route('rubrosDetalles.edit', ['id' => $value->id]) }}" class="btn btn-warning col-md-6 col-xs-6 btn-margin">
                      Actualizar
                    </a>
                    <a href="{{ route('rubrosDetalles.asignarCuenta', ['id' => $value->id]) }}" class="btn btn-primary col-md-6 col-xs-6 btn-margin">
                      Asignar Cuenta Contable
                    </a>
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
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($rubrosDetalles)}} de {{$rubrosDetalles->total()}} registros</div>
          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $rubrosDetalles->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.box-body -->
  </div>
</section>
<!-- /.content -->
</div>

<script type="text/javascript">
  $('#seguimiento').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
  });

  $(document).ready(function() {


    $('#example2').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': true,
      'info': false,
      'autoWidth': false
    });




  });
</script>
@endsection