<style>
    .quitar {
        display: none;
    }
</style>
<div class="modal fade" id="modal_clientes" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" id="bodyhtml"></div>
</div>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ ucwords(strtolower(config('data.submodulo'))) }}</h1>
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
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ config('data.titulo') }}</h3>
                    </div>
                    <div class="card-body hero-factura">
                        <div class="ibox-content">
                            <form id="frm_">
                                {{ csrf_field() }}
                                <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveNotaCreditoProveedores" />
                                <input type="hidden" id="d" name="d" value="|" />
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">Buscador</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="card-body">
                                                <label class="font-normal">{{trans('v3/new_tabla.proveedor')}}</label>
                                                <div class="input-group mb-3">
                                                    <select class="form-control select2" id="id_proveedor" name="id_proveedor" id="id_proveedor" style="width: 100%;" onchange="obtenerFacturaProveedor()">
                                                        <option value="">{{trans('v3/new_varios.seleccione')}}</option>
                                                        <?php
                                                            if (config('data.id_proveedor') != '' && count(config('data.id_proveedor')) > 0) {
                                                                foreach (config('data.id_proveedor') as $row) {
                                                                    echo '<option value="' . $row->id . '">' . $row->id . ' - ' . $row->razonsocial . ' | ' . $row->direccion . '</option>';
                                                                }
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">Datos de la Nota de Débito</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="font-normal">Fecha</label>
                                                        <input type="date" class="form-control" id="fecha_nota" name="fecha_nota" placeholder="dd/mm/aaaa" value="<?= date('Y-m-d') ?>" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">Fecha de Caducidad</label>
                                                        <input type="date" class="form-control" id="fecha_caducidad" name="fecha_caducidad" placeholder="dd/mm/aaaa" value="<?= date('Y-m-d') ?>" />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Establecimiento</label>
                                                        <select class="form-control select2" style="width: 100%;" id="establecimiento" name="establecimiento" onchange="obtener_caja()">
                                                            <option value="">--Seleccione--</option>
                                                            <?php
                                                            $sucursales = config('data.sucursales');
                                                            if (!empty($sucursales) && count($sucursales) > 0) {
                                                                if (count($sucursales) === 1) {
                                                                    $sucursalUnica = $sucursales[0];
                                                                    echo "<option value='" . $sucursalUnica->id . "' selected>" . $sucursalUnica->codigo_sucursal . "</option>";
                                                                } else {
                                                                    foreach ($sucursales as $suc) {
                                                                        echo '<option value="' . $suc->id . ' " >' . $suc->codigo_sucursal . '</option>';
                                                                    }
                                                                }
                                                            }
                                                            ?>

                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Emisión</label>
                                                        <select class="form-control select2" style="width: 100%;" id="emision" name="emision">
                                                            <option value="">--Seleccione--</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Secuencial</label>
                                                        <input type="text" class="form-control text-right" id="secuencia" name="secuencia" placeholder="0000000000" />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="font-normal">Clave de acceso</label>
                                                        <input type="text" class="form-control text-right" id="autorizacion_nota" name="autorizacion_nota" placeholder="# clave de acceso" maxlength="49" />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Fecha NC</label>
                                                        <input type="date" class="form-control" id="fechand" name="fechand" placeholder="dd/mm/aaaa" value="<?= date('Y-m-d') ?>" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_tabla.tipodecomprobante')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="tipo_comprobante_nota" name="tipo_comprobante_nota">
                                                            <option value="">{{trans('v3/new_tabla.--seleccione--')}}</option>
                                                            <?php

                                                            if (config('data.tipoComprobante') != '' && count(config('data.tipoComprobante')) > 0) {
                                                                foreach (config('data.tipoComprobante') as $value) {
                                                                    echo '<option value="' . $value->codigo . '" >' . $value->codigo . ' | ' .  $value->nombre . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_tabla.creditotributario')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="credito_tributario" name="credito_tributario">
                                                            <option value="">{{trans('v3/new_tabla.--seleccione--')}}</option>
                                                            <?php
                                                            if (config('data.c_tributario') != '' && count(config('data.c_tributario')) > 0) {
                                                                foreach (config('data.c_tributario') as $value) {
                                                                    echo '<option value="' . $value->codigo . '" >' . $value->codigo . ' | ' . $value->nombre . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="font-normal">Concepto</label>
                                                        <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="Concepto" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="font-normal">Nota</label>
                                                        <input type="text" class="form-control" id="nota" name="nota" placeholder="Nota" />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Fecha Fact.</label>
                                                        <input type="date" class="form-control" id="fecha_factura" name="fecha_factura" placeholder="dd/mm/aaaa" value="<?= date('Y-m-d') ?>" />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Serie Fact.</label>
                                                        <input type="text" class="form-control text-right" id="serie_factura" name="serie_factura" maxlength="7" onkeypress="return soloNumeros(event);" placeholder="000-000" />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="font-normal">Autorización Fact.</label>
                                                        <input type="text" class="form-control text-right" id="autorizacion_factura" name="autorizacion_factura" placeholder="# clave de acceso" maxlength="49" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">Detalle de Rubros</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <table style="width: 100%;">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 22%;">Rubro</th>
                                                                <th style="width: 3%;">Iva</th>
                                                                <th style="width: 25%;">Detalle</th>
                                                                <th style="width: 15%;">Divisa</th>
                                                                <th style="width: 15%;">Valor</th>
                                                                <th style="width: 15%;">Total Base</th>
                                                                <th style="width: 5%;"><a href="javascript:;" class="btn btn-primary"
                                                                        onclick="agregarRubrosNd();"><i
                                                                            class="fa-solid fa-plus"></i></a></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="rubros"></tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>&nbsp;</th>
                                                                <th>&nbsp;</th>
                                                                <th>&nbsp;</th>
                                                                <th>Subtotal</th>
                                                                <th>Impuesto</th>
                                                                <th>Total</th>
                                                                <th>&nbsp;</th>
                                                            </tr>
                                                            <tr>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td><input type="hidden" id="impuestoIva" value="{{config('data.impuestoIva')}}" />
                                                                <input type="text"
                                                                        class="form-control text-right" id="subtotalrubro"
                                                                        name="subtotalrubro" value="0.00" readonly /></td>
                                                                <td><input type="text"
                                                                        class="form-control text-right" id="impuestorubro"
                                                                        name="impuestorubro" value="0.00" readonly /></td>
                                                                <td><input type="text"
                                                                        class="form-control text-right" id="sumtotalrubro"
                                                                        name="sumtotalrubro" value="0.00" readonly /></td>
                                                                <td>&nbsp;</td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">Listado de Facturas</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <table  class="table table-striped table-bordered table-hover dataTables-example tablaDatos" style="width: 100%;" id="tabla_facturas">
                                                        <thead>
                                                            <tr style="background-color: #F1F1F1">
                                                                <th style="text-align: center;">Vence</th>
                                                                <th style="text-align: center;">Tipo</th>
                                                                <th style="text-align: center;">Numero</th>
                                                                <th style="text-align: center;">Concepto</th>
                                                                <th style="text-align: center;">Div</th>
                                                                <th style="text-align: center;">Saldo</th>
                                                                <th style="text-align: center;">Abono</th>
                                                                <th style="text-align: center;">Saldo Base</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="consolidado_facturas"></tbody>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div style='text-align: center; margin-bottom: 2%'>
                                            <button type="button" class="btn btn-danger" onclick="regresar('/v3/proveedores/notadecredito')">
                                                <i class="fa fa-long-arrow-left" aria-hidden="true"></i> Regresar
                                            </button>
                                            <a href="javascript:;" class="btn btn-success" id="btnguardarNotaCredito"><i class="fa-solid fa-save"></i> Guardar</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>