

@extends('reportes.hospitalizados.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
      <form method="POST" action="{{route('hospitalizados.reporte')}}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="col-md-3 {{ $errors->has('fecha') ? ' has-error' : '' }} " >
          <label class="col-md-4 control-label">Fecha Desde</label>
          <div class="input-group date col-md-8">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" value="{{$fecha}} " name="fecha" class="form-control" id="fecha" required >
          </div>
        </div>
        <div class="col-md-3 {{ $errors->has('fecha_hasta') ? ' has-error' : '' }} " >
          <label class="col-md-4 control-label">Fecha Hasta</label>
          <div class="input-group date col-md-8">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" value="{{$fecha_hasta}} " name="fecha_hasta" class="form-control" id="fecha_hasta" required >
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-3">
            <button type="submit" id="buscar3" class="btn btn-primary">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
            </button>
          </div>
        </div>
      </form>

        <form method="POST" action="{{route('hospitalizados.excel')}}">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" value="{{$fecha}} " name="fecha" class="form-control" id="fecha2" >
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary">
              <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Exportar a excel
            </button>
          </div>
        </form>
    </div>
      <div class="col-sm-12 text-center">
        <h2>Hospitalizados</h2>
        <h4>FECHA: {{substr($fecha, 8, 2)}} de <?php $mes = substr($fecha, 5, 2);if ($mes == 01) {echo "ENERO";}if ($mes == 02) {echo "FEBRERO";}if ($mes == 03) {echo "MARZO";}if ($mes == 04) {echo "ABRIL";}if ($mes == 05) {echo "MAYO";}if ($mes == 06) {echo "JUNIO";}if ($mes == 07) {echo "JULIO";}if ($mes == '08') {echo "AGOSTO";}if ($mes == '09') {echo "SEPTIEMBRE";}if ($mes == '10') {echo "OCTUBRE";}if ($mes == '11') {echo "NOVIEMBRE";}if ($mes == '12') {echo "DICIEMBRE";}?> DEL {{substr($fecha, 0, 4)}}</h4>
      </div>
    </div>
    <div class="box-body">
      <div class="row">
      <div class="col-sm-12">

      </div>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="table-responsive col-md-12">
      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
        <thead>
          <tr >
            <!--th >id_agenda</th-->
            <th >Fecha Ingreso</th>
            <th >Apellidos</th>
            <th >Nombres</th>
            <th >Cédula</th>
            <th >Doctor</th>
            <th >Seguro</th>
            <th >Fecha Alta</th>
          </tr>
        </thead>
        <tbody >
          @php $vid = ""; @endphp
          @foreach($hospitalizados as $value)
            @php $texto=explode(" ",$value->campos); @endphp
            @if($vid!=$value->id_agenda)
              @php $vid=$value->id_agenda; @endphp
              <tr >
                <!--td>{{ $value->id_agenda }} {{$vid}}</td-->
                <td>{{substr($value->fechaini,0,10)}}</td>
                <td>{{$value->papellido1}} {{$value->papellido2}}</td>
                <td>{{$value->pnombre1}} {{$value->pnombre2}}</td>
                <td>{{$value->pid}}</td>
                <td>{{$value->d1nombre1}} {{$value->d1apellido1}}</td>
                <td>@if(count($texto)>2){{substr($texto['2'],9,20)}}@else {{$value->nombre_seguro}} @endif</td>
                <td>@if($value->estado=='2'){{substr($value->fechafin,0,10)}}@else @endif</td>
              </tr>
            @endif
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
      </div>
    </div>
</div>
</section>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>


<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">

$('#fecha').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075

        });

$('#fecha_hasta').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075

        });

$('#fecha').on('dp.change', function(e){

      buscar();
  });

$('#fecha_hasta').on('dp.change', function(e){

      buscar();
  })

function buscar()
{
  var obj = document.getElementById("buscar3");
  obj.click();
}

$('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
</script>



@endsection