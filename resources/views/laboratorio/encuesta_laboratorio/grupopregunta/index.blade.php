@extends('laboratorio.grupopreguntalabs.base')
@section('action-content')
<!-- Ventana modal editar -->
                      <div class="modal fade" id="seguimiento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content">

                          </div>
                        </div>  
                      </div>

    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
      <div class="col-md-12">
        <div class="col-md-8">
          <h3 class="box-title">Listado de Grupos de Preguntas</h3>
        </div>
        <div class="col-md-3 ">
          <a class="btn btn-primary" href="{{ route('grupopreguntaslabs.create') }}" style="width: 100%;">Nuevo Grupos de Preguntas</a>
        </div>
      </div>
    </div>
  </div>  
  <!-- /.box-header -->
  <div class="box-body">
      <form method="POST" action="{{ route('grupopreguntaslabs.search') }}">
         {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Buscar'])
          @component('layouts.two-cols-search-row', ['items' => ['Nombre'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['nombre'] : '']])
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
                <th >Nombre</th>
                <th >Descripcion</th>
                <th >Tipo de Calificacion</th>
                <th >Acción</th>
              </tr>
            </thead>
            <tbody>  

            @foreach ($preguntas as $value)
                <tr >
                  <td >{{ $value->nombre }}</td>
                  <td >{{ $value->descripcion }}</td>
                  <td >@if($value->tipo_calificacion == 1)Texto @endif @if($value->tipo_calificacion == 2)Tiempo @endif @if($value->tipo_calificacion == 3)Reaccion @endif</td>
                  <!--<td>{{ $value->precio_compra }}</td>-->
                  <td>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('grupopreguntas.edit', ['id' => $value->id]) }}" class="btn btn-warning col-md-6 col-xs-6 btn-margin">
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($preguntas)}} de {{$preguntas->total()}} registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $preguntas->links() }}
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
    $('#seguimiento').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

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