@extends('contable.configuracion.base')
@section('action-content')
<!-- Ventana modal editar -->

  <!-- Main content -->
  <section class="content">
    <div class="box" style=" background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Listado de Configuraciones</h3>
            </div>
        </div>

      <!-- /.box-header -->
      <div class="box-body">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                <h3>Configuraciones de Consultas de Agenda</h3>

                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr >
                      <th >Nombre</th>
                      <th >Cuenta</th>
                      <th >Nombre de la Cuenta</th>
                      <th >Accion</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($agenda as $value)
                      <tr>
                        <td>{{$value->nombre}}</td>
                        <td>{{$value->id_plan}}</td>
                        <td>{{$value->cuenta->nombre}}</td>
                        <td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <a href="{{ route('configuraciones.editar', ['id' => $value->id]) }}" class="btn btn-warning col-md-6 col-xs-6 btn-margin">
                            Actualizar
                            </a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
                <h3>Otras Configuraciones</h3>
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr >
                      <th >Nombre</th>
                      <th >Cuenta</th>
                      <th >Nombre de la Cuenta</th>
                      <th >Accion</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($configuracion as $value)
                      <tr>
                        <td>{{$value->nombre}}</td>
                        <td>{{$value->id_plan}}</td>
                        <td>{{$value->cuenta->nombre}}</td>
                        <td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <a href="{{ route('configuraciones.editar', ['id' => $value->id]) }}" class="btn btn-warning col-md-6 col-xs-6 btn-margin">
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
