@extends('insumos.equipo.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Listado de Equipos Medicos</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{route('equipo.create')}}">Agregar nuevo Equipo Medicos</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{route('equipo.search')}}">
         {{ csrf_field() }}
         @component('layouts.search1', ['title' => 'Buscar'])
          @component('layouts.one-cols-search-row', ['items' => ['Nombre'],
          'oldVals' => [isset($searchingVals) ? $searchingVals['nombre'] : '']])
          @endcomponent
        @endcomponent
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div >
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" >
            <thead>
              <tr >
                <th >Nombre</th>
                <th >Modelo</th>
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Tipo</th>
                <th >Serie</th>
                <th >Fecha de Ingreso</th>
                <th >Fecha de Mantenimiento</th>
                <th >Estado</th>
                <th >Equipo en Calidad de Prestamo</th>
                <th >Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($equipo as $value)
                <tr role="row" class="odd">
                  <td class="sorting_1">{{ $value->nombre}}</td>
                  <td >{{ $value->modelo}}</td>
                  <td >{{ $value->tipo}}</td>
                  <td >{{ $value->serie}}</td>
                  <td >{{ $value->fecha_ingreso }}</td>
                  <td >{{ $value->fecha_mantenimiento }}</td>
                  <td >@if($value->estado == 0){{"Inactivo"}}@else{{'Activo'}}@endif</td>
                  <td >@if($value->prestamo == 1){{"Si"}}@else{{'No'}}@endif</td>
                  <td>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{ route('equipo.edit', ['id' => $value->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                        Actualizar
                    </a>

                    <a href="{{ route('imprimir.barras_unico_equipo', ['id' => $value->id]) }}" target="_blank" class="btn btn-success col-md-8 col-xs-8 btn-margin">
                    Imprimir Codigo
                    </a>
                  </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>


  <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
  <script type="text/javascript">

  $(document).ready(function() {
    $('#example2').DataTable({
        'paging'      : true,
        'lengthChange': true,
        "scrollY"     : 1000,
        'searching'   : true,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false,
        buttons: [
          {
            extend: 'excelHtml5',
            footer: true,
            title: 'REPORTE DE EQUIPOS'
          },
          {
            extend: 'pdfHtml5',
            orientation: 'landscape',
            pageSize: 'TABLOID',
            footer: true,
            title: 'REPORTE DE EQUIPOS',
            customize: function(doc) {
              doc.styles.title = {
                color: 'black',
                fontSize: '16',
                alignment: 'center'
              }
            }
          }
        ],
      })

    });


 </script>
@endsection
