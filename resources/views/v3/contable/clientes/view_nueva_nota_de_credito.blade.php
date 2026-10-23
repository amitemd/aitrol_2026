<style>
    input[type=checkbox] {
        transform: scale(1.5);
    }

    .ocultos {
        display: none;
        width: 90%;
    }

    .ocultosp {
        width: 90%;
    }
</style>
<section class="content-header ibox float-e-margins">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ ucwords(strtolower(trans('v3/new_submodulo.'.config('data.submodulo')))) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">{{ ucwords(strtolower(config('data.submodulo'))) }}</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-xxl">
        <form id="frm_notacreditocliente">
            {{ csrf_field() }}
            <input type="hidden" name="tipoC" value="0">
            <input type="hidden" id="d" name="d" value="|" />
            <input type="hidden" id="id" name="id" value="" />
            <input type="hidden" id="tipo" name="tipo" value="1">
            <input type="hidden" id="action" value="{{ url('v3/') }}/{{ config('data.controlador') }}/guardarNotaCreditoCliente" />
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ config('data.titulo') }}</h3>
                            <div>
                                <div style="position: absolute; right: 5px;top:7px">
                                    SRI <input type="checkbox" name="sri_v" id="sri_v" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="ibox-content">


                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Datos</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row hero-cliente">
                                            <div class="col-md-2">
                                                <label class="font-normal">{{trans('v3/new_tabla.sucursal(empresa)')}}</label>
                                                <select class="form-control select2" style="width: 100%;" id="sucursal" name="sucursal" onchange="obtener_caja()">
                                                    <option value="">{{trans('v3/new_tabla.--seleccione--')}}</option>
                                                    <?php
                                                    if (config('data.sucursales') != '' && count(config('data.sucursales')) > 0) {
                                                        foreach (config('data.sucursales') as $value) {
                                                            echo '<option value="' . $value->id . '" >' . $value->codigo_sucursal . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="font-normal">{{trans('v3/new_tabla.p.emision(empresa)')}}</label>
                                                <select class="form-control select2" style="width: 100%;" id="punto_emision" name="punto_emision">
                                                    <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="font-normal">{{trans('v3/new_tabla.fecha')}}</label>
                                                <input type="date" name="fecha" id="fecha" class="form-control" value="<?php echo date("Y-m-d") ?>">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="font-normal">{{trans('v3/new_tabla.concepto')}}</label>
                                                <input type="text" class="form-control text-left" id="concepto" name="concepto" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Datos Facturas</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row hero-cliente">
                                            <div class="col-md-4">
                                                <label class="font-normal">Ambiente</label>
                                                <input type="text" class="form-control" value="PRODUCCIÓN" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="font-normal">Cliente</label>
                                                <select id="js-data-nombre-cliente" name="cliente" class="js-data-cliente form-control" style="width: 100%;" autocomplete="off">
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="font-normal">Facturas</label>
                                                <select name="facturas" id="facturas" class="form-control select2" onchange="cargarTablaCliente()">
                                                    <option value="">Seleccione</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-top: 2%">
                                </div>

                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Totales</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row hero-cliente">
                                            <div class="col-md-3">
                                                <label class="font-normal">Total Factura</label>
                                                <input type="text" class="form-control" name="total" id="total_final" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="font-normal">Saldo Contable</label>
                                                <input type="text" class="form-control" name="valor_contable" id="valor_contable" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-top: 2%">
                                </div>

                                <div class="col-md-12" style="top: 20px;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="detalle_deuda">DETALLE DE PRODUCTOS</label>
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-top: 2%">
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="tablaNotaCredito" class="table table-bordered table-hover" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 20%;">{{trans('v3/new_tabla.codigo')}}</th>
                                                    <th style="width: 20%;">{{trans('v3/new_tabla.cantidad')}}</th>
                                                    <th style="width: 20%;">{{trans('v3/new_tabla.nombre')}}</th>
                                                    <th style="width: 20%;">{{trans('v3/new_tabla.valor')}}</th>
                                                    <th style="width: 10%;">{{trans('v3/new_tabla.iva')}}</th>
                                                    <th style="width: 10%;">{{trans('v3/new_tabla.accion')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detalleNotaCredito"></tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-md-9">
                                    </div>
                                    <div class="col-md-3">
                                        <table class="table table-bordered">
                                            <tr>
                                                <td style="width: 60%;">subtotal12 %</td>
                                                <td><input name="subtotal121" class="form-control" id="subtotal12" type="text" readonly value="0.00"></td>
                                            </tr>
                                            <tr>
                                                <td>subtotal0 %</td>
                                                <td><input name="subtotal01" class="form-control" id="subtotal0" type="text" readonly value="0.00"></td>
                                            </tr>

                                            <tr>
                                                <td>descuentos</td>
                                                <td><input name="descuento1" class="form-control" id="descuento" type="text" readonly value="0.00"></td>
                                            </tr>

                                            <tr>
                                                <td>subtotal</td>
                                                <td><input name="subtotal1" class="form-control" id="subtotal" type="text" readonly value="0.00"></td>
                                            </tr>

                                            <tr>
                                                <td>impuesto</td>
                                                <td><input name="impuesto1" class="form-control" id="impuesto" type="text" readonly value="0.00"></td>
                                            </tr>

                                            <tr>
                                                <td>total</td>
                                                <td><input name="total1" class="form-control" id="total" type="text" readonly value="0.00"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="font-normal">Observaciones</label>
                                        <input type="text" class="form-control" name="observaciones" id="observaciones" style="height: 65px;">
                                    </div>
                                </div>
                                <div style="display: flex;justify-content:center;gap:10px;margin-top:2%">
                                    <button type="button" class="btn btn-danger" onclick="history.back()">
                                        <i class="fa fa-long-arrow-left" aria-hidden="true"></i> {{trans('v3/new_tabla.regresar')}}
                                    </button>

                                    <button type="button" class="btn btn-success" onclick="guardarNotaCreditoCliente()">
                                        <i class="fa fa-floppy-o" aria-hidden="true"></i> {{trans('v3/new_tabla.guardar')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>