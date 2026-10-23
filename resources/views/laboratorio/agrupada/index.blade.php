@extends('laboratorio.agrupada.base')
@section('action-content')
@php
$anios = [2020,2021,2022,2023,2024];
$meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
@endphp


<div class="modal fade fullscreen-modal" id="modal_registro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="agrupada_registro">
    </div>
  </div>
</div>
<div class="modal fade fullscreen-modal" id="modal_editar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="agrupada_editar">
    </div>
  </div>
</div>

<div class="modal fade fullscreen-modal" id="modal_recalcular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="agrupada_recalcular">
    </div>
  </div>
</div>

<div class="modal fade fullscreen-modal" id="modal_espera" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="espera">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrar_espera"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        <h4 class="modal-title" id="myModalLabel" style="text-align: center;">{{trans('laboratorio.procesando')}}</h4>
      </div>
      <div class="modal-body">
        <center>
          <div class="col-md-6 col-md-offset-2">
            <img id="imagen_espera" src="{{asset('/images/espera.gif')}}" style="width: 30%;">

          </div>
        </center>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('laboratorio.cerrar')}}</button>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-md-6">
          <h3 class="box-title">{{trans('laboratorio.factura_agrupada')}}</h3>
        </div>
        <div class="col-md-6" style="text-align: left;">
          <a id="btn_registro" class="btn btn-success" onclick="registro();">{{trans('laboratorio.crearRegistros')}}</a>
        </div>
      </div>
      <form action="{{route('facturalabs.buscador')}}" method="POST">
        {{ csrf_field() }}
        <div class="col-md-12" style="margin-top: 3px;">
          <div class="row">
            <div class="col-md-1">
              <label for="">{{trans('laboratorio.anio')}}</label>
            </div>
            <div class="col-md-2">
              <select name="anio" id="anio" class="form-control">
                <option value="">{{trans('laboratorio.seleccione')}}</option>
                @foreach($anios as $val)
                <option @if($val==$anio) selected @endif value="{{$val}}">{{$val}}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-1">
              <label for="">{{trans('laboratorio.mes')}}</label>
            </div>
            <div class="col-md-2">
              <select name="mes" id="mes" class="form-control">
                <option value="">{{trans('laboratorio.seleccione')}}</option>
                @foreach($meses as $i=>$val)
                <option @if($mes==$i) selected @endif value="{{$i}}">{{$val}}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <input type="submit" class="btn btn-primary">
            </div>
          </div>
        </div>

      </form>
    </div>

    <div class="box-body">
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12" style="min-height: 210px;">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
              <thead>
                <tr>
                  <th width="10%">{{trans('laboratorio.anio')}}</th>
                  <th width="10%">{{trans('laboratorio.mes')}}</th>
                  <th width="35%">{{trans('laboratorio.cliente')}}</th>
                  <th width="10%">{{trans('laboratorio.totalPublicas')}}</th>
                  <th width="10%">{{trans('laboratorio.totalPrivadas')}}</th>
                  <th width="25%">{{trans('laboratorio.accion')}}</th>
                  <th width="10%">{{trans('laboratorio.envio')}}</th>
                </tr>
              </thead>
              <tbody>
                @php
                @endphp

                @foreach($factura_cab as $value)
                @php
                $detalle = $value->detalles;
                @endphp

                <tr>
                  <td>{{$value->anio}}</td>
                  <td>@if($value->mes == 1) @lang('laboratorio.enero') @elseif($value->mes == 2) @lang('laboratorio.febrero') @elseif($value->mes == 3) @lang('laboratorio.marzo') @elseif($value->mes == 4) @lang('laboratorio.abril') @elseif($value->mes == 5) @lang('laboratorio.mayo') @elseif($value->mes == 6) @lang('laboratorio.junio') @elseif($value->mes == 7) @lang('laboratorio.julio') @elseif($value->mes == 8) @lang('laboratorio.agosto') @elseif($value->mes == 9) @lang('laboratorio.septiembre') @elseif($value->mes == 10) @lang('laboratorio.octubre') @elseif($value->mes == 11) @lang('laboratorio.noviembre') @elseif($value->mes == 12) @lang('laboratorio.diciembre') @endif</td>
                  <td>{{$value->cedula_factura}} - {{$value->nombre_factura}} - {{$value->direccion_factura}} - {{$value->telefono_factura}} - {{$value->ciudad_factura}} - {{$value->email_factura}} </td>
                  <!--Total de publicas-->
                  <td>
                    @for($cont = 0; $cont < count($detalle) ; $cont++) @if($detalle[$cont]->pub_priv==0)
                      <p><?php echo $detalle[$cont]->total; ?> </p>
                      @endif
                      @endfor
                  </td>
                  <!--total Privadas-->
                  <td>
                    @for($cont = 0; $cont < count($detalle) ; $cont++) @if($detalle[$cont]->pub_priv==1)
                      <p><?php echo $detalle[$cont]->total; ?> </p>
                      @endif
                      @endfor
                  </td>
                  <!---->
                  <td>
                    <a href="{{route('factura_agrupada.index_privadas',['id_cab' => $value->id])}}" class="btn btn-default btn-xs"><i class="fa fa-plus"></i>{{trans('laboratorio.agregarprivadas-')}}</a>
                    <a href="{{route('factura_agrupada.editar_privadas',['id_cab' => $value->id])}}" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i>{{trans('laboratorio.editarprivadas-')}}</a><br>
                    <a onclick="agregar_publicas('{{$value->id}}')" class="btn btn-default btn-xs"><i class="fa fa-plus"></i> {{trans('laboratorio.agregarpublicas-')}}</a>
                    <a href="{{route('factura_agrupada.editar_publicas',['id_cab' => $value->id])}}" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i>{{trans('laboratorio.editarPublicas-')}}</a>
                  </td>
                  <td>@if($value->fecha_envio != null) <a href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->comprobante, 'id_empresa' => '0993075000001', 'tipo' => 'pdf']) }}" class="btn btn-info btn-xs">RIDE</a> @else <a onclick="recalcular('{{$value->id}}');" class="btn btn-danger btn-xs">SRI</a> @endif
                    <a class="btn btn-primary btn-xs" href="{{route('factura_agrupada.excel_detalle_orden',['id_cab'=>$value->id])}}"> {{trans('laboratorio.detalle-')}}</a>
                    <a class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal_editar" href="{{route('factura_agrupada.modal_edit_cliente',['id_cab'=>$value->id])}}"> {{trans('laboratorio.editar-')}}</a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>


<script type="text/javascript">
  $('#example2').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': true,
    'info': false,
    'autoWidth': false,
    'order': [
      [1, "desc"]
    ]
  })

  $('#modal_registro').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');

  });
  $('#modal_editar').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
    location.reload();
  });

  $('#agrupada_registro').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');

  });

  function registro() {
    $.ajax({
      type: 'get',
      url: "{{url('humanlabs/modal_registro')}}",
      datatype: 'json',
      success: function(data) {
        $('#agrupada_registro').empty().html(data);
        $('#modal_registro').modal();

      },
      error: function(data) {
        //console.log(data);
      }
    });
  }

  function editar() {
    $.ajax({
      type: 'get',
      url: "{{url('humanlabs/modal_editar')}}",
      datatype: 'json',
      success: function(data) {
        $('#modal_editar').modal();
        if (data == 'ok') {
          location.reload();
        }
      },
      error: function(data) {
        //console.log(data);
      }
    });

  }

  function recalcular(id_cab) {
    $.ajax({
      type: 'get',
      url: "{{url('humanlabs/recalcular_agrupada')}}/" + id_cab,
      datatype: 'json',
      beforeSend: function() {
        // setting a timeout
        $('#modal_espera').modal();
      },
      success: function(data) {
        $('#cerrar_espera').click();
        $('#agrupada_recalcular').empty().html(data);
        $('#modal_recalcular').modal();
      },
      error: function(data) {
        //console.log(data);
      }
    });

  }

  function agregar_publicas(id_cab) {
    $.ajax({
      type: 'get',
      url: "{{url('humanlabs/factura_agrupada/carga_publicas')}}/" + id_cab,
      datatype: 'json',
      beforeSend: function() {
        // setting a timeout
        $('#modal_espera').modal();
      },
      success: function(data) {
        if (data == 'ok') {
          $('#cerrar_espera').click();
          alert('Ordenes publicas cargadas');
          location.reload();

        }
      },
      error: function(data) {
        //console.log(data);
        //$('#cerrar_espera').click();

      }
    });

  }
</script>

@endsection