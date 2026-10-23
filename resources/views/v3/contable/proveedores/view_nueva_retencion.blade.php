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
                <h1>{{ trans('v3/new_submodulo.'.config('data.submodulo')) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">{{ trans('v3/new_submodulo.'.config('data.submodulo')) }}</li>
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
                                <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveRetencionProveedor" />
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
                                                <select id="facturas" class="select2_ajax form-control" style="width: 100%;" name="facturas" autocomplete="off" onchange="obtenerFactura()">
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
                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <label class="font-normal">ID</label>
                                                        <input type="text" class="form-control text-right" id="id_factura" name="id_factura" placeholder="Id documento" readonly />
                                                    </div>
                                                    <div class="col-md-1">
                                                        <label class="font-normal">Fecha</label>
                                                        <input type="text" class="form-control text-right" id="fecha" placeholder="dd/mm/aaaa" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Tipo comprobante</label>
                                                        <input type="text" class="form-control" id="tipo_comprobante" placeholder="Tipo del comprobante" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Serie</label>
                                                        <input type="text" class="form-control text-right" id="serie" placeholder="000-000" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Secuencial</label>
                                                        <input type="text" class="form-control text-right" id="secuencial" placeholder="000000000" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Valor Base</label>
                                                        <input type="text" class="form-control text-right" id="valorBase" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Iva</label>
                                                        <input type="text" class="form-control text-right" id="ivaBase" readonly />
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
                                                        <input type="text" class="form-control text-right" id="autorizacion" placeholder="# autorización" readonly />
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
                                                <h3 class="card-title">Datos de la retención</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Establecimiento</label>
                                                        <select class="form-control select2" style="width: 100%;" id="establecimiento" name="establecimiento" onchange="obtener_caja()">
                                                            <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>
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
                                                        <label class="font-normal">Emisión</label>
                                                        <select class="form-control select2" style="width: 100%;" id="emision" name="emision">
                                                            <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 quitar">
                                                        <label class="font-normal">Secuencial</label>
                                                        <input type="text" class="form-control text-right" id="secuencial_retencion" name="secuencial_retencion" placeholder="0000000000" readonly />
                                                    </div>
                                                    <div class="col-md-5 quitar">
                                                        <label class="font-normal">Clave de acceso</label>
                                                        <input type="text" class="form-control text-right" id="serie" placeholder="# clave de acceso" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">Fecha emisión</label>
                                                        <input type="date" class="form-control text-right" name="fecha_retencion" id="fecha_retencion" onblur="validarCierre(this);" min="<?= config('data.fechaCierreContable') !== null ? date('Y-m-d', strtotime(config('data.fechaCierreContable') . '+1 day')) : ''; ?>" value="<?= date('Y-m-d'); ?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="font-normal">Concepto</label>
                                                        <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="Concepto" />
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
                                                <h3 class="card-title">Impuestos</h3>
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
                                                                <th>Tipo de impuesto</th>
                                                                <th>Nombre</th>
                                                                <th>Porcentaje</th>
                                                                <th>Base</th>
                                                                <th>Valor</th>
                                                                <th><a href="javascript:;" class="btn btn-primary" onclick="agregarImpuesto();"><i class="fa-solid fa-plus"></i></a></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="impuestos"></tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>&nbsp;</th>
                                                                <th>&nbsp;</th>
                                                                <th>Renta</th>
                                                                <th>Iva</th>
                                                                <th>Total</th>
                                                                <th>&nbsp;</th>
                                                            </tr>
                                                            <tr>
                                                                <td>&nbsp;</td>
                                                                <td>&nbsp;</td>
                                                                <td><input type="text" class="form-control text-right" id="renta" name="renta" value="0.00" readonly /></td>
                                                                <td><input type="text" class="form-control text-right" id="iva" name="iva" value="0.00" readonly /></td>
                                                                <td><input type="text" class="form-control text-right" id="total" name="total" value="0.00" readonly /></td>
                                                                <td>&nbsp;</td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="display:flex;align-items:center;justify-content:center">
                                            <a href="javascript:;" class="btn btn-danger" onclick="goBack('retenciones');"><i class="fa-solid fa-circle-xmark"></i> Cancelar</a>
                                            <a href="javascript:;" class="btn btn-success" id="btnguardarRetencion"><i class="fa-solid fa-save"></i> Guardar</a>
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