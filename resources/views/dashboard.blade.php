<?php
$rolUsuario = Auth::user()->id_tipo_usuario;
$empresa = \Sis_medico\Empresa::where('prioridad', '1')->first();

?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- <title>SM | Sistema Médico</title> -->
  <title>
    @if($empresa->compras == 0) Aitrol @else {{$empresa->nombrecomercial}} @endif
  </title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3 + AdminLTE 2 + skin del sistema legado -->
  <link href="{{ asset('/css/app-template.css') }}?v={{ filemtime(public_path('css/app-template.css')) }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('/css/new-system-legacy.css') }}?v={{ filemtime(public_path('css/new-system-legacy.css')) }}" rel="stylesheet" type="text/css" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
</head>
<style>
  * {
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
  }

  .title {
    background: #fff !important;
    padding: 10px;
    /* text-align: center; */
  }

  .contendor-card {
    box-shadow: 0px 0px 5px 0px rgb(0 0 0 / 35%);
    -webkit-box-shadow: 0px 0px 5px 0px rgb(0 0 0 / 35%);
    -moz-box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.35);
    /* padding: 18px 0px; */
    border-radius: 7px;
    /* margin-bottom: 12px; */
  }

  .floating-btn {
    position: fixed;
    bottom: 20px;
    /* Espacio desde la parte inferior */
    right: 20px;
    /* Espacio desde la parte derecha */
    background-color: #007bff;
    /* Color de fondo del botón */
    color: #fff;
    /* Color del texto */
    padding: 10px 20px;
    /* Espaciado interno del botón */
    border-radius: 5px;
    /* Bordes redondeados */
    text-decoration: none;
    /* Quita la subrayado del texto */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    /* Sombra */
    transition: background-color 0.3s ease;
    /* Transición suave */
  }

  .floating-btn:hover {
    background-color: #0056b3;
    /* Color de fondo al pasar el ratón */
  }

  .content {
    width: 100%;
    padding: 20px 24px;
  }

  .dashboard-tabs-nav {
    background: #fff;
    border-top: 1px solid #e5e7eb;
    padding: 0 24px;
  }

  .dashboard-tabs-nav .nav-tabs {
    border-bottom: 0;
  }

  .dashboard-tabs-nav .nav-tabs > li > a {
    border: 0;
    border-bottom: 3px solid transparent;
    color: #475569 !important;
    font-weight: 600;
    margin: 0 20px 0 0;
    padding: 13px 4px 11px;
  }

  .dashboard-tabs-nav .nav-tabs > li > a:hover,
  .dashboard-tabs-nav .nav-tabs > li > a:focus {
    background: transparent;
    color: #1f6fb2 !important;
  }

  .dashboard-tabs-nav .nav-tabs > li.active > a,
  .dashboard-tabs-nav .nav-tabs > li.active > a:hover,
  .dashboard-tabs-nav .nav-tabs > li.active > a:focus {
    border: 0;
    border-bottom: 3px solid #3c8dbc;
    color: #3c8dbc !important;
  }

  .dashboard-main-tab-pane {
    min-height: 300px;
  }

  .dashboard-main-tab-pane .content {
    max-width: none;
    padding: 20px 24px;
    width: 100%;
  }

  @keyframes palpitar {
    0% {
      transform: scale(1);
    }

    50% {
      transform: scale(1.1);
    }

    100% {
      transform: scale(1);
    }
  }

  .palpitar {
    animation: palpitar 1s infinite;
    padding: 10px 20px;
    font-size: 16px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .palpitar:hover {
    background-color: #0056b3;
  }

  .btn-colapse {
    border: 1px solid #3c8dbc;
    color: #3c8dbc;
    background-color: transparent;
    transition: ease .3s all;
  }

  .btn-colapse:hover {
    color: #fff;
    background-color: #3c8dbc;
  }

  .dashboard-box {
    margin-bottom: 12px;
  }

  .dashboard-box .box-header {
    border-top: 3px solid #3c8dbc;
  }

  .dashboard-box .table {
    margin-bottom: 0;
  }

  .item-autocomplete-producto {
    padding: 8px 10px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    background: #fff;
  }

  .item-autocomplete-producto:hover,
  .item-autocomplete-producto.activo {
    background: #eaf4fb;
  }

  .item-autocomplete-producto .codigo {
    font-weight: 700;
    color: #3c8dbc;
    font-size: 12px;
  }

  .item-autocomplete-producto .nombre {
    color: #333;
    font-size: 12px;
    margin-top: 2px;
  }

  .mensaje-autocomplete-producto {
    padding: 10px;
    color: #777;
    font-size: 12px;
  }

  .chart-panel {
    min-height: 320px;
    padding: 8px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    background: #fff;
    overflow: hidden;
  }

  #chartVentasVendedor {
    display: block;
    width: 100% !important;
    height: 230px !important;
    max-height: 230px !important;
  }

  #chartVentasGeneral {
    display: block;
    width: 100% !important;
    height: 230px !important;
    max-height: 230px !important;
  }

  .opt-caducado {
    color: #8b1e1e;
    font-weight: bold;
  }

  .chart-title {
    font-weight: 600;
    margin-bottom: 8px;
  }

  .chart-equation {
    font-size: 12px;
    color: #555;
    margin-top: 6px;
  }

  .estado-alerta {
    white-space: nowrap;
    font-weight: 600;
  }

  .dot-alerta {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 6px;
    vertical-align: middle;
  }

  .dot-alerta.critico {
    background-color: #dc3545;
  }

  .dot-alerta.advertencia {
  background-color: #ffc107;
}

.dot-alerta.normal {
  background-color: #28a745;
}

  .dot-alerta.bajo {
    background-color: #fd7e14;
  }

  .dot-alerta.optimo {
    background-color: #28a745;
  }

  .stock-kpi {
    font-size: 12px;
    color: #666;
    margin-bottom: 8px;
  }

  .conversion-kpi-grid {
    margin-bottom: 15px;
  }

  .conversion-kpi-card {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    min-height: 92px;
    margin-bottom: 12px;
    padding: 14px 12px;
    background: #fff;
    border: 1px solid #dce3ea;
    border-radius: 4px;
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.06);
  }

  .conversion-kpi-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    color: #fff;
    border-radius: 3px;
    flex: 0 0 28px;
  }

  .conversion-kpi-icon.blue { background: #1f78b4; }
  .conversion-kpi-icon.teal { background: #15937c; }
  .conversion-kpi-icon.orange { background: #f39c12; }
  .conversion-kpi-icon.green { background: #19a765; }
  .conversion-kpi-icon.navy { background: #2c3e50; }

  .conversion-kpi-label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    color: #667085;
    text-transform: uppercase;
  }

  .conversion-kpi-value {
    display: block;
    margin-top: 4px;
    font-size: 22px;
    font-weight: 700;
    line-height: 1.1;
    color: #0f2438;
  }

  .conversion-kpi-help {
    display: block;
    margin-top: 5px;
    font-size: 10px;
    color: #7b8794;
  }

  .table-pager {
    margin-top: 8px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
  }

  .table-pager .btn {
    padding: 3px 10px;
  }

  .modal-vinculos-producto .modal-header {
    border-bottom: 1px solid #e5e7eb;
    background: #f8fafc;
  }

  .modal-vinculos-producto .modal-title {
    font-weight: 600;
  }

  .modal-vinculos-producto .venta-contexto {
    display: block;
    margin-top: 4px;
    color: #667085;
    font-size: 12px;
    font-weight: 400;
  }

  .bloque-link-producto {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 10px;
    background: #fff;
  }

  .bloque-link-producto label {
    margin-bottom: 6px;
    font-weight: 600;
  }

  .bloque-link-producto .help-block {
    margin-bottom: 0;
    font-size: 11px;
  }





.card-evolucion-ventas {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 16px;
  min-height: 170px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.card-evolucion-ventas .titulo-mes {
  font-size: 12px;
  color: #666;
  text-transform: uppercase;
  margin-bottom: 4px;
}

.card-evolucion-ventas .anio {
  font-size: 28px;
  font-weight: 700;
  line-height: 1.1;
  margin-bottom: 6px;
}

.card-evolucion-ventas .total-ventas {
  font-size: 30px;
  font-weight: 700;
  color: #111;
  line-height: 1.1;
}

.card-evolucion-ventas .facturas {
  font-size: 12px;
  color: #666;
  margin-top: 4px;
}

.card-evolucion-ventas .bloque-cliente {
  margin-top: 12px;
  padding-top: 10px;
  border-top: 1px solid #eee;
}

.card-evolucion-ventas .cliente-label {
  font-size: 11px;
  color: #777;
  font-weight: 700;
  text-transform: uppercase;
}

.card-evolucion-ventas .cliente-nombre {
  font-size: 13px;
  font-weight: 700;
  color: #222;
  line-height: 1.3;
  margin-top: 2px;
}

.card-evolucion-ventas .cliente-monto {
  font-size: 18px;
  font-weight: 700;
  margin-top: 4px;
}

.card-evolucion-ventas .cliente-facturas {
  font-size: 11px;
  color: #777;
  margin-top: 2px;
}


</style>
@php
$empresa = \Sis_medico\Empresa::where('prioridad', '1')->first();
@endphp

<body class="hold-transition skin-blue sidebar-mini aitrol-shell">
  <div class="wrapper">

    <!-- Main Header -->
    @include('layouts.header')
    <!-- Sidebar -->
    @include('layouts.sidebar', ['newSystemNavigation' => true])


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header title">
        <h1 style="text-transform: uppercase; font-weight: bold; font-size: 2.5em;">
          @if($empresa->compras == 0) Sistema medico @else {{$empresa->nombrecomercial}} @endif
        </h1>
      </section>
      @if(!empty($mostrar_dashboard_gastroclinica) || !empty($mostrar_dashboard_gastritech) || !empty($mostrar_dashboard_labs))
      <section class="dashboard-tabs-nav" aria-label="Secciones del dashboard">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="{{ $tab_dashboard === 'principal' ? 'active' : '' }}">
            <a href="#dashboard-principal" aria-controls="dashboard-principal" role="tab"
              data-toggle="tab" data-dashboard-tab="principal">
              <i class="fa fa-home"></i> Inicio
            </a>
          </li>
          @if(!empty($mostrar_dashboard_gastroclinica))
          <li role="presentation" class="{{ $tab_dashboard === 'gastroclinica' ? 'active' : '' }}">
            <a href="#dashboard-gastroclinica" aria-controls="dashboard-gastroclinica" role="tab"
              data-toggle="tab" data-dashboard-tab="gastroclinica">
              <i class="fa fa-line-chart"></i> Dashboard Gastroclínica
            </a>
          </li>
          @endif
          @if(!empty($mostrar_dashboard_gastritech))
          <li role="presentation" class="{{ $tab_dashboard === 'gastritech' ? 'active' : '' }}">
            <a href="#dashboard-gastritech" aria-controls="dashboard-gastritech" role="tab"
              data-toggle="tab" data-dashboard-tab="gastritech">
              <i class="fa fa-line-chart"></i> Dashboard Gastritech
            </a>
          </li>
          @endif
          @if(!empty($mostrar_dashboard_labs))
          <li role="presentation" class="{{ $tab_dashboard === 'labs' ? 'active' : '' }}">
            <a href="#dashboard-labs" aria-controls="dashboard-labs" role="tab"
              data-toggle="tab" data-dashboard-tab="labs">
              <i class="fa fa-flask"></i> Dashboard Labs
            </a>
          </li>
          @endif
          @if(!empty($mostrar_dashboard_gastritech))
          <li role="presentation" class="{{ $tab_dashboard === 'ficha_tecnica' ? 'active' : '' }}">
            <a href="#dashboard-ficha-tecnica" aria-controls="dashboard-ficha-tecnica" role="tab"
              data-toggle="tab" data-dashboard-tab="ficha_tecnica">
              <i class="fa fa-file-pdf-o"></i> Asignar Ficha Tecnica
            </a>
          </li>
          @endif
        </ul>
      </section>
      <div class="tab-content">
        <div role="tabpanel" id="dashboard-principal"
          class="tab-pane dashboard-main-tab-pane {{ $tab_dashboard === 'principal' ? 'active' : '' }}">
      @endif

      @include('menu_usuario')
      @if(Auth::user()->id_tipo_usuario == 1 or Auth::user()->id_tipo_usuario == 7)
      @include('presentacion')
      @endif

      @if(!empty($mostrar_dashboard_gastroclinica) || !empty($mostrar_dashboard_gastritech) || !empty($mostrar_dashboard_labs))
        </div>
        @if(!empty($mostrar_dashboard_gastroclinica))
        <div role="tabpanel" id="dashboard-gastroclinica"
          class="tab-pane dashboard-main-tab-pane {{ $tab_dashboard === 'gastroclinica' ? 'active' : '' }}">
          @include('dashboard_gastroclinica.index')
        </div>
        @endif
        @if(!empty($mostrar_dashboard_gastritech))
        <div role="tabpanel" id="dashboard-gastritech"
          class="tab-pane dashboard-main-tab-pane {{ $tab_dashboard === 'gastritech' ? 'active' : '' }}">
          @include('dashboard.gastritech')
        </div>
        @endif
          @if(!empty($mostrar_dashboard_labs))
          @php
            $parametrosDashboardLabs = request()->only(['fecha_desde', 'fecha_hasta', 'seguro', 'doctor', 'creador', 'estado']);
          @endphp
          <div role="tabpanel" id="dashboard-labs"
            class="tab-pane dashboard-main-tab-pane {{ $tab_dashboard === 'labs' ? 'active' : '' }}" data-url="{{ route('dashboard.labs.index', $parametrosDashboardLabs) }}">
            <div class="text-center" style="padding: 45px;">
              <i class="fa fa-spinner fa-spin"></i> Cargando dashboard...
            </div>
          </div>
          @endif
          @if(!empty($mostrar_dashboard_gastritech))
          <div role="tabpanel" id="dashboard-ficha-tecnica"
            class="tab-pane dashboard-main-tab-pane {{ $tab_dashboard === 'ficha_tecnica' ? 'active' : '' }}">
            @include("dashboard.asignar_ficha_tecnica")
          </div>
        @endif
      </div>
      @endif
    </div>
    
    @include('layouts.footer')
    <?php
    $usuario = Sis_medico\User::where('id', Auth::user()->id)->first();
    //if (isset($usuario->new_system) && $usuario->new_system == 1) {
    if (Auth::user()->id_tipo_usuario == 1 || (isset($usuario->new_system) && $usuario->new_system == 1)) {
      echo '<a href="' . url('/') . '/v3/admin" class="floating-btn palpitar" title="Ir a la nueva versión"><i class="fa fa-rocket"></i></a>';
    }
    ?>
    @if(!empty($mostrar_dashboard_gastritech))
    <div class="modal fade"
      id="modalDetalleVentasVendedor"
      tabindex="-1"
      role="dialog">

      <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content modal-vinculos-producto">

          <div class="modal-header">

            <button type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Cerrar">

              <span aria-hidden="true">&times;</span>
            </button>

            <h4 class="modal-title">
              Ventas por empresa:
              <span id="modalNombreVendedor"></span>
            </h4>

          </div>

          <div class="modal-body">

            <div id="cargandoDetalleVendedor"
              class="text-center"
              style="display:none; padding:20px;">

              <i class="fa fa-spinner fa-spin"></i>
              Cargando...
            </div>

            <div class="table-responsive">

              <table class="table table-bordered table-striped table-condensed">

                <thead>
                  <tr>
                    <th>Empresa</th>
                    <th class="text-right">Facturas</th>
                    <th class="text-right">Total ventas</th>
                  </tr>
                </thead>

                <tbody id="tbodyDetalleVentasVendedor">
                </tbody>

                <tfoot>
                  <tr style="font-weight:bold; background:#f5f5f5;">
                    <td class="text-right">TOTAL</td>

                    <td class="text-right"
                      id="totalFacturasDetalleVendedor">
                      0
                    </td>

                    <td class="text-right"
                      id="totalVentasDetalleVendedor">
                      $0.00
                    </td>
                  </tr>
                </tfoot>

              </table>

            </div>

          </div>

          <div class="modal-footer">
            <button type="button"
              class="btn btn-default"
              data-dismiss="modal">

              Cerrar
            </button>
          </div>

        </div>
      </div>
    </div>


    <div class="modal fade" id="modalConversionVendedor" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-vinculos-producto">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
              Conversion ventas por cotizacion: <span id="modalConversionNombreVendedor"></span>
            </h4>
          </div>
          <div class="modal-body">
            <div id="cargandoConversionVendedor" class="text-center" style="display:none; padding:20px;">
              <i class="fa fa-spinner fa-spin"></i> Cargando...
            </div>
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-condensed">
                <thead>
                  <tr>
                    <th>Empresas</th>
                    <th class="text-right">Cant. Cotizado</th>
                    <th class="text-right">Cant. Facturas</th>
                    <th class="text-right">Total cotizado</th>
                    <th class="text-right">Total facturado</th>
                    <th class="text-right">% conversi&oacute;n</th>
                  </tr>
                </thead>
                <tbody id="tbodyConversionVendedorEmpresas"></tbody>
                <tfoot>
                  <tr style="font-weight:bold; background:#f5f5f5;">
                    <td class="text-right">TOTAL</td>
                    <td class="text-right" id="totalConversionEmpresaCotizaciones">0</td>
                    <td class="text-right" id="totalConversionEmpresaFacturas">0</td>
                    <td class="text-right" id="totalConversionEmpresaCotizado">$0.00</td>
                    <td class="text-right" id="totalConversionEmpresaFacturado">$0.00</td>
                    <td class="text-right" id="totalConversionEmpresaPorcentaje">0.00%</td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
   
     <div class="modal fade"
      id="modalVinculosProducto"
      tabindex="-1"
      role="dialog">

      <div class="modal-dialog" role="document">

        <div class="modal-content">

          <div class="modal-header">

            <button type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Cerrar">

              <span aria-hidden="true">&times;</span>
            </button>

            <h4 class="modal-title">
              Ventas por producto
              <span id="modalVinculosProductoNombre"
                class="venta-contexto">
              </span>
            </h4>

          </div>

          <div class="modal-body">

            <input type="hidden" id="modalVinculosVentaId" value="">
            <input type="hidden" id="modalVinculosProductoCodigo" value="">

            <div class="bloque-link-producto">
              <label>Registro sanitario</label>
              <input type="url"
                class="form-control input-sm"
                id="urlVinculoProducto"
                placeholder="https://drive.google.com/file/d/...">
            </div>

            <div class="bloque-link-producto">
              <label>Ficha tecnica</label>
              <input type="url"
                class="form-control input-sm"
                id="urlVinculoProducto2"
                placeholder="https://drive.google.com/file/d/...">
            </div>

            <div id="mensajeVinculosProducto"
              class="alert"
              style="display:none;">
            </div>

            <div class="table-responsive">
              <table class="table table-bordered table-striped table-condensed">
                <thead>
                  <tr>
                    <th>Documento</th>
                    <th class="text-center" style="width:90px;">Abrir</th>
                  </tr>
                </thead>
                <tbody id="tbodyVinculosProducto">
                  <tr>
                    <td colspan="2" class="text-center">
                      Sin vinculos guardados
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

          </div>

          <div class="modal-footer">
            <button type="button"
              class="btn btn-primary"
              id="btnGuardarVinculoProducto">

              <i class="fa fa-save"></i>
              Guardar links
            </button>

            <button type="button"
              class="btn btn-default"
              data-dismiss="modal">

              Cerrar
            </button>
          </div>

        </div>
      </div>
    </div>
    @endif


    <div class="modal" id="modalElectro"> tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Documentos electrónicos no emitidos</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
              <thead>
                <th>ID</th>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Número</th>
              </thead>
              <tbody id="cuerpoElectro"></tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <script src="{{ asset('/js/jquery-2.1.4.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/custom.js') }}?v={{ filemtime(public_path('js/custom.js')) }}" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    @if(!empty($mostrar_dashboard_gastroclinica) || !empty($mostrar_dashboard_gastritech) || !empty($mostrar_dashboard_labs))
  <script>
    $(function () {
      function cargarDashboardTab($panel) {
        var url = $panel.data('url');
        if (!url || $panel.data('loaded') || $panel.data('loading')) {
          return;
        }

        $panel.data('loading', true).attr('aria-busy', 'true').html(
          '<div class="text-center" style="padding:45px"><i class="fa fa-spinner fa-spin"></i> Cargando dashboard...</div>'
        );

        $.ajax({ url: url, type: 'GET', dataType: 'html', cache: false })
          .done(function (html) {
            $panel.html(html).data('loaded', true);
          })
          .fail(function () {
            $panel.html(
              '<div class="alert alert-danger text-center" style="margin:20px">' +
              'No fue posible cargar el dashboard de laboratorio. ' +
              '<button type="button" class="btn btn-danger btn-xs js-dashboard-tab-retry">Reintentar</button></div>'
            );
          })
          .always(function () {
            $panel.data('loading', false).removeAttr('aria-busy');
          });
      }

      $('a[data-dashboard-tab]').on('shown.bs.tab', function () {
        var tab = $(this).data('dashboard-tab');
        var url = '{{ route('dashboard') }}';
        if (tab === 'gastroclinica') {
          url += '?tab=gastroclinica';
        }
        if (tab === 'gastritech') {
          url += '?tab=gastritech';
        }
         if (tab === 'labs') {
          url += '?tab=labs';
        }
        if (window.history && window.history.replaceState) {
          window.history.replaceState(null, document.title, url);
        }

        cargarDashboardTab($($(this).attr('href')));
      });

      $(document).on('click', '.js-dashboard-tab-retry', function () {
        var $panel = $(this).closest('.dashboard-main-tab-pane');
        $panel.removeData('loaded').removeData('loading');
        cargarDashboardTab($panel);
      });

      var $tabActivo = $('a[data-dashboard-tab]').filter(function () {
        return $(this).closest('li').hasClass('active');
      }).first();
      if ($tabActivo.length) {
        cargarDashboardTab($($tabActivo.attr('href')));
      }
    });
  </script>
  @endif
    <script>
      $(document).ready(function() {
        // ==================== VENTAS POR VENDEDOR (con filtros) ====================
        let ventasVendedorRaw = {!! json_encode($ventas_por_vendedor) !!};
        let ventasVendedorChart = null;
        const mesesNombres = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
          'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        function actualizarTituloRango() {
          const mes  = $('#filtroMesVentas').val();
          const anio = $('#filtroAnioVentas').val();
          const excl = $('#filtroExcluirRobles').is(':checked');
          $('#tituloRangoVentas').text(
            '(' + mesesNombres[parseInt(mes, 10)] + ' ' + anio +
            (excl ? ' — sin Grupo Robles' : '') + ')'
          );
        }

        function pintarTablaVentasVendedor(datos) {
          const tbody = $('#tbodyVentasVendedor');
          tbody.empty();
          if (!datos || !datos.length) {
            tbody.append('<tr><td colspan="4" class="text-center">Sin datos</td></tr>');
            return;
          }
          datos.forEach(function(row) {
            const total = parseFloat(row.total_ventas || 0);
            const totalFmt = '$' + total.toLocaleString('en-US', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            });
            const vendedorSeguro = escaparHtmlVendedor(
              row.vendedor_nombre
            );
            const totalEmpresas = parseInt(row.total_empresas || 0, 10);
            const totalFacturas = parseInt(row.total_facturas || 0, 10);

            tbody.append(
              '<tr>' +

              '<td>' +
              '<a href="#" ' +
              'class="link-detalle-vendedor" ' +
              'data-vendedor="' + vendedorSeguro + '">' +
              vendedorSeguro +
              '</a>' +
              '</td>' +

              '<td class="text-right">' +
              totalEmpresas.toLocaleString('en-US') +
              '</td>' +

              '<td class="text-right">' +
              totalFacturas.toLocaleString('en-US') +
              '</td>' +

              '<td class="text-right">' +
              totalFmt +
              '</td>' +

              '</tr>'
            );
          });
        }

        function renderVentasVendedorChart(intento) {
          intento = intento || 0;
          if (!ventasVendedorRaw.length) {
            if (ventasVendedorChart) {
              ventasVendedorChart.destroy();
              ventasVendedorChart = null;
            }
            const etiqEq = document.getElementById('chartVentasVendedorEq');
            if (etiqEq) etiqEq.textContent = '';
            return;
          }
          if (typeof Chart === 'undefined') return;

          const puntos = ventasVendedorRaw.map((row, idx) => {
            const yVal = parseFloat(String(row.total_ventas).replace(/,/g, ''));
            return {
              x: idx + 1,
              y: Number.isFinite(yVal) ? yVal : 0,
              vendedor: row.vendedor_nombre
            };
          });

          const n = puntos.length;
          let sumX = 0,
            sumLnY = 0,
            sumXLnY = 0,
            sumX2 = 0,
            nPositivos = 0;
          puntos.forEach((p) => {
            if (p.y > 0) {
              const lnY = Math.log(p.y);
              sumX += p.x;
              sumLnY += lnY;
              sumXLnY += (p.x * lnY);
              sumX2 += (p.x * p.x);
              nPositivos += 1;
            }
          });

          const denom = (nPositivos * sumX2) - (sumX * sumX);
          const bExpCalc = denom === 0 ? -0.01 : ((nPositivos * sumXLnY) - (sumX * sumLnY)) / denom;
          const aLnCalc = nPositivos === 0 ? 0 : (sumLnY - (bExpCalc * sumX)) / nPositivos;
          const bExp = Number.isFinite(bExpCalc) ? -Math.abs(bExpCalc) : -0.01;
          const aExp = Number.isFinite(aLnCalc) ? Math.exp(aLnCalc) : 0;

          const curva = [];
          const totalPuntosCurva = Math.max(30, n * 15);
          for (let i = 0; i <= totalPuntosCurva; i++) {
            const xVal = 1 + ((n - 1) * i / totalPuntosCurva);
            curva.push({
              x: xVal,
              y: aExp * Math.exp(bExp * xVal)
            });
          }

          const etiquetaEq = document.getElementById('chartVentasVendedorEq');
          if (etiquetaEq) {
            etiquetaEq.textContent = 'y = ' + aExp.toFixed(2) + 'e^(' + bExp.toFixed(4) + 'x)';
          }

          const ctx = document.getElementById('chartVentasVendedor');
          if (!ctx) return;
          if ((ctx.offsetParent === null || ctx.clientWidth === 0 || ctx.clientHeight === 0) && intento < 8) {
            setTimeout(function() {
              renderVentasVendedorChart(intento + 1);
            }, 180);
            return;
          }

          if (ventasVendedorChart) {
            ventasVendedorChart.destroy();
            ventasVendedorChart = null;
          }

          ventasVendedorChart = new Chart(ctx, {
            type: 'scatter',
            data: {
              datasets: [{
                  label: 'Total ventas por vendedor',
                  data: puntos,
                  pointBackgroundColor: '#2c7fb8',
                  pointBorderColor: '#2c7fb8',
                  pointRadius: 5
                },
                {
                  type: 'line',
                  label: 'Curva descendente',
                  data: curva,
                  borderColor: '#d7301f',
                  backgroundColor: 'rgba(0,0,0,0)',
                  borderWidth: 2,
                  pointRadius: 0,
                  lineTension: 0
                }
              ]
            },
            options: {
              responsive: true,
              maintainAspectRatio: true,
              legend: {
                position: 'bottom'
              },
              tooltips: {
                callbacks: {
                  label: function(tooltipItem, data) {
                    const ds = data.datasets[tooltipItem.datasetIndex];
                    if (ds.label === 'Total ventas por vendedor') {
                      const p = ds.data[tooltipItem.index];
                      return p.vendedor + ': $' + p.y.toFixed(2);
                    }
                    return ds.label + ': ' + tooltipItem.yLabel.toFixed(2);
                  }
                }
              },
              scales: {
                xAxes: [{
                  type: 'linear',
                  position: 'bottom',
                  ticks: {
                    min: 1,
                    max: n,
                    stepSize: 1,
                    callback: function(value) {
                      const i = value - 1;
                      if (i >= 0 && i < ventasVendedorRaw.length) {
                        return ventasVendedorRaw[i].vendedor_nombre;
                      }
                      return '';
                    }
                  },
                  scaleLabel: {
                    display: true,
                    labelString: 'Vendedor'
                  }
                }],
                yAxes: [{
                  ticks: {
                    beginAtZero: true
                  },
                  scaleLabel: {
                    display: true,
                    labelString: 'Total de ventas'
                  }
                }]
              }
            }
          });
        }

        function aplicarFiltrosVentasVendedor() {
          const mes  = $('#filtroMesVentas').val();
          const anio = $('#filtroAnioVentas').val();
          const excl = $('#filtroExcluirRobles').is(':checked') ? 1 : 0;

          const btn = $('#btnAplicarFiltroVentas');
          btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Cargando...');

          $.ajax({
            url: '{{ route("dashboard.filtrar_ventas_vendedor") }}',
            type: 'POST',
            data: {
              _token: '{{ csrf_token() }}',
              mes: mes,
              anio: anio,
              excluir_grupo_robles: excl
            },
            dataType: 'json',
            success: function(resp) {
              if (resp && resp.ok) {
                ventasVendedorRaw = resp.ventas || [];
                pintarTablaVentasVendedor(ventasVendedorRaw);
                actualizarTituloRango();
                renderVentasVendedorChart();
              } else {
                alert('No se pudo cargar la información.');
              }
            },

            error: function(xhr, status, error) {
              console.error('ERROR VENTAS POR VENDEDOR');
              console.error('HTTP:', xhr.status);
              console.error('Status:', status);
              console.error('Error:', error);
              console.error('Respuesta:', xhr.responseText);

              alert(
                'Error al filtrar ventas por vendedor.\n' +
                'Código HTTP: ' + xhr.status + '\n' +
                'Revisa la consola del navegador.'
              );
            },
            complete: function() {
              btn.prop('disabled', false).html('<i class="fa fa-filter"></i> Aplicar filtros');
            }
          });
        }

        function escaparHtmlVendedor(valor) {
          return String(valor || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
        }

        // Render inicial
        pintarTablaVentasVendedor(ventasVendedorRaw);
        actualizarTituloRango();

        $('#btnAplicarFiltroVentas').on('click', aplicarFiltrosVentasVendedor);

        const boxVentasVendedor = $('#chartVentasVendedor').closest('.box');
        if (boxVentasVendedor.length && !boxVentasVendedor.hasClass('collapsed-box')) {
          renderVentasVendedorChart();
        }
        setTimeout(function() {
          renderVentasVendedorChart();
        }, 400);

        boxVentasVendedor.find('[data-widget="collapse"]').on('click', function() {
          setTimeout(function() {
            if (!boxVentasVendedor.hasClass('collapsed-box')) {
              renderVentasVendedorChart();
            }
          }, 350);
        });

        $('#filtroAlertaAccesorios').on('change', function () {
            let valor = $(this).val();
            let indicador = $('#indicadorEstado');

            indicador.removeClass('critico advertencia normal');

            if (valor) {
                indicador.addClass(valor);
            }
        });

        let ventasGeneralRaw = {!! json_encode($ventas_general) !!};
        let evolucionVentasGeneralRaw = {!! json_encode($evolucion_ventas_general) !!};

        function actualizarTituloRangoVentasGeneral() {
          const mes = $('#filtroMesVentasGeneral').val();
          const anio = $('#filtroAnioVentasGeneral').val();
          const excl = $('#filtroExcluirRoblesGeneral').is(':checked');

          $('#tituloRangoVentasGeneral').text(
            '(' + mesesNombres[parseInt(mes, 10)] + ' ' + anio +
            (excl ? ' — sin Grupo Robles' : '') + ')'
          );
        }

        function escaparHtml(texto) {
          return String(texto || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
        }


        function pintarEvolucionVentasGeneral(datos) {
          const contenedor = $('#cardsEvolucionVentasGeneral');
          const mes = $('#filtroMesVentasGeneral').val();
          const nombreMes = mesesNombres[parseInt(mes, 10)];

          const coloresCard = [
            '#3c8dbc',
            '#00a65a',
            '#f39c12',
            '#dd4b39',
            '#605ca8'
          ];

          contenedor.empty();

          if (!datos || !datos.length) {
            contenedor.append(
              '<div class="col-md-12">' +
                '<div class="alert alert-info" style="margin-bottom:0;">' +
                  'Sin datos de evolución.' +
                '</div>' +
              '</div>'
            );

            return;
          }

          datos.forEach(function(row, index) {
            const colorCard = coloresCard[index % coloresCard.length];

            const total = parseFloat(row.total_ventas || 0);
            const facturas = parseInt(row.total_facturas || 0, 10);

            const clienteVentas = parseFloat(row.cliente_top_ventas || 0);
            const clienteFacturas = parseInt(row.cliente_top_facturas || 0, 10);

            const totalFmt = '$' + total.toLocaleString('en-US', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            });

            const clienteVentasFmt = '$' + clienteVentas.toLocaleString('en-US', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            });

            const clienteNombre = row.cliente_top
              ? String(row.cliente_top)
              : 'Sin cliente';

            const clienteNombreCorto = clienteNombre.length > 42
              ? clienteNombre.substring(0, 42) + '...'
              : clienteNombre;

            contenedor.append(
              '<div class="col-md-4">' +

                '<div class="card-evolucion-ventas" ' +
                    'style="border-left:5px solid ' + colorCard + ';">' +

                  '<div class="titulo-mes">' +
                    nombreMes +
                  '</div>' +

                  '<div class="anio" style="color:' + colorCard + ';">' +
                    row.anio +
                  '</div>' +

                  '<div class="total-ventas">' +
                    totalFmt +
                  '</div>' +

                  '<div class="facturas">' +
                    facturas.toLocaleString('en-US') +
                    ' facturas' +
                  '</div>' +

                  '<div class="bloque-cliente">' +

                    '<div class="cliente-label">' +
                      'Mayor cliente' +
                    '</div>' +

                    '<div class="cliente-nombre" title="' +
                      escaparHtml(clienteNombre) +
                    '">' +
                      escaparHtml(clienteNombreCorto) +
                    '</div>' +

                    '<div class="cliente-monto" style="color:' + colorCard + ';">' +
                      clienteVentasFmt +
                    '</div>' +

                    '<div class="cliente-facturas">' +
                      clienteFacturas.toLocaleString('en-US') +
                      ' facturas' +
                    '</div>' +

                  '</div>' +

                '</div>' +

              '</div>'
            );
          });
        }


        function pintarTablaVentasGeneral(datos) {
          const tbody = $('#tbodyVentasGeneral');
          const totalFacturasEl = $('#totalFacturasVentasGeneral');
          const totalValorEl = $('#totalValorVentasGeneral');

          let totalFacturas = 0;
          let totalValor = 0;

          tbody.empty();

          if (!datos || !datos.length) {
            tbody.append('<tr><td colspan="3" class="text-center">Sin datos</td></tr>');
            totalFacturasEl.text('0');
            totalValorEl.text('$0.00');
            return;
          }

          datos.forEach(function(row) {
            const facturas = parseInt(row.total_facturas || 0, 10);
            const total = parseFloat(row.total_ventas || 0);

            totalFacturas += isNaN(facturas) ? 0 : facturas;
            totalValor += isNaN(total) ? 0 : total;

            const totalFmt = '$' + total.toLocaleString('en-US', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            });

            tbody.append(
              '<tr>' +
                '<td>' + row.fecha + '</td>' +
                '<td class="text-right">' + facturas + '</td>' +
                '<td class="text-right">' + totalFmt + '</td>' +
              '</tr>'
            );
          });

          totalFacturasEl.text(totalFacturas.toLocaleString('en-US'));

          totalValorEl.text('$' + totalValor.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          }));
        }

        function aplicarFiltrosVentasGeneral() {
          const mes = $('#filtroMesVentasGeneral').val();
          const anio = $('#filtroAnioVentasGeneral').val();
          const excl = $('#filtroExcluirRoblesGeneral').is(':checked') ? 1 : 0;
          const btn = $('#btnAplicarFiltroVentasGeneral');
          btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Cargando...');
          $.ajax({
            url: '{{ route("dashboard.filtrar_ventas_general") }}',
            type: 'POST',
            data: {
              _token: '{{ csrf_token() }}',
              mes: mes,
              anio: anio,
              excluir_grupo_robles: excl
            },
            dataType: 'json',
            success: function(resp) {
              if (resp && resp.ok) {
                ventasGeneralRaw = resp.ventas || [];
                evolucionVentasGeneralRaw = resp.evolucion || [];

                pintarTablaVentasGeneral(ventasGeneralRaw);
                pintarEvolucionVentasGeneral(evolucionVentasGeneralRaw);
                actualizarTituloRangoVentasGeneral();
              } else {
                alert(
                  resp && resp.mensaje
                    ? resp.mensaje
                    : 'No se pudo cargar la información.'
                );
              }
            },
            error: function() {
              alert('Error de conexión al filtrar ventas general.');
            },
            complete: function() {
              btn.prop('disabled', false).html('<i class="fa fa-filter"></i> Aplicar filtros');
            }
          });
        }

        pintarTablaVentasGeneral(ventasGeneralRaw);
        actualizarTituloRangoVentasGeneral();
        $('#btnAplicarFiltroVentasGeneral').on('click', aplicarFiltrosVentasGeneral);


        let temporizadorBusquedaProducto = null;
        let indiceProductoActivo = -1;

        const inputProductoVentas = $('#filtroProductoVentasTexto');
        const codigoProductoVentas = $('#filtroProductoVentas');
        const listaProductosVentas = $('#listaAutocompleteProductos');
        const textoProductoSeleccionado = $('#productoSeleccionadoTexto');

        function escaparHtmlProducto(valor) {
          return String(valor === null || valor === undefined ? '' : valor)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
        }

        function cerrarAutocompleteProductos() {
          listaProductosVentas.hide().empty();
          indiceProductoActivo = -1;
        }

        function seleccionarProductoAutocomplete(codigo, nombre) {
          codigoProductoVentas.val(codigo);

          inputProductoVentas.val(
            codigo + ' - ' + nombre
          );

          textoProductoSeleccionado.text(
            'Producto seleccionado: ' + codigo
          );

          cerrarAutocompleteProductos();
        }

        function pintarAutocompleteProductos(productos) {
          listaProductosVentas.empty();
          indiceProductoActivo = -1;

          if (!productos || !productos.length) {
            listaProductosVentas.html(
              '<div class="mensaje-autocomplete-producto">' +
                'No se encontraron productos.' +
              '</div>'
            ).show();

            return;
          }

          productos.forEach(function(producto) {
            const codigo = String(producto.codigo || '');
            const nombre = String(producto.nombre || '');

            listaProductosVentas.append(
              '<div class="item-autocomplete-producto" ' +
                  'data-codigo="' + escaparHtmlProducto(codigo) + '" ' +
                  'data-nombre="' + escaparHtmlProducto(nombre) + '">' +

                '<div class="codigo">' +
                  escaparHtmlProducto(codigo) +
                '</div>' +

                '<div class="nombre">' +
                  escaparHtmlProducto(nombre) +
                '</div>' +

              '</div>'
            );
          });

          listaProductosVentas.show();
        }

        function buscarProductosAutocomplete(texto) {
          const anio = $('#filtroAnioVentasProducto').val();

          listaProductosVentas.html(
            '<div class="mensaje-autocomplete-producto">' +
              '<i class="fa fa-spinner fa-spin"></i> Buscando...' +
            '</div>'
          ).show();

          $.ajax({
            url: '{{ route("dashboard.buscar_productos_ventas") }}',
            type: 'GET',
            dataType: 'json',

            data: {
              q: texto,
              anio: anio
            },

            success: function(resp) {
              if (resp && resp.ok) {
                pintarAutocompleteProductos(
                  resp.productos || []
                );
              } else {
                listaProductosVentas.html(
                  '<div class="mensaje-autocomplete-producto">' +
                    'No se pudo realizar la búsqueda.' +
                  '</div>'
                ).show();
              }
            },

            error: function(xhr) {
              console.error(
                'Error autocomplete productos:',
                xhr.responseText
              );

              listaProductosVentas.html(
                '<div class="mensaje-autocomplete-producto">' +
                  'Error al buscar productos.' +
                '</div>'
              ).show();
            }
          });
        }

        inputProductoVentas.on('input', function() {
          const texto = $.trim($(this).val());

          /*
          * Al modificar el texto se limpia el código seleccionado.
          * Solo vuelve a llenarse cuando se elige una sugerencia.
          */
          codigoProductoVentas.val('');

          textoProductoSeleccionado.text(
            'Todos los productos'
          );

          clearTimeout(temporizadorBusquedaProducto);

          if (texto.length < 3) {
            cerrarAutocompleteProductos();
            return;
          }

          temporizadorBusquedaProducto = setTimeout(function() {
            buscarProductosAutocomplete(texto);
          }, 350);
        });

        listaProductosVentas.on(
          'click',
          '.item-autocomplete-producto',
          function() {
            const codigo = String(
              $(this).attr('data-codigo') || ''
            );

            const nombre = String(
              $(this).attr('data-nombre') || ''
            );

            seleccionarProductoAutocomplete(
              codigo,
              nombre
            );
          }
        );

        // ==================== VENTAS POR PRODUCTO ====================

        let ventasProductoRaw = {!! json_encode($ventas_por_producto) !!};

        let paginaVentasProducto = 1;
        const registrosPorPaginaVentasProducto = 15;

        function escaparHtmlVentasProducto(valor) {
          return String(
            valor === null || valor === undefined ? '' : valor
          )
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
        }

        function formatoNumeroVentasProducto(valor) {
          const numero = parseFloat(valor || 0);

          return numero.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          });
        }

        function actualizarTituloVentasProducto() {
          const anio = $('#filtroAnioVentasProducto').val();
          const codigo = $('#filtroProductoVentas').val();

          let titulo = '(' + anio;

          if (codigo) {
            titulo += ' — ' + codigo;
          }

          titulo += ')';

          $('#tituloRangoVentasProducto').text(titulo);
        }

        function actualizarSelectProductosVentas(productos, seleccionado) {
          const select = $('#filtroProductoVentas');

          select.empty();

          select.append(
            '<option value="">Todos los productos</option>'
          );

          (productos || []).forEach(function(producto) {
            const codigo = escaparHtmlVentasProducto(
              producto.id_ct_productos
            );

            const nombre = escaparHtmlVentasProducto(
              producto.producto || ''
            );

            select.append(
              '<option value="' + codigo + '">' +
                codigo + ' - ' + nombre +
              '</option>'
            );
          });

          if (seleccionado) {
            select.val(seleccionado);
          }
        }

        function pintarTablaVentasProducto() {
          const tbody = $('#tbodyVentasProducto');

          let totalCantidad = 0;
          let totalVenta = 0;

          tbody.empty();

          ventasProductoRaw.forEach(function(row) {
            totalCantidad += parseFloat(row.total_cantidad || 0);
            totalVenta += parseFloat(row.total_venta || 0);
          });

          $('#totalCantidadVentasProducto').text(
            formatoNumeroVentasProducto(totalCantidad)
          );

          $('#totalValorVentasProducto').text(
            '$' + formatoNumeroVentasProducto(totalVenta)
          );

          if (!ventasProductoRaw.length) {
            tbody.append(
              '<tr>' +
                '<td colspan="9" class="text-center">Sin datos</td>' +
              '</tr>'
            );

            $('#ventasProductoPager').hide();

            return;
          }

          const totalPaginas = Math.max(
            1,
            Math.ceil(
              ventasProductoRaw.length /
              registrosPorPaginaVentasProducto
            )
          );

          paginaVentasProducto = Math.min(
            Math.max(paginaVentasProducto, 1),
            totalPaginas
          );

          const inicio =
            (paginaVentasProducto - 1) *
            registrosPorPaginaVentasProducto;

          const filas = ventasProductoRaw.slice(
            inicio,
            inicio + registrosPorPaginaVentasProducto
          );

          filas.forEach(function(row) {
            tbody.append(
              '<tr>' +

                '<td>' +
                  escaparHtmlVentasProducto(row.id_ct_productos) +
                '</td>' +

                '<td>' +
                  escaparHtmlVentasProducto(row.producto) +
                '</td>' +

                '<td class="text-right">' +
                  formatoNumeroVentasProducto(row.total_cantidad) +
                '</td>' +

                '<td class="text-center">' +
                  escaparHtmlVentasProducto(row.anio) +
                '</td>' +

                '<td class="text-right">$' +
                  formatoNumeroVentasProducto(row.total_venta) +
                '</td>' +

                '<td>' +
                  escaparHtmlVentasProducto(row.identificacion) +
                '</td>' +

                '<td>' +
                  escaparHtmlVentasProducto(row.cliente) +
                '</td>' +

                '<td>' +
                  escaparHtmlVentasProducto(row.vendedor) +
                '</td>' +

                '<td class="text-center">' +
                  '<button type="button" ' +
                  'class="btn btn-primary btn-xs" ' +
                  'title="Agregar vinculos PDF" ' +
                  'data-venta-id="' +
                  escaparHtmlVentasProducto(row.id_venta) +
                  '" ' +
                  'data-producto-id="' +
                  escaparHtmlVentasProducto(row.id_ct_productos) +
                  '" ' +
                  'data-producto="' +
                  escaparHtmlVentasProducto(row.producto) +
                  '" ' +
                  'onclick="abrirModalVinculosProducto(this)">' +
                  '<i class="fa fa-plus"></i>' +
                  '</button>' +
                '</td>' +

              '</tr>'
            );
          });

          $('#ventasProductoPageInfo').text(
            'Página ' +
            paginaVentasProducto +
            ' de ' +
            totalPaginas
          );

          $('#ventasProductoPrev').prop(
            'disabled',
            paginaVentasProducto === 1
          );

          $('#ventasProductoNext').prop(
            'disabled',
            paginaVentasProducto === totalPaginas
          );

          if (
            ventasProductoRaw.length >
            registrosPorPaginaVentasProducto
          ) {
            $('#ventasProductoPager').show();
          } else {
            $('#ventasProductoPager').hide();
          }
        }

        function mostrarMensajeVinculosProducto(tipo, mensaje) {
          const contenedor = $('#mensajeVinculosProducto');

          contenedor
            .removeClass('alert-success alert-danger alert-info')
            .addClass('alert-' + tipo)
            .text(mensaje)
            .show();
        }

        function pintarVinculoProducto(link, link2) {
          const tbody = $('#tbodyVinculosProducto');

          tbody.empty();

          if (!link && !link2) {
            tbody.append(
              '<tr>' +
              '<td colspan="2" class="text-center">' +
              'Sin links guardados' +
              '</td>' +
              '</tr>'
            );

            return;
          }

          if (link) {
            tbody.append(
              '<tr>' +
              '<td>Registro sanitario</td>' +
              '<td class="text-center">' +
              '<a class="btn btn-info btn-xs" ' +
              'href="' + escaparHtmlVentasProducto(link) + '" ' +
              'target="_blank" rel="noopener noreferrer" ' +
              'title="Abrir documento">' +
              '<i class="fa fa-external-link"></i>' +
              '</a>' +
              '</td>' +
              '</tr>'
            );
          }

          if (link2) {
            tbody.append(
              '<tr>' +
              '<td>Ficha tecnica</td>' +
              '<td class="text-center">' +
              '<a class="btn btn-info btn-xs" ' +
              'href="' + escaparHtmlVentasProducto(link2) + '" ' +
              'target="_blank" rel="noopener noreferrer" ' +
              'title="Abrir documento">' +
              '<i class="fa fa-external-link"></i>' +
              '</a>' +
              '</td>' +
              '</tr>'
            );
          }
        }

        function cargarVinculoProducto(idVenta, codigoProducto) {
          $('#tbodyVinculosProducto').html(
            '<tr>' +
            '<td colspan="2" class="text-center">' +
            '<i class="fa fa-spinner fa-spin"></i> Cargando...' +
            '</td>' +
            '</tr>'
          );

          $.ajax({
            url: '{{ route("dashboard.ventas_producto_link_pdf_obtener") }}',
            type: 'POST',
            dataType: 'json',
            data: {
              _token: '{{ csrf_token() }}',
              id_venta: idVenta,
              codigo_producto: codigoProducto
            },
            success: function(resp) {
              if (resp && resp.ok) {
                const link = resp.link || '';
                const link2 = resp.link_2 || '';

                $('#urlVinculoProducto').val(link);
                $('#urlVinculoProducto2').val(link2);
                pintarVinculoProducto(link, link2);

                if (resp.mensaje) {
                  mostrarMensajeVinculosProducto('info', resp.mensaje);
                }
              } else {
                pintarVinculoProducto('', '');
                mostrarMensajeVinculosProducto(
                  'danger',
                  resp && resp.mensaje ?
                  resp.mensaje :
                  'No se pudo cargar el link.'
                );
              }
            },
            error: function(xhr) {
              pintarVinculoProducto('', '');
              mostrarMensajeVinculosProducto(
                'danger',
                'Error al cargar el link. Codigo HTTP: ' + xhr.status
              );
            }
          });
        }

        window.abrirModalVinculosProducto = function(boton) {
          const idVenta = String(
            $(boton).attr('data-venta-id') || ''
          );
          const codigoProducto = String(
            $(boton).attr('data-producto-id') || ''
          );
          const producto = String(
            $(boton).attr('data-producto') || ''
          );

          $('#modalVinculosVentaId').val(idVenta);
          $('#modalVinculosProductoCodigo').val(codigoProducto);
          $('#modalVinculosProductoNombre').text(
            'Venta #' + idVenta + ' - ' + codigoProducto + ' - ' + producto
          );
          $('#urlVinculoProducto').val('');
          $('#urlVinculoProducto2').val('');
          $('#mensajeVinculosProducto').hide();
          $('#modalVinculosProducto').modal('show');

          cargarVinculoProducto(idVenta, codigoProducto);
        };

        $('#btnGuardarVinculoProducto')
          .off('click.vinculosProducto')
          .on('click.vinculosProducto', function() {
            const btn = $(this);
            const idVenta = $('#modalVinculosVentaId').val();
            const url = $.trim($('#urlVinculoProducto').val());
            const url2 = $.trim($('#urlVinculoProducto2').val());

            if (!url && !url2) {
              mostrarMensajeVinculosProducto(
                'danger',
                'Ingrese al menos un link valido.'
              );

              return;
            }

            if (url && !/^https?:\/\/.+/i.test(url)) {
              mostrarMensajeVinculosProducto(
                'danger',
                'El link 1 debe empezar con http:// o https://.'
              );

              return;
            }

            if (url2 && !/^https?:\/\/.+/i.test(url2)) {
              mostrarMensajeVinculosProducto(
                'danger',
                'El link 2 debe empezar con http:// o https://.'
              );

              return;
            }

            btn
              .prop('disabled', true)
              .html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

            $.ajax({
              url: '{{ route("dashboard.ventas_producto_link_pdf_guardar") }}',
              type: 'POST',
              dataType: 'json',
              data: {
                _token: '{{ csrf_token() }}',
                id_venta: idVenta,
                url: url,
                url_2: url2
              },
              success: function(resp) {
                if (resp && resp.ok) {
                  pintarVinculoProducto(
                    resp.link || url,
                    resp.link_2 || url2
                  );
                  mostrarMensajeVinculosProducto(
                    'success',
                    resp.mensaje || 'Links guardados correctamente.'
                  );
                } else {
                  mostrarMensajeVinculosProducto(
                    'danger',
                    resp && resp.mensaje ?
                    resp.mensaje :
                    'No se pudo guardar el link.'
                  );
                }
              },
              error: function(xhr) {
                let mensaje = 'Error al guardar el link. Codigo HTTP: ' +
                  xhr.status;

                if (
                  xhr.responseJSON &&
                  xhr.responseJSON.mensaje
                ) {
                  mensaje = xhr.responseJSON.mensaje;
                }

                mostrarMensajeVinculosProducto('danger', mensaje);
              },
              complete: function() {
                btn
                  .prop('disabled', false)
                  .html('<i class="fa fa-save"></i> Guardar links');
              }
            });
          });

        function aplicarFiltrosVentasProducto() {
          const anio = $('#filtroAnioVentasProducto').val();
          const producto = $('#filtroProductoVentas').val();

          const btn = $('#btnAplicarFiltroVentasProducto');

          btn
            .prop('disabled', true)
            .html(
              '<i class="fa fa-spinner fa-spin"></i> Cargando...'
            );

          $.ajax({
            url: '{{ route("dashboard.filtrar_ventas_producto") }}',
            type: 'POST',
            dataType: 'json',

            data: {
              _token: '{{ csrf_token() }}',
              anio: anio,
              producto: producto
            },

            success: function(resp) {
              if (resp && resp.ok) {
                ventasProductoRaw = resp.ventas || [];
                paginaVentasProducto = 1;

                actualizarSelectProductosVentas(
                  resp.productos || [],
                  producto
                );

                pintarTablaVentasProducto();
                actualizarTituloVentasProducto();
              } else {
                alert(
                  resp && resp.mensaje
                    ? resp.mensaje
                    : 'No se pudieron cargar las ventas por producto.'
                );
              }
            },

            error: function(xhr) {
              console.error(
                'Error ventas por producto:',
                xhr.responseText
              );

              alert(
                'Error al filtrar ventas por producto. ' +
                'Código HTTP: ' +
                xhr.status
              );
            },

            complete: function() {
              btn
                .prop('disabled', false)
                .html(
                  '<i class="fa fa-filter"></i> Aplicar filtros'
                );
            }
          });
        }

        $('#btnAplicarFiltroVentasProducto')
          .off('click.ventasProducto')
          .on('click.ventasProducto', function() {
            aplicarFiltrosVentasProducto();
          });

        $('#ventasProductoPrev')
          .off('click.ventasProducto')
          .on('click.ventasProducto', function() {
            paginaVentasProducto--;
            pintarTablaVentasProducto();
          });

        $('#ventasProductoNext')
          .off('click.ventasProducto')
          .on('click.ventasProducto', function() {
            paginaVentasProducto++;
            pintarTablaVentasProducto();
          });

        $('#boxVentasProducto select')
          .off('keydown.ventasProducto')
          .on('keydown.ventasProducto', function(e) {
            if (e.keyCode === 13) {
              e.preventDefault();
              aplicarFiltrosVentasProducto();
            }
          });

        pintarTablaVentasProducto();
        actualizarTituloVentasProducto();
        window.aplicarFiltrosVentasProducto = aplicarFiltrosVentasProducto;

        function initStockTablePagination() {
          const allRows = $('.stock-row');

          if (!allRows.length) {
            return;
          }

          const perPage = 10;
          let currentPage = 1;
          let filteredRows = allRows;

          const pager = $('#stockTablePager');
          const info = $('#stockPageInfo');
          const prev = $('#stockPagePrev');
          const next = $('#stockPageNext');
          const vacioFiltro = $('#stockVaciosFiltro');

          const selBodega = $('#filtroBodegaStockMinimo');
          const selAlerta = $('#filtroAlertaStockMinimo');
          const btnAplicar = $('#btnAplicarFiltrosStockMinimo');

          function formatoNumero(valor) {
            valor = parseFloat(valor || 0);

            return valor.toLocaleString('en-US', {
              minimumFractionDigits: 0,
              maximumFractionDigits: 0
            });
          }

          function actualizarKpisEstadosStock() {
            const resumen = {
              critico: {
                stockMinimo: 0,
                cantidadActual: 0
              },
              advertencia: {
                stockMinimo: 0,
                cantidadActual: 0
              },
              normal: {
                stockMinimo: 0,
                cantidadActual: 0
              },
              caducado: {
                stockMinimo: 0,
                cantidadActual: 0
              }
            };

            filteredRows.each(function() {
              const row = $(this);

              const alerta = String(row.data('alerta') || '');
              const stockMinimo = parseFloat(row.data('stock-minimo') || 0);
              const cantidadActual = parseFloat(row.data('cantidad-actual') || 0);

              if (resumen[alerta]) {
                resumen[alerta].stockMinimo += stockMinimo;
                resumen[alerta].cantidadActual += cantidadActual;
              }
            });

            $('#kpiStockCriticoMinimo').text(
              formatoNumero(resumen.critico.stockMinimo)
            );

            $('#kpiStockCriticoActual').text(
              formatoNumero(resumen.critico.cantidadActual)
            );

            $('#kpiStockAdvertenciaMinimo').text(
              formatoNumero(resumen.advertencia.stockMinimo)
            );

            $('#kpiStockAdvertenciaActual').text(
              formatoNumero(resumen.advertencia.cantidadActual)
            );

            $('#kpiStockNormalMinimo').text(
              formatoNumero(resumen.normal.stockMinimo)
            );

            $('#kpiStockNormalActual').text(
              formatoNumero(resumen.normal.cantidadActual)
            );

            $('#kpiStockCaducadoMinimo').text(
              formatoNumero(resumen.caducado.stockMinimo)
            );

            $('#kpiStockCaducadoActual').text(
              formatoNumero(resumen.caducado.cantidadActual)
            );
          }

          function actualizarKpisBodegasStock() {
            const resumenBodegas = {};

            // Primero reiniciamos todas las cards en cero
            $('.kpiStockBodegaMinimo').text('0');
            $('.kpiStockBodegaActual').text('0');

            filteredRows.each(function() {
              const row = $(this);

              const bodega = String(row.data('bodega') || '');
              const stockMinimo = parseFloat(row.data('stock-minimo') || 0);
              const cantidadActual = parseFloat(row.data('cantidad-actual') || 0);

              if (!resumenBodegas[bodega]) {
                resumenBodegas[bodega] = {
                  stockMinimo: 0,
                  cantidadActual: 0
                };
              }

              resumenBodegas[bodega].stockMinimo += stockMinimo;
              resumenBodegas[bodega].cantidadActual += cantidadActual;
            });

            $('.kpiStockBodegaMinimo').each(function() {
              const elemento = $(this);
              const bodega = String(elemento.data('bodega') || '');

              const resumen = resumenBodegas[bodega] || {
                stockMinimo: 0,
                cantidadActual: 0
              };

              elemento.text(formatoNumero(resumen.stockMinimo));
            });

            $('.kpiStockBodegaActual').each(function() {
              const elemento = $(this);
              const bodega = String(elemento.data('bodega') || '');

              const resumen = resumenBodegas[bodega] || {
                stockMinimo: 0,
                cantidadActual: 0
              };

              elemento.text(formatoNumero(resumen.cantidadActual));
            });
          }

          function aplicarFiltros() {
            const bodegaSel = String(selBodega.val() || '');
            const alertaSel = String(selAlerta.val() || '');

            filteredRows = allRows.filter(function() {
              const rowBodega = String($(this).data('bodega') || '');
              const rowAlerta = String($(this).data('alerta') || '');

              const matchBodega =
                !bodegaSel ||
                rowBodega === bodegaSel;

              const matchAlerta =
                !alertaSel ||
                rowAlerta === alertaSel;

              return matchBodega && matchAlerta;
            });

            allRows.hide();
            currentPage = 1;

            actualizarKpisEstadosStock();
            actualizarKpisBodegasStock();

            if (filteredRows.length === 0) {
              pager.hide();
              vacioFiltro.show();
              return;
            }

            vacioFiltro.hide();
            renderPage(1);
          }

          function renderPage(page) {
            const totalPages = Math.max(
              1,
              Math.ceil(filteredRows.length / perPage)
            );

            currentPage = Math.min(
              Math.max(page, 1),
              totalPages
            );

            allRows.hide();

            const start = (currentPage - 1) * perPage;

            filteredRows
              .slice(start, start + perPage)
              .show();

            info.text(
              'Página ' + currentPage + ' de ' + totalPages
            );

            prev.prop(
              'disabled',
              currentPage === 1
            );

            next.prop(
              'disabled',
              currentPage === totalPages
            );

            if (filteredRows.length > perPage) {
              pager.show();
            } else {
              pager.hide();
            }
          }

          prev.off('click.stock').on('click.stock', function() {
            renderPage(currentPage - 1);
          });

          next.off('click.stock').on('click.stock', function() {
            renderPage(currentPage + 1);
          });

          btnAplicar.off('click.stock').on('click.stock', function() {
            aplicarFiltros();
          });

          aplicarFiltros();
        }

        function initAccesoriosTablePagination() {
          const allRows = $('.accesorio-row');

          if (!allRows.length) {
            return;
          }

          const perPage = 30;
          let currentPage = 1;
          let filteredRows = allRows;

          const pager = $('#accesoriosTablePager');
          const info = $('#accesoriosPageInfo');
          const prev = $('#accesoriosPagePrev');
          const next = $('#accesoriosPageNext');
          const vacioFiltro = $('#accesoriosVaciosFiltro');
          const selBodega = $('#filtroBodegaAccesorios');
          const selAlerta = $('#filtroAlertaAccesorios');
          const btnAplicar = $('#btnAplicarFiltrosAccesorios');
          const totalExistencia = $('#totalExistenciaAccesorios');

          function formatoNumero(valor) {
            valor = parseFloat(valor || 0);

            return valor.toLocaleString('en-US', {
              minimumFractionDigits: 0,
              maximumFractionDigits: 0
            });
          }

          function formatoPorcentaje(valor) {
            valor = parseFloat(valor || 0);

            return valor.toLocaleString('en-US', {
              minimumFractionDigits: 1,
              maximumFractionDigits: 1
            });
          }

          function alertaCoincide(rowAlerta, alertaSel) {
            if (!alertaSel) {
              return true;
            }

            // Para negocio: caducado también es crítico
            if (alertaSel === 'critico') {
              return rowAlerta === 'critico' || rowAlerta === 'caducado';
            }

            return rowAlerta === alertaSel;
          }

          function actualizarTotalExistencia() {
            let total = 0;

            filteredRows.each(function() {
              total += parseFloat($(this).data('existencia') || 0);
            });

            totalExistencia.text(formatoNumero(total));
          }

          function actualizarKpisBodegasCaducados() {
            const acumuladoPorBodega = {};

            // Primero dejamos todas las bodegas en 0
            $('.kpiBodegaCaducadosCantidad').text('0');

            filteredRows.each(function() {
              const row = $(this);

              const bodega = String(row.data('bodega') || '');
              const alerta = String(row.data('alerta') || '');
              const existencia = parseFloat(row.data('existencia') || 0);

              // Las cards de bodega son solo de caducados
              if (alerta === 'caducado') {
                if (!acumuladoPorBodega[bodega]) {
                  acumuladoPorBodega[bodega] = 0;
                }

                acumuladoPorBodega[bodega] += existencia;
              }
            });

            $('.kpiBodegaCaducadosCantidad').each(function() {
              const card = $(this);
              const bodega = String(card.data('bodega') || '');
              const total = acumuladoPorBodega[bodega] || 0;

              card.text(formatoNumero(total));
            });
          }

          function actualizarKpisAccesorios() {
            let totalInventario = 0;

            let critico = 0;
            let advertencia = 0;
            let normal = 0;
            let caducado = 0;

            filteredRows.each(function() {
              const row = $(this);
              const existencia = parseFloat(row.data('existencia') || 0);
              const alerta = String(row.data('alerta') || '');

              totalInventario += existencia;

              if (alerta === 'caducado') {
                caducado += existencia;
                critico += existencia; // caducado también cuenta como crítico
              } else if (alerta === 'critico') {
                critico += existencia;
              } else if (alerta === 'advertencia') {
                advertencia += existencia;
              } else if (alerta === 'normal') {
                normal += existencia;
              }
            });

            const base = totalInventario > 0 ? totalInventario : 1;

            $('#kpiAccesoriosCritico').text(formatoNumero(critico));
            $('#kpiAccesoriosAdvertencia').text(formatoNumero(advertencia));
            $('#kpiAccesoriosNormal').text(formatoNumero(normal));
            $('#kpiAccesoriosCaducado').text(formatoNumero(caducado));

            $('#kpiAccesoriosCriticoPct').text(
              formatoPorcentaje((critico / base) * 100) + '% sobre el inventario total'
            );

            $('#kpiAccesoriosAdvertenciaPct').text(
              formatoPorcentaje((advertencia / base) * 100) + '% sobre el inventario total'
            );

            $('#kpiAccesoriosNormalPct').text(
              formatoPorcentaje((normal / base) * 100) + '% sobre el inventario total'
            );
          }

          function aplicarFiltros() {
            const bodegaSel = selBodega.val();
            const alertaSel = selAlerta.val();

            filteredRows = allRows.filter(function() {
              const rowBodega = String($(this).data('bodega') || '');
              const rowAlerta = String($(this).data('alerta') || '');

              const matchBodega = !bodegaSel || rowBodega === String(bodegaSel);
              const matchAlerta = alertaCoincide(rowAlerta, alertaSel);

              return matchBodega && matchAlerta;
            });

            allRows.hide();
            currentPage = 1;

            actualizarTotalExistencia();
            actualizarKpisAccesorios();
            actualizarKpisBodegasCaducados();

            if (filteredRows.length === 0) {
              pager.hide();
              vacioFiltro.show();
              return;
            }
            vacioFiltro.hide();
            renderPage(1);
          }

          function renderPage(page) {
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / perPage));
            currentPage = Math.min(Math.max(page, 1), totalPages);
            allRows.hide();
            const start = (currentPage - 1) * perPage;
            filteredRows.slice(start, start + perPage).show();
            info.text('Página ' + currentPage + ' de ' + totalPages);
            prev.prop('disabled', currentPage === 1);
            next.prop('disabled', currentPage === totalPages);
            if (filteredRows.length > perPage) {
              pager.show();
            } else {
              pager.hide();
            }
          }
          prev.off('click').on('click', function() {
            renderPage(currentPage - 1);
          });
          next.off('click').on('click', function() {
            renderPage(currentPage + 1);
          });
          btnAplicar.off('click').on('click', function() {
            aplicarFiltros();
          });

          aplicarFiltros();
        }

        initAccesoriosTablePagination();
        initStockTablePagination();        
        
        <?php
        if ($rolUsuario == 20 || $rolUsuario == 1) {
        ?>
          $.ajax({
            url: 'apifacturaopciones/revisionSinEmitir',
            type: 'post',
            data: {
              _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            success: function(data) {
              var cont = 0;
              let ventas = data.ventas !== undefined ? data.ventas : 0;
              let liquidaciones = data.liquidaciones !== undefined ? data.liquidaciones : 0;
              let retenciones = data.retenciones !== undefined ? data.retenciones : 0;
              let guias = data.guias !== undefined ? data.guias : 0;
              let nc = data.nc !== undefined ? data.nc : 0;
              var html = '';
              if (ventas.length > 0) {
                cont++;
                for (var i = 0; i < ventas.length; i++) {
                  html += '<tr><td>' + ventas[i].id + '</td><td>' + ventas[i].fecha + '</td><td>Factura</td><td align="right">' + ventas[i].nro_comprobante + '</td></tr>';
                }
              }
              if (liquidaciones.length > 0) {
                cont++;
                for (var j = 0; j < liquidaciones.length; j++) {
                  html += '<tr><td>' + liquidaciones[j].id + '</td><td>' + liquidaciones[j].fecha + '</td><td>Liquidaciones</td><td align="right">' + liquidaciones[j].numero + '</td></tr>';
                }
              }
              if (retenciones.length > 0) {
                cont++;
                for (var k = 0; k < retenciones.length; k++) {
                  html += '<tr><td>' + retenciones[k].id + '</td><td>' + retenciones[k].fecha + '</td><td>Retenciones</td><td align="right">' + retenciones[k].nro_comprobante + '</td></tr>';
                }
              }
              if (guias.length > 0) {
                cont++;
                for (var l = 0; l < guias.length; l++) {
                  html += '<tr><td>' + guias[l].id + '</td><td>' + guias[l].fecha_emision_documento + '</td><td>Guias</td><td align="right">' + guias[l].establecimiento + '-' + guias[l].punto_emision + '-' + guias[l].num_secuencial + '</td></tr>';
                }
              }
              if (nc.length > 0) {
                cont++;
                for (var m = 0; m < nc.length; m++) {
                  html += '<tr><td>' + nc[m].id + '</td><td>' + nc[m].fecha + '</td><td>Nota de crédito</td><td align="right">' + nc[m].nro_comprobante + '</td></tr>';
                }
              }
              if (cont > 0) {
                $('#cuerpoElectro').html(html);
                $('#modalElectro').modal('toggle');
              }
            }
          });
        <?php
        }
        ?>

        $('#btnExcelVentasVendedor').on('click', function() {
          const mes = $('#filtroMesVentas').val();
          const anio = $('#filtroAnioVentas').val();

          const excluir = $('#filtroExcluirRobles').is(':checked') ?
            1 :
            0;

          const urlBase =
            '{{ route("dashboard.exportar_ventas_vendedor") }}';

          const url =
            urlBase +
            '?mes=' + encodeURIComponent(mes) +
            '&anio=' + encodeURIComponent(anio) +
            '&excluir_grupo_robles=' +
            encodeURIComponent(excluir);

          window.location.href = url;
        });

        $(document).on(
          'click',
          '.link-detalle-vendedor',
          function(e) {
            e.preventDefault();

            const vendedor = String(
              $(this).attr('data-vendedor') || ''
            );

            const mes = $('#filtroMesVentas').val();
            const anio = $('#filtroAnioVentas').val();

            const excluir = $('#filtroExcluirRobles').is(':checked') ?
              1 :
              0;

            $('#modalNombreVendedor').text(vendedor);

            $('#tbodyDetalleVentasVendedor').empty();

            $('#totalFacturasDetalleVendedor').text('0');
            $('#totalVentasDetalleVendedor').text('$0.00');

            $('#cargandoDetalleVendedor').show();
            $('#modalDetalleVentasVendedor').modal('show');

            $.ajax({
              url: '{{ route("dashboard.detalle_ventas_vendedor") }}',
              type: 'POST',
              dataType: 'json',

              data: {
                _token: '{{ csrf_token() }}',
                vendedor: vendedor,
                mes: mes,
                anio: anio,
                excluir_grupo_robles: excluir
              },

              success: function(resp) {
                const tbody = $('#tbodyDetalleVentasVendedor');

                tbody.empty();

                let totalFacturas = 0;
                let totalVentas = 0;

                if (
                  !resp ||
                  !resp.ok ||
                  !resp.clientes ||
                  !resp.clientes.length
                ) {
                  tbody.append(
                    '<tr>' +
                    '<td colspan="3" class="text-center">' +
                    'Sin datos' +
                    '</td>' +
                    '</tr>'
                  );

                  return;
                }

                resp.clientes.forEach(function(row) {
                  const facturas = parseInt(
                    row.total_facturas || 0,
                    10
                  );

                  const ventas = parseFloat(
                    row.total_ventas || 0
                  );

                  totalFacturas += facturas;
                  totalVentas += ventas;

                  tbody.append(
                    '<tr>' +

                    '<td>' +
                    escaparHtmlVendedor(row.empresa) +
                    '</td>' +

                    '<td class="text-right">' +
                    facturas.toLocaleString('en-US') +
                    '</td>' +

                    '<td class="text-right">$' +
                    ventas.toLocaleString('en-US', {
                      minimumFractionDigits: 2,
                      maximumFractionDigits: 2
                    }) +
                    '</td>' +

                    '</tr>'
                  );
                });

                $('#totalFacturasDetalleVendedor').text(
                  totalFacturas.toLocaleString('en-US')
                );

                $('#totalVentasDetalleVendedor').text(
                  '$' +
                  totalVentas.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  })
                );
              },

              error: function(xhr) {
                console.error(
                  'Error detalle vendedor:',
                  xhr.responseText
                );

                $('#tbodyDetalleVentasVendedor').html(
                  '<tr>' +
                  '<td colspan="3" class="text-center text-danger">' +
                  'No se pudo cargar el detalle.' +
                  '</td>' +
                  '</tr>'
                );
              },

              complete: function() {
                $('#cargandoDetalleVendedor').hide();
              }
            });
          }
        );
        if ($('#boxConversionCotizaciones').length) {
          let conversionVendedoresRaw = {!! json_encode($conversion_cotizaciones_vendedor) !!};
          let conversionEstadosRaw = {!! json_encode($conversion_estados_proforma) !!};

          function formatoMonedaConversion(valor) {
            const numero = parseFloat(valor || 0);
            return '$' + (isNaN(numero) ? 0 : numero).toLocaleString('en-US', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            });
          }

          function formatoNumeroConversion(valor, decimales) {
            const numero = parseFloat(valor || 0);
            return (isNaN(numero) ? 0 : numero).toLocaleString('en-US', {
              minimumFractionDigits: decimales,
              maximumFractionDigits: decimales
            });
          }

          function numeroConversion(valor) {
            const numero = parseFloat(valor || 0);
            return isNaN(numero) ? 0 : numero;
          }

          function actualizarKpisEstadoConversion(estados) {
            const datosEstado = estados || {};
            $('#kpiConversionEstadoAnuladas').text(formatoNumeroConversion(datosEstado.anuladas || 0, 0));
            $('#kpiConversionEstadoAprobadas').text(formatoNumeroConversion(datosEstado.aprobadas || 0, 0));
            $('#kpiConversionEstadoPendientes').text(formatoNumeroConversion(datosEstado.pendientes || 0, 0));
            $('#kpiConversionEstadoCaducadas').text(formatoNumeroConversion(datosEstado.caducadas || 0, 0));
          }

          function actualizarKpisConversion(datos) {
            const totales = (datos || []).reduce(function(acc, row) {
              acc.facturado += numeroConversion(row.total_facturado);
              acc.cotizado += numeroConversion(row.total_cotizado);
              acc.facturas += numeroConversion(row.cant_facturas);
              acc.cotizaciones += numeroConversion(row.cant_cotizaciones);
              return acc;
            }, {
              facturado: 0,
              cotizado: 0,
              facturas: 0,
              cotizaciones: 0
            });

            const conversion = totales.cotizado > 0 ? (totales.facturado / totales.cotizado) * 100 : 0;

            $('#kpiConversionTotalFacturado').text(formatoMonedaConversion(totales.facturado));
            $('#kpiConversionTotalCotizado').text(formatoMonedaConversion(totales.cotizado));
            $('#kpiConversionPorcentaje').text(formatoNumeroConversion(conversion, 2) + '%');
            $('#kpiConversionNumeroFacturas').text(formatoNumeroConversion(totales.facturas, 0));
            $('#kpiConversionNumeroCotizaciones').text(formatoNumeroConversion(totales.cotizaciones, 0));
          }

          function pintarTablaConversionVendedores(datos) {
            actualizarKpisConversion(datos);

            const tbody = $('#tbodyConversionCotizaciones');
            tbody.empty();

            if (!datos || !datos.length) {
              tbody.html('<tr><td colspan="6" class="text-center">Sin datos para el per&iacute;odo seleccionado</td></tr>');
              return;
            }

            datos.forEach(function(row) {
              tbody.append(
                '<tr>' +
                '<td><a href="#" class="link-conversion-vendedor" data-id-vendedor="' + escaparHtmlVendedor(row.id_vendedor || '') + '" data-vendedor="' + escaparHtmlVendedor(row.vendedor || 'SIN VENDEDOR') + '">' + escaparHtmlVendedor(row.vendedor || 'SIN VENDEDOR') + '</a></td>' +
                '<td class="text-right">' + formatoNumeroConversion(row.cant_cotizaciones, 0) + '</td>' +
                '<td class="text-right">' + formatoNumeroConversion(row.cant_facturas, 0) + '</td>' +
                '<td class="text-right">' + formatoMonedaConversion(row.total_cotizado) + '</td>' +
                '<td class="text-right">' + formatoMonedaConversion(row.total_facturado) + '</td>' +
                '<td class="text-right">' + formatoNumeroConversion(row.conversion, 2) + '%</td>' +
                '</tr>'
              );
            });
          }

          function buscarConversionVendedores() {
            const btn = $('#btnBuscarConversionVendedor');
            const fechaDesde = $('#f_desde').val();
            const fechaHasta = $('#f_hasta').val();

            if (!fechaDesde || !fechaHasta) {
              alert('Seleccione las fechas desde y hasta.');
              return;
            }

            if (fechaDesde > fechaHasta) {
              alert('La fecha desde no puede ser mayor que la fecha hasta.');
              return;
            }

            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Buscando...');

            $.ajax({
              url: '{{ route("dashboard.conversion_cotizaciones_vendedor") }}',
              type: 'POST',
              dataType: 'json',
              data: {
                _token: '{{ csrf_token() }}',
                f_desde: fechaDesde,
                f_hasta: fechaHasta,
                id_responsable: $('#id_responsable').val() || '',
                estado: $('#estado_p').val() || ''
              },
              success: function(resp) {
                if (!resp || !resp.ok) {
                  alert(resp && resp.mensaje ? resp.mensaje : 'No se pudo cargar la conversion.');
                  return;
                }

                conversionVendedoresRaw = resp.vendedores || [];
                conversionEstadosRaw = resp.estados || {};
                actualizarKpisEstadoConversion(conversionEstadosRaw);
                pintarTablaConversionVendedores(conversionVendedoresRaw);
              },
              error: function(xhr) {
                alert('Error al consultar conversion de cotizaciones. Codigo HTTP: ' + xhr.status);
              },
              complete: function() {
                btn.prop('disabled', false).html('<i class="fa fa-filter"></i> Aplicar filtros');
              }
            });
          }

          function iniciarSelectsConversion() {
            if (typeof $.fn.select2 === 'undefined') {
              return;
            }

            if (!$('#estado_p').hasClass('select2-hidden-accessible')) {
              $('#estado_p').select2({
                width: '100%',
                placeholder: 'Seleccione un estado...',
                allowClear: true,
                dropdownParent: $('#boxConversionCotizaciones')
              });
            }

            if (!$('#id_responsable').hasClass('select2-hidden-accessible')) {
              $('#id_responsable').select2({
                width: '100%',
                placeholder: 'Seleccione un vendedor...',
                allowClear: true,
                minimumInputLength: 3,
                dropdownParent: $('#boxConversionCotizaciones'),
                ajax: {
                  url: "{{ route('dashboard.buscar_vendedores_conversion') }}",
                  type: 'GET',
                  dataType: 'json',
                  delay: 300,
                  cache: true,
                  data: function(params) {
                    return { search: params.term || '', type: 'public' };
                  },
                  processResults: function(data) {
                    return { results: Array.isArray(data) ? data : (data.results || []) };
                  }
                }
              });
            }
          }

          $('#btnBuscarConversionVendedor').off('click.conversion').on('click.conversion', buscarConversionVendedores);


          $(document).off('click.conversionVendedor', '.link-conversion-vendedor').on('click.conversionVendedor', '.link-conversion-vendedor', function(e) {
            e.preventDefault();

            const idVendedor = String($(this).attr('data-id-vendedor') || '');
            const vendedor = String($(this).attr('data-vendedor') || 'SIN VENDEDOR');

            $('#modalConversionNombreVendedor').text(vendedor);
            $('#tbodyConversionVendedorEmpresas').empty();
            $('#totalConversionEmpresaCotizaciones').text('0');
            $('#totalConversionEmpresaFacturas').text('0');
            $('#totalConversionEmpresaCotizado').text('$0.00');
            $('#totalConversionEmpresaFacturado').text('$0.00');
            $('#totalConversionEmpresaPorcentaje').text('0.00%');
            $('#cargandoConversionVendedor').show();
            const modalConversionVendedor = $('#modalConversionVendedor');
            if (typeof modalConversionVendedor.modal === 'function') {
              modalConversionVendedor.modal('show');
            } else {
              modalConversionVendedor.show().addClass('in');
            }

            $.ajax({
              url: '{{ route("dashboard.conversion_cotizaciones_vendedor_empresas") }}',
              type: 'POST',
              dataType: 'json',
              data: {
                _token: '{{ csrf_token() }}',
                id_vendedor: idVendedor,
                f_desde: $('#f_desde').val(),
                f_hasta: $('#f_hasta').val(),
                estado: $('#estado_p').val() || ''
              },
              success: function(resp) {
                const tbody = $('#tbodyConversionVendedorEmpresas');
                const empresas = resp && resp.ok && resp.empresas ? resp.empresas : [];
                let totalCotizaciones = 0;
                let totalFacturas = 0;
                let totalCotizado = 0;
                let totalFacturado = 0;

                tbody.empty();

                if (!empresas.length) {
                  tbody.html('<tr><td colspan="6" class="text-center">Sin empresas para este vendedor.</td></tr>');
                  return;
                }

                empresas.forEach(function(row) {
                  const cotizaciones = parseInt(row.cant_cotizaciones || 0, 10);
                  const facturas = parseInt(row.cant_facturas || 0, 10);
                  const cotizado = parseFloat(row.total_cotizado || 0);
                  const facturado = parseFloat(row.total_facturado || 0);

                  totalCotizaciones += isNaN(cotizaciones) ? 0 : cotizaciones;
                  totalFacturas += isNaN(facturas) ? 0 : facturas;
                  totalCotizado += isNaN(cotizado) ? 0 : cotizado;
                  totalFacturado += isNaN(facturado) ? 0 : facturado;

                  tbody.append(
                    '<tr>' +
                    '<td>' + escaparHtmlVendedor(row.empresa || 'SIN EMPRESA') + '</td>' +
                    '<td class="text-right">' + formatoNumeroConversion(cotizaciones, 0) + '</td>' +
                    '<td class="text-right">' + formatoNumeroConversion(facturas, 0) + '</td>' +
                    '<td class="text-right">' + formatoMonedaConversion(cotizado) + '</td>' +
                    '<td class="text-right">' + formatoMonedaConversion(facturado) + '</td>' +
                    '<td class="text-right">' + formatoNumeroConversion(row.conversion, 2) + '%</td>' +
                    '</tr>'
                  );
                });

                const conversionTotal = totalCotizado > 0 ? (totalFacturado / totalCotizado) * 100 : 0;
                $('#totalConversionEmpresaCotizaciones').text(totalCotizaciones.toLocaleString('en-US'));
                $('#totalConversionEmpresaFacturas').text(totalFacturas.toLocaleString('en-US'));
                $('#totalConversionEmpresaCotizado').text(formatoMonedaConversion(totalCotizado));
                $('#totalConversionEmpresaFacturado').text(formatoMonedaConversion(totalFacturado));
                $('#totalConversionEmpresaPorcentaje').text(formatoNumeroConversion(conversionTotal, 2) + '%');
              },
              error: function(xhr) {
                $('#tbodyConversionVendedorEmpresas').html(
                  '<tr><td colspan="6" class="text-center text-danger">No se pudo cargar el detalle. Codigo HTTP: ' + xhr.status + '</td></tr>'
                );
              },
              complete: function() {
                $('#cargandoConversionVendedor').hide();
              }
            });
          });

          iniciarSelectsConversion();
          actualizarKpisEstadoConversion(conversionEstadosRaw);
          pintarTablaConversionVendedores(conversionVendedoresRaw);
        }

      });


      function notificarVentasProducto(titulo, mensaje, tipo) {
          if (typeof Swal !== 'undefined' && Swal.fire) {
            Swal.fire({
              title: titulo,
              text: mensaje,
              icon: tipo || 'info'
            });
            return;
          }

          if (typeof sweetAlert !== 'undefined' && sweetAlert.fire) {
            sweetAlert.fire({
              title: titulo,
              text: mensaje,
              icon: tipo || 'info'
            });
            return;
          }

          if (typeof swal !== 'undefined') {
            swal(titulo, mensaje, tipo || 'info');
            return;
          }

          $('#modalAvisoVentasProductoTitulo').text(titulo);
          $('#modalAvisoVentasProductoMensaje').text(mensaje);
          $('#modalAvisoVentasProducto').modal('show');
        }

       function sincronizarDriveVentasProducto() {
          const btn = $('#btnSincronizarDriveVentasProducto');
          const anio = $('#filtroAnioVentasProducto').val();

          btn
            .prop('disabled', true)
            .html(
              '<i class="fa fa-spinner fa-spin"></i> Sincronizando...'
            );

          $.ajax({
            url: '{{ route("dashboard.ventas_producto_sincronizar_drive") }}',
            type: 'POST',
            dataType: 'json',
            data: {
              _token: '{{ csrf_token() }}',
              anio: anio
            },
            success: function(resp) {
              if (!resp || !resp.ok) {
                notificarVentasProducto(
                  'Error',
                  resp && resp.mensaje ?
                  resp.mensaje :
                  'No se pudo sincronizar Google Drive.',
                  'error'
                );

                return;
              }

              const registro = resp.registro_sanitario || {};
              const ficha = resp.ficha_tecnica || {};
              const mensaje =
                'Registro sanitario: ' +
                (registro.ventas_actualizadas || 0) +
                ' ventas actualizadas, ' +
                (registro.ventas_sin_pdf || 0) +
                ' sin PDF. ' +
                'Ficha tecnica: ' +
                (ficha.ventas_actualizadas || 0) +
                ' ventas actualizadas, ' +
                (ficha.ventas_sin_pdf || 0) +
                ' sin PDF.';

              notificarVentasProducto(
                'Sincronizacion finalizada',
                mensaje,
                'success'
              );

              if (typeof window.aplicarFiltrosVentasProducto === 'function') {
                window.aplicarFiltrosVentasProducto();
              }
            },
            error: function(xhr) {
              let mensaje = 'Error HTTP: ' + xhr.status;

              if (
                xhr.responseJSON &&
                xhr.responseJSON.mensaje
              ) {
                mensaje = xhr.responseJSON.mensaje;
              }

              notificarVentasProducto(
                'Error',
                mensaje,
                'error'
              );
            },
            complete: function() {
              btn
                .prop('disabled', false)
                .html(
                  '<i class="fa fa-refresh"></i> Sincronizar Drive'
                );
            }
          });
        }

         $('#btnSincronizarDriveVentasProducto')
          .off('click.ventasProductoDrive')
          .on('click.ventasProductoDrive', function() {
            sincronizarDriveVentasProducto();
          });

    </script>
</body>

</html>
