@extends('contable.acreedores.base')
@section('action-content')
<!-- Ventana modal editar -->

  <!-- Main content -->
  <section class="content">
    <div class="box" style=" background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Cruce de Valores Acreedores</h3>
            </div>

            <div class="col-md-2">
              <button type="button"  class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i aria-hidden="true"></i>Agregar cruce 
              </button>
            </div>
        </div>

      <!-- /.box-header -->
      <div class="box-body">
        <form method="POST" action="{{route('acreedores_search')}}">
          {{ csrf_field() }}
          @component('layouts.search', ['title' => 'Buscar'])
            @component('layouts.two-cols-search-row', ['items' => ['Identificacion', 'Nombre'],
            'oldVals' => [isset($searchingVals) ? $searchingVals['identificacion'] : '', isset($searchingVals) ? $searchingVals['nombre'] : '']])
            @endcomponent
            </br>
          @endcomponent

        </form>
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr>
                      <th >Identificacion</th>
                      <th >Nombre</th>
                      <th >Tipo Proveedor</th>
                      <th >Grupo</th>
                      <th >Estado</th>
                      <th >Accion</th>
                    </tr>
                  </thead>
                  <tbody>
                   
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite"></div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  
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
