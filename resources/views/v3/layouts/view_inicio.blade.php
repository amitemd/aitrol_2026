<section>
    <input type="hidden" id="chart" value="{{ config('data.chart') }}">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ventas - {{session('periodo')}}</h3>
                <div class="card-tools"><button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-<?= config('data.chart') == '' ? 'plus' : 'minus' ?>"></i></button><button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button></div>
            </div>
            <div class="card-body">
                <div class="chart" id="chartVentas"><canvas id="lineVentas" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas></div>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Tabla de Ventas -->
            <div class="col-md-6">
                <div class="card-header bg-success text-white">
                    <strong>Ventas</strong>
                </div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Documento</th>
                            <th>Sri</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $estadoSri = '';
                        if (config('data.ventas') !== null) {
                            foreach (config('data.ventas') as $venta) {

                        ?>
                                <tr>
                                    <td><?= date('d-m-Y', strtotime($venta->fecha)); ?></td>
                                    <td><?= substr($venta->nombre_cliente, 0, 30); ?></td>
                                    <td><?= $venta->nro_comprobante ?></td>
                                    <td>
                                        @if ($venta->doc_electronico == 0)
                                        <i class="fas fa-exclamation-circle text-warning" title="Pendiente de enviar"></i>
                                        @elseif ($venta->doc_electronico == 5)
                                        <i class="fas fa-check-circle text-success" title="Autorizado"></i>
                                        @else
                                        <i class="fas fa-times-circle text-danger" title="Error o rechazado"></i>
                                        @endif
                                    </td>
                                    <td><?= $venta->total_final ?></td>
                                    <td>
                                        @if ($venta->valor_contable>0)
                                        <i class="fas fa-clock text-warning" title="Pendiente de pago"></i>
                                        @else
                                        <i class="fas fa-money-bill-wave text-success" title="Pagado"></i>
                                        @endif
                                    </td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Tabla de Compras -->
            <div class="col-md-6">
                <div class="card-header bg-primary text-white">
                    <strong>Compras</strong>
                </div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $estadoSri = '';
                        if (config('data.compras') !== null) {
                            foreach (config('data.compras') as $compra) {

                        ?>
                                <tr>
                                    <td><?= date('d-m-Y', strtotime($compra->fecha)); ?></td>
                                    <td><?= substr($compra->proveedor, 0, 30); ?></td>
                                    <td><?= $compra->numero ?></td>
                                    <td><?= $compra->total_final ?></td>
                                    <td>
                                        @if ($compra->valor_contable>0)
                                        <i class="fas fa-clock text-warning" title="Pendiente de pago"></i>
                                        @else
                                        <i class="fas fa-money-bill-wave text-success" title="Pagado"></i>
                                        @endif
                                    </td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Últimos Pagos -->
            <div class="col-md-4">
                <div class="card-header bg-success text-white">
                    <strong>Últimos Pagos</strong>
                </div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#1001</td>
                            <td>Juan Pérez</td>
                            <td><i class="fas fa-money-bill-wave text-success" title="Pagado"></i></td>
                        </tr>
                        <tr>
                            <td>#1002</td>
                            <td>María López</td>
                            <td><i class="fas fa-clock text-warning" title="Pendiente de pago"></i></td>
                        </tr>
                        <tr>
                            <td>#1003</td>
                            <td>Carlos Jiménez</td>
                            <td><i class="fas fa-money-bill-wave text-success" title="Pagado"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Últimos Proveedores -->
            <div class="col-md-4">
                <div class="card-header bg-primary text-white">
                    <strong>Últimos Proveedores</strong>
                </div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Distribuidora Eléctrica S.A.</td>
                            <td>01-04-2025</td>
                        </tr>
                        <tr>
                            <td>Alimentos del Valle</td>
                            <td>29-03-2025</td>
                        </tr>
                        <tr>
                            <td>Imprenta Rápida</td>
                            <td>27-03-2025</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Últimos Productos -->
            <div class="col-md-4">
                <div class="card-header bg-secondary text-white">
                    <strong>Últimos Productos</strong>
                </div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Fecha Creación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Tóner HP 17A</td>
                            <td>05-04-2025</td>
                        </tr>
                        <tr>
                            <td>Papel Bond Carta</td>
                            <td>04-04-2025</td>
                        </tr>
                        <tr>
                            <td>Kit de Seguridad</td>
                            <td>02-04-2025</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>