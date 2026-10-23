@extends('laboratorio.agrupada.base')
@section('action-content')
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-md-6">
          <h3 class="box-title">{{trans('laboratorio.factura_agrupada')}}</h3>
        </div>
      </div>
      <form method="POST" action="{{route('factura_agrupada.index_privadas_buscador', ['id'=> $id_cab])}} ">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group col-md-3 ">
          <div class="row">
            <div class="form-group col-md-12 ">
              <label for="cedula" class="col-md-4 control-label">{{trans('laboratorio.cedula')}}:</label>
              <div class="col-md-8">
                <input id="cedula" maxlength="13" type="text" class="form-control input-sm" name="cedula" placeholder="Cedula" value="{{$cedula}}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-3 ">
          <div class="row">
            <div class="form-group col-md-12 ">
              <label for="id_orden" class="col-md-4 control-label">{{trans('laboratorio.orden')}}:</label>
              <div class="col-md-8">
                <input id="id_orden" maxlength="11" type="number" class="form-control input-sm" name="id_orden" value="{{$id_orden}}" >
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-3 ">
          <div class="row">
            <div class="form-group col-md-12 ">
              <label for="usuario" class="col-md-4 control-label">{{trans('laboratorio.paciente')}}:</label>
              <div class="col-md-8">
                <input id="usuario" maxlength="100" type="text" class="form-control input-sm" name="nombre" placeholder="Nombres y Apellidos" value="{{$nombre}}">
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-2 ">
          <div class="col-md-7">
            <button id="buscar" type="submit" class="btn btn-primary"> <span class="glyphicon glyphicon-search"> {{trans('laboratorio.buscar')}}</span></button>
          </div>
        </div>
      </form>

      @if(count($ordenes)>0)
      <div class="col-md-12" style="text-align:end">
        <div class="">
          <button onclick="agregarTodo()" class="btn btn-success"> <span class="glyphicon glyphicon-ok-sign"> {{trans('laboratorio.agregarTodo')}}</span></button>
        </div>
      </div>
      @endif
      <div class="box-body" id="index_privada">
        <form id="frm_privada">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" id="id_cab" name="id_cab" value="{{$id_cab}}">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12" style="min-height: 210px;">
                <table id="example6" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
                  <thead>

                    <tr>
                      <th>Nº</th>
                      <th>{{trans('laboratorio.orden')}}</th>
                      <th>{{trans('laboratorio.cedula')}}</th>
                      <th>{{trans('laboratorio.paciente')}}</th>
                      <th>{{trans('laboratorio.seguro')}}</th>
                      <th>{{trans('laboratorio.cantidad')}}</th>
                      <th>{{trans('laboratorio.total')}}</th>
                      <th>{{trans('laboratorio.formaPago')}}</th>
                      <th>{{trans('laboratorio.fechaOrden')}}</th>
                      <th>{{trans('laboratorio.accion')}}</th>
                    </tr>
                  </thead>
                  <tbody id="body_tabla">
                    @php

                    $x=0;
                    $total=0;

                    @endphp

                    @foreach($ordenes as $orden)
                    @php
                    $total = $total + $orden->total_valor;
                    $forma_pago = Sis_medico\Examen_Detalle_Forma_Pago::where('id_examen_orden', $orden->id)->get();
                    //echo ($forma_pago);
                    @endphp
                    <tr>
                      @php $x++; @endphp
                      <td>{{$x}}</td>
                      <td>{{$orden->id}}</td>
                      <td>{{$orden->id_paciente}}</td>
                      <td>{{$orden->paciente->apellido1}} {{$orden->paciente->apellido2}} {{$orden->paciente->nombre1}} {{$orden->paciente->nombre2}}</td>
                      <td>{{$orden->seguro->nombre}}</td>
                      <td>{{$orden->cantidad}}</td>
                      <td>{{$orden->total_valor}}</td>
                      <td>@foreach($forma_pago as $fp) {{$fp->tipo_pago->nombre}} @endforeach</td>
                      <td>{{$orden->fecha_orden}}</td>
                      <td><a id="prueba<?php echo $x; ?>" onclick="guardar_det('{{$orden->id}}');" class="btn btn-success btn-xs">{{trans('laboratorio.agregar')}}</a></td>
                    </tr>
                    @endforeach
                    @if($x > 0)
                    <tr>
                      <td style="display: none;"><?php echo count($ordenes) + 1; ?> </td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td style="font-weight: bold;">{{trans('laboratorio.total')}}</td>
                      <td style="font-weight: bold;"><?php echo  $total; ?></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    @endif
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
</section>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.js') }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.all.min.js') }}"></script>

<script type="text/javascript">
  $('#example6').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': true,
    'info': false,
    'autoWidth': false,
  });

  /*function cargar_tabla_privadas(id_cab){
        $.ajax({
                type: 'get',
                url: "{{ url('humanlabs/index_privadas_ajax/')}}/"+id_cab,
                datatype: 'json',
          success: function(datahtml){

              $("#index_privada").empty().html(datahtml);

          },
          error:  function(){
            alert('error al cargar');
          }
        });
      }*/

  function guardar_det(id_orden) {
    $.ajax({
      type: 'post',
      url: "{{ url('humanlabs/privadas/guardar_det')}}/" + id_orden,
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#frm_privada").serialize(),
      success: function(data) {
        if (data.estado == 'ok') {
          alertasPersonalizadas('success', 'Exito', 'Se ha agregado la factura correctamente');
          setTimeout(function() {
            location.reload();
          }, 1500);
        }
      },

      error: function(data) {

        alert('error al cargar');
      }
    });
  }

  function agregarTodo() {
    let id_cab = document.getElementById('id_cab').value;

    var parametros = {
      "id_cab": id_cab
    };
    $.ajax({
      data: parametros,
      type: 'post',
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      url: "{{ route ('factura_agrupada.guardar_det_todo') }}",
      success: function(data) {
        // console.log(data);
        if (data == 'correcto') {
          alertasPersonalizadas('success', 'Exito', 'Se han agregado todas las facturas correctamente');
          setTimeout(function() {
            location.reload();
          }, 1500);

        } else {
          alertasPersonalizadas('error', 'Error...!', 'Hubo un error al guardar');
        }
      },
      error: function(data) {
        alertasPersonalizadas('error', 'Error...!', 'Hubo un error al guardar');
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
</script>
@endsection
