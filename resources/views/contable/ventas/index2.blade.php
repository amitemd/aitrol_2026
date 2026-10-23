@extends('contable.ventas.base')
@section('action-content')
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">

<style>
  .ventas-modernas { padding: 18px 20px 30px; background: #f4f7fa; color: #000; }
  .ventas-modernas,
  .ventas-modernas * { color: #000 !important; }
  .ventas-modernas input::placeholder { color: #000 !important; }
  .ventas-modernas .breadcrumb { margin: 0 0 18px; padding: 0; background: transparent; }
  .ventas-modernas .breadcrumb a { color: #087ea4; }
  .ventas-modernas .box { margin: 0; border: 0; background: transparent; box-shadow: none; }
  .ventas-modernas .header_new { display: flex; align-items: center; justify-content: space-between; min-height: 62px; margin-bottom: 18px; padding: 0; border: 0; background: transparent; }
  .ventas-modernas .header_new > div:first-child { flex: 1; width: auto; padding: 0; }
  .ventas-modernas .header_new > div:last-child { width: auto; padding: 0; }
  .ventas-modernas .box-title { color: #17324d; font-size: 28px; font-weight: 700; line-height: 1.2; }
  .ventas-subtitle { display: block; margin-top: 5px; color: #66768a; font-size: 13px; font-weight: 400; }
  .ventas-modernas .head-title { margin: 0; border: 0; border-radius: 10px 10px 0 0; background: #fff; box-shadow: 0 3px 14px rgba(23, 50, 77, .09); }
  .ventas-modernas .cabecera { min-height: auto; padding: 18px 20px 7px; background: transparent; }
  .ventas-modernas .cabecera .color_texto { margin: 0; color: #17324d; font-size: 17px; font-weight: 700; }
  .ventas-modernas .dobra { margin-bottom: 18px; padding: 12px 20px 20px; border-radius: 0 0 10px 10px; background: #fff; box-shadow: 0 5px 14px rgba(23, 50, 77, .09); }
  .ventas-modernas #reporte_master:before, .ventas-modernas #reporte_master:after { display: table; clear: both; content: " "; }
  .ventas-modernas #reporte_master .form-group { margin-bottom: 16px; }
  .ventas-modernas #reporte_master .texto { padding-top: 11px; color: #263d55; font-size: 12px; font-weight: 700; }
  .ventas-modernas .form-control { height: 40px; border: 1px solid #d5dee8; border-radius: 7px; background: #fff; box-shadow: none; color: #17324d; }
  .ventas-modernas .form-control:focus { border-color: #087ea4; box-shadow: 0 0 0 3px rgba(8, 126, 164, .12); }
  .ventas-modernas .input-group-addon { border-color: #d5dee8; border-radius: 7px 0 0 7px; background: #fff; color: #526479; }
  .ventas-modernas .btn-ventas-principal { min-width: 126px; height: 40px; padding: 9px 16px; border: 1px solid #087ea4; border-radius: 7px; background: #087ea4; color: #fff; font-weight: 600; box-shadow: 0 3px 8px rgba(8, 126, 164, .22); transition: background .15s ease, transform .15s ease; }
  .ventas-modernas .btn-ventas-principal,
  .ventas-modernas .btn-ventas-principal * { color: #fff !important; }
  .ventas-modernas .btn-ventas-principal:hover, .ventas-modernas .btn-ventas-principal:focus { border-color: #066b8c; background: #066b8c; color: #fff; transform: translateY(-1px); }
  .ventas-modernas .buscar-wrap { display: flex; justify-content: flex-end; width: 100%; padding-right: 15px; }
  .ventas-modernas .table-responsive { padding: 0; border: 1px solid #dce4ec; border-radius: 8px; }
  .ventas-modernas #example2 { min-width: 1260px; margin: 0; border: 0; }
  .ventas-modernas #example2 > thead > tr > th { padding: 12px 9px; border: 0; border-bottom: 1px solid #dce4ec; background: #edf4f8; color: #000; font-size: 13px !important; font-weight: 700; vertical-align: middle; white-space: nowrap; }
  .ventas-modernas #example2 > tbody > tr > td { padding: 13px 9px; border: 0; border-bottom: 1px solid #edf1f5; color: #000; font-size: 13px; vertical-align: top; }
  .ventas-modernas #example2 > tbody > tr:nth-child(even) { background: #fbfcfd; }
  .ventas-modernas #example2 > tbody > tr:hover { background: #f2f8fb; }
  .ventas-modernas #example2 > tbody > tr > td:nth-child(12) { color: #12813d; font-weight: 700; }
  .ventas-modernas #example2 > tbody > tr > td:last-child { min-width: 188px; padding: 10px 8px; }
  .ventas-modernas #example2 > tbody > tr > td:last-child .btn { float: none !important; display: inline-flex; width: 82px !important; height: 29px !important; min-height: 29px; align-items: center; justify-content: center; margin: 2px !important; padding: 4px 6px !important; border: 1px solid #cbd6e1; border-radius: 6px; background: #fff; color: #40566d; font-size: 11px !important; font-weight: 600; line-height: 1; box-shadow: none; transition: background .15s ease, border-color .15s ease, color .15s ease; }
  .ventas-modernas #example2 > tbody > tr > td:last-child .btn,
  .ventas-modernas #example2 > tbody > tr > td:last-child .btn * { color: #40566d !important; }
  .ventas-modernas #example2 > tbody > tr > td:last-child .btn:hover { border-color: #087ea4; background: #eef8fb; color: #087ea4; text-decoration: none; }
  .ventas-modernas #example2 a[href^="javascript:anular"] { border-color: #efb4b4 !important; color: #c62828 !important; }
  .ventas-modernas #example2 a[href*="ventas_editar"] { border-color: #aad4e2 !important; color: #087ea4 !important; }
  .ventas-modernas #example2 a[href*="generarPdf"], .ventas-modernas #example2 a[href*="tipo=pdf"] { color: #08756a !important; }
  .ventas-modernas #example2 a[href*="descargarXML"], .ventas-modernas #example2 a[href*="tipo=xml"] { color: #218838 !important; }
  .ventas-modernas #example2 a[data-target="#log_factura"] { color: #526479 !important; }
  .ventas-modernas #example2 .btn[disabled] { border-color: #efc7c7 !important; background: #fff5f5 !important; color: #c62828 !important; opacity: .62; }
  .ventas-modernas .dataTables_info { padding-top: 18px; color: #66768a; font-size: 12px; }
  .ventas-modernas .dataTables_paginate { padding-top: 12px; text-align: right; }
  .ventas-modernas .pagination { margin: 0; }
  .ventas-modernas .pagination > li > a, .ventas-modernas .pagination > li > span { margin-left: 4px; border-radius: 5px; color: #087ea4; }
  .ventas-modernas .pagination > .active > span { border-color: #087ea4; background: #087ea4; color: #fff !important; }
  .fv-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px 20px; }
  .fv-field label { display: block; margin-bottom: 7px; color: #263d55; font-size: 12px; font-weight: 700; }
  .fv-filter-actions { display: flex; justify-content: flex-end; gap: 9px; margin-top: 18px; }
  .fv-secondary { min-width: 116px; height: 40px; border: 1px solid #c8d2dd; border-radius: 7px; background: #fff; color: #607086; font-weight: 600; }
  .ventas-modernas .fv-secondary,
  .ventas-modernas .fv-secondary * { color: #607086 !important; }
  .fv-count { display: inline-block; margin-left: 8px; padding: 3px 8px; border: 1px solid #c9e1fb; border-radius: 11px; background: #edf6ff; color: #087ea4; font-size: 10px; vertical-align: middle; }
  .fv-sub { display: block; margin-top: 3px; color: #7a8999; font-size: 10px; }
  .fv-pill { display: inline-block; padding: 4px 8px; border-radius: 11px; font-size: 10px; font-weight: 700; white-space: nowrap; }
  .fv-ok { border: 1px solid #b7e6c7; background: #e8f8ed; color: #18823e; }
  .fv-warn { border: 1px solid #f2d694; background: #fff7e4; color: #986400; }
  .fv-off { border: 1px solid #d6dee7; background: #f4f6f8; color: #657488; }
  .fv-cancel { border: 1px solid #f3b8b8; background: #fff0f0; color: #cf2929; }
  .fv-row-actions { white-space: nowrap; }
  .ventas-modernas #example2 .fv-icon { display: inline-flex; width: 32px !important; height: 32px !important; align-items: center; justify-content: center; margin: 0 5px 0 0 !important; padding: 0 !important; border: 1px solid #cad5e0; border-radius: 6px; background: #fff; color: #354d66; }
  .ventas-modernas #example2 .fv-icon,
  .ventas-modernas #example2 .fv-icon * { color: #354d66 !important; }
  .fv-menu { display: inline-block; position: relative; }
  .fv-menu > summary { display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center; border: 1px solid #cad5e0; border-radius: 6px; background: #fff; color: #354d66; cursor: pointer; list-style: none; }
  .ventas-modernas .fv-menu > summary,
  .ventas-modernas .fv-menu > summary * { color: #354d66 !important; }
  .fv-menu > summary::-webkit-details-marker { display: none; }
  .fv-menu[open] > summary { border-color: #087ea4; color: #087ea4; }
  .fv-menu-list { position: absolute; z-index: 50; top: 37px; right: 0; width: 190px; padding: 6px; border: 1px solid #d8e0e8; border-radius: 7px; background: #fff; box-shadow: 0 9px 24px rgba(24,49,79,.18); }
  .fv-menu-list a { display: block; padding: 7px 9px; border-radius: 4px; color: #3a4f66; font-size: 11px; }
  .fv-menu-list a:hover { background: #eef5fc; color: #087ea4; text-decoration: none; }
  .fv-menu-list i { width: 17px; }
  .fv-danger { margin-top: 4px; padding-top: 9px !important; border-top: 1px solid #e9eef3; color: #d32020 !important; }
  .fv-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 15px; color: #68788a; font-size: 10px; }
  .ventas-modernas #example2 { width: 100%; min-width: 0; table-layout: fixed; }
  .ventas-modernas #example2 th, .ventas-modernas #example2 td { overflow-wrap: anywhere; }
  .ventas-modernas #example2 th:nth-child(1) { width: 8%; }
  .ventas-modernas #example2 th:nth-child(2) { width: 18%; }
  .ventas-modernas #example2 th:nth-child(3) { width: 13%; }
  .ventas-modernas #example2 th:nth-child(4) { width: 17%; }
  .ventas-modernas #example2 th:nth-child(5) { width: 13%; }
  .ventas-modernas #example2 th:nth-child(6) { width: 14%; }
  .ventas-modernas #example2 th:nth-child(7) { width: 9%; }
  .ventas-modernas #example2 th:nth-child(8) { width: 8%; }
  .ventas-modernas #example2 thead th:nth-child(2), .ventas-modernas #example2 thead th:nth-child(7), .ventas-modernas #example2 thead th:nth-child(8) { font-size: 0 !important; }
  .ventas-modernas #example2 thead th:nth-child(2):after { content: 'N.º factura'; font-size: 13px; }
  .ventas-modernas #example2 thead th:nth-child(7):after { content: 'Estado SRI'; font-size: 13px; }
  .ventas-modernas #example2 thead th:nth-child(8):after { content: 'Acciones'; font-size: 13px; }
  @media (max-width: 767px) {
    .ventas-modernas { padding: 14px 10px 24px; }
    .ventas-modernas .header_new { align-items: flex-start; flex-direction: column; gap: 14px; }
    .ventas-modernas .box-title { font-size: 24px; }
    .ventas-modernas .header_new > div:last-child, .ventas-modernas .btn-ventas-principal { width: 100%; }
    .ventas-modernas .buscar-wrap { padding: 0 15px; }
    .fv-grid { grid-template-columns: 1fr; }
    .fv-filter-actions .btn { flex: 1; }
    .fv-footer { align-items: flex-start; flex-direction: column; gap: 10px; }
  }
</style>

<!-- Main content -->
<section class="content ventas-modernas">
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
        <h3 class="box-title">Facturas de venta conglomerada <span class="ventas-subtitle">Consulte, administre y controle las facturas de venta generadas.</span></h3>
      </div>
      <div class="col-md-1 text-right">
        <button onclick="location.href='{{route('insumos')}}'" class="btn btn-ventas-principal">
          <i class="fa fa-plus" aria-hidden="true"></i>&nbsp; Nueva factura
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCAR FACTURAS CONGLOMERADAS</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('venta_index2_buscar') }}">
        {{ csrf_field() }}
        <div class="fv-grid">
        <div class="fv-field">
          <label for="id">ID</label>
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Id..." />
        </div>
        <div class="fv-field">
          <label for="numero">Número</label>
          <input class="form-control" type="text" id="numero" name="numero" value="@if(isset($searchingVals)){{$searchingVals['numero']}}@endif" placeholder="Ingrese número..." />
        </div>
        <div class="fv-field">
          <label for="id_asiento">Asiento</label>
          <input class="form-control" type="text" id="id_asiento" name="id_asiento" value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif" placeholder="Ingrese asiento..." />
        </div>

        <div class="fv-field">
          <label for="fecha">Fecha</label>
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha']}}@endif">
          </div>
        </div>
        <div class="fv-field">
          <label for="nombre_cliente">Cliente</label>
          <input class="form-control" type="text" id="nombre_cliente" name="nombre_cliente" value="@if(isset($searchingVals)){{$searchingVals['nombre_cliente']}}@endif" placeholder="Ingrese el nombre del cliente..." />
        </div>
        <div class="fv-field">
          <label for="nombres_paciente">Paciente</label>
          <input class="form-control" type="text" id="nombres_paciente" name="nombres_paciente" value="@if(isset($searchingVals)){{$searchingVals['nombres_paciente']}}@endif" placeholder="Ingrese el nombre del paciente..." />
        </div>
        </div>
        <div class="fv-filter-actions">
          <button type="button" id="limpiarFiltros" class="btn fv-secondary"><i class="fa fa-eraser"></i>&nbsp; Limpiar</button>
          <button type="submit" id="buscarAsiento" class="btn btn-ventas-principal">
            <i class="fa fa-search" aria-hidden="true"></i>&nbsp; Buscar
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">Ventas <span class="fv-count">{{$ventas->total()}} registros</span></label>
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
                        <th>Código</th>
                        <th >Número_Factura</th>
                        <th >Fecha Factura</th>
                        <th >Cliente</th>
                        <!--<th >RUC/CID</th>-->
                        <th >Seguro</th>
                        <th >Creado por </th>
                        <th  style="font-size: 10px;">Electronica</th>
                        <th>Acción</th>
                      </tr>
                    </thead>
                    <tbody>

                      @foreach($ventas as $value)
                      <tr class="well">
                        <td>@if(!is_null($value->id)){{$value->id}}@endif</td>
                        <td>@if(!is_null($value->nro_comprobante)){{$value->nro_comprobante}}@endif</td>
                        <td>@if(!is_null($value->fecha)){{substr($value->fecha,0,11)}}@endif</td>
                        @php
                        $cliente = \Sis_medico\Ct_Clientes::where('identificacion',$value->id_cliente)->first();
                        $seguro = Sis_medico\Seguro::find($value->seguro_paciente);
                        @endphp
                        <td>@if(!is_null($cliente) && !is_null($cliente->nombre)){{$cliente->nombre}}@endif<span class="fv-sub">@if(!is_null($value->nombres_paciente)){{$value->nombres_paciente}}@endif</span></td>
                        <td>@if(!is_null($seguro)){{$seguro->nombre}}@endif</td>
                        <td>@if(isset($value->usuario)) {{$value->usuario->nombre1}} {{$value->usuario->apellido1}} @endif</td>
                        @if($value->estado == 0)
                          <td><span class="fv-pill fv-cancel">Anulada</span>@if(isset($value->usuariomod))<span class="fv-sub">Por {{$value->usuariomod->nombre1}} {{$value->usuariomod->apellido1}}</span>@endif</td>
                        @elseif($value->electronica == 0)
                          <td><span class="fv-pill fv-off">No electrónica</span></td>
                        @else
                          @if($value->estado_electronica == 3)
                            <td><span class="fv-pill fv-ok">Autorizado</span></td>
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
                                    echo '<td><span class="fv-pill fv-ok">Autorizado</span></td>';
                                  }elseif($envio->details->log[0]->mensaje == "Documento recibido"){
                                    $estado = 2;
                                    echo '<td><span class="fv-pill fv-warn">Enviado SRI</span></td>';
                                  }elseif($envio->details->log[0]->proceso == "Firma"){
                                    $estado = 1;
                                    echo '<td><span class="fv-pill fv-warn">Firmado</span></td>';
                                  }else{
                                    echo '<td><span class="fv-pill fv-warn">Enviado</span></td>';
                                  }
                                }else{
                                  echo '<td><span class="fv-pill fv-warn">Pendiente de envío</span></td>';
                                }
                                $documento->estado_electronica =  $estado;
                                $documento->save();
                              @endphp
                            @else
                              <td><span class="fv-pill fv-warn">Pendiente de envío</span></td>
                            @endif
                          @endif
                        @endif
                        <td class="fv-row-actions">
                          <a href="{{ route('ventas_editar', ['id' => $value->id]) }}" class="fv-icon" title="Visualizar factura"><i class="fa fa-eye"></i></a>
                          <details class="fv-menu"><summary title="Más acciones"><i class="fa fa-ellipsis-v"></i></summary><div class="fv-menu-list">
                          <a href="{{ route('ventas_editar', ['id' => $value->id]) }}"><i class="fa fa-eye"></i> Visualizar factura</a>
                          @if(!empty($value->rutapdf))
                          <a target="_blank" href="{{ route('pdf.visualizar', ['id' => $value->id]) }}"><i class="fa fa-file-pdf-o"></i> Ver archivo</a>
                          @endif
                          <!--<a class="btn btn-warning col-md-3 col-xs-3 btn-margin btn-gray" data-remote="{{route('detalle_paquete.facturacion',['id' => $value->id])}}" data-toggle="modal" data-target="#detalle_paquete" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Detall Ord</a>-->
                          @if($value->orden_venta!=null)
                           <a target="_blank" href="{{ route('pdf_comprobante_detalle.paquete', ['id' => $value->id]) }}"><i class="fa fa-list-alt"></i> Detalle de orden</a>
                          @endif
                          @if($value->electronica == 0)
                          @if($value->ip_creacion=="OMNI")
                          <a target="_blank" href="{{ route('ventas.pdf_omni', ['id' => $value->id]) }}"><i class="fa fa-file-pdf-o"></i> Descargar PDF</a>
                          @else
                          <a target="_blank" href="{{ route('pdf_comprobante_no.tributario', ['id' => $value->id]) }}"><i class="fa fa-file-pdf-o"></i> Descargar PDF</a>
                          @endif
                          @else
                            
                          @php $numComp = explode('-',$value->nro_comprobante); @endphp

                          @if(count($numComp)>1 && $numComp[1]==100 )

                          <a target="_blank" href="{{asset('/')}}api_doc_electronico?opcion=generarPdf&clave={{$value->nro_autorizacion}}"><i class="fa fa-file-pdf-o"></i> Descargar RIDE</a>
                          <a target="_blank" href="{{asset('/')}}api_doc_electronico?opcion=descargarXML&clave={{$value->nro_autorizacion}}"><i class="fa fa-code"></i> Descargar XML</a>
                          @else
                          <a target="_blank" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'pdf']) }}"><i class="fa fa-file-pdf-o"></i> Descargar RIDE</a>

                          <a target="_blank" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'xml']) }}"><i class="fa fa-code"></i> Descargar XML</a>
                          @endif

                          <a data-toggle="modal" data-target="#log_factura" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'comprobante']) }}"><i class="fa fa-list"></i> Ver log</a>
                          @endif
                          @if($value->estado == 1)<a href="javascript:anular({{$value->id}});" class="fv-danger"><i class="fa fa-ban"></i> Anular factura</a>@else<a class="fv-danger" style="opacity:.55;cursor:not-allowed"><i class="fa fa-ban"></i> Factura anulada</a>@endif
                          </div></details>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="fv-footer">
                <div>
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($ventas->currentPage() - 1) * $ventas->perPage())}} / {{count($ventas) + (($ventas->currentPage() - 1) * $ventas->perPage())}} de {{$ventas->total()}} registros</div>
                </div>
                <div>
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
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
  });
  $(document).ready(function() {
    $('#limpiarFiltros').on('click', function() {
      $('#reporte_master').find('input[type="text"]').val('');
      $('#id').focus();
    });
    $(document).on('click', '.fv-menu > summary', function() {
      var current = $(this).parent()[0];
      $('.fv-menu[open]').each(function() { if (this !== current) this.removeAttribute('open'); });
    });
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