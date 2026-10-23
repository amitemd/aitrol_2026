@extends('activosfijos.mantenimientos.tipo.base')
@section('action-content')
<!-- Ventana modal editar -->

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
  <!-- Main content -->
  <section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Activos Fijos</a></li>
      <li class="breadcrumb-item"><a href="#">Mantenimientos</a></li>
      <li class="breadcrumb-item active">Tipos de Activos Fijos</li>
    </ol>
  </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
              <h3 class="box-title">Tipos de Activos Fijos</h3>
            </div>
            <div class="col-md-1 text-right">
              <a type="button" href="{{route('afTipo.create')}}" class="btn btn-success btn-gray">
                   <i aria-hidden="true"></i>Crear Tipo
              </a>
            </div>
        </div>

        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR DE TIPOS DE ACTIVOS FIJOS</label>
          </div>
        </div>
      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{ route('activofjo.tipo.search') }}" >
        {{ csrf_field() }}
          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="buscar_nombre">Nombre: </label>
          </div>
          <div class="form-group col-md-3 col-xs-10 container-4">
            <input class="form-control" type="text" id="nombre" name="nombre" value="@if(isset($searchingVals)){{$searchingVals['nombre']}}@endif" placeholder="Ingrese nombre del tipo..." />
          </div>
          <div class="col-xs-offset-2 col-xs-2">
            <button type="submit" id="buscarTipo" class="btn btn-primary">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
            </button>
          </div>
        </form>
      </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" >LISTADO DE TIPO DE ACTIVOS</label>
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
                            <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">ID</th>
                            <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Codigo</th>
                            <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombre</th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cta Mayor</th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cta Depreciación acumulada</th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cta Gastos</th>
                            <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tasa</th>
                            <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Vida Util</th>
                            <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acciones</th> 
                          </tr>
                        </thead>
                        <tbody id="tbl_detalles" name="tbl_detalles"> 
                          @foreach (@$tipos as $value)
                            <tr class="well"> 
                              <td >{{ $value->id}}</td>
                              <td >{{ $value->codigo}}</td>
                              <td >{{ $value->nombre}}</td>
                              <td >@if(isset($value->cuenta_mayor)) {{ $value->cuenta_mayor->nombre}} @endif</td>
                              <td >@if(isset($value->cuenta_depreciacion)) {{ $value->cuenta_depreciacion->nombre}} @endif</td>
                              <td >@if(isset($value->cuenta_gastos)) {{ $value->cuenta_gastos->nombre}} @endif</td>
                              <td >{{ $value->tasa}}</td>
                              <td >{{ $value->vidautil}}</td>
                              <td >
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <a href="{{ route('afTipo.edit', ['id' => $value->id]) }}" class="btn btn-success btn-gray">
                                  <i class="glyphicon glyphicon-edit" aria-hidden="true"></i><!--&nbsp;&nbsp; Revisar Nota-->
                                </a>

                                @if($value->estado == '1')
                                <form action="{{ route('afTipo.destroy', ['id' => $value->id]) }}" id="frm-eliminar-{{ $value['id'] }}" method="post">
                                    <button class="btn btn-danger btn-gray" onclick="confirmar({{ $value['id'] }})" type="button"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i> </button>
                                    {!! method_field('delete') !!}
                                    {!! csrf_field() !!}
                                </form>
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
                      <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($tipos->currentPage() - 1) * $tipos->perPage())}} / {{count($tipos) + (($tipos->currentPage() - 1) * $tipos->perPage())}} de {{$tipos->total()}} registros</div>
                    </div>
                    <div class="col-sm-7">
                      <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                        {{ $tipos->appends(Request::only(['codigo', 'nombre']))->links() }}
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">
   
    $(document).ready(function(){
      $('#estado').val({{ @$estado }});
      $('#tipo').val('{{ @$tipo }}');

      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : false,
        'info'        : false,
        'autoWidth'   : false
      });
      $('#fecha_desde').datetimepicker({
        format: 'YYYY-MM-DD',
      });
      $('#fecha_hasta').datetimepicker({
        format: 'YYYY-MM-DD',
      });

    });

    function actualizar(obj){
      $.post("{{route('conciliacionbancaria.actualizar')}}",
        {
            id:       obj.value, 
            _token:   "{{ csrf_token() }}"
        }
      ); 
    }

    function actualizarmasivo(obj, accion=""){
      $.post("{{route('conciliacionbancaria.actualizarmasivo')}}",
        {
            id:       obj.value,
            accion:   accion,
            _token:   "{{ csrf_token() }}"
        }
      ); 
    }

    function seleccionar_todo(checked){
      var miTabla = document.getElementById('tbl_detalles');
      for (i=0; i<miTabla.rows.length; i++)
      {	
        miTabla.rows[i].getElementsByTagName("input")[0].checked = checked;
        actualizarmasivo(miTabla.rows[i].getElementsByTagName("input")[0], checked);
      } 
    }
 
function confirmar(id){
      // confirm('Are you sure?');
      Swal.fire({
        title: 'Alerta!',
        text: "Esta seguro que desea eliminar el registro?",
        // icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, borrar!'
      }).then((result) => {
        if (result.value) {
          Swal.fire(
            'Eliminado!',
            'El registro ha sido eliminado.',
            'success'
          ); 
          $( "#frm-eliminar-"+id).submit();
          return true;
        }else{
          event.preventDefault();
          return false; 
        }
      });
      
    }


</script>
@endsection
