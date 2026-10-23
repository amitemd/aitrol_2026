@extends('contable.retenciones.base')
@section('action-content')
<style>
  .autocomplete {
    z-index: 999999 !important;
    z-index: 999999999 !important;
    z-index: 99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 160px;
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box;
  }
  .ui-autocomplete {
    z-index: 5000;
  }

  .ui-autocomplete {
    z-index: 999999;
    list-style: none;
    background-color: #FFFFFF;
    width: 300px;
    border: solid 1px #EEE;
    border-radius: 5px;
    padding-left: 10px;
    line-height: 2em;
  }
</style>
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
@php
$id_usuario = Auth::user()->id;
@endphp
<div class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">Retenciones Proveedores</a></li>
      <li class="breadcrumb-item active" aria-current="page">Registro de Retenciones</li>
    </ol>
  </nav>
  <div class="modal fade" id="log_factura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

      </div>
    </div>
  </div>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-8">
        <!--<h8 class="box-title size_text">Empleados</h8>-->
        <!--<label class="size_text" for="title">EMPLEADOS</label>-->
        <h3 class="box-title">Comprobante de Retenciones</h3>
      </div>

      <div class="col-md-1 text-right">
        <button onclick="location.href='<?= route('r.crear_retencion'); ?>'" class="btn btn-success btn-gray">
          <i class="fa fa-file"></i>
        </button>
      </div>
      <!--div class="col-md-1 text-right">
        <button onclick="location.href='{{route('retenciones_anuladas')}}'" class="btn btn-success btn-gray">
          <i class="fa fa-file"></i> Anuladas
        </button>
      </div-->
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR DE RETENCIONES</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('retenciones.buscar') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="id">Id Retencion: </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['ct_c.id']}}@endif" placeholder="Ingrese Id..." />
        </div>

        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="nombre_proveedor">{{trans('contableM.proveedor')}}: </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <select class="form-control select2_find_proveedor" name="id_proveedor" id="id_proveedor" style="width: 100%;">
          </select>
        </div>
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="detalle">{{trans('contableM.detalle')}}: </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <input class="form-control" type="text" id="detalle" name="detalle" value="@if(isset($searchingVals)){{$searchingVals['descripcion']}}@endif" placeholder="Ingrese detalle..." />
        </div>
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="secuencia_f">Secuencia Factura: </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">

          <select class="form-control select2" style="width: 100%;" id="secuencia_f" name="secuencia_f" value="@if(isset($searchingVals)){{$searchingVals['co.numero']}}@endif">
            <option value="">Seleccione...</option>
            @foreach($compras as $value)
            <option @if(isset($searchingVals)){{ $searchingVals['co.numero']== $value->numero ? 'selected' : ''}} @else value="{{$value->numero}}" @endif> {{$value->numero}} </option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto col-md-2" for="fecha">{{trans('contableM.fecha')}}: </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['ct_c.created_at']}}@endif">
          </div>
        </div>
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="fac_crea">Creo: </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <input class="form-control" type="text" id="fac_crea" name="fac_crea" value="@if(isset($searchingVals)){{$searchingVals['ct_c.id_usuariocrea']}}@endif" placeholder="Ingrese quien creo la factura..." />
        </div>
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="secuencia">Numero de Retencion: </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <input class="form-control" type="text" id="secuencia" name="secuencia" value="@if(isset($searchingVals)){{$searchingVals['ct_c.secuencia']}}@endif" placeholder="Ingrese la secuencia..." />
        </div>
        <div class="col-md-offset-11 col-xs-2">
          <button type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">LISTADO DE RETENCIONES</label>
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
                  <table id="tablaRetenciones" class="table table-hover dataTable" role="grid" aria-describedby="example2_info" width="100%">
                    <thead class="well-dark">
                      <tr>
                        <th>#</th>
                        <th>{{trans('contableM.accion')}}</th>
                        <th>{{trans('contableM.fecha')}}</th>
                        <th>{{trans('contableM.proveedor')}}</th>
                        <th>{{trans('contableM.Descripcion')}}</th>
                        <th> # Compra </th>
                        <th>Comprobante</th>
                        <th>{{trans('contableM.totalrfir')}}</th>
                        <th>Total RFIVA</th>
                        <th>{{trans('contableM.total')}}</th>
                        <th>{{trans('contableM.creadopor')}}</th>
                        <th>{{trans('contableM.estado')}}</th>
                        <th>Electronica</th>
                        <th>Estado SRI</th>
                      </tr>
                    </thead>
                    <!--   //tipo es
                            // comprobante = informacion del comprobante
                            // pdf =  el ride pdf de autorizacion
                            // xml =  Documento en archivo xml
                            //tipo_comprobante es
                            // retencion / nota_credito / guia
                            $data['empresa']     = "0993075000001";
                            $data['comprobante'] = "001-002-000000137";
                            $data['tipo']        = "comprobante";
                            $data['tipo_comprobante']        = "retencion";

                            $envio = ApiFacturacionController::estado_comprobante_general($data); -->
                    <tbody style="background-color: white;">
                      @php
                      $id_auth = Auth::user()->id;
                      $id_empresa= Session::get('id_empresa');
                      $deEmpresa = Sis_medico\De_Empresa::where('id_empresa',$id_empresa)->first();
                      @endphp
                      @foreach($retenciones as $value)
                        <tr align="center">
                        <td>{{$value->id}}</td>
                        @if($value->punto_emision!=100)
                        <!--PYF-->
                          @if($value->electronica==0)
                            <td>
                              <a class="btn btn-success btn-gray col-md-2" target="_blank" href="{{route('pdf_comprobante_retenciones',['id'=>$value->id])}}"><i class="fa fa-file-pdf-o"></i></a>
                              <a class="btn btn-danger btn-gray col-md-2" href="{{route('retenciones_edit',['id'=>$value->id])}}"><i class="fa fa-eye"></i></a>
                              @if($value->estado!=0)
                              <a href="javascript:anular({{$value->id}});" class="btn btn-danger btn-gray col-md-2"><i class="fa fa-trash"> </i> </a>
                              @endif
                            </td>
                          @else
                          <td>
                            <div class="dropdown">
                              <button class="btn-gray btn btn-success dropdown-toggle btn-xs" title="Acciones" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-thumbs-o-up"></i>&nbsp;<i class="fa fa-caret-down"></i></button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                <a class="btn btn-gray btn-danger btn-xs" title="Descargar PDF" target="_blank" href="{{route('pdf_comprobante_retenciones',['id'=>$value->id])}}" style="font-size: 12px;padding-left: 2px;padding-right: 20px;text-align: left;width: 40%;"><i class="fa fa-file-pdf-o"></i> PDF</a>
                                @if($value->estado!=0)
                                <a href="javascript:anular({{$value->id}});" title="Anular Retención" class="btn btn-gray btn-warning btn-xs" style="font-size: 12px;padding-left: 2px;padding-right: 20px;text-align: left;width: 40%;"><i class="fa fa-trash"> </i>&nbsp; Anular</a>
                                <a class="btn btn-gray btn-primary btn-xs" title="Ver Retención" href="{{route('retenciones_edit',['id'=>$value->id])}}" style="font-size: 12px;padding-left: 2px;padding-right: 20px;text-align: left;width: 40%;"><i class="fa fa-eye"></i>&nbsp; Ver</a>
                                <a target="_blank" href="{{ route('facturacion.comprobante_publico_general', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'pdf', 'documento' => 'retencion']) }}" title="Descargar RIDE" class="btn btn-gray btn-danger btn-xs" style="font-size: 12px;padding-left: 2px;padding-right: 20px;text-align: left;width: 40%;"><i class="fa fa-file-pdf-o"></i>&nbsp; Ride</a>
                                <a target="_blank" href="{{ route('facturacion.comprobante_publico_general', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'xml', 'documento' => 'retencion']) }}" title="Descargar XML" class="btn btn-gray btn-success btn-xs" style="font-size: 12px;padding-left: 2px;padding-right: 20px;text-align: left;width: 40%;"><i class="fa fa-file-code-o"></i>&nbsp; XML</a>
                                <a data-toggle="modal" data-target="#log_factura" href="{{ route('facturacion.comprobante_publico_general', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'comprobante', 'documento' => 'retencion']) }}" title="Ver LOG" class="btn btn-gray btn-info btn-xs" style="font-size: 12px;padding-left: 2px;padding-right: 20px;text-align: left;width: 40%;"><i class="fa fa-file-text-o"></i>&nbsp; Ver Log</a>
                                @else
                                <a class="btn btn-gray btn-primary btn-xs" title="Ver Retención" href="{{route('retenciones_edit',['id'=>$value->id])}}" style="font-size: 12px;padding-left: 2px;padding-right: 20px;text-align: left;width: 40%;"><i class="fa fa-eye"></i>&nbsp; Ver</a>
                                @endif
                              </div>
                            </div>
                          </td>
                          @endif
                        @else
                        @if($value->doc_electronico=='')
                        <!-- PYF -->
                          @if($value->electronica == 0)
                          <td>
                            <a class="btn btn-success btn-gray col-md-2" target="_blank" href="{{route('pdf_comprobante_retenciones',['id'=>$value->id])}}"><i class="fa fa-file-pdf-o"></i></a>
                            <a class="btn btn-danger btn-gray col-md-2" href="{{route('retenciones_edit',['id'=>$value->id])}}"><i class="fa fa-eye"></i></a>
                            @if($value->estado!=0)
                            <a href="javascript:anular({{$value->id}});" class="btn btn-danger btn-gray col-md-2"><i class="fa fa-trash"> </i> </a>
                            @endif
                          </td>
                          @else
                          <td>
                            <div class="dropdown">
                              <button class="btn-gray btn btn-success dropdown-toggle btn-xs" title="Acciones" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-thumbs-o-up"></i>&nbsp;<i class="fa fa-caret-down"></i></button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                <a class="btn btn-gray btn-danger btn-xs" title="Descargar PDF" target="_blank" href="{{route('pdf_comprobante_retenciones',['id'=>$value->id])}}" style="font-size: 12px;padding-left: 2px;padding-right: 20px;text-align: left;width: 40%;"><i class="fa fa-file-pdf-o"></i> PDF</a>
                                @if($value->estado!=0)
                                <a href="javascript:anular({{$value->id}});" title="Anular Retención" class="btn btn-gray btn-warning btn-xs" style="font-size: 12px;padding-left: 2px;padding-right: 20px;text-align: left;width: 40%;"><i class="fa fa-trash"> </i>&nbsp; Anular</a>
                                <a class="btn btn-gray btn-primary btn-xs" title="Ver Retención" href="{{route('retenciones_edit',['id'=>$value->id])}}" style="font-size: 12px;padding-left: 2px;padding-right: 20px;text-align: left;width: 40%;"><i class="fa fa-eye"></i>&nbsp; Ver</a>
                                <a target="_blank" href="{{ route('facturacion.comprobante_publico_general', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'pdf', 'documento' => 'retencion']) }}" title="Descargar RIDE" class="btn btn-gray btn-danger btn-xs" style="font-size: 12px;padding-left: 2px;padding-right: 20px;text-align: left;width: 40%;"><i class="fa fa-file-pdf-o"></i>&nbsp; Ride</a>
                                <a target="_blank" href="{{ route('facturacion.comprobante_publico_general', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'xml', 'documento' => 'retencion']) }}" title="Descargar XML" class="btn btn-gray btn-success btn-xs" style="font-size: 12px;padding-left: 2px;padding-right: 20px;text-align: left;width: 40%;"><i class="fa fa-file-code-o"></i>&nbsp; XML</a>
                                <a data-toggle="modal" data-target="#log_factura" href="{{ route('facturacion.comprobante_publico_general', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'comprobante', 'documento' => 'retencion']) }}" title="Ver LOG" class="btn btn-gray btn-info btn-xs" style="font-size: 12px;padding-left: 2px;padding-right: 20px;text-align: left;width: 40%;"><i class="fa fa-file-text-o"></i>&nbsp; Ver Log</a>
                                @endif
                              </div>
                            </div>
                          </td>
                          @endif
                        @else
                        <td>
                          @if($value->doc_electronico==-1)
                          <a href="javascript:anular({{$value->id}});" class="btn btn-danger btn-gray"><i class="fa fa-trash"> </i> </a>
                          @elseif($value->doc_electronico==0)
                          <button type="button" class="btn btn-gray btn-warning text-center"><i class="fa fa-spinner" aria-hidden="true"></i></button>
                          @elseif($value->doc_electronico==5)
                          <div class="dropdown">
                            <button class="btn-gray btn btn-success dropdown-toggle botonFinal" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">✓<i class="fa fa-caret-down"></i></button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                              <a href="{{ asset('/') }}api_doc_electronico?opcion=descargarXML&clave={{ $value->nro_autorizacion }}" class="btn btn-success btn-gray">
                                <i class="fa fa-file-code-o"></i></a>
                              <a href="{{ asset('/') }}api_doc_electronico?opcion=generarPdf&clave={{ $value->nro_autorizacion }}" class="btn btn-danger btn-gray">
                                <i class="fa fa-file-pdf-o"></i></a>
                                <a href="javascript:anular({{$value->id}});" class="btn btn-danger btn-gray"><i class="fa fa-trash"> </i> </a>
                            </div>
                          </div>
                          @elseif($value->doc_electronico==7)
                          <button class="btn btn-gray btn-danger text-center" onclick="verErrorXml('<?= $value->id; ?>')"><i class="fa fa-close" aria-hidden="true"></i></button>
                          @if($value->estado!=0)
                          <a href="javascript:anular({{$value->id}});" class="btn btn-danger btn-gray"><i class="fa fa-trash"> </i> </a>
                          @endif
                          @elseif($value->doc_electronico==9)
                          <button class="btn btn-gray btn-danger text-center" onclick="verIErrorTributaria('<?= $value->nro_autorizacion; ?>')"><i class="fa fa-close" aria-hidden="true"></i></button>
                          <a href="javascript:anular({{$value->id}});" class="btn btn-danger btn-gray"><i class="fa fa-trash"> </i> </a>
                          @else
                          <a href="javascript:anular({{$value->id}});" class="btn btn-danger btn-gray"><i class="fa fa-trash"> </i> </a>
                          @endif
                          <a class="btn btn-gray btn-primary text-center" href="{{route('retenciones_edit',['id'=>$value->id])}}"><i class="fa fa-eye"></i></a>
                          @if($value->doc_electronico==-2)
                          <a data-toggle="modal" data-target="#log_factura" href="{{ route('facturacion.comprobante_publico_general', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'comprobante', 'documento' => 'retencion']) }}" class="btn btn-dark btn-gray text-center"><i class="fa fa-align-justify" aria-hidden="true"></i></a>
                          <a href="javascript:anular({{$value->id}});" class="btn btn-danger btn-gray"><i class="fa fa-trash"> </i> </a>
                          @endif
                        </td>
                        @endif
                        @endif
                        <td>{{$value->fecha}}</td>
                        <td>{{$value->razonsocial}}</td>
                        <td>{{$value->descripcion}}</td>
                        <td>{{$value->numero}}</td>
                        <td>{{$value->nro_comprobante}}</td>
                        <td>{{$value->valor_fuente}}</td>
                        <td>{{$value->valor_iva}}</td>
                        <td>@php $total= $value->valor_fuente+$value->valor_iva; @endphp {{number_format($total,2)}}</td>
                        <td>{{$value->nombre1}} {{$value->apellido1}}</td>
                        <td>@if(($value->estado)==1) {{trans('contableM.activo')}} @elseif(($value->estado)==0) INACTIVO @endif</td>
                        <td>@if($value->electronica==0) No @elseif($value->electronica==1) Si @endif</td>

                        @if($deEmpresa=='')
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
                        $data['empresa'] = $empresa->id;
                        $data['comprobante'] = $value->nro_comprobante;
                        $data['tipo'] = "comprobante";
                        $data['tipo_comprobante']="retencion";
                        try{
                          $envio = \Sis_medico\Http\Controllers\ApiFacturacionController::estado_comprobante_general($data);
                        }
                          catch(Exception $e){
                          $envio = 0;
                        }
                        $estado = 0;
                        $documento = \Sis_medico\Ct_Retenciones::find($value->id);
                        if(isset($envio->details)){
                        if($envio->details->log[0]->proceso == "Notificación" || $envio->details->log[0]->mensaje == "Autorizado"){
                        $estado = 3;
                        $documento->autorizacion = $envio->details->autorizacion;
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
                        $documento->estado_electronica = $estado;
                        $documento->save();
                        @endphp
                        @else
                        <td>Pendiente de Envio</td>
                        @endif
                        @endif
                        @endif
                        @else
                        <td id="colEstadoSRI">
                          @if(!is_null($value->doc_electronico))
                          @if($value->doc_electronico==-1)
                          POR ENVIAR
                          @elseif($value->doc_electronico==0)
                          EN PROCESO
                          @elseif($value->doc_electronico==5)
                          AUTORIZADO
                          @elseif($value->doc_electronico==7||$value->doc_electronico==9)
                          ERROR
                          @else
                          ERRORES EN PROCESO
                          @endif
                          @else
                          @if($value->estado_electronica == 3)
                          AUTORIZADO
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
                          $data['empresa'] = $empresa->id;
                          $data['comprobante'] = $value->nro_comprobante;
                          $data['tipo'] = "comprobante";
                          $data['tipo_comprobante']="retencion";
                          try{
                            $envio = \Sis_medico\Http\Controllers\ApiFacturacionController::estado_comprobante_general($data);
                          }
                          catch(Exception $e){
                            $envio = 0;
                          }
                          $estado = 0;
                          $documento = \Sis_medico\Ct_Retenciones::find($value->id);
                          if(isset($envio->details)){
                          if($envio->details->log[0]->proceso == "Notificación" || $envio->details->log[0]->mensaje == "Autorizado"){
                          $estado = 3;
                          $documento->autorizacion = $envio->details->autorizacion;
                          echo "AUTORIZADO";
                          }elseif($envio->details->log[0]->mensaje == "Documento recibido"){
                          $estado = 2;
                          echo "ENVIADO SRI";
                          }elseif($envio->details->log[0]->proceso == "Firma"){
                          $estado = 1;
                          echo "FIRMADO";
                          }else{
                          echo "ENVIADO";
                          }
                          }else{
                          echo "Pendiente de Envio";
                          }
                          $documento->estado_electronica = $estado;
                          $documento->save();
                          @endphp
                          @else
                          Pendiente de Envio
                          @endif
                          @endif
                          @endif
                        </td>
                        @endif
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-5">
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($retenciones->currentPage() - 1) * $retenciones->perPage())}} / {{count($retenciones) + (($retenciones->currentPage() - 1) * $retenciones->perPage())}} de {{$retenciones->total()}} {{trans('contableM.registros')}}</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $retenciones->appends(Request::only(['id', 'nombre_proveedor','id_proveedor', 'detalle','secuencia_f','fecha','secuencia_f','fac_crea','secuencia']))->links() }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modalErrorTributaria" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 class="modal-title">Información Tributaria</h3>
      </div>
      <div class="modal-body">
        <p><b>Estado:</b> <input type="text" class="form-control text-right" id="estadoErrorT" readonly /></p>
        <p><b>Clave Acceso:</b> <input type="text" class="form-control text-right" id="claveAccesoErrorT" readonly /></p>
        <p><b>Mensajes:</b> <textarea class="form-control" id="mensajeErrorT" readonly></textarea></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script type="text/javascript">
  let CONTROLLLER = "{{route('retenciones.autcom_fc')}}";
  $(document).ready(function() {
    $('#log_factura').on('hidden.bs.modal', function() {
      $(this).removeData('bs.modal');
    });
    $('#tablaRetenciones').DataTable({
      "order": [
        [0, "desc"]
      ],
      responsive: true,
      displayLengtn: 25,
      bPaginate: false,
      info: false,
      searching: false,
    });


  });

  function prueba() {
    $.ajax({
      type: 'post',
      url: "{{route('retenciones.autcom_fc')}}",
      data: '',
      success: function(respuesta) {}
    });
  }
  //metodo post retenciones.buscar_proveedor
  function buscar_proveedor() {
    var proveedor = $("#nombre_proveedor").val();
    if (proveedor != "") {
      $.ajax({
        type: 'get',
        url: "{{route('retenciones.buscar_proveedor')}}",
        datatype: 'html',
        data: $("#buscador_form").serialize(),
        success: function(datahtml) {
          //console.log(datahtml);
          $("#resultados").html(datahtml);
          //alert("dsada");
          $("#resultados").show();
          $("#contenedor").hide();
        },
        error: function(data) {
          console.log(data);
        }
      });
    } else {
      $("#resultados").hide();
      $("#contenedor").show();

    }
  }
  $('.select2').select2({
    tags: false
  });
  $("#nombre_proveedor").autocomplete({
    source: function(request, response) {
      $.ajax({
        url: "{{route('compra_buscar_nombreproveedor')}}",
        dataType: "json",
        data: {
          term: request.term
        },
        success: function(data) {
          //console.log(data)
          response(data);
        }
      });
    },
    minLength: 4,
  });

  function secuencia_factura() {

  }

  $("#buscarnombre").autocomplete({
    source: function(request, response) {
      $.ajax({
        method: 'GET',
        url: "{{route('retenciones.nombre_proveedor')}}",
        dataType: "json",
        data: {
          term: request.term
        },
        success: function(data) {
          response(data);
        }
      });
    },
    minLength: 4,
    change: function(event, ui) {}
  });

  $('.select2').select2({
    tags: false
  });

  $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
  });

  function anular(id) {
    /*if (confirm('¿Desea Anular Factura  ?')) {
      $.ajax({
          type: 'get',
          url:"{{ url('contable/compras/factura/')}}/"+id,
          datatype: 'json',
          data: $("#fecha_enviar").serialize(),
          success: function(data){
            swal(`{{trans('contableM.correcto')}}!`,`{{trans('contableM.anulacioncorrecta')}}`,"success");
            location.href ="{{route('compras_index')}}";
          },
          error: function(data){
            console.log(data);
          }
        });
    }else{
      compras.verificar_anulacion
       location.href ="{{route('compras_index')}}";
    }*/

    Swal.fire({
      title: '¿Desea Anular esta comprobante?',
      text: `{{trans('contableM.norevertiraccion')}}!`,
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        var acumulate = "";
        $.ajax({
          type: 'get',
          url: "{{ route('compras.verificar_anulacion')}}",
          datatype: 'json',
          data: {
            'verificar': '3',
            'id_compra': id
          },
          success: function(data) {
            console.log(data);
            if (data.respuesta == 'si') {
              let id_com_egreso = data.id_egreso;
              let enlace = `<a target="_blank" href="{{ url('contable/acreedores/documentos/comprobante/egreso/comprobante/buscar?id=${data.ids[0]}')}}"><b>${data.tablas[0]}</b></a>`;
              let enlace2 = `<a target="_blank" href="{{ url('contable/Banco/debito/acreedores/buscar?id=${data.ids[1]}')}}"> <b>${data.tablas[1]}</b> </a>`;
              let enlace3 = `<a target="_blank" href="{{ url('contable/cruce/valores?id=${data.ids[2]}')}}"><b>${data.tablas[2]}</b></a>`;
              let texto = `Existen algunos ${enlace} ${enlace2} ${enlace3} generados con esta factura`;
              alertas("error", "Error", texto);
            } else {
              test(id);
            }
            //console.log(acumulate);
            /*if (data[0] != 0) {
              acumulate += " Esta factura tiene un comprobante de egreso con el id: " + data[0];
            }
            if (acumulate != "") {
              Swal.fire("Error!", "Existen algunos comprobantes generados con esta factura, observaciones encontradas: <br> " + acumulate, "error");
            } else {
              console.log("entra aqui" + id);
              test(id);
            }*/
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
          url: "{{ url('contable/retenciones/acreedores/anular/')}}/" + id,
          datatype: 'json',
          data: {
            'concepto': text
          },
          success: function(data) {
            Swal.fire(`{{trans('contableM.correcto')}}!`, `{{trans('contableM.anulacioncorrecta')}}`, "success");
            location.href = "{{route('retenciones_index')}}";
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

  function alertas(icon, title, text) {
    Swal.fire({
      icon: `${icon}`,
      title: `${title}`,
      html: `${text}`
    })
  }

  const confirmar = (id) => {
    Swal.fire({
      title: 'Esta seguro que quiere enviar al sri?',
      showDenyButton: false,
      showCancelButton: true,
      confirmButtonText: 'Enviar',
      denyButtonText: 'Cerrar',
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        enviar(id);
      }
    });
  }
  const enviar = (id) => {
    $.ajax({
      type: 'post',
      url: "{{ route('retenciones.send_information') }}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        'id': id
      },
      success: function(data) {
        if (!data.error) {
          Swal.fire({
            icon: 'success',
            title: 'Verificación correcta',
          }).then(() => {
            $('#colEstadoSRI').html('<a style="color: blaCK;">En Proceso</a>')
          })
        }
      },
      error: function(data) {}
    })
  }
  function verErrorXml(num) {
    $.ajax({
      url: "{{ route('sri_electronico/errorGeneral') }}",
      type: 'post',
      data: {
        _token: $('input[name=_token]').val(),
        id: num
      },
      dataType: 'json',
      success: function(json) {
        var texto = '';
        $.each(JSON.parse(json.descripcion_error), function(i, item) {
          texto += item + '<br/>';
        });
        Swal.fire({
          icon: 'error',
          html: texto,
          title: 'Error al generar el XML',
          confirmButtonText: 'Ok',
        });
      }
    });
  }
  function verIErrorTributaria(clave) {
    $.ajax({
      url: "{{ route('sri_electronico/errorTributario') }}",
      type: 'post',
      data: {
        _token: $('input[name=_token]').val(),
        clave: clave
      },
      dataType: 'json',
      success: function(json) {
        console.log(json);
        $('#estadoErrorT').val(json.xml.estado);
        $('#claveAccesoErrorT').val(json.xml.comprobantes.comprobante.claveAcceso);
        $('#mensajeErrorT').val(json.xml.MensajesDb.mensajeDb);
        $('#modalErrorTributaria').modal('show');
      }
    });
  }

  $('.select2_find_proveedor').select2({
    placeholder: "Escriba el nombre del proveedor",
    allowClear: true,
    ajax: {
            url: '{{ route('compras.proveedorsearch') }}',
        data: function(params) {
            var query = {
                search: params.term,
                type: 'public'
            }
            return query;
        },
        processResults: function(data) {
            // Transforms the top-level key of the response object from 'items' to 'results'
            console.log(data);
            return {
                results: data
            };
        }
    }
  });
</script>

@endsection
