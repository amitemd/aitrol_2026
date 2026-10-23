@extends('contable.plan_cuentas.base')
@section('action-content')
<!-- Ventana modal editar -->


<style type="text/css">
  #arbol a:hover {
    color: #3c8dbc;
    cursor: pointer;
  }

  #arbol a {
    color: #3c8dbc;
    mouse: pointer;
    font-size: 12px;
  }

  #arbol ul {
    list-style-type: none;
  }

  .active {
    display: block !important;
  }

  .treeview-menu {
    display: none;
  }

  #tabla_elementos {
    font-size: 12px;
  }

  .form-control2 {
    border-radius: 5px;
    background-color: #fff !important;
  }

  .form-control2:focus {
    /* border-color:#0F6CBD; */
    /* border:2px solid #0F6CBD; */
  }

  .contendor-empresa {
    box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.35);
    -webkit-box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.35);
    -moz-box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.35);
    padding: 18px 0px;
    border-radius: 7px;
    margin-bottom: 12px;
  }

  * {
    font-family: var(--fuentePrincipal);
  }
</style>

<div class="modal fade" id="log_factura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 70%;">
    <div class="modal-content">

    </div>
  </div>
</div>
<!-- Main content -->
<section class="content">
  <div class="box ">
    <div class="box-header header_new" style="color: black; font-family: 'Helvetica general3';">
      <div class="col-md-12">
        <h5>{{trans('contableM.empresaactual')}}: <b>{{$empresa->razonsocial}}</b></h5>
      </div>
    </div>

    <!-- /.box-header -->
    <div class="box-body dobra">
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <form method="POST" action="{{route('plan_cuentas.guardar_empresa')}}">
            {{ csrf_field() }}


            <!-- <div class="col-md-12"> -->
            <div class="form-group col-md-12" style="display: flex;justify-content: center;">
              <div class="col-md-6 contendor-empresa" style="display: flex;justify-content: center;">
                <label for="id_empresa" class="col-md-5 control-label">{{trans('contableM.selecempresaausar')}}</label>
                <div class="col-md-6">
                  <select class="form-control form-control2" name="id_empresa" id="id_empresa" onchange="consultar_inventario();" style="width: 100%!important;">
                    @foreach($empresas as $value)
                    <option value="{{$value->id}}" @if($empresa->id == $value->id) selected="selected" @endif >{{$value->nombrecomercial}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <!-- </div> -->




            <div class="col-md-12" style="display: flex;justify-content: center;">


              <div class="col-md-6 contendor-empresa" style="display: flex;justify-content: center;">


                <div class="form-group col-md-6">
                  <label for="inventario" class="col-md-6 control-label">{{trans('contableM.inventario')}}</label>
                  <div class="col-md-6">

                    <select id="inventario" name="inventario" onchange="egresoKardex()" class="form-control input-sm form-control2" required style="font-size: 13px">
                      @if(!is_null($empresa))
                      <option @if($empresa->inventario=='0'){{'selected '}}@endif value="0">{{trans('laboratorio.no')}}</option>
                      <option @if($empresa->inventario=='1'){{'selected '}}@endif value="1">{{trans('laboratorio.si')}}</option>
                      @else
                      <option value="0" selected>{{trans('laboratorio.no')}}</option>
                      <option value="1">{{trans('laboratorio.si')}}</option>
                      @endif
                    </select>
                    <!-- <input type="checkbox" name="inventario" id="inventario" @if($empresa->inventario==1) checked @endif value="1"> -->
                  </div>
                </div>


                <div class="form-group col-md-6">
                  <label for="inventario" class="col-md-6 control-label">Egreso Kardex</label>
                  <div class="col-md-6">

                    <select class="form-control form-control2" @if($empresa->inventario == 0) disabled @endif id="egreso_kardex" onchange="egresoKardex()" name="egreso_kardex" class="form-control input-sm" required style="font-size: 13px">
                      @if(!is_null($empresa))
                      <option @if($empresa->egreso_kardex=='0'){{'selected '}}@endif value="0">{{trans('laboratorio.no')}}</option>
                      <option @if($empresa->egreso_kardex=='1'){{'selected '}}@endif value="1">{{trans('laboratorio.si')}}</option>
                      @endif
                    </select>
                    <!-- <input type="checkbox" name="inventario" id="inventario" @if($empresa->inventario==1) checked @endif value="1"> -->
                  </div>
                </div>

              </div>
            </div>



            <div class="col-md-12" style="display: flex;justify-content: center; margin-bottom: 10px;">
              <button class="btn btn-primary" style="padding: 6px 35px;border-radius: 15px;" type="submit">{{trans('laboratorio.enviar')}}</button>
            </div>
          </form>
        </div>
        @php $pageDocument = true; @endphp
        @if($pageDocument)
        <div class="row">
          @php
          //dd('entra');
          $fecha = new DateTime();
          $fecha->modify('-2 hours');
          $ct_ventas = \Sis_medico\Ct_ventas::where('id_empresa', $empresa->id)
          ->where('electronica', 1)
          ->where('estado_electronica', '<>', 3)
            ->where('revision_electronica', 0)
            ->where('estado','<>', 0)
              ->where('created_at', '<', $fecha->format('Y-m-d H:i:s'))
                ->get();
                foreach($ct_ventas as $value){
                $data['empresa'] = $empresa->id;
                $data['comprobante'] = $value->nro_comprobante;
                $data['tipo'] = "comprobante";
                $deEmpresa=\Sis_medico\De_Empresa::getDatos(session('id_empresa'));
                if($deEmpresa!='')
                {
                $documento = \Sis_medico\Ct_ventas::find($value->id);
                $estado = 0;
                $revision=0;
                if(isset($documento->nro_autorizacion) && $documento->nro_autorizacion!=''){
                $estado = 3;
                $revision=1;
                }
                $documento->revision_electronica=$revision;
                $documento->estado_electronica = $estado;
                $documento->save();
                }else
                {
                try{
                $envio = \Sis_medico\Http\Controllers\ApiFacturacionController::estado_comprobante($data);
                }
                catch(Exception $e)
                {
                $envio=0;
                }
                $documento = \Sis_medico\Ct_ventas::find($value->id);
                $estado = 0;
                if(isset($envio->details)){
                if($envio->details->log[0]->proceso == "Notificación" || $envio->details->log[0]->mensaje == "Autorizado"){
                $estado = 3;
                $documento->nro_autorizacion = $envio->details->autorizacion;
                }elseif($envio->details->log[0]->mensaje == "Documento recibido"){
                $estado = 2;
                }elseif($envio->details->log[0]->proceso == "Firma"){
                $estado = 1;
                }
                }
                $documento->estado_electronica = $estado;
                $documento->save();
                }
                }
                @endphp
                @if(count($ct_ventas) > 0)
                <div class="col-md-12">
                  <div class="table-responsive col-md-12">
                    <span><b>Facturas Pendientes</b></span>
                    <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                      <thead>
                        <tr class="well-dark">
                          <th>Codigo</th>
                          <th>Número Factura</th>
                          <th>Fecha Factura</th>
                          <th>Cliente</th>
                          <th style="font-size: 10px;">Estado</th>
                          <th>Acción</th>
                        </tr>
                      </thead>
                      <tbody>

                        @foreach($ct_ventas as $value)
                        <tr class="well">
                          <td>@if(!is_null($value->id)){{$value->id}}@endif</td>
                          <td>@if(!is_null($value->numero)){{$value->sucursal}}-{{$value->punto_emision}}-{{$value->numero}}@endif</td>
                          <td>@if(!is_null($value->fecha)){{substr($value->fecha,0,11)}}@endif</td>
                          @php
                          $cliente = \Sis_medico\Ct_Clientes::where('identificacion',$value->id_cliente)->first();
                          $seguro = Sis_medico\Seguro::find($value->seguro_paciente);
                          @endphp
                          <td>@if(!is_null($cliente->nombre)){{$cliente->nombre}}@endif</td>
                          @if($value->electronica == 0)
                          <td>No</td>
                          @else

                          @if($value->estado_electronica == 3)
                          <td>AUTORIZADO</td>
                          @elseif($value->estado_electronica == 2)
                          <td>FIRMADO</td>
                          @elseif($value->estado_electronica == 1)
                          <td>FIRMADO</td>
                          @else
                          <td>ENVIADO</td>
                          @endif
                          @endif
                          <td style="padding-left: 1px;padding-right: 1px;">
                            <a data-toggle="modal" data-target="#log_factura" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'comprobante']) }}" class="btn btn-info col-md-6 col-xs-6 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Log Factura</a>
                            <a target="_blank" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'pdf']) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Ride Fact</a>
                            <a onclick="actualizar_venta('{{$value->id}}')" class="btn btn-danger col-md-6 col-xs-6 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Corregido</a>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                      <tfoot>
                      </tfoot>
                    </table>
                  </div>
                </div>
                @endif
        </div>
        @endif
      </div>
    </div>
    <!-- /.box-body -->
  </div>
</section>

<script type="text/javascript">
  $(document).ready(function() {
    $('#log_factura').on('hidden.bs.modal', function() {
      $(this).removeData('bs.modal');
    });
  });

  function consultar_inventario() {
    id_empresa = document.getElementById("id_empresa").value;
    $.ajax({
      type: 'post',
      url: "{{ route('plan_cuentas.consultar_inventario') }}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        'id_empresa': id_empresa
      },
      success: function(data) {
        $("#inventario").val(data.inventario_estado);
        $("#inventario").trigger('change');
      },
      error: function(data) {
        console.log(data);
      }
    })
  };

  function actualizar_venta(id) {
    Swal.fire({
      title: 'Esta Seguro que esta revisado?',
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: `Si`,
      denyButtonText: `No`,
      customClass: {
        cancelButton: 'order-1 right-gap',
        confirmButton: 'order-2',
        denyButton: 'order-3',
      }
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'get',
          url: "{{ url('contable/ventas/estado/factura/')}}/" + id,
          success: function(data) {
            Swal.fire('Listo!', '', 'success');
            window.location.reload();
          },
          error: function(data) {
            Swal.fire('Error al Enviar Datos!', '', 'error');
          }
        });
      }
    });
  }
  const egresoKardex = () => {
    let inventario = document.getElementById('inventario').value;
    let egreso_kardex = document.getElementById('egreso_kardex')
    if (inventario == 0) {
      egreso_kardex.disabled = true;
      return;
    } else {
      egreso_kardex.disabled = false;
    }
    $.ajax({
      url: `{{ route('plan_cuentas.egresoKardex') }}`,
      type: 'get',
      dataType: 'json',
      data: {
        'egreso_kardex': egreso_kardex.value,
        'id_empresa': document.getElementById('id_empresa').value
      },
      success: function(data) {

      },
      error: function(data) {}
    })
  }
</script>
<!-- /.content -->

@endsection