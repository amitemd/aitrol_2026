@if(!empty($mostrar_dashboard_gastritech))
<section class="content dashboard-gastritech dashboard-ficha-tecnica">

        <div class="box box-primary dashboard-box"
          id="boxVentasProducto">

          <div class="box-header">

            <h3 class="box-title">
              <b style="color:#3c8dbc">
                Asignar Ficha Tecnica
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

              <div class="col-md-5">
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

              <div class="col-md-2">
                <label>&nbsp;</label>

                <button type="button"
                  class="btn btn-success btn-block btn-sm"
                  id="btnSincronizarDriveVentasProducto">

                  <i class="fa fa-refresh"></i>
                  Sincronizar Drive
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

</section>
@endif
