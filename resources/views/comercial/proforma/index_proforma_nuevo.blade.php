@extends('comercial.proforma.base')
@section('action-content')
@php $rolUsuario = Auth::user()->id_tipo_usuario; @endphp
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<section class="content">
    <div class="box">
        <div class="box-header">
        </div>
        <div class="box-body">
            <form id="form_producto" method="post" action="">
                {{ csrf_field() }}
                
                <!-- <div class="form-group col-md-4 ">
                    <div class="row">
                        <div class="form-group col-md-10 ">
                            <label for="nombre" class="col-md-4 control-label">N. Proforma:</label>
                            <div class="col-md-8">
                                <input type="text" name="num_prof" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-2 ">
                    <div class="col-md-7">
                        <button type="submit" class="btn btn-primary" id="boton_buscar">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('prod_tar.buscar')}}</button>
                    </div>
                </div> -->
                <div class="row">
                    <div class="row">
                        <div class="form-group col-md-6" style="text-align: right;">
                                <a href="{{route('proforma.index')}}" class="btn btn-info">{{trans('contableM.crearproforma')}}</a>
                        </div>
                        <div class="form-group col-md-6" style="text-align:left;">
                            @if(in_array($rolUsuario, array(1)))
                                <a href="{{route('proforma.index_nuevo')}}" class="btn btn-info">Cobrar Historia Clinica</a>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-md-4" style="padding-left: 0px;padding-right: 0px;">
                      <label class="control-label col-md-3">ID:</label>
                        <div class="col-md-9">
                        <input type="text" name="id" id="id" class="form-control autocom input-sm" placeholder="Ingrese Id" value="@if(isset($id)){{$id}}@endif">
                        </div>
                    </div>

                    <div class="form-group col-md-4" style="padding-left: 0px;padding-right: 0px;">
                        <label for="fecha_desde" class="col-md-3 control-label">Fecha Desde:</label>
                        <div class="col-md-9">
                          <div class="input-group date">
                            <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off">
                            <div class="input-group-addon">
                              <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = '';"></i>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="form-group col-md-4" style="padding-left: 0px;padding-right: 0px;">
                        <label for="fecha_hasta" class="col-md-3 control-label">Fecha Hasta:</label>
                        <div class="col-md-9">
                          <div class="input-group date">
                            <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                            <div class="input-group-addon">
                              <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = '';"></i>
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="form-group col-md-4" style="padding-left: 0px;padding-right: 0px;">
                      <label class="control-label col-md-3">Cedula:</label>
                        <div class="col-md-9">
                        <input type="text" name="cedula" id="cedula" class="form-control autocom input-sm" placeholder="Ingrese cedula del paciente" value="@if(isset($cedula)){{$cedula}}@endif">
                        </div>
                    </div>

                    <div class="form-group col-md-4" style="padding-left: 0px;padding-right: 0px;">
                      <label class="control-label col-md-3">{{trans('winsumos.Paciente')}}</label>
                        <div class="col-md-9">
                        <input type="text" name="paciente" id="paciente" class="form-control autocom input-sm" placeholder="{{trans('winsumos.Ingrese_paciente')}}" value="@if(isset($paciente)){{$paciente}}@endif">
                        </div>
                    </div>

                    <div class="form-group col-md-4" style="padding-left: 0px;padding-right: 0px;">
                        <label for="seguro" class="col-md-3 control-label">Seguro:</label>
                        <div class="col-md-9">
                            <select class="form-control input-sm" name="seguro" id="seguro" onchange="buscar();">
                                <option value="">{{trans('winsumos.seleccione')}}</option>
                            @foreach($seguros as $value)
                                <option @if($value->id==$seguro) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-2 " style="text-align: right;">
                    <button type="submit" formaction="{{ route('comercial.proforma.index_proforma')}}" class="btn btn-primary btn-sm" id="boton_buscar">
                        <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;{{trans('winsumos.Buscar')}}&nbsp;</span></button>
                </div>

                <div class="form-group col-md-2 col-xs-6" style="text-align: right;">
                    <button type="submit" formaction="{{ route('comercial.proforma.store_cotizacion')}}" class="btn btn-primary btn-sm" id="boton_buscar">
                        <span class="glyphicon glyphicon-file" aria-hidden="true" style="font-size: 16px">&nbsp;Cotizacion&nbsp;</span></button>
                </div>

                <div class="form-group col-md-2 col-xs-6" style="text-align: right;">
                    <button type="submit" formaction="{{ route('comercial.proforma.index_cotizacion')}}" class="btn btn-primary btn-sm" id="boton_buscar">
                        <span class="glyphicon glyphicon-file" aria-hidden="true" style="font-size: 16px">&nbsp;Ver Cotizaciones &nbsp;</span></button>
                </div>

                <div class="col-md-2 text-right">
                    <button type="submit" formaction="{{route('comercial.excel_proforma')}}" class="btn btn-success">Excel</button>
                </div>
                @php 
                   $id_empresa = session('id_empresa');                       
                @endphp

                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th>N.</th>
                                        <th>Cedula</th>
                                        <th>{{trans('proforma.Paciente')}}</th>
                                        <th>{{trans('proforma.observacion')}}</th>
                                        <th>{{trans('proforma.Seguro')}}</th>
                                        <th style="width: 15%;">{{trans('proforma.estado')}}</th>
                                        <th>{{trans('proforma.total')}}</th>
                                        <th>{{trans('proforma.accion')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proformas as $value)
                                    <tr>
                                        <td>{{$value->id}}</td>
                                        <td>{{$value->id_paciente}}</td>
                                        <td> {{$value->nombre1}}  {{$value->nombre2}}  {{$value->apellido1}} {{$value->apellido2}}</td>
                                        <td> {{$value->observacion}}</td>
                                        <td> {{$value->nombre_seguro}}</td>
                                            @if($value->id_orden != null)
                                                @php 
                                                    $npro   = Sis_medico\Proforma_Cabecera::find($value->id);
                                                    $recibo = $npro->recibo;     
                                                @endphp
                                                @if(!is_null($recibo))
                                                    @if($recibo->estado == 1)
                                                    <td style="background: #00a65a; font-weight: bold; text-align: center;">Emitida</td>
                                                    @else
                                                    <td style="background: #f39c12; font-weight: bold; text-align: center;">Pendiente de emitir</td>
                                                    @endif
                                                @endif   
                                            @endif
                                        <td> {{$value->total}}</td> 
                                        <td>
                                        
                                            @if($value->pagado == 0 and $value->estado == -1)
                                            <a href="{{route('comercial.proforma.editar',['id' => $value->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                            <a href="{{route('comercial.proforma.eliminar_proforma', ['id' => $value->id])}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                            @endif
                                            @if($value->estado == 1)
                                            <a target="_blank" href="{{route('comercial.proforma.pdf_proforma',['id' => $value->id])}}" class="btn btn-success"> <i class="fa fa-download"></i></a>
                                            @endif
                                            @if($value->id_orden != null)
                                                @php 
                                                    $npro   = Sis_medico\Proforma_Cabecera::find($value->id);
                                                    
                                                    $recibo = $npro->recibo; 
                                                @endphp
                                                @if(!is_null($recibo))
                                                @if($recibo->estado == 1)
                                                    <a target="_blank" href="{{ route('facturacion.imprimir_ride', ['id_orden' =>$recibo->id]) }}" class="btn btn-success  btn-xs" ><span class="glyphicon glyphicon-download-alt">Recibo/Cobro</span></a>
                                                @else
                                                <a href="{{ route('nuevorecibocobro.editar', ['orden' =>$value->id_orden]) }}" class="btn btn-warning  btn-xs">
                                                    <span class="glyphicon glyphicon-edit"></span>
                                                </a>
                                                @endif
                                                @endif
                                            @endif   
                                            @if ($value->id_orden == null and $value->estado == 1)
                                            <a href="{{route('comercial.proforma.editar',['id' => $value->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                            @php $rolUsuario = Auth::user()->id_tipo_usuario; @endphp
                                            @if($rolUsuario == '1')
                                            <a href="{{route('comercial.proforma.eliminar_proforma', ['id' => $value->id])}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                            @endif
                                            @endif
                                        </td>

                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($proformas->currentPage() - 1) * $proformas->perPage())}} / {{count($proformas) + (($proformas->currentPage() - 1) * $proformas->perPage())}} de {{$proformas->total()}} registros</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                    {{ $proformas->appends(Request::only(['proveedor', 'observacion', 'secuencia_factura','ct_c.tipo_comprobante','fecha','id_asiento_cabecera','id','detalle','secuencia_f']))->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $('#example2').DataTable({
        'paging': false,
        'lengthChange': false,
        'searching': false,
        'ordering': false,
        'info': false,
        'autoWidth': false
    })

    $(function() {
        $('#fecha_desde').datetimepicker({
          format: 'YYYY/MM/DD',
          //defaultDate: '{{$fecha_desde}}',
        });

        $('#fecha_hasta').datetimepicker({
          format: 'YYYY/MM/DD',
          //defaultDate: '{{$fecha_hasta}}',
        });

        $("#fecha_desde").on("dp.change", function(e) {
          $('#fecha_hasta').data("DateTimePicker").minDate(e.date);
        });

    });

    function buscar()
    {
      var obj = document.getElementById("boton_buscar");
      obj.click();
    }

</script>

@endsection



