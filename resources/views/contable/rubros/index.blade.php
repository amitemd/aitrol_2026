@extends('contable.rubros.base')
@section('action-content')
<!-- Ventana modal editar -->

  <!-- Main content -->
  <section class="content">
    <div class="box" style=" background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Fichero de Rubros</h3>
            </div>

            <div class="col-md-2">
              <button type="button" onclick="location.href='{{route('rubros.create')}}'" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i aria-hidden="true"></i>Agregar Rubro
              </button>
            </div>
        </div>
      <!-- /.box-header -->
      <div class="box-body">
        <form method="POST" action="{{route('rubros_search')}}">
          {{ csrf_field() }}
          @component('layouts.search', ['title' => 'Buscar'])
            @component('layouts.two-cols-search-row', ['items' => ['Codigo', 'Nombre'],
            'oldVals' => [isset($searchingVals) ? $searchingVals['codigo'] : '', isset($searchingVals) ? $searchingVals['nombre'] : '']])
            @endcomponent
            </br>
          @endcomponent
        </form>
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr >
                      <th >Código</th>
                      <th >Nombre</th>
                      <th >Cuenta Debe</th>
                      <th >Cuenta Haber</th>
                      <th >Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($rubros as $value)
                      <tr>
                        <td>@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
                        <td>@if(!is_null($value->nombre)){{$value->nombre}}@endif</td>
                        @php
                          $debe = \Sis_medico\Plan_Cuentas::find($value->debe);
                          $haber = \Sis_medico\Plan_Cuentas::find($value->haber);
                        @endphp
                        <td>@if(!is_null($debe->nombre)){{$debe->nombre}}@endif</td>
                        <td>@if(!is_null($haber->nombre)){{$haber->nombre}}@endif</td>
                        <td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <a href="{{route('rubros_editar', ['id' => $value->codigo])}}" class="btn btn-warning col-md-6 col-xs-6 btn-margin">
                            Actualizar
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
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($rubros)}} de {{$rubros->total()}} registros</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $rubros->links() }}
                </div>
              </div>
            </div>
        </div>
      </div>
      <!-- /.box-body -->
    </div>
  </section>
  <!-- /.content -->

<script type="text/javascript">
    
    $(document).ready(function(){

      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
      });

    });
</script>
@endsection
