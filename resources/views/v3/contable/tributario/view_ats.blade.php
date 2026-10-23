<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{trans('v3/new_submodulo.'.config('data.submodulo'))}}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{url('v3/admin')}}">{{trans('v3/new_menu.inicio')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('v3/new_submodulo.'.config('data.submodulo'))}}</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i>{{config('data.titulo')}}
                    <div>
                        <div style="position: absolute; right: 5px;top:7px">
                            <a href="javascript:;" class="btn btn-primary btn-sm"><i class="fa-regular fa-save"></i> <span class="botones">Guardar</span></a>
                        </div>
                    </div>
                </h3>
            </div>
            <div class="card-body">
                <form method="post" action="v3/{{config('data.controlador')}}/{{config('data.submodulo')}}">
                    <div class="row">
                        {{ csrf_field() }}
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label>Año</label>
                                <input type="month" class="form-control" id="periodo" name="periodo" value="<?= config('data.input') != null ? config('data.input')->periodo : '' ?>" />
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success"><i class="fas fa-search"></i> Visualizar</button>
                            </div>
                        </div>
                    </div>
                </form>
                <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="ventas-tab" data-toggle="pill" href="#ventas" role="tab" aria-controls="ventas" aria-selected="true">Ventas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="compras-tab" data-toggle="pill" href="#compras" role="tab" aria-controls="compras" aria-selected="false">Compras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="formulario_104-tab" data-toggle="pill" href="#formulario_104" role="tab" aria-controls="formulario_104" aria-selected="false">Comprobantes anulados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="formulario_103-tab" data-toggle="pill" href="#formulario_103" role="tab" aria-controls="formulario_103" aria-selected="false">ATS generados</a>
                    </li>
                </ul>
                <div class="tab-content" id="custom-content-below-tabContent">
                    <div class="tab-pane fade show active" id="ventas" role="tabpanel" aria-labelledby="ventas-tab" style="height: 600px;overflow: auto;">
                        <table id="tableVentasAts" class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>...</th>
                                    <th>No</th>
                                    <th>Tipo</th>
                                    <th>Comprobante</th>
                                    <th>Cliente</th>
                                    <th>T. Cliente</th>
                                    <th>RUC/CI</th>
                                    <th>Tipo comprobante</th>
                                    <th>Base excenta</th>
                                    <th>Base 0%</th>
                                    <th>Base iva</th>
                                    <th>Descuento</th>
                                    <th>Monto iva</th>
                                    <th>Subtotal</th>
                                    <th>Ret. iva</th>
                                    <th>Ret. fuente</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (config('data.input') != null) {
                                    foreach (config('data.ventas') as $venta) {
                                        echo '<tr>
                                        <td align="center">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" class="chePendientes" onchange="calcularPendientes(\'' . $venta->id . '\')" id="chePendientes_' . $venta->id . '">
                                                <label for="chePendientes_' . $venta->id . '"></label>
                                            </div>
                                        </td>
                                        <td>' . $venta->id . '</td>
                                        <td align="center">' . $venta->tipo_doc . '</td>
                                        <td>' . $venta->nro_comprobante . '</td>
                                        <td>' . $venta->nombre . '</td>
                                        <td align="center">' . str_pad($venta->tipo, 2, 0, STR_PAD_LEFT) . '</td>
                                        <td>' . $venta->identificacion . '</td>
                                        <td align="center">' . $venta->tipo_comprobante . '</td>
                                        <td align="right">' . $venta->baseex . '</td>
                                        <td align="right">' . $venta->subtotal_0 . '</td>
                                        <td align="right">' . $venta->subtotal_12 . '</td>
                                        <td align="right">' . $venta->descuento . '</td>
                                        <td align="right">' . $venta->iva_total . '</td>
                                        <td align="right">' . $venta->total_final . '</td>
                                        <td align="right">' . $venta->valor_fuente . '</td>
                                        <td align="right">' . $venta->valor_iva . '</td>                                        
                                        </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="compras" role="tabpanel" aria-labelledby="compras-tab" style="height: 600px;overflow: auto;">
                        <table class="table table-bordered table-striped" style="font-size: 10px;width: 100%;">
                            <thead>
                                <tr>
                                    <th style="vertical-align:middle;">No</th>
                                    <th style="vertical-align:middle;">Tipo</th>
                                    <th style="vertical-align:middle;">Asiento</th>
                                    <th style="vertical-align:middle;">Fecha</th>
                                    <th style="vertical-align:middle;">Proveedor</th>
                                    <th style="vertical-align:middle;">Ruc</th>
                                    <th style="vertical-align:middle;">Autorización</th>
                                    <th style="vertical-align:middle;">Observación</th>
                                    <th style="vertical-align:middle;">Documento</th>
                                    <th style="vertical-align:middle;">Base 0%</th>
                                    <th style="vertical-align:middle;">Base diferente 0%</th>
                                    <th style="vertical-align:middle;">Descuento</th>
                                    <th style="vertical-align:middle;">Subtotal</th>
                                    <th style="vertical-align:middle;">Iva</th>
                                    <th style="vertical-align:middle;">Total</th>
                                    <?php
                                    if (config('data.porcentajes') != '' && count(config('data.porcentajes')) > 0) {
                                        foreach (config('data.porcentajes') as $row) {
                                            echo '<th style="vertical-align:middle;">' . $row->nombre . '<br/> ' . $row->valor . '%</th>';
                                        }
                                    }
                                    ?>
                                    <th style="vertical-align:middle;">Total a pagar</th>
                                    <th style="vertical-align:middle;"># Retención</th>
                                    <th style="vertical-align:middle;">Autorización</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $htmlCompras = '';
                                if (config('data.compras') != null && count(config('data.compras')) > 0) {
                                    $count = 0;
                                    foreach (config('data.compras') as $row) {
                                        $count++;
                                        $htmlCompras .= '<tr>
                                        <td>' . $count . '</td>
                                        <td>' . $row->nombre . '</td>
                                        <td>' . $row->id_asiento_cabecera . '</td>
                                        <td>' . $row->fecha . '</td>
                                        <td>' . $row->razonsocial . '</td>
                                        <td>' . $row->proveedor . '</td>
                                        <td>' . $row->autorizacion . '</td>
                                        <td>' . $row->observacion . '</td>
                                        <td>' . $row->numero . '</td>
                                        <td align="right">' . $row->subtotal_0 . '</td>
                                        <td align="right">' . $row->subtotal_12 . '</td>
                                        <td align="right">' . $row->descuento . '</td>
                                        <td align="right">' . $row->subtotal . '</td>
                                        <td align="right">' . $row->impuesto . '</td>
                                        <td align="right">' . $row->total . '</td>
                                        ' . Retenciones::getDatosRetencionCompras($row, config('data.porcentajes')) . '
                                        </tr>';
                                    }
                                }
                                echo $htmlCompras;
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="formulario_104" role="tabpanel" aria-labelledby="formulario_104-tab">
                        Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna.
                    </div>
                    <div class="tab-pane fade" id="formulario_103" role="tabpanel" aria-labelledby="formulario_103-tab">
                        Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>