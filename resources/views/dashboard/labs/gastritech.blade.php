<style>
  .dashboard-gastritech .dashboard-box {
    background: #fff;
    border: 1px solid #dce4ec;
    border-radius: 8px;
    box-shadow: 0 3px 12px rgba(15, 23, 42, .06);
    overflow: hidden;
  }

  .dashboard-gastritech .dashboard-box .box-header {
    background: #fff;
  }

  .dashboard-gastritech .dashboard-box .box-body {
    background: #fff;
  }
</style>
      @if(!empty($mostrar_dashboard_gastritech))
      <section class="content dashboard-gastritech">


        <div class="box box-primary dashboard-box" id="boxVentasVendedor">
          <div class="box-header">
            <h3 class="box-title">
              <b style="color:#3c8dbc">Ventas por vendedor</b>
              <small id="tituloRangoVentas" style="color:#666; margin-left:8px;"></small>
            </h3>
          </div>
          <div class="box-body">
            <div class="row" style="margin-bottom: 15px; padding: 10px; background:#f9f9f9; border-radius:5px;">
              <div class="col-md-3">
                <label>Mes</label>
                <select id="filtroMesVentas" class="form-control input-sm">
                  <option value="0">
                    Todos
                  </option>
                  @php
                  $meses = [
                  'Enero',
                  'Febrero',
                  'Marzo',
                  'Abril',
                  'Mayo',
                  'Junio',
                  'Julio',
                  'Agosto',
                  'Septiembre',
                  'Octubre',
                  'Noviembre',
                  'Diciembre'
                  ];
                  @endphp
                  @foreach($meses as $idx => $nombre)
                  <option value="{{ $idx + 1 }}"
                    {{ ($idx + 1) == $mes_actual ? 'selected' : '' }}>
                    {{ $nombre }}
                  </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label>Año</label>
                <select id="filtroAnioVentas" class="form-control input-sm">
                  @php
                  $anioSistema = (int) date('Y');
                  @endphp
                  @for($y = $anioSistema; $y >= $anioSistema - 5; $y--)
                  <option value="{{ $y }}"
                    {{ $y == $anio_actual ? 'selected' : '' }}>

                    {{ $y }}
                  </option>
                  @endfor
                </select>
              </div>
              <div class="col-md-3">
                <label>&nbsp;</label>
                <div class="checkbox" style="margin-top:2px;">
                  <label>
                    <input type="checkbox" id="filtroExcluirRobles" checked>
                    <b>Excluir Grupo Robles</b>
                  </label>
                </div>
              </div>
              <div class="col-md-3">
                <label>&nbsp;</label>
                <div class="row">

                  <div class="col-xs-6" style="padding-right:4px;">
                    <button type="button"
                      class="btn btn-primary btn-block btn-sm"
                      id="btnAplicarFiltroVentas">
                      <i class="fa fa-filter"></i>
                      Aplicar
                    </button>
                  </div>

                  <div class="col-xs-6" style="padding-left:4px;">
                    <button type="button"
                      class="btn btn-success btn-block btn-sm"
                      id="btnExcelVentasVendedor">

                      <i class="fa fa-file-excel-o"></i>
                      Excel
                    </button>
                  </div>

                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped table-condensed">
                    <thead>
                      <tr>
                        <th>Vendedor</th>
                        <th class="text-right">Empresas</th>
                        <th class="text-right">Facturas</th>
                        <th class="text-right">Total</th>
                      </tr>
                    </thead>
                    <tbody id="tbodyVentasVendedor"></tbody>
                  </table>
                </div>
              </div>
              <div class="col-md-6">
                <div class="chart-panel">
                  <div class="chart-title">Regresión: vendedor vs total de ventas</div>
                  <canvas id="chartVentasVendedor" height="230"></canvas>
                  <div id="chartVentasVendedorEq" class="chart-equation"></div>
                </div>
              </div>
            </div>
          </div>
        </div>



        <div class="box box-primary dashboard-box" id="boxVentasGeneral">
          <div class="box-header">
            <h3 class="box-title">
              <b style="color:#3c8dbc">Ventas general</b>
              <small id="tituloRangoVentasGeneral" style="color:#666; margin-left:8px;"></small>
            </h3>
          </div>

          <div class="box-body">
            <div class="row" style="margin-bottom: 15px; padding: 10px; background:#f9f9f9; border-radius:5px;">
              <div class="col-md-3">
                <label>Mes</label>
                <select id="filtroMesVentasGeneral" class="form-control input-sm">
                  @foreach($meses as $idx => $nombre)
                  <option value="{{ $idx + 1 }}" {{ ($idx + 1) == $mes_actual ? 'selected' : '' }}>
                    {{ $nombre }}
                  </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-3">
                <label>Año</label>
                <select id="filtroAnioVentasGeneral" class="form-control input-sm">
                  @for($y = $anio_actual; $y >= $anio_actual - 5; $y--)
                  <option value="{{ $y }}" {{ $y == $anio_actual ? 'selected' : '' }}>{{ $y }}</option>
                  @endfor
                </select>
              </div>

              <div class="col-md-3">
                <label>&nbsp;</label>
                <div class="checkbox" style="margin-top:2px;">
                  <label>
                    <input type="checkbox" id="filtroExcluirRoblesGeneral" checked>
                    <b>Excluir Grupo Robles</b>
                  </label>
                </div>
              </div>

              <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-primary btn-block btn-sm" id="btnAplicarFiltroVentasGeneral">
                  <i class="fa fa-filter"></i> Aplicar filtros
                </button>
              </div>
            </div>


            <div class="row" id="cardsEvolucionVentasGeneral" style="margin-bottom:15px;">
              @foreach($evolucion_ventas_general as $index => $row)

              @php
              $coloresCard = ['#3c8dbc', '#00a65a', '#f39c12', '#dd4b39', '#605ca8'];
              $colorCard = $coloresCard[$index % count($coloresCard)];

              $clienteNombre = !empty($row->cliente_top) ? $row->cliente_top : 'Sin cliente';
              $clienteVentas = isset($row->cliente_top_ventas) ? $row->cliente_top_ventas : 0;
              $clienteFacturas = isset($row->cliente_top_facturas) ? $row->cliente_top_facturas : 0;
              @endphp

              <div class="col-md-4">
                <div class="card-evolucion-ventas" style="border-left:5px solid {{ $colorCard }};">
                  <div class="titulo-mes">
                    {{ $meses[$mes_actual - 1] }}
                  </div>

                  <div class="anio" style="color: {{ $colorCard }};">
                    {{ $row->anio }}
                  </div>

                  <div class="total-ventas">
                    ${{ number_format($row->total_ventas, 2) }}
                  </div>

                  <div class="facturas">
                    {{ number_format($row->total_facturas, 0) }} facturas
                  </div>

                  <div class="bloque-cliente">
                    <div class="cliente-label">Mayor cliente</div>

                    <div class="cliente-nombre" title="{{ $clienteNombre }}">
                      {{ \Illuminate\Support\Str::limit($clienteNombre, 42) }}
                    </div>

                    <div class="cliente-monto" style="color: {{ $colorCard }};">
                      ${{ number_format($clienteVentas, 2) }}
                    </div>

                    <div class="cliente-facturas">
                      {{ number_format($clienteFacturas, 0) }} facturas
                    </div>
                  </div>
                </div>
              </div>
              @endforeach
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped table-condensed">
                    <thead>
                      <tr>
                        <th>Fecha</th>
                        <th class="text-right">Cant. Facturas</th>
                        <th class="text-right">Valor Mensual</th>
                      </tr>
                    </thead>
                    <tbody id="tbodyVentasGeneral">
                      @forelse($ventas_general as $row)
                      <tr>
                        <td>{{ $row->fecha }}</td>
                        <td class="text-right">{{ $row->total_facturas }}</td>
                        <td class="text-right">${{ number_format($row->total_ventas, 2) }}</td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="3" class="text-center">Sin datos</td>
                      </tr>
                      @endforelse
                    </tbody>
                    <tfoot>
                      <tr style="background:#f5f5f5; font-weight:bold;">
                        <td class="text-right">TOTAL</td>
                        <td class="text-right" id="totalFacturasVentasGeneral">0</td>
                        <td class="text-right" id="totalValorVentasGeneral">$0.00</td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="box box-primary dashboard-box"
          id="boxVentasProducto">

          <div class="box-header">

            <h3 class="box-title">
              <b style="color:#3c8dbc">
                Ventas por producto
              </b>

              <small id="tituloRangoVentasProducto"
                style="color:#666; margin-left:8px;">
              </small>
            </h3>

          </div>

          <div class="box-body">

            {{-- FILTROS --}}
            <div class="row"
              style="
                  margin-bottom:15px;
                  padding:10px;
                  background:#f9f9f9;
                  border-radius:5px;
                ">

              <div class="col-md-3">
                <label>Año</label>

                <select id="filtroAnioVentasProducto"
                  class="form-control input-sm">

                  @for($y = $anio_actual; $y >= $anio_actual - 5; $y--)
                  <option value="{{ $y }}"
                    {{ $y == $anio_actual ? 'selected' : '' }}>

                    {{ $y }}
                  </option>
                  @endfor

                </select>
              </div>

              <div class="col-md-7">
                <label>Producto</label>

                <div style="position:relative;">

                  <input type="text"
                    id="filtroProductoVentasTexto"
                    class="form-control input-sm"
                    autocomplete="off"
                    placeholder="Escriba mínimo 3 caracteres del código o nombre">

                  <input type="hidden"
                    id="filtroProductoVentas"
                    value="">

                  <div id="listaAutocompleteProductos"
                    style="
                        display:none;
                        position:absolute;
                        top:100%;
                        left:0;
                        right:0;
                        z-index:9999;
                        max-height:260px;
                        overflow-y:auto;
                        background:#fff;
                        border:1px solid #ccc;
                        border-top:none;
                        box-shadow:0 4px 8px rgba(0,0,0,.15);
                      ">
                  </div>

                </div>

                <small id="productoSeleccionadoTexto"
                  style="display:block; margin-top:4px; color:#777;">
                  Todos los productos
                </small>
              </div>

              <div class="col-md-2">
                <label>&nbsp;</label>

                <button type="button"
                  class="btn btn-primary btn-block btn-sm"
                  id="btnAplicarFiltroVentasProducto">

                  <i class="fa fa-filter"></i>
                  Aplicar filtros
                </button>
              </div>

            </div>

            {{-- TABLA --}}
            <div class="table-responsive">

              <table class="table table-bordered table-striped table-condensed">

                <thead>
                  <tr>
                    <th style="width:110px;">Código</th>
                    <th>Producto</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-center">Año</th>
                    <th class="text-right">Total venta</th>
                    <th>Identificación</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th class="text-center" style="width:80px;">PDF</th>
                  </tr>
                </thead>

                <tbody id="tbodyVentasProducto">

                  @forelse($ventas_por_producto as $row)

                  <tr>
                    <td>{{ $row->id_ct_productos }}</td>

                    <td>{{ $row->producto }}</td>

                    <td class="text-right">
                      {{ number_format($row->total_cantidad, 2) }}
                    </td>

                    <td class="text-center">
                      {{ $row->anio }}
                    </td>

                    <td class="text-right">
                      ${{ number_format($row->total_venta, 2) }}
                    </td>

                    <td>{{ $row->identificacion }}</td>

                    <td>{{ $row->cliente }}</td>

                    <td>{{ $row->vendedor }}</td>

                    <td class="text-center">
                      <button type="button"
                        class="btn btn-primary btn-xs"
                        title="Agregar vinculos PDF"
                        data-venta-id="{{ $row->id_venta }}"
                        data-producto-id="{{ $row->id_ct_productos }}"
                        data-producto="{{ $row->producto }}"
                        onclick="abrirModalVinculosProducto(this)">
                        <i class="fa fa-plus"></i>
                      </button>
                    </td>
                  </tr>

                  @empty

                  <tr>
                    <td colspan="9" class="text-center">
                      Sin datos
                    </td>
                  </tr>

                  @endforelse

                </tbody>

                <tfoot>
                  <tr style="background:#f5f5f5; font-weight:bold;">

                    <td colspan="2" class="text-right">
                      TOTAL
                    </td>

                    <td class="text-right"
                      id="totalCantidadVentasProducto">
                      0.00
                    </td>

                    <td></td>

                    <td class="text-right"
                      id="totalValorVentasProducto">
                      $0.00
                    </td>

                    <td colspan="4"></td>
                  </tr>
                </tfoot>

              </table>

            </div>

            <div class="table-pager"
              id="ventasProductoPager"
              style="display:none;">

              <button type="button"
                class="btn btn-default btn-xs"
                id="ventasProductoPrev">
                Anterior
              </button>
              <span id="ventasProductoPageInfo">
                Página 1 de 1
              </span>
              <button type="button"
                class="btn btn-default btn-xs"
                id="ventasProductoNext">
                Siguiente
              </button>
            </div>
          </div>
        </div>

        <div class="box box-primary dashboard-box">
          <div class="box-header">
            <h3 class="box-title"><b style="color:#3c8dbc">Accesorios por expirar (60 d&iacute;as)</b></h3>
          </div>

          <div class="box-body">
            {{-- FILTROS --}}
            <div class="row" style="margin-bottom: 15px; padding: 10px; background:#f9f9f9; border-radius:5px;">
              <div class="col-md-5">
                <label>Bodega</label>
                <select id="filtroBodegaAccesorios" class="form-control input-sm">
                  <option value="">Todas las bodegas</option>
                  @foreach($bodegas_accesorios as $bodega)
                  <option value="{{ $bodega }}">{{ $bodega }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-5">
                <label>Estado de alerta</label>

                <style>
                  .opt-critico {
                    color: #e74c3c;
                    font-weight: bold;
                  }

                  .opt-advertencia {
                    color: #f1c40f;
                    font-weight: bold;
                  }

                  .opt-normal {
                    color: #2ecc71;
                    font-weight: bold;
                  }

                  .fila-total-accesorios {
                    background: #f5f5f5;
                    font-weight: bold;
                  }
                </style>

                <select id="filtroAlertaAccesorios" class="form-control input-sm">
                  <option value="">Todos los estados</option>
                  <option value="critico" class="opt-critico">● Crítico</option>
                  <option value="advertencia" class="opt-advertencia">● Advertencia</option>
                  <option value="normal" class="opt-normal">● Normal</option>
                </select>

                <span id="indicadorEstado" class="indicador"></span>
              </div>

              <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-primary btn-block btn-sm" id="btnAplicarFiltrosAccesorios">
                  <i class="fa fa-filter"></i> Aplicar filtros
                </button>
              </div>
            </div>

            {{-- KPI PRINCIPALES DE ACCESORIOS --}}
            <div class="row" style="margin-bottom:15px;">
              @foreach($kpi_accesorios as $item)

              @php
              $idCantidad = '';
              $idPorcentaje = '';

              if ($item['key'] == 'critico') {
              $idCantidad = 'kpiAccesoriosCritico';
              $idPorcentaje = 'kpiAccesoriosCriticoPct';
              } elseif ($item['key'] == 'advertencia') {
              $idCantidad = 'kpiAccesoriosAdvertencia';
              $idPorcentaje = 'kpiAccesoriosAdvertenciaPct';
              } elseif ($item['key'] == 'normal') {
              $idCantidad = 'kpiAccesoriosNormal';
              $idPorcentaje = 'kpiAccesoriosNormalPct';
              } elseif ($item['key'] == 'caducado') {
              $idCantidad = 'kpiAccesoriosCaducado';
              $idPorcentaje = 'kpiAccesoriosCaducadoPct';
              }
              @endphp

              <div class="col-md-3">
                <div style="background:#fff; border:1px solid #e5e7eb; border-left:5px solid {{ $item['color'] }}; border-radius:8px; padding:14px; min-height:105px;">

                  <div style="font-size:12px; color:#666; text-transform:uppercase;">
                    {{ $item['titulo'] }}
                  </div>

                  <div id="{{ $idCantidad }}"
                    style="font-size:28px; font-weight:bold; color:{{ $item['color'] }}; line-height:1.1;">
                    {{ number_format($item['cantidad'], 0) }}
                  </div>

                  @if($item['key'] == 'caducado')
                  <div id="{{ $idPorcentaje }}" style="font-size:12px; color:#666; margin-top:6px;">
                    Items vencidos detectados
                  </div>
                  @else
                  <div id="{{ $idPorcentaje }}" style="font-size:12px; color:#666; margin-top:6px;">
                    {{ number_format($item['porcentaje'], 1) }}% sobre el inventario total
                  </div>
                  @endif

                </div>
              </div>

              @endforeach
            </div>


            {{-- KPI CADUCADOS POR BODEGA --}}
            @if($kpi_bodegas_caducados->count() > 0)
            <div class="row" style="margin-bottom:15px;" id="contenedorKpiBodegasCaducados">
              @foreach($kpi_bodegas_caducados as $item)
              <div class="col-md-3">
                <div class="kpi-bodega-caducados-card"
                  data-bodega="{{ $item->bodega }}"
                  style="background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:14px; min-height:90px;">

                  <div style="font-size:11px; color:#777; text-transform:uppercase;">
                    Caducados por bodega
                  </div>

                  <div style="font-size:13px; font-weight:bold; color:#333; min-height:34px;">
                    {{ $item->bodega }}
                  </div>

                  <div class="kpiBodegaCaducadosCantidad"
                    data-bodega="{{ $item->bodega }}"
                    style="font-size:24px; font-weight:bold; color:#8b1e1e;">
                    {{ number_format($item->cantidad, 0) }}
                  </div>

                </div>
              </div>
              @endforeach
            </div>
            @endif

            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped table-condensed">
                    <thead>
                      <tr>
                        <th>Producto</th>
                        <th>Bodega</th>
                        <th>Vence</th>
                        <th class="text-right">Existencia</th>
                      </tr>
                    </thead>

                    <tbody id="tbodyAccesorios">
                      @forelse($accesorios_por_expirar as $row)
                      @php
                      $estilo_vencimiento = '';

                      if ($row->alerta_color == 'caducado') {
                      $estilo_vencimiento = 'background-color: #ead1d1;';
                      } elseif ($row->alerta_color == 'critico') {
                      $estilo_vencimiento = 'background-color: #f8d7da;';
                      } elseif ($row->alerta_color == 'advertencia') {
                      $estilo_vencimiento = 'background-color: #fff3cd;';
                      } elseif ($row->alerta_color == 'normal') {
                      $estilo_vencimiento = 'background-color: #d4edda;';
                      }
                      @endphp

                      <tr class="accesorio-row"
                        data-bodega="{{ $row->bodega }}"
                        data-alerta="{{ $row->alerta_color }}"
                        data-existencia="{{ $row->existencia }}"
                        style="{{ $estilo_vencimiento }}"
                        title="{{ $row->alerta_texto }} | Proyección ventas: {{ $row->venta_proyectada }}">
                        <td>{{ $row->producto }}</td>
                        <td><small>{{ $row->bodega }}</small></td>
                        <td>{{ $row->fecha_vence }}</td>
                        <td class="text-right">{{ number_format($row->existencia, 0) }}</td>
                      </tr>
                      @empty
                      <tr id="accesoriosVacio">
                        <td colspan="4" class="text-center">Sin datos</td>
                      </tr>
                      @endforelse
                    </tbody>

                    <tfoot>
                      <tr style="background:#f5f5f5; font-weight:bold;">
                        <td colspan="3" class="text-right">Total existencia</td>
                        <td class="text-right" id="totalExistenciaAccesorios">0</td>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <div class="table-pager" id="accesoriosTablePager" style="display:none;">
                  <button type="button" class="btn btn-default btn-xs" id="accesoriosPagePrev">Anterior</button>
                  <span id="accesoriosPageInfo">Página 1 de 1</span>
                  <button type="button" class="btn btn-default btn-xs" id="accesoriosPageNext">Siguiente</button>
                </div>

                <div id="accesoriosVaciosFiltro" style="display:none; text-align:center; padding:15px; color:#999;">
                  <i class="fa fa-filter"></i> No hay resultados con los filtros aplicados
                </div>
              </div>
            </div>
          </div>
        </div>



        @if(false)
        {{-- Stock mínimos (items de venta) oculto temporalmente. --}}
        <div class="box box-primary dashboard-box">
          <div class="box-header">
            <h3 class="box-title">
              <b style="color:#3c8dbc">Stock m&iacute;nimos (items de venta)</b>
            </h3>
          </div>

          <div class="box-body">

            <div class="row" style="margin-bottom: 15px; padding: 10px; background:#f9f9f9; border-radius:5px;">
              <div class="col-md-5">
                <label>Bodega</label>
                <select id="filtroBodegaStockMinimo" class="form-control input-sm">
                  <option value="">Todas las bodegas</option>
                  @foreach($bodegas_stock_minimos as $bodega)
                  <option value="{{ $bodega }}">{{ $bodega }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-5">
                <label>Estado de alerta</label>
                <select id="filtroAlertaStockMinimo" class="form-control input-sm">
                  <option value="">Todos los estados</option>
                  <option value="critico">● Crítico</option>
                  <option value="advertencia">● Advertencia</option>
                  <option value="normal">● Normal</option>
                  <option value="caducado">● Caducado</option>
                </select>
              </div>

              <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-primary btn-block btn-sm" id="btnAplicarFiltrosStockMinimo">
                  <i class="fa fa-filter"></i> Aplicar filtros
                </button>
              </div>
            </div>

            {{-- KPI DE STOCK MÍNIMO POR ESTADO --}}
            <div class="row" style="margin-bottom:15px;">

              {{-- CRÍTICO --}}
              <div class="col-md-3">
                <div style="
                  background:#fff;
                  border:1px solid #e5e7eb;
                  border-left:5px solid #dc3545;
                  border-radius:8px;
                  padding:14px;
                  min-height:120px;
                ">
                  <div style="font-size:12px; color:#666; text-transform:uppercase;">
                    Crítico
                  </div>

                  <div style="margin-top:8px;">
                    <span style="font-size:12px; color:#777;">Stock mínimo</span>
                    <div id="kpiStockCriticoMinimo"
                      style="font-size:24px; font-weight:bold; color:#dc3545;">
                      0
                    </div>
                  </div>

                  <div style="font-size:12px; color:#666;">
                    Cantidad actual:
                    <b id="kpiStockCriticoActual">0</b>
                  </div>
                </div>
              </div>

              {{-- ADVERTENCIA --}}
              <div class="col-md-3">
                <div style="
                  background:#fff;
                  border:1px solid #e5e7eb;
                  border-left:5px solid #f0ad4e;
                  border-radius:8px;
                  padding:14px;
                  min-height:120px;
                ">
                  <div style="font-size:12px; color:#666; text-transform:uppercase;">
                    Advertencia
                  </div>

                  <div style="margin-top:8px;">
                    <span style="font-size:12px; color:#777;">Stock mínimo</span>
                    <div id="kpiStockAdvertenciaMinimo"
                      style="font-size:24px; font-weight:bold; color:#f0ad4e;">
                      0
                    </div>
                  </div>

                  <div style="font-size:12px; color:#666;">
                    Cantidad actual:
                    <b id="kpiStockAdvertenciaActual">0</b>
                  </div>
                </div>
              </div>

              {{-- NORMAL --}}
              <div class="col-md-3">
                <div style="
                  background:#fff;
                  border:1px solid #e5e7eb;
                  border-left:5px solid #28a745;
                  border-radius:8px;
                  padding:14px;
                  min-height:120px;
                ">
                  <div style="font-size:12px; color:#666; text-transform:uppercase;">
                    Normal
                  </div>

                  <div style="margin-top:8px;">
                    <span style="font-size:12px; color:#777;">Stock mínimo</span>
                    <div id="kpiStockNormalMinimo"
                      style="font-size:24px; font-weight:bold; color:#28a745;">
                      0
                    </div>
                  </div>

                  <div style="font-size:12px; color:#666;">
                    Cantidad actual:
                    <b id="kpiStockNormalActual">0</b>
                  </div>
                </div>
              </div>

              {{-- CADUCADO --}}
              <div class="col-md-3">
                <div style="
                  background:#fff;
                  border:1px solid #e5e7eb;
                  border-left:5px solid #8b1e1e;
                  border-radius:8px;
                  padding:14px;
                  min-height:120px;
                ">
                  <div style="font-size:12px; color:#666; text-transform:uppercase;">
                    Caducado
                  </div>

                  <div style="margin-top:8px;">
                    <span style="font-size:12px; color:#777;">Stock mínimo</span>
                    <div id="kpiStockCaducadoMinimo"
                      style="font-size:24px; font-weight:bold; color:#8b1e1e;">
                      0
                    </div>
                  </div>

                  <div style="font-size:12px; color:#666;">
                    Cantidad actual:
                    <b id="kpiStockCaducadoActual">0</b>
                  </div>
                </div>
              </div>
            </div>


            {{-- KPI DE STOCK MÍNIMO POR BODEGA --}}
            @if(isset($bodegas_stock_minimos) && $bodegas_stock_minimos->count() > 0)
            <div class="row" style="margin-bottom:15px;" id="contenedorKpiStockBodegas">

              @foreach($bodegas_stock_minimos as $bodega)
              <div class="col-md-3" style="margin-bottom:10px;">
                <div class="kpi-stock-bodega-card"
                  data-bodega="{{ $bodega }}"
                  style="
                          background:#fff;
                          border:1px solid #e5e7eb;
                          border-radius:8px;
                          padding:14px;
                          min-height:110px;
                        ">

                  <div style="font-size:11px; color:#777; text-transform:uppercase;">
                    Stock mínimo por bodega
                  </div>

                  <div style="
                        font-size:13px;
                        font-weight:bold;
                        color:#333;
                        min-height:35px;
                      ">
                    {{ $bodega }}
                  </div>

                  <div style="font-size:12px; color:#777;">
                    Stock mínimo:
                    <b class="kpiStockBodegaMinimo"
                      data-bodega="{{ $bodega }}">
                      0
                    </b>
                  </div>

                  <div style="font-size:12px; color:#777;">
                    Cantidad actual:
                    <b class="kpiStockBodegaActual"
                      data-bodega="{{ $bodega }}">
                      0
                    </b>
                  </div>

                </div>
              </div>
              @endforeach

            </div>
            @endif


            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped table-condensed">
                    <thead>
                      <tr>
                        <th>Producto</th>
                        <th>Bodega</th>
                        <th class="text-right">Stock mínimo</th>
                        <th class="text-right">Cantidad actual</th>
                        <th>Estado de alerta</th>
                      </tr>
                    </thead>

                    <tbody id="tbodyStockMinimo">
                      @forelse($stock_minimos_venta as $row)
                      <tr class="stock-row"
                        data-bodega="{{ $row->bodega }}"
                        data-alerta="{{ $row->estado_clase }}"
                        data-stock-minimo="{{ $row->stock_minimo }}"
                        data-cantidad-actual="{{ $row->cantidad_actual }}">

                        <td>{{ $row->producto }}</td>

                        <td>{{ $row->bodega }}</td>

                        <td class="text-right">
                          {{ number_format($row->stock_minimo, 0) }}
                        </td>

                        <td class="text-right">
                          <b>{{ number_format($row->cantidad_actual, 0) }}</b>
                        </td>

                        <td class="estado-alerta">
                          <span class="dot-alerta {{ $row->estado_clase }}"></span>
                          {{ $row->estado_alerta }}
                        </td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="5" class="text-center">Sin datos</td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>

                <div class="table-pager" id="stockTablePager" style="display:none;">
                  <button type="button" class="btn btn-default btn-xs" id="stockPagePrev">Anterior</button>
                  <span id="stockPageInfo">Página 1 de 1</span>
                  <button type="button" class="btn btn-default btn-xs" id="stockPageNext">Siguiente</button>
                </div>

                <div id="stockVaciosFiltro" style="display:none; text-align:center; padding:15px; color:#999;">
                  <i class="fa fa-filter"></i> No hay resultados con los filtros aplicados
                </div>
              </div>
            </div>

          </div>
        </div>
        @endif
      </section>
      @endif
