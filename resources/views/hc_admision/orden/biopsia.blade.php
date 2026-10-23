@extends('paciente.base')
@section('action-content')
  <style type="text/css">
    table{
      font-size: 12px;
    }
    .warning{
      background-color: #e08e0b !important;
    }
    .success{
      background-color: #00a65a !important;
    }
    .xinfo{
      background-color: #00c0ef ;
    }
    .navy{
      background-color: #001f3f !important;
    }
    .dropdown-menu > li > a{
      color: white !important;
    }
    .warning > li > a:hover {
      background-color: #d58512 !important;
    }
    .success > li > a:hover {
      background-color: #008d4c !important;
    }
    .xinfo > li > a:hover {
      background-color: #00acd6 !important;
    }
    .navy > li > a:hover {
      background-color: #23527c !important;
    }
    .table-bordered>tbody>tr>td{
      padding: 2px !important;
    }

  </style> 
<div class="modal fade" id="modal_ordenes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">
      </div>
  </div>
</div>
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Lista de Ordenes de Biopsia</h3>
        </div>
        <div class="col-sm-4">
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{route('ordenes_biopsia.orden_biopsia')}}">
         {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Buscar'])
          @component('layouts.two-cols-search-row', ['items' => ['Cédula', 'Apellidos', 'Nombres'],
          'oldVals' => [isset($searchingVals) ? $searchingVals['id'] : '', isset($searchingVals) ? $searchingVals['apellidos'] : '', isset($searchingVals) ? $searchingVals['nombres'] : '']])
          @endcomponent
          <div class="col-md-6">
            <div class="form-group">
                <label for="inputfecha_desde" class="col-sm-3 control-label">Fecha Desde:</label>
                <div class="col-sm-9">
                  <input value="{{isset($searchingVals) ? $searchingVals['fecha_desde']: ''}}" type="date" class="form-control" name="fecha_desde" id="inputfecha_desde">
                </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
                <label for="inputfecha_hasta" class="col-sm-3 control-label">Fecha Hasta:</label>
                <div class="col-sm-9">
                  <input value="{{isset($searchingVals) ? $searchingVals['fecha_hasta']: ''}}" type="date" class="form-control" name="fecha_hasta" id="inputfecha_hasta" >
                </div>
            </div>
          </div>
        @endcomponent
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12" style="padding-bottom: 90px;padding-right: 44px; ">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="5%" >Fecha Orden</th>
                <th width="5%" >Cédula</th>
                <th width="23%" >Nombres</th>
                <th width="15%" >Procedimiento</th>
                <th width="5%" >Cantidad de Muestras</th>
                <th width="15%" >Accion</th>
              </tr>
            </thead>
            <tbody>
            @php
              $contador = 0;
            @endphp
            @foreach ($ordenes as $value)
             @php
               $pro   = Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->hc_id_procedimiento)->get();
                $mas   = true;
                $texto = "";

                foreach ($pro as $value_pro) {
                    if ($mas == true) {
                        $texto = $texto . $value_pro->procedimiento->nombre;
                        $mas   = false;
                    } else {
                        $texto = $texto . ' + ' . $value_pro->procedimiento->nombre;
                    }
                }

                $lista_proced = $texto;
                $cantidad =  Sis_medico\Hc4_Biopsias::where('hc_id_procedimiento', $value->hc_id_procedimiento)->count();
                $contador += $cantidad;
             @endphp
                <tr >
                  <td>{{substr($value->created_at, 0, 10)}}</td>
                  <td>{{ $value->pacientes->id}}</td>
                  <td>{{$value->pacientes->nombre1}} @if($value->pacientes->nombre2!='(N/A)'){{$value->pacientes->nombre2}}@endif {{$value->pacientes->apellido1}} @if($value->pacientes->apellido2!='(N/A)'){{$value->pacientes->apellido2}}@endif</td>
                  <td>{{$lista_proced}}</td>
                  <td style="text-align: center;">{{$cantidad}}</td>
                  <td>
                    <div class="btn-group-vertical">
                      <a type="button" class="btn btn-warning btn-xs btn-block" target="_blank" href="{{ route('imprimir.biopsias_hc4', ['id' => $value->hc_id_procedimiento,'id_hcid' => $value->hcid,'id_doct' => $value->id_doctor]) }}">Ver Orden</a>
                      <a type="button" class="btn btn-info btn-xs btn-block" target="_blank" href="{{ route('formulario.013B', ['id' => $value->hc_id_procedimiento]) }}">Descargar Resultado</a>
                      <a type="button" class="btn btn-success btn-xs btn-block" data-remote="{{ route('orden_proc.modal_biopsia', ['id_orden' => $value->hc_id_procedimiento]) }}" data-toggle="modal" data-target="#modal_ordenes" >Ingresar Resultado</a>
                      <!--  href="{{route('pdf_hospital.informe_bioxias_crear', ['id'=>$value->hc_id_procedimiento])}}"-->
                    </div>                                   
                  </td>
              </tr>
            @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="4"><b>Total de Ordenes</b></td>
                <td colspan="2">{{$ordenes->count()}}</td>
              </tr>
              <tr>
                <td colspan="4"><b>Total de Muestras</b></td>
                <td colspan="2">{{$contador}}</td>
              </tr>

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
  </div>


<script type="text/javascript">
  $('#modal_ordenes').on('hidden.bs.modal', function(){
            $(this).removeData('bs.modal');
            //tinymce.remove('.text_input');
    });
  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,

    });
</script>

@endsection
