@extends('insumos.marca.base')
@section('action-content')

<section class="content">
  <div class="box" style="background-color: white;">

      <div class="col-sm-8">
        <h3 class="box-title">Lista de Marcas</h3>
      </div>
      <div class="col-sm-4">
        <button type="button" onclick="location.href='{{route('marca.create')}}'" class="btn btn-danger" style="margin-top: 5px; color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;"> 
                    <span style="font-family: 'Helvetica general';">Agregar Nueva Marca</span>
        </button>
      </div>

    <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{ route('marca.search') }}">
         {{ csrf_field() }}
         @component('layouts.search1', ['title' => 'Buscar'])
          @component('layouts.one-cols-search-row', ['items' => ['Nombre'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['nombre'] : '']])
          @endcomponent
        @endcomponent
      </form>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr >
                  <th >Nombre</th>
                  <th >Descripcion</th>
                  <th >Estado</th>
                  <th >Acción</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($marcas as $marca)
                  <tr >
                    <td> {{ $marca->nombre }}</td>
                    <td> {{ $marca->descripcion }}</td>
                    <td> {{$marca->estado == 0 ? 'INACTIVO' : 'ACTIVO'}}</td>
                    <td>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{ route('marca.edit', ['id' => $marca->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
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
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($marcas)}} de {{$marcas->total()}} registros</div>
          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $marcas->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

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