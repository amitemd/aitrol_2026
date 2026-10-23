@extends('contable.debito_bancario.base')
@section('action-content')
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('Kconciliacion.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('Kconciliacion.banco')}}</a></li>
      <li class="breadcrumb-item active">{{trans('Kconciliacion.ConciliacionBancaria')}}</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <h3 class="box-title">{{trans('Kconciliacion.ConciliarCuentaBancaria')}}</h3>
      </div>
    </div>

    <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="row">
          <div class="table-responsive col-md-12">
            <div class="row" style="background: indianred;">
              <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">{{trans('Kconciliacion.PendientesdeConciliar')}}</label>
              </div>
            </div>
            <table id="tableP" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr>

                  <th width="5%">{{trans('Kconciliacion.Fecha')}}</th>
                  <th width="5%">{{trans('Kconciliacion.FechaBanco')}}</th>
                  <th width="10%">{{trans('Kconciliacion.tipo')}}</th>
                  <th width="10%">Id Asiento</th>
                  <th width="35%">{{trans('Kconciliacion.Detalle')}}</th>
                  <th width="10%">{{trans('Kconciliacion.Valor')}}</th>
                  <th width="5%">{{trans('Kconciliacion.Numero')}}</th>
                  <th width="5%">{{trans('Kconciliacion.Cheque')}}</th>
                  <th width="35%">{{trans('Kconciliacion.Beneficiario')}}</th>
                  <th width="5%">{{trans('Kconciliacion.Accion')}}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($pendientes as $pend)

                <tr>

                  <td>{{$pend->fecha}}
                    <input type="hidden" name="fecha_b[]" id="fecha_b{{ $pend['id_concilia'] }}" value="{{$pend['fecha']}}">
                  </td>
                  <td>{{$pend->fecha}}</td>
                  <td>{{$pend->tipo}}
                    <input type="hidden" name="tip[]" class="form-control tipoc " id="tip{{ $pend['id_concilia'] }}" value="@if(isset($pend['tipo'])){{$pend['tipo']}}@endif">
                  </td>
                  <td>
                    <a href="{{ url('contable/contabilidad/libro/edit') }}/{{$pend->id_asiento}}" target="_blank">{{$pend->id_asiento}}</a>
                    
                    <input type="hidden" name="id_asiento[]" class="form-control " id="id_asiento{{ $pend['id_concilia'] }}" value="@if(isset($pend['idasiento'])){{$pend['idasiento']}}@endif">
                  </td>
                  <td>{{$pend->detalle}}
                    <input type="hidden" name="det[]" id="det{{ $pend['id_concilia'] }}" value="{{$pend['detalle']}}">
                  </td>
                  <td>{{$pend->valor}}
                    <input type="hidden" name="val" class="form-control valorc {{$pend['tipo']}}" id="val" value="@if(isset($pend['valor'])){{$pend['valor']}}@endif">
                    <input type="hidden" name="valor_con[]" id="valor_con{{$pend['id_concilia'] }}" value="{{$pend['valor']}}">
                  </td>
                  <td>{{$pend->secuencia}}
                    <input type="hidden" name="numsec[]" id="numsec{{ $pend['id_concilia'] }}" value="{{$pend['numero']}}">
                  </td>
                  <td>{{$pend->cheque}}
                    <input type="hidden" name="num_ch[]" id="num_ch{{ $pend['id_concilia'] }}" value="{{$pend['numcheque']}}">
                  </td>
                  <td>{{$pend->beneficiario}}
                    <input type="hidden" name="benef[]" id="benef{{ $pend['id_concilia'] }}" value="{{$pend['beneficiario']}}">
                  </td>
                  <td>
                    <a type="button" id="btnAnular" name="btnAnular" class="btn btn-danger btn-xs" href="javascript:anular({{$pend->id}});"><i class="fa fa-trash"></i> {{trans('kconciliacion.anular')}}</a>
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
<script type="text/javascript">
  $(document).ready(function() {

    $('#tableP').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false,
    });
  });

  function anular(id) {
    Swal.fire({
      title: `{{trans('kconciliacion.cancelarConciliacion')}}`,
      text: "{{trans('contableM.norevertiraccion')}}",
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: `{{trans('kconciliacion.si')}}`,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'get',
          url: `{{url('contable/banco/conciliacionbancaria/anular_conciliacion_bancaria_pendiente/${id}')}}`,
          datatype: 'json',
          data: {
            'id': id,
          },
          success: function(data) {
            alertas("success", "{{trans('kconciliacion.exitoso')}}", "{{trans('kconciliacion.anulacionExitosa')}}");
            setTimeout(function() {
              location.reload();
            }, 1500)

          },
          error: function(data) {
            console.log(data);
          }
        });
      }
    });
  }
  function alertas(icon, title, msj) {
    Swal.fire({
      icon: icon,
      title: title,
      html: msj
    });
  }
</script>
@endsection