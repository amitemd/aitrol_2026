@extends('contable.rh_configuracion_valores.base')
@section('action-content')

  
<style type="text/css">

    .boton_burbuja{
      color: white;
      border-radius: 3px;
      padding: 5px;
      height: 10%;

      margin: 2px;
      -moz-animation: 2s bote 1;
      animation: 2s bote 1;
      -webkit-transform: 2s bote 1;
    }

</style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
  <!-- Main content -->
  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item active" aria-current="page">Aporte y Salario</li>
      </ol>
    </nav>
    <div class="box">
        <div class="box-header">
          <div class="col-md-9">
            <h5><b>APORTES Y SALARIOS</b></h5>
          </div>
          <div class="col-md-1 text-right">
            <button onclick="location.href='{{route('configuracion_valores.create')}}'" class="btn btn-success btn-gray" >
              <i aria-hidden="true"></i>Agregar Tipo Valor
              </button>
          </div>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR</label>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
          <form method="POST" id="reporte_master" action="{{ route('config_valor.buscar') }}" >
            {{ csrf_field() }}
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="tipo">Tipo:</label>
                </div>
                <div class="form-group col-md-4 col-xs-10 container-4" style="padding-left: 15px;">
                  <select class="form-control" id="tipo" name="tipo">
                      <option value="">Seleccione...</option>
                      @foreach($tipo_aport as $value)
                        <option value="{{$value->id}}" @if(isset($searchingVals)) @if($value->id==$searchingVals['tipo']) selected @endif @endif>{{$value->descripcion}}</option>
                      @endforeach
                  </select>
                </div>
                <div class="col-xs-2">
                  <button type="submit" id="buscarvalor" class="btn btn-primary">
                      <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                  </button>
                </div>
          </form>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" >LISTADO</label>
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
                          <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                            <thead>
                              <tr class='well-dark'>
                                <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Tipo</th>
                                <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor</th>
                                <!--<th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Empresa</th>-->
                                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                                <th width="8%" class="" tabindex="0" aria-controls="example2" rowspan="5" colspan="5" aria-label="Codigo: activate to sort column ascending">Accion</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($lista_configuracion as $value)
                                @php  $tipo_aporte = Sis_medico\Ct_Rh_Tipo_Aporte::where('id', $value->tipo)->first();  @endphp
                                <tr  class="well">
                                  <td>@if(!is_null($tipo_aporte)) {{$tipo_aporte->descripcion}} @endif</td>
                                  <!-- <td>@if($value->tipo == '1') Aporte Personal @elseif($value->tipo =='2') Aporte Patronal @elseif($value->tipo =='3') Salario basico @elseif($value->tipo =='4') Fondo de Reserva IESS @elseif($value->tipo =='5') Aporte IESS Conyugue @endif</td> -->
                                  <td>@if(!is_null($value->valor)){{$value->valor}}@endif</td>              
                                  <td>@if($value->estado == '1') Activo @elseif($value->estado =='0') Anulada @endif</td>
                                  <td>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    @if($value->estado == '1')
                                    <a href="{{ route('configuracion_valores.editar', ['id' => $value->id]) }}" class="btn btn-success btn-gray">
                                        <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('configuracion_valores.anular', ['id' => $value->id]) }}" class="btn btn-danger btn-gray">
                                      <i class="glyphicon glyphicon-trash" aria-hidden="true"></i><!-- Eliminar -->
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
                          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($lista_configuracion->currentPage() - 1) * $lista_configuracion->perPage())}} / {{count($lista_configuracion) + (($lista_configuracion->currentPage() - 1) * $lista_configuracion->perPage())}} de {{$lista_configuracion->total()}} registros
                        </div>
                        </div>
                        <div class="col-sm-7">
                          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                            {{ $lista_configuracion->appends(Request::only(['tipo']))->links() }}
                          </div>
                        </div>
                      </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
    </div>
    <!-- /.box-body -->
  </section>
  <!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
  
  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : false,
      'info'        : false,
      'autoWidth'   : false
  })

  function confirmar(){
  
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
          )
          return true;
        }
      });
      return false;
    }

</script>
@endsection
