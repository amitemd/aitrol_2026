@extends('contable.ventas.base')
@section('action-content')
@vite('resources/js/aitrol-datatables.js')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
<style>
  <?php

  use Illuminate\Support\Facades\Auth;
  use Sis_medico\Ct_Clientes;
  use Sis_medico\Ct_ventas;
  use Sis_medico\De_Empresa;
  use Sis_medico\Http\Controllers\ApiFacturacionController;

  if (Auth::user()->id == "0957258056") {
  ?>* {
    font-family: 'Poppins',
      sans-serif;
  }

  .sidebar-menu>li>a {
    font-size: 13px;
  }

  .well>td {
    font-size: 12px;
    font-weight: 600;
  }

  th {
    font-size: 12px !important;
  }

  <?php
  }

  ?>
</style>
<!-- Ventana modal editar -->
<?php
$deempresa = De_Empresa::getDatos(session('id_empresa'));
/*if (Auth::user()->id == '0921605895') {
  echo '<pre>';
  print_r($deempresa);
  exit;
}*/
$ventas = Ct_ventas::where('estado', '<', 2)
  ->where('id_empresa', session('id_empresa'))
  ->where('tipo', 'VEN-FA')
  ->where('electronica', 1)
  ->where('punto_emision', '!=', 100)
  ->where('estado_electronica', '<>', 3)
  ->where('estado', '<>', 0)
  ->whereYear('fecha', '>', 2021)
  ->orderby('id', 'desc')
  ->paginate(10);
/*if (Auth::user()->id == '0921605895') {
    echo '<pre>';
    print_r($ventas);
    exit;
  }*/
if (count($ventas) > 0) {
  foreach ($ventas as $value) {
    if ($value->punto_emision != 100) {
      $cliente = Ct_Clientes::where('identificacion', $value->id_cliente)->first();
      $seguro = Sis_medico\Seguro::find($value->seguro_paciente);
      $fecha1 = new DateTime($value->created_at); //fecha inicial
      $fecha2 = new DateTime(date('Y-m-d H:i:s')); //fecha de cierre
      $intervalo = $fecha1->diff($fecha2);
      $minutos = $intervalo->format('%i');
      $horas = $intervalo->format('%h');
      $tiempo = $minutos + ($horas * 60);
      if ($tiempo > 5) {
        $data['empresa']     = $empresa->id;
        $data['comprobante'] = $value->nro_comprobante;
        $data['tipo'] = 'comprobante';
        try {
          $envio = ApiFacturacionController::estado_comprobante($data);
        } catch (Exception $e) {
        }
        $estado = 0;
        $documento = Ct_ventas::find($value->id);
        if (isset($envio->details)) {
          if ($envio->details->log[0]->proceso == 'Notificación' || $envio->details->log[0]->mensaje == 'Autorizado') {
            $estado = 3;
            $documento->nro_autorizacion =  $envio->details->autorizacion;
          } elseif ($envio->details->log[0]->mensaje == "Documento recibido") {
            $estado = 2;
          } elseif ($envio->details->log[0]->proceso == "Firma") {
            $estado = 1;
          } else {
          }
        }
        $documento->estado_electronica =  $estado;
        $documento->update();
      }
    }
  }
}
?>
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css') }}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<input type="hidden" id="deempresa" name="deempresa" />
<div class="modal fade" id="detalle_paquete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="width: 95%;">
    </div>
  </div>
</div>
<div class="modal fade" id="edit_orde_det_paq" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="width: 95%;">
    </div>
  </div>
</div>
<div class="modal fade" id="log_factura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<div class="modal fade" id="md_planilla" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<div class="modal fade bd-example-modal-lg" id="myModalDocumentos" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
    </div>
  </div>
</div>




<!-- Main content -->
<section class="content aitrol-page aitrol-sales-page">
  <div class="aitrol-sales-toolbar">
    <a href="{{ route('ventas_crear') }}" class="aitrol-primary-action aitrol-primary-action--subtle">
      <i class="fa fa-plus" aria-hidden="true"></i>
      <span>{{trans('wfact_venta.Nueva Factura')}}</span>
    </a>
  </div>
  <div class="box aitrol-card">
    <div class="aitrol-section-heading">
      <span>{{trans('wfact_venta.BUSCADOR FACTURA DE VENTA')}}</span>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra aitrol-filter-panel">
      <form method="POST" id="reporte_master" action="{{ route('ventas_search') }}">
        {{ csrf_field() }}
        <input type="hidden" id="page" name="page" value="{{ config('data.page') }}" />
        <div class="row">
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="numero">{{trans('wfact_venta.Id')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="{{trans('wfact_venta.Ingrese_id')}}" />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="numero">{{trans('wfact_venta.Numero')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="numero" name="numero" value="@if(isset($searchingVals)){{$searchingVals['nro_comprobante']}}@endif" placeholder="{{trans('wfact_venta.Ingrese_numero')}}" />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="asiento">{{trans('wfact_venta.Asiento')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="id_asiento" name="id_asiento" value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif" placeholder="{{trans('wfact_venta.Ingrese_asiento')}}" />
        </div>
        </div>
        <div class="row">
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">{{trans('wfact_venta.Fecha')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="date" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha']}}@endif">
          </div>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="cliente">{{trans('wfact_venta.Cliente')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="nombre_cliente" name="nombre_cliente" value="@if(isset($searchingVals)){{$searchingVals['nombre_cliente']}}@endif" placeholder="{{trans('wfact_venta.Ingrese_cliente')}}" />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="paciente">{{trans('wfact_venta.Paciente')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="nombres_paciente" name="nombres_paciente" value="@if(isset($searchingVals)){{$searchingVals['nombres_paciente']}}@endif" placeholder="{{trans('wfact_venta.Ingrese_paciente')}}" />
        </div>
        </div>
        <div class="aitrol-filter-actions">
            <button type="submit" class="btn btn-primary">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
            {{ trans('contableM.buscar') }}
          </button>
        </div>
      </form>
    </div>
    <div class="aitrol-section-heading">
      <span>{{trans('wfact_venta.Ventas')}}</span>
    </div>
    <div class="box-body dobra aitrol-table-panel">
      <div class="form-group col-md-12">
        <div class="form-row">
          <div id="resultados">
          </div>
          <div id="example2_wrapper">
            <div class="row">
              <div class="table-responsive aitrol-table-scroll col-md-12">
                <table id="tablaVentas" class="table table-hover aitrol-table" role="grid" aria-describedby="example2_info" width="100%"
                  data-aitrol-datatable
                  data-paging="false"
                  data-searching="false"
                  data-info="false"
                  data-order-column="0"
                  data-order-direction="desc"
                  data-export-name="Estado de documentos electrónicos">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>ID Asiento</th>
                      <th>{{trans('wfact_venta.accion')}}</th>
                      <th>{{trans('wfact_venta.estadosri')}}</th>
                      @if($empresa->inventario == 1 && $empresa->egreso_kardex == 1) <th>Egreso</th> @endif
                      <th>{{trans('wfact_venta.Comprobante')}}</th>
                      <th class="text-right">Valor sin IVA</th>
                      <th>{{trans('wfact_venta.Fecha')}}</th>
                      <th>{{trans('wfact_venta.Cliente')}}</th>
                      <th>{{trans('wfact_venta.Paciente')}}</th>
                      <th>{{trans('wfact_venta.seguro')}}</th>
                      <th>{{trans('wfact_venta.Procedimiento')}}</th>
                      <th>{{trans('wfact_venta.fecha_procedimiento')}}</th>
                      <th>{{trans('wfact_venta.creado_por')}}</th>
                      <th>{{trans('wfact_venta.anulado_por')}}</th>
                      <!--<th>{{trans('wfact_venta.informacion')}}</th>-->
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $tabla = '';
                    $abbreviateUserName = static function (mixed $firstName, mixed $lastName): string {
                      $firstName = trim((string) $firstName);
                      $lastName = mb_convert_case(trim((string) $lastName), MB_CASE_TITLE, 'UTF-8');
                      $initial = $firstName !== '' ? mb_strtoupper(mb_substr($firstName, 0, 1, 'UTF-8'), 'UTF-8') . '.' : '';

                      return trim($initial . ' ' . $lastName);
                    };
                    foreach ($dato as $row) {
                      $venta_kardex = \Sis_medico\Venta_Kardex::where('id_venta', $row->id)->first();
                      $tabla .= '<tr><td style="color:#4CA5D5;">' . $row->id . '</td><td><a href="' . asset('/') . 'contable/ventas/editar/' . $row->id . '"> ' . $row->id_asiento . '</a></td>';
                      $variable = [];
                      $tipo = 'Factura';
                      $actionMenuId = 'venta-actions-' . $row->id;
                      if ($row->nro_comprobante !== null) {
                        $variable = explode('-', $row->nro_comprobante);
                      }
                      //if (in_array('100', $variable)) {
                      if ($row->emisor == 'MD' || in_array('100', $variable)) {
                        $tabla .= '<td><div class="dropdown aitrol-action-menu"><button class="btn btn-xs aitrol-action-menu__trigger" type="button" id="' . $actionMenuId . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Abrir acciones de la factura ' . $row->id . '"><i class="fa fa-cog" aria-hidden="true"></i><i class="fa fa-caret-down" aria-hidden="true"></i></button>';
                        $tabla .= '<div class="dropdown-menu aitrol-action-menu__panel" aria-labelledby="' . $actionMenuId . '">';
                        if ($row->orden_venta != null) {
                          /* Revisar la modal */
                          $tabla .= '<a target="_blank" href="' . route('pdf_comprobante_detalle.paquete', ['id' => $row->id]) . '" class="btn btn-xs" style="text-align: left; color: black; padding: 5px 0px 5px 15px;"> <i class="fa fa-list"></i> Detalle orden</a>';
                        }
                        if ($row->doc_electronico == 5) {
                          $tabla .= '<a href="' . asset('/') . 'api_doc_electronico?opcion=descargarXML&clave=' . $row->nro_autorizacion . '" class="btn btn-xs " style="text-align: left; color: black; padding: 5px 0px 5px 15px;"><i class="fa fa-file-code-o"></i> Exportar XML</a>';
                          $tabla .= '<a href="' . asset('/') . 'api_doc_electronico?opcion=generarPdf&clave=' . $row->nro_autorizacion . '" class="btn btn-xs " style="text-align: left; color: black; padding: 5px 0px 5px 15px;"><i class="fa fa-file-pdf-o"></i> Exportar PDF</a>';
                        }
                        // $tabla .= '<a href="#" data-toggle="modal" onclick="limpiarModal();logFactura(\'' . $row->id . '\');" data-target="#log_factura" class="btn btn-xs" style="text-align: left; color: black; padding: 5px 0px 5px 15px;"><i class="fa fa-align-left"></i> Log Factura</a>';
                        $tabla .= '<a href="#" data-toggle="modal" onclick="limpiarModal();verLogDocumento(\'' . $row->id . '\',\'' . $tipo . '\');" data-target="#myModalDocumentos" class="btn btn-xs" style="text-align: left; color: black; padding: 5px 0px 5px 15px;"><i class="fa fa-align-left"></i> Log Factura</a>';
                        $tabla .= '<a href="' . asset('/') . 'contable/ventas/descargarnotaentrega?id=' . $row->id . '" class="btn btn-xs" style="text-align: left; color: black; padding: 5px 0px 5px 15px;"><i class="fa fa-sign-out"></i> Nota entrega</a>';
                        $tabla .= '<a href="' . asset('/') . 'contable/ventas/editar/' . $row->id . '" class="btn btn-xs" style="text-align: left; color: black; padding: 5px 0px 5px 15px;"><i class="fa fa-eye"></i> Visualizar</a><a  target="_blank" href="' . asset('/') . 'v3/ventas/descargarComprobanteVenta?id=' . $row->id . '" class="btn btn-xs" style="text-align: left; color: black; padding: 5px 0px 5px 15px;"><i class="fa fa-file-pdf-o"></i> Comprobante de venta</a>';
                        if ($row->estado == 0) {
                          $tabla .= '<a href="#" class="btn-xs dropdown-item aitrol-action-menu__item--danger" style="text-align: left; color: red; padding: 5px 0px 5px 15px;" ><i class="fa fa-minus"></i> Anulado</a>';
                        } else {
                          $tabla .= '<a onclick="anular(' . $row->id . ')" class="btn btn-xs button aitrol-action-menu__item--danger" style="text-align: left; color: red; padding: 5px 0px 5px 15px;"><i class="fa fa-trash" ></i> Anular</a>';
                        }
                        $tabla .= '</div>';
                        $tabla .= '</td>';
                      } else {
                        $tabla .= '<td><div class="dropdown aitrol-action-menu"><button class="btn btn-xs aitrol-action-menu__trigger" type="button" id="' . $actionMenuId . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Abrir acciones de la factura ' . $row->id . '"><i class="fa fa-cog" aria-hidden="true"></i><i class="fa fa-caret-down" aria-hidden="true"></i></button>';
                        $tabla .= '<div class="dropdown-menu aitrol-action-menu__panel" aria-labelledby="' . $actionMenuId . '">';
                        if ($row->rutapdf != '') {
                          $tabla .= '<a target="_blank" href="' . route('pdf.visualizar', ['id' => $row->id])  . '" class="btn btn-xs" style="text-align: left; color: black; padding: 5px 0px 5px 15px;"> Archivo</a>';
                        }
                        if ($row->orden_venta != null) {
                          $tabla .= '<a target="_blank" href="' . route('pdf_comprobante_detalle.paquete', ['id' => $row->id]) . '" class="btn btn-xs" style="text-align: left; color: black; padding: 5px 0px 5px 15px;"> <i class="fa fa-list"></i> Detalle orden</a>';
                        }
                        if ($row->electronica == 0) {
                          if ($row->ip_creacion == 'OMNI') {
                            $tabla .= '<a target="_blank" href="' . route('ventas.pdf_omni', ['id' => $row->id]) . '" class="btn btn-xs" style="text-align: left; color: black; padding: 5px 0px 5px 15px;"><i class="fa fa-file-pdf-o"></i> Exportar PDF</a>';
                          } else {
                            $tabla .= '<a target="_blank" href="' . asset('/') . 'contable/ventas/comprobante/no.tributario/' . $row->id . '" class="btn btn-xs" style="text-align: left; color: black; padding: 5px 0px 5px 15px;"><i class="fa fa-file-pdf-o"></i> Exportar PDF</a>';
                          }
                        } else {
                          $tabla .= '<a target="_blank" href="' . asset('/') . 'contable/ventas/ride_pdf/' . $row->nro_comprobante . '/' . $row->id_empresa . '/pdf" class="btn btn-xs " style="text-align: left; color: black; padding: 5px 0px 5px 15px;"><i class="fa fa-file-pdf-o"></i> Ride Factura</a>';
                          $tabla .= '<a target="_blank" href="' . asset('/') . 'contable/ventas/ride_pdf/' . $row->nro_comprobante . '/' . $row->id_empresa . '/xml" class="btn btn-xs " style="text-align: left; color: black; padding: 5px 0px 5px 15px;"><i class="fa fa-file-code-o"></i> Exportar XML</a>';
                          $tabla .= '<a data-toggle="modal" data-target="#log_factura" href="' . asset('/') . 'contable/ventas/ride_pdf/' . $row->nro_comprobante . '/' . $row->id_empresa . '/comprobante" class="btn btn-xs " style="text-align: left; color: black; padding: 5px 0px 5px 15px;"><i class="fa fa-align-left"></i> Log factura</a>';
                        }
                        if ($usuario_actual == '0957258056') {
                          $tabla .= '<a  onclick="enviarSri(' . $row->id . ')" class="btn btn-xs " style="text-align: left; margin-left: 0px; color: black; padding: 5px 0px 5px 15px;">Enviar SRI</a>';
                        }
                        $tabla .= '<a href="' . asset('/') . 'contable/ventas/editar/' . $row->id . '" class="btn btn-xs " style="text-align: left; color: black; padding: 5px 0px 5px 15px;"><i class="fa fa-eye"></i>Visualizar</a>';
                        if ($row->estado == 1) {
                          $tabla .= '<a onclick="anular(' . $row->id . ')" class="btn btn-xs button aitrol-action-menu__item--danger" style="text-align: left; color: red; padding: 5px 0px 5px 15px;"><i class="fa fa-trash" ></i> Anular</a>';
                        } else {
                          $tabla .= '<a href="#" class="btn-xs dropdown-item aitrol-action-menu__item--danger" style="text-align: left; color: red; padding: 5px 0px 5px 15px;" ><i class="fa fa-minus"></i> Anulado</a>';
                        }
                        $tabla .= '</div>';
                        $tabla .= '</td>';
                      }
                      $tabla .= '<td>';
                      if ($row->estado == '0') {
                        $sriStatusClass = 'aitrol-sri-status--annulled';
                        $sriStatusIcon = 'fa-times-circle';
                        $sriStatusLabel = 'Anulado';
                      } else {
                        if (in_array('100', $variable)) {
                          $isSriAuthorized = $row->doc_electronico == 5;
                        } else {
                          $isSriAuthorized = $row->estado_electronica == 3;
                        }

                        $sriStatusClass = $isSriAuthorized
                          ? 'aitrol-sri-status--authorized'
                          : 'aitrol-sri-status--pending';
                        $sriStatusIcon = $isSriAuthorized ? 'fa-check-circle' : 'fa-clock-o';
                        $sriStatusLabel = $isSriAuthorized ? 'Autorizado' : 'Sin autorizar';
                      }
                      $tabla .= '<span class="aitrol-sri-status ' . $sriStatusClass . '" title="Estado SRI: ' . $sriStatusLabel . '" aria-label="Estado SRI: ' . $sriStatusLabel . '">';
                      $tabla .= '<i class="fa ' . $sriStatusIcon . '" aria-hidden="true"></i>';
                      $tabla .= '</span>';
                      $tabla .= '</td>';
                      if ($empresa->inventario == 1 && $empresa->egreso_kardex == 1) {
                        if (!is_null($venta_kardex)) {
                          $tabla .= '<td></td>';
                        } else {
                          $tabla .= '<td> <a href="' . asset('/') . 'contable/ventas/asignar/egreso/' . $row->id . '" class="btn btn-danger btn-xs" type="button"> Pendiente Asignar Egreso </a> </td>';
                        }
                      }
                      $tabla .= '<td>';
                      $tabla .= '<a href="' . asset('/') . 'contable/ventas/editarCabeceraFactura?id=' . $row->id . '">' . $row->nro_comprobante . '</a>';
                      $tabla .= '</td>';
                      $tabla .= '<td class="text-right" data-order="' . $row->valor_sin_iva . '">';
                      $tabla .= '<a class="aitrol-money-value" href="' . asset('/') . 'contable/ventas/editarCabeceraFactura?id=' . $row->id . '">$ ' . number_format((float) $row->valor_sin_iva, 2, '.', ',') . '</a>';
                      $tabla .= '</td>';
                      $tabla .= '<td>';
                      $tabla .= '<a href="' . asset('/') . 'contable/ventas/editarCabeceraFactura?id=' . $row->id . '">' . $row->fecha . '</a>';
                      $tabla .= '</td>';
                      $tabla .= '<td>';
                      $tabla .= '<a href="' . asset('/') . 'contable/ventas/editarCabeceraFactura?id=' . $row->id . '">' . $row->nombre_cliente . '</a>';
                      $tabla .= '</td>';
                      $tabla .= '<td>';
                      $tabla .= '<a href="' . asset('/') . 'contable/ventas/editarCabeceraFactura?id=' . $row->id . '">' . $row->nombres_paciente . '</a>';
                      $tabla .= '</td>';
                      $tabla .= '<td>';
                      $tabla .= '<a href="' . asset('/') . 'contable/ventas/editarCabeceraFactura?id=' . $row->id . '">' . $row->nombre . '</a>';
                      $tabla .= '</td>';
                      $tabla .= '<td>';
                      $tabla .= '<a href="' . asset('/') . 'contable/ventas/editarCabeceraFactura?id=' . $row->id . '">' . $row->procedimientos . '</a>';
                      $tabla .= '</td>';
                      $tabla .= '<td>';
                      $fechapro = $row->fecha_procedimiento != '' ? date('Y-m-d', strtotime($row->fecha_procedimiento)) : '';
                      //$fechapro = $row->fecha_procedimiento;
                      $tabla .= '<a href="' . asset('/') . 'contable/ventas/editarCabeceraFactura?id=' . $row->id . '">' . $fechapro . '</a>';
                      $tabla .= '</td>';
                      $tabla .= '<td>';
                      $tabla .= '<a href="' . asset('/') . 'contable/ventas/editarCabeceraFactura?id=' . $row->id . '">' . e($abbreviateUserName($row->nombre1, $row->apellido1)) . '</a>';
                      $tabla .= '</td>';
                      $tabla .= '<td>';
                      if ($row->estado == 0) {
                        $tabla .= '<a href="' . asset('/') . 'contable/ventas/editarCabeceraFactura?id=' . $row->id . '">' . e($abbreviateUserName($row->nombremod1, $row->apellidomod1)) . '</a>';
                      } else {
                        $tabla .= '';
                      }
                      $tabla .= '</td>';
                    }
                    echo $tabla;
                    ?>
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="row aitrol-table-summary">
              <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                  Mostrando {{ 1 + ($dato->currentPage() - 1) * $dato->perPage() }} /
                  {{ count($dato) + ($dato->currentPage() - 1) * $dato->perPage() }} de
                  {{ $dato->total() }} registros
                </div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $dato->appends(Request::all())->links() }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script>
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
  //contable/ventas/factura/
  function anular(id) {
    Swal.fire({
      title: "{{trans('wfact_venta.anular_fact')}}",
      text: "{{trans('wfact_venta.no_revertir')}}",
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: `{{trans('wfact_venta.si')}}`
    }).then((result) => {
      if (result.isConfirmed) {
        let ids = "";
        var acumulate = "";
        var mensaje = "";
        let msj_ingr = "";
        let msj_ret = "";
        let msj_cruce = "";
        let msj_che = "";
        let msj_cruce_cue = "";
        let msj_credito = "";
        let mensajeaux = "";
        $.ajax({
          type: 'get',
          url: "{{ route('ventas.verificar')}}",
          datatype: 'json',
          data: {
            'verificacion': '1',
            'id_venta': id
          },
          success: function(data) {
            console.log(data)

              if (typeof(data.ingreso) != "undefined") {
                if (data.ingreso.length > 0) {
                  for (let i = 0; i < data.ingreso.length; i++) {
                    mensaje = `${data.ingreso[i]}`;
                  }
                  msj_ingr = ` Ingreso: ${mensaje}`;
                  mensaje = "ok";
                }
              }
              if (typeof(data.retencion) != "undefined") {
                msj_ret = ` Retencion: ${data.retencion}`;
                mensaje = "ok";
              }
              if (typeof(data.cruce) != "undefined") {
                if (data.cruce.length > 0) {
                  for (let i = 0; i < data.cruce.length; i++) {
                    mensaje = `${data.cruce[i]}`;
                  }
                  msj_cruce = ` Cruces: ${mensaje}`;
                  mensaje = "ok";
                }
              }
              if (typeof(data.chequepost) != "undefined") {
                for (let i = 0; i < data.chequepost.length; i++) {
                  mensaje = `${data.chequepost[i]}`;
                }
                msj_che = ` Chequepost: ${mensaje}`;
                mensaje = "ok";
              }
              if (typeof(data.cruce_cuentas) != "undefined") {
                if (data.cruce_cuentas.length > 0) {
                  for (let i = 0; i < data.cruce_cuentas.length; i++) {
                    mensaje = `${data.cruce_cuentas[i]}`;
                  }
                  msj_cruce = ` Cruce cuentas: ${mensaje}`;
                  mensaje = "ok";
                }
              }
              if (typeof(data.credito) != "undefined" && data.credito.length > 0) {
                for (let i = 0; i < data.credito.length; i++) {
                  mensaje = `${data.credito[i]}`;
                }
                msj_credito = ` Credito: ${mensaje}`;
                mensaje = "ok";
              }

              if (mensaje != "") {
                Swal.fire("{{ trans('wfact_venta.error') }}",
                  `{{ trans('wfact_venta.observacion_encontrada') }} ${msj_ingr}${msj_ret}${msj_cruce}${msj_che}${msj_cruce}${msj_credito}`,
                  "error");
              } else {
                test(id);
            }
            /*console.log(data);
            if (data[1] != 0) {
              acumulate += "Existe formas de pago, con el id : " + data[1] + " <br> ";
            }
            if (data[2] != 0) {
              acumulate += "Existe formas de pago, con el id : " + data[2] + " <br> ";
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
        inputPlaceholder: `{{trans('wfact_venta.motivo_anulacion ')}}`,
        inputAttributes: {
          'aria-label': `{{trans('wfact_venta.motivo_anulacion ')}}`
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
            if (data.status == "success") {
              Swal.fire("{{ trans('wfact_venta.correcto') }}", "{{ trans('wfact_venta.anulacion_correcta') }}", "success");
              setTimeout(() => {
                location.href = "{{ route('venta_index') }}";
              }, 1500);
            } else {
              Swal.fire("Error", "Error al anular", "error");
            }

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

  function limpiarModal() {
    $('.modal-content').html('');
  }

  function logFactura(id) {

    $.ajax({
      url: "ventas/logFactura",
      type: 'get',
      data: {

        id: id,
        tipo: '01',

      },
      dataType: 'html',
      success: function(html) {
        console.log(html);
        $('.modal-content').html(html);
      }
    });
  }


  function verLogDocumento(id, tipo) {
    var data = {
      id: id,
      tipo: tipo,
      _token: $('input[name=_token]').val(),
    };
    $.ajax({
      url: "{{url('v3/tributario/verLogDocumento')}}",
      type: 'post',
      data: data,
      dataType: 'html',
      success: function(html) {
        $('#myModalDocumentos .modal-content').html(`
                <div class="modal-header">
                    <h5 class="modal-title" id="titulo_modal_documentos"><i class="fa fa-plus-square"></i> Log Facturación  ${tipo} :  ${id}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="cuerpoDocumento">
                    ${html} <!-- Aquí va la respuesta HTML que devuelve el servidor -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            `);
      }
    });
  }
</script>
@endsection
