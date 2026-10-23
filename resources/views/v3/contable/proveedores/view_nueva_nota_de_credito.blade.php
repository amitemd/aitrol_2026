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
                                                <label class="font-normal">Buscar factura</label>
                                                <select class="select2 factura form-control" style="width: 100%;" id="facturas" name="facturas" autocomplete="off" onchange="obtenerFactura()">
                                                    <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">Datos de la factura</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <input type="hidden" id="subtotal" name="subtotal" />
                                                <input type="hidden" id="iva_total" name="iva_total" />
                                                <input type="hidden" id="tipo" name="tipo" />
                                                <input type="hidden" id="numero" name="numero" />
                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <label class="font-normal">ID</label>
                                                        <input type="text" class="form-control text-right" id="id_factura" name="id_factura" placeholder="Id documento" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Fecha</label>
                                                        <input type="text" class="form-control text-right" id="fecha" name="fecha" placeholder="dd/mm/aaaa" readonly />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">Tipo comprobante</label>
                                                        <input type="text" class="form-control" id="tipo_comprobante" name='tipo_comprobante' placeholder="Tipo del comprobante" readonly />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">Serie</label>
                                                        <input type="text" class="form-control text-right" id="serie" name="serie" placeholder="000-000" readonly />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">Secuencial</label>
                                                        <input type="text" class="form-control text-right" id="secuencial" placeholder="000000000" readonly />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="font-normal">Proveedor</label>
                                                        <input type="hidden" id="id_proveedor_" name="id_proveedor_" />
                                                        <input type="text" class="form-control" id="proveedor" placeholder="Proveedor" readonly />
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="font-normal">Concepto</label>
                                                        <input type="text" class="form-control" id="observacion" placeholder="Concepto" readonly />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="font-normal">Autorización</label>
                                                        <input type="text" class="form-control text-right" id="autorizacion" name="autorizacion" placeholder="# autorización" readonly />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Valor Factura</label>
                                                        <input type="text" class="form-control text-right" id="total_final" name="total_final" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Saldo Contable</label>
                                                        <input type="text" class="form-control text-right" id="valor_contable" name="valor_contable" readonly />
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
                                                <h3 class="card-title">Datos de la Nota de Crédito</h3>
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
                                                        <input type="text" class="form-control text-right"
                                                            id="establecimiento" name="establecimiento" maxlength="3"
                                                            onkeypress="return soloNumeros(event);"
                                                            placeholder="000" />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Emisión</label>
                                                        <input type="text" class="form-control text-right"
                                                            id="emision" name="emision" maxlength="3"
                                                            onkeypress="return soloNumeros(event);"
                                                            placeholder="000" />
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
                                                            <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>
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
                                                            <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">Detalles</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <table class="table table-striped table-bordered table-hover dataTables-example tablaDatos" style="width: 100%;" style="width: 100%;" id="tabla_compra">
                                                        <thead>
                                                            <tr>
                                                                <th>Sel.</th>
                                                                <th>Codigo</th>
                                                                <th>Producto</th>
                                                                <th>Cantidad</th>
                                                                <th>Precio</th>
                                                                <th>Descuento</th>
                                                                <th>Impuesto</th>
                                                                <th>Iva</th>
                                                                <th>Total</th>
                                                                <th>Abono</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="detalle_compra"></tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>&nbsp;</th>
                                                                <th></th>
                                                                <th>&nbsp;</th>
                                                                <th>&nbsp;</th>
                                                                <th>&nbsp;</th>
                                                                <th>Subtotal 0</th>
                                                                <th>Subtotal <?= config('data.impuestoIvaPorcentaje'); ?></th>
                                                                <th>Subtotal</th>
                                                                <th>Impuesto</th>
                                                                <th>Total</th>
                                                            </tr>
                                                            <tr>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td>
                                                                    <input type="text" class="form-control text-right" id="subtotal0" name="subtotal0" value="0.00" readonly />
                                                                    <input type="hidden" class="form-control text-right" id="a_detalles" name="a_detalles" />
                                                                    <input type="hidden" class="form-control text-right" id="a_row" name="a_row" value="0.00" />
                                                                </td>
                                                                <td><input type="text" class="form-control text-right" id="subtotal12" name="subtotal12" value="0.00" readonly /></td>
                                                                <td><input type="text" class="form-control text-right" id="subtotal_total" name="subtotal_total" value="0.00" readonly /></td>
                                                                <td><input type="text" class="form-control text-right" id="impuesto" name="impuesto" value="0.00" readonly /></td>
                                                                <td><input type="text" class="form-control text-right" id="total" name="total" value="0.00" readonly /></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div style='text-align: center; margin-bottom: 2%'>
                                            <button type="button" class="btn btn-danger" onclick="regresar('/v3/proveedores/notadecredito')"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Regresar</button>
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