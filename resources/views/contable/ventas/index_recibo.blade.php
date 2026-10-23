@extends('contable.ventas.base')
@section('action-content')
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">

<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Ventas</a></li>
      <li class="breadcrumb-item active">Registro de Factura de Ventas</li>

    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <h3 class="box-title">Facturas de Venta </h3>
      </div>
      <div class="col-md-1 text-right">
        <button onclick="location.href='{{route('venta.create_recibo')}}'" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>Nueva Factura
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR FACTURA DE VENTA</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('venta.index_recibo') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="numero">Id: </label>
        </div>

        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($request)){{$request['id']}}@endif" placeholder="Ingrese Id..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="numero">N&uacute;mero: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="numero" name="numero" value="@if(isset($request)){{$request['numero']}}@endif" placeholder="Ingrese número..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="asiento">Asiento: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="id_asiento" name="id_asiento" value="@if(isset($request)){{$request['id_asiento']}}@endif" placeholder="Ingrese asiento..." />
        </div>

        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">Fecha: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($request)){{$request['fecha']}}@endif">
          </div>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="cliente">Cliente: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="nombre_cliente" name="nombre_cliente" value="@if(isset($request)){{$request['nombre_cliente']}}@endif" placeholder="Ingrese el nombre del cliente..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="paciente">Paciente: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="nombres_paciente" name="nombres_paciente" value="@if(isset($request)){{$request['nombres_paciente']}}@endif" placeholder="Ingrese el nombre del paciente..." />
        </div>
        <div class="col-md-offset-9 col-xs-2">
          <button type="submit" id="buscarAsiento" class="btn btn-primary btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">VENTAS</label>
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
                        <th >Codigo</th>
                        <th >Número_Factura</th>
                        <th >Tipo</th>
                        <th >Fecha Factura</th>
                        <th >Cliente</th>
                        <!--<th >RUC/CID</th>-->
                        <th >Paciente</th>
                        <th >Seguro</th>
                        <th >Procedimiento</th>
                        <th style="font-size: 14px;" >Fecha Procedimiento</th>
                        <th >Creado por </th>
                        <th>Anulado por</th>
                        <th  style="font-size: 10px;">Electronica</th>
                        <th>Acción</th>
                      </tr>
                    </thead>
                    <tbody>

                      @foreach($ventas as $value)
                      <tr class="well">
                        <td>@if(!is_null($value->id)){{$value->id}}@endif</td>
                        <td>@if(!is_null($value->nro_comprobante)){{$value->nro_comprobante}}@endif</td>
                        <td>@if(!is_null($value->tipo)){{$value->tipo}}@endif</td>
                        <td>@if(!is_null($value->fecha)){{substr($value->fecha,0,11)}}@endif</td>
                        @php
                        $cliente = \Sis_medico\Ct_Clientes::where('identificacion',$value->id_cliente)->first();
                        $seguro = Sis_medico\Seguro::find($value->seguro_paciente);
                        @endphp
                        <td>@if(!is_null($cliente->nombre)){{$cliente->nombre}}@endif</td>
                        <!-- <td>@if(!is_null($cliente->cedula_representante)){{$cliente->cedula_representante}}@endif</td>-->
                        <td>@if(!is_null($value->nombres_paciente)){{$value->nombres_paciente}}@endif</td>
                        <td>@if(!is_null($seguro)){{$seguro->nombre}}@endif</td>
                        <td>@if(!is_null($value->procedimientos)){{$value->procedimientos}}@endif</td>
                        <td>@if(!is_null($value->fecha_procedimiento)){{$value->fecha_procedimiento}}@endif</td>
                        <!--<td>@if($value->estado == '1') Activo @endif</td>-->
                        <td>@if(isset($value->usuario)) {{$value->usuario->nombre1}} {{$value->usuario->nombre2}} @endif</td>
                        <td>@if($value->estado == 0) @if(isset($value->usuariomod)) {{$value->usuariomod->nombre1}} {{$value->usuariomod->nombre2}} @endif  @endif</td>
                        @if($value->electronica == 0)
                          <td>No</td>
                        @else
                          @if($value->estado_electronica == 3)
                            <td>AUTORIZADO</td>
                          @else
                            @php
                              $fecha1 = new DateTime($value->created_at);//fecha inicial
                              $fecha2 = new DateTime(date('Y-m-d H:i:s'));//fecha de cierre
                              $intervalo = $fecha1->diff($fecha2);
                              $minutos = $intervalo->format('%i');
                              $horas = $intervalo->format('%h');
                              $tiempo = $minutos +($horas*60);
                            @endphp
                            @if($tiempo > 5)
                              @php
                                $data['empresa']     = $empresa->id;
                                $data['comprobante'] = $value->nro_comprobante;
                                $data['tipo']        = "comprobante";

                                $envio = \Sis_medico\Http\Controllers\ApiFacturacionController::estado_comprobante($data);
                                $estado = 0;
                                $documento = \Sis_medico\Ct_ventas::find($value->id);
                                if(isset($envio->details)){
                                  if($envio->details->log[0]->proceso == "Notificación" || $envio->details->log[0]->mensaje == "Autorizado"){
                                    $estado = 3;
                                    $documento->nro_autorizacion =  $envio->details->autorizacion;
                                    echo "<td>AUTORIZADO</td>";
                                  }elseif($envio->details->log[0]->mensaje == "Documento recibido"){
                                    $estado = 2;
                                    echo "<td>ENVIADO SRI</td>";
                                  }elseif($envio->details->log[0]->proceso == "Firma"){
                                    $estado = 1;
                                    echo "<td>FIRMADO</td>";
                                  }else{
                                    echo "<td>ENVIADO</td>";
                                  }
                                }else{
                                  echo "<td>Pendiente de Envio</td>";
                                }
                                $documento->estado_electronica =  $estado;
                                $documento->save();
                              @endphp
                            @else
                              <td>Pendiente de Envio</td>
                            @endif
                          @endif
                        @endif
                        <td style="padding-left: 1px;padding-right: 1px;">
                          @if($value->estado == 1)
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="javascript:anular({{$value->id}});" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">&nbsp;&nbsp;Anular</a>
                          @elseif($value->estado == 0)
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a class="btn btn-danger col-md-3 col-xs-3 btn-margin" disabled style="font-size: 10px;padding-left: 2px;padding-right: 20px;">&nbsp;&nbsp;Anular</a>
                          @endif
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{ route('ventas_editar', ['id' => $value->id]) }}" class="btn btn-warning col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Visualizar</a>
                          @if(!empty($value->rutapdf))
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a target="_blank" href="{{ route('pdf.visualizar', ['id' => $value->id]) }}" class="btn btn-warning col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Archivo</a>
                          @endif
                          <!--<a class="btn btn-warning col-md-3 col-xs-3 btn-margin btn-gray" data-remote="{{route('detalle_paquete.facturacion',['id' => $value->id])}}" data-toggle="modal" data-target="#detalle_paquete" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Detall Ord</a>-->
                          @if($value->orden_venta!=null)
                           <a target="_blank" href="{{ route('pdf_comprobante_detalle.paquete', ['id' => $value->id]) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Detall Ord</a>
                          @endif
                          @if($value->electronica == 0)
                          @if($value->ip_creacion=="OMNI")
                          <a target="_blank" href="{{ route('ventas.pdf_omni', ['id' => $value->id]) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Pdf Fact</a>
                          @else
                          <a target="_blank" href="{{ route('pdf_comprobante_no.tributario', ['id' => $value->id]) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Pdf Fact</a>
                          @endif
                          @else
                          <a target="_blank" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'pdf']) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Ride Fact</a>

                          <a target="_blank" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'xml']) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">XML Fact</a>

                          <a data-toggle="modal" data-target="#log_factura"  href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'comprobante']) }}" class="btn btn-info col-md-6 col-xs-6 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Log Factura</a>
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
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($ventas->currentPage() - 1) * $ventas->perPage())}} / {{count($ventas) + (($ventas->currentPage() - 1) * $ventas->perPage())}} de {{$ventas->total()}} registros</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $ventas->appends(Request::only(['id', 'numero','nombre_cliente','nombres_paciente','fecha','id_asiento']))->links() }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


  </div>
</section>
<script src="{{ asset("/plugins/datetimepicker/bootstrap-material-datetimepicker.js")}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
  });
  $(document).ready(function() {
    $('#example92').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': true,
      'info': false,
      'autoWidth': false
    });
  });

  //contable/ventas/factura/
  function anular(id) {

    Swal.fire({
      title: '¿Desea Anular esta factura?',
      text: "No puedes revertir esta acccion!",
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
        let ids ="";
        var acumulate = "";
        var mensaje="";
        
        let msj_ingr ="";
        let msj_ret ="";
        let msj_cruce="";
        let msj_che="";
        let msj_cruce_cue="";
        let msj_credito="";


        let mensajeaux="";
      if (result.isConfirmed) {
        var acumulate = "";

        $.ajax({
          type: 'get',
          url: "{{ route('ventas.verificar')}}",
          datatype: 'json',
          data: {
            'verificacion': '1',
            'id_venta': id
          },
          success: function(data) {
            //console.log(data+" dsada "+id);
            if(typeof (data.ingreso) != "undefined"){
              if(data.ingreso.length > 0){
                for(let i = 0; i < data.ingreso.length; i++){
                  mensaje = `${data.ingreso[i]}`;
                }
                msj_ingr = ` Ingreso: ${mensaje}`;

              mensaje ="ok";
              }
             

              
            }

            if(typeof (data.retencion) != "undefined"){
              msj_ret = ` Retencion: ${data.retencion}`;

              mensaje ="ok";
            }

            if(typeof (data.cruce) != "undefined"){
             
              for(let i = 0; i < data.cruce.length; i++){
                mensaje = `${data.cruce[i]}`;
              }
              msj_cruce = ` Cruces: ${mensaje}`;

              mensaje ="ok";
            }

            if(typeof (data.chequepost) != "undefined"){
              for(let i = 0; i < data.chequepost.length; i++){
                mensaje = `${data.chequepost[i]}`;
              }
              msj_che = ` Chequepost: ${mensaje}`;

              mensaje ="ok";
          }

            if(typeof (data.cruce_cuentas) != "undefined"){
              
              for(let i = 0; i < data.cruce_cuentas.length; i++){
                mensaje = `${data.cruce_cuentas[i]}`;
              }
              msj_cruce = ` Cruce cuentas: ${mensaje}`;

              mensaje ="ok";
          }

            if(typeof (data.credito) != "undefined"){
              
              for(let i = 0; i < data.credito.length; i++){
                mensaje = `${data.credito[i]}`;
              }
              msj_credito = ` Credito: ${mensaje}`;

              mensaje ="ok";
          }
            
           console.log(mensaje);
           if(mensaje != ""){
              Swal.fire("Error!", `Existen algunos observaciones encontradas: ${msj_ingr}${msj_ret}${msj_cruce}${msj_che}${msj_cruce}${msj_credito}`, "error");
           } else {
              console.log("entra aqui" + id);
              test(id);
            }
          },
          error: function(data) {
            console.log(data);
          }
        });

      }
    })
  }
  async function test(id) {
    try {
      const {
        value: text
      } = await Swal.fire({
        input: 'textarea',
        inputPlaceholder: 'Ingrese motivo de anulación...',
        inputAttributes: {
          'aria-label': 'Ingrese motivo de anulación'
        },
        showCancelButton: true
      })

      if (text) {
        $.ajax({
          type: 'get',
          url: "{{ url('contable/ventas/factura/')}}/" + id,
          datatype: 'json',
          data: {
            'observacion': text
          },
          success: function(data) {
            Swal.fire("Correcto!", "Anulación Correcta", "success");
            location.href = "{{route('venta_index2')}}";
          },
          error: function(data) {
            console.log(data);
          }
        });
      }

    } catch (err) {
      console.log(err);
    }
  }
</script>
@endsection