@extends('laboratorio.agrupada.base')
@section('action-content')
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-md-6">
          <h3 class="box-title">@lang('laboratorio.factura_agrupada') - @lang('laboratorio.privada')</h3>
        </div>
      </div>
    </div>
    <form method="POST" action="{{route('factura_agrupada.editar_privadas_buscador', ['id'=> $id_cab])}} ">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <div class="form-group col-md-3 ">
        <div class="row">
          <div class="form-group col-md-12 ">
            <label for="cedula" class="col-md-4 control-label">@lang('laboratorio.cedula'):</label>
            <div class="col-md-8">
              <input id="cedula" maxlength="13" type="text" class="form-control input-sm" name="cedula" placeholder="@lang('laboratorio.cedula')" value="{{$cedula}}">
            </div>
          </div>
        </div>
      </div>

      <div class="form-group col-md-3 ">
        <div class="row">
          <div class="form-group col-md-12 ">
            <label for="id_orden" class="col-md-4 control-label">{{trans('laboratorio.orden')}}:</label>
            <div class="col-md-8">
              <input id="id_orden" maxlength="11" type="number" class="form-control input-sm" name="id_orden" value="{{$id_orden}}">
            </div>
          </div>
        </div>
      </div>

      <div class="form-group col-md-3 ">
        <div class="row">
          <div class="form-group col-md-12 ">
            <label for="usuario" class="col-md-4 control-label">@lang('laboratorio.nombres_apellidos'):</label>
            <div class="col-md-8">
              <input id="usuario" maxlength="100" type="text" class="form-control input-sm" name="nombre" value="{{$nombre}}" placeholder="@lang('laboratorio.nombres_apellidos')">
            </div>
          </div>
        </div>
      </div>

      <div class="form-group col-md-2 ">
        <div class="col-md-7">
          <button id="buscar" type="submit" class="btn btn-primary"> <span class="glyphicon glyphicon-search"> @lang('laboratorio.buscar')</span></button>
        </div>
      </div>
    </form>

    <div class="box-body">
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12" style="min-height: 210px;">
            <table id="example5" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
              <thead>
                <input type="hidden" name="id_cab" value="{{$id_cab}}">
                <tr>
                  <th>Nº</th>
                  <th>{{trans('laboratorio.orden')}}</th>
                  <th>{{trans('laboratorio.paciente')}}</th>
                  <th>{{trans('laboratorio.paciente')}}</th>
                  <th>{{trans('laboratorio.seguro')}}</th>
                  <th>{{trans('laboratorio.cantidad')}}</th>
                  <th>{{trans('laboratorio.total')}}</th>
                  <th>{{trans('laboratorio.fechaOrden')}}</th>
                  <th>{{trans('laboratorio.accion')}}</th>
                </tr>
              </thead>
              <tbody>
                @php
                $x=0;
                $total =0;
                @endphp
                @if(!is_null($detalle))
                @foreach($ordenes as $orden)
                @php
                $examen_orden = Sis_medico\Examen_Orden::where('id',$orden->id_examen_orden)->get();
                @endphp
                @foreach($examen_orden as $ord)
                <?php $total = $total + $ord->total_valor; ?>
                <tr>
                  @php $x++; @endphp
                  <td>{{$x}}</td>
                  <td>{{$ord->id}}</td>
                  <td>{{$ord->id_paciente}}</td>
                  <td>{{$ord->paciente->apellido1}} {{$ord->paciente->apellido2}} {{$ord->paciente->nombre1}} {{$ord->paciente->nombre2}}</td>
                  <td>{{$ord->seguro->nombre}}</td>
                  <td>{{$ord->cantidad}}</td>
                  <td>{{$ord->total_valor}}</td>
                  <td>{{substr($ord->fecha_orden,0,10)}}</td>
                  <td><a onclick="confEliminacion('{{$ord->id}}','{{$id_cab}}');" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a></td>
                </tr>
                @endforeach
                @endforeach
                @if($x > 0)
                <tr>
                  <td style="display: none;"><?php echo count($ordenes) + 1; ?> </td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td style="font-weight: bold;">@lang('agenda.total')</td>
                  <td style="font-weight: bold;"><?php echo $total; ?> </td>
                  <td></td>
                  <td></td>
                </tr>
                @endif
                @endif

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
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.js') }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.all.min.js') }}"></script>

<script type="text/javascript">
  $('#example5').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': true,
    'info': false,
    'autoWidth': false,
  });

  function eliminar_orden(id_orden, id_cab) {
    $.ajax({
      type: 'get',
      url: "{{url('humanlabs/eliminar_orden_privada')}}/" + id_orden + '/' + id_cab,
      datatype: 'json',
      success: function(data) {
        if (data == 'ok') {
          //alertasPersonalizadas('success', 'Exito', 'Se elimino la factura');
          setTimeout(function() {
            location.reload();
          }, 1500);
        }
      },
      error: function(data) {
        alertasPersonalizadas('error', 'Error...!', 'Ocurrio un error');
      }
    });
  }

  function alertasPersonalizadas(icon, title, text) {
    Swal.fire({
      icon: `${icon}`,
      title: `${title}`,
      text: `${text}`,
      showConfirmButton: false,
      timer: 1500
    })
  }

  function confEliminacion(id_orden, id_cab) {
    Swal.fire({
      title: `{{trans('laboratorio.estasegurodeeliminar')}}`,
      text: `{{trans('laboratorio.estaaccionirreversible')}}`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        eliminar_orden(id_orden, id_cab);
      }
    })
  }
</script>
@endsection
