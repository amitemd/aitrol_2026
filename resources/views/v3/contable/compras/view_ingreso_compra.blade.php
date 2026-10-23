<section class="content-header">
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
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ config('data.titulo') }}</h3>
                    </div>
                    <div class="card-body">
                        <form id="frm_">
                            {{ csrf_field() }}
                            <input type="hidden" name="tipoC" value="0">
                            <input type="hidden" id="d" name="d" value="|" />
                            <input type="hidden" id="id" name="id" value="" />
                            <input type="hidden" id="tipo" name="tipo" value="1">
                            <input type="hidden" id="action" value="{{ url('v3/') }}/{{ config('data.controlador') }}/saveCompra" />
                            <div class="ibox-content">
                                <div class="row hero-datoscompra">
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.fecha')}}</label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d'); ?>" />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.tipodecomprobante')}}</label>
                                        <select class="form-control select2" style="width: 100%;" id="tipo_comprobante" name="tipo_comprobante">
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
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.p.emision(empresa)')}}</label>
                                        <select class="form-control select2" style="width: 100%;" id="punto_emision" name="punto_emision">
                                            <option value="">{{trans('v3/new_tabla.--seleccione--')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.proveedor')}}</label>
                                        <select class="form-control select2_provi" style="width: 100%;" id="proveedor" name="proveedor">
                                            <option value="">{{trans('v3/new_tabla.--seleccione--')}}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.buscarporpedido')}}</label>
                                        <input type="text" class="form-control" id="pedido" name="pedido" value="" disabled />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.serie')}}</label>
                                        <input type="text" class="form-control text-right" id="serie" name="serie" maxlength="7" onkeypress="return soloNumeros(event);" placeholder={{trans('v3/new_tabla.seriedeldocumento')}} />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.secuenciafactura')}}</label>
                                        <input type="text" class="form-control text-right" id="secuencia_factura" name="secuencia_factura" onkeypress="return soloNumeros(event);" maxlength="9" placeholder={{trans('v3/new_tabla.secuenciadefactura')}} />
                                    </div>
                                </div>
                                <div class="row">
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
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.termino')}}</label>
                                        <select class="form-control select2" style="width: 100%;" id="termino" name="termino">
                                            <option value="">{{trans('v3/new_tabla.--seleccione--')}}</option>
                                            <?php
                                            if (config('data.terminos') != '' && count(config('data.terminos')) > 0) {
                                                foreach (config('data.terminos') as $value) {
                                                    echo '<option value="' . $value->id . '" >' . $value->nombre . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.fechacaducidad')}}</label>
                                        <input type="date" class="form-control" id="f_caducidad" name="f_caducidad" value="<?= date('Y-m-d'); ?>" />
                                    </div>
                                    <div class="col-md-3">
                                        <br>
                                        <center>
                                            <label class="font-normal">{{trans('v3/new_tabla.aparecearchivosri')}}<br />
                                                <div class="icheck-primary d-inline"><input type="checkbox" class="cheTransferencias" name="archivo_sri" id="archivo_sri" checked><label for="archivo_sri"></label></div>
                                            </label>
                                        </center>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="font-normal">{{trans('v3/new_tabla.autorizacion:')}}</label>
                                        <input type="text" class="form-control text-right" id="autorizacion" name="autorizacion" maxlength="49" onkeypress="return soloNumeros(event);" placeholder="{{trans('v3/new_tabla.numerodeautorizacion')}}" />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="font-normal">{{trans('v3/new_tabla.fechafacturacion')}}</label>
                                        <input type="date" class="form-control text-right" id="f_autorizacion" name="f_autorizacion" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="font-normal">{{trans('v3/new_tabla.concepto')}}</label>
                                        <input type="text" class="form-control" id="observacion" name="observacion" placeholder="{{trans('v3/new_tabla.observacionadicionaldelafactura')}}" />
                                    </div>
                                </div>
                                <div style="margin-top: 2%">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="javascript:;" id="agregarLinea" class="btn btn-danger hero-agregarlinea"><i class="fa-solid fa-circle-plus"></i> {{trans('v3/new_tabla.agregarlinea')}}</a>
                                        <!--<a href="javascript:;" id="agregarProveedor" class="btn btn-primary hero-agregarProveedor"><i class="fa-solid fa-user-plus"></i> {{trans('v3/new_tabla.agregarproveedor')}}</a>-->
                                        <a href="javascript:;" style="margin: 0px;">
                                            <label title="Archivo xml del documento a ingresar" style="margin: 0px;font-size:10px;" for="archivoXMLCompra" class="ladda-button ladda-button-xml-compra btn btn-success" data-style="zoom-in">
                                                <input type="file" name="archivoXMLCompra" id="archivoXMLCompra" style="display:none">
                                                <i class="fas fa-file-code"></i> {{trans('v3/new_tabla.subirXMLCompra')}}
                                            </label>
                                        </a>
                                    </div>
                                </div>
                                <div style="margin-top: 2%">
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered table-hover" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10%;">{{trans('v3/new_tabla.codigo')}}</th>
                                                    <th style="width: 10%;">{{trans('v3/new_tabla.producto')}}</th>
                                                    <th style="width: 10%;">{{trans('v3/new_tabla.cantidad')}}</th>
                                                    <th style="width: 10%;">{{trans('v3/new_tabla.preciounitario')}}</th>
                                                    <th style="width: 10%;">{{trans('v3/new_tabla.descuento')}}</th>
                                                    <th style="width: 10%;">{{trans('v3/new_tabla.(%)descuento')}}</th>
                                                    <th style="width: 3%;">M/C</th>
                                                    <th style="width: 10%;">Total</th>
                                                    <th style="width: 3%;">...</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detalleCompra"></tbody>
                                        </table>
                                        <table style="width: 100%;">
                                            <tr>
                                                <td style="width: 60%;" valign="top">
                                                    <a href="javascript:;" class="btn btn-danger" onclick="window.history.back();"><i class="fa-solid fa-circle-xmark"></i> Cancelar</a>
                                                    <!--<a href="javascript:;" class="btn btn-primary" id="btnagregarAdicional"><i class="fa-solid fa-plus-large"></i> Adicional</a>-->
                                                    <a href="javascript:;" class="btn btn-success" id="btnguardarCompra"><i class="fa-solid fa-save"></i> Guardar</a>
                                                </td>
                                                <td style="width: 40%;" align="right">
                                                    <table cellspacing="0">
                                                        <tr>
                                                            <td>{{trans('v3/new_tabla.subtotal')}} <?= config('data.impuestoIvaPorcentaje'); ?>%</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" id="subtotal_12" name="subtotal_12" readonly /></td>
                                                        </tr>
                                                        <tr class="m_c" style="display: none;">
                                                            <td>Subtotal M/C 5%</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" id="subtotal_mc" name="subtotal_mc" readonly /></td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{trans('v3/new_tabla.subtotal0%')}}</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" readonly id="subtotal_0" name="subtotal_0" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{trans('v3/new_tabla.descuento')}}</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" readonly id="descuento" name="descuento" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{trans('v3/new_tabla.total')}}</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" readonly id="total" name="total" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{trans('v3/new_tabla.impuesto')}} <?= config('data.impuestoIvaPorcentaje'); ?>%</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" readonly id="iva" name="iva" /></td>
                                                        </tr>
                                                        <tr class="m_c" style="display: none;">
                                                            <td>{{trans('v3/new_tabla.impuesto')}} MC/ 5%</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" readonly id="iva_mc" name="iva_mc" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{trans('v3/new_tabla.totalapagar')}}</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" readonly id="total_pagar" name="total_pagar" /></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr valign="top">
                                                <td colspan="3">
                                                    <div style="position: relative;">
                                                        <div style="position: absolute;top:-191px;">
                                                            <input type="hidden" id="contadorAdicionales" value="0">
                                                            <table width="100%" cellspacing="0" cellspacing="0">
                                                                <tr>
                                                                    <td align="left">
                                                                        <table id="tablaAdicionales" width="100%" cellspacing="0" cellspacing="0"></table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="modal_proveedores" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" id="bodyhtml">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i> {{trans('v3/new_varios.cargando')}}...</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card card-default">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-content-above-general-tab" data-toggle="pill" href="#custom-content-above-general" role="tab" aria-controls="custom-content-above-general" aria-selected="true"><i class="fas fa-user"></i> <span class="botones">{{trans('v3/new_tabla.general')}}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-content-above-direccion-tab" data-toggle="pill" href="#custom-content-above-direccion" role="tab" aria-controls="custom-content-above-direccion" aria-selected="false"><i class="fas fa-map-marked-alt"></i> <span class="botones"> {{trans('v3/new_varios.ubicacion')}}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-content-above-bank-tab" data-toggle="pill" href="#custom-content-above-bank" role="tab" aria-controls="custom-content-above-bank" aria-selected="false"><i class="fas fa-bank"></i> <span class="botones"> {{trans('v3/new_varios.infoBancaria')}}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-content-above-otros-tab" data-toggle="pill" href="#custom-content-above-otros" role="tab" aria-controls="custom-content-above-otros" aria-selected="false"><i class="fa fa-files-o"></i> <span class="botones">{{trans('v3/new_varios.documentosOtros')}}</span></a>
                                </li>
                            </ul>
                            <form id="provee_frm">
                                {{csrf_field()}}
                                <input type="hidden" id="actionSaveProveedor" value="{{ url('v3/') }}/proveedores/saveProveedor" />
                                <input type="hidden" id="d" name="d" value="|">
                                <input type="hidden" id="estado" name="estado">
                                <input type="hidden" id="ciudad" name="ciudad">
                                <div class="tab-content" id="custom-content-above-tabContent">
                                    <div class="tab-pane fade show active" id="custom-content-above-general" role="tabpanel" aria-labelledby="custom-content-above-general-tab">
                                        @include('v3.contable.proveedores.view_general')
                                    </div>
                                    <div class="tab-pane fade" id="custom-content-above-direccion" role="tabpanel" aria-labelledby="custom-content-above-direccion-tab">
                                        @include('v3.contable.proveedores.view_ubicacion')
                                    </div>
                                    <div class="tab-pane fade" id="custom-content-above-bank" role="tabpanel" aria-labelledby="custom-content-above-bank-tab">
                                        @include('v3.contable.proveedores.view_bancarios')
                                    </div>
                                    <div class="tab-pane fade" id="custom-content-above-otros" role="tabpanel" aria-labelledby="custom-content-above-otros-tab">
                                        @include('v3.contable.proveedores.view_otros')
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> {{trans('v3/new_varios.cerrar')}}</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarProveedor"><i class="fas fa-save"></i> {{trans('v3/new_varios.guardar')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_pedidos" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalPe" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" id="bodyhtml">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="titulo_modal_ped"><i class="fas fa-edit"></i> {{trans('v3/new_varios.cargando')}}...</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card card-default table-responsive">
                        <table id="table_data_pedido" class="table table-bordered table-hover responsive" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{trans('v3/new_tabla.fecha')}}</th>
                                    <th>Proveedor</th>
                                    <th>{{trans('v3/new_tabla.numerodepedido')}}</th>
                                    <th>{{trans('v3/new_tabla.numerodefactura')}}</th>
                                    <th>{{trans('v3/new_tabla.realizadopor')}}</th>
                                    <th>{{trans('v3/new_tabla.tipo')}}</th>
                                    <th>{{trans('v3/new_tabla.accion')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <th>{{trans('v3/new_tabla.fecha')}}</th>
                                <th>Proveedor</th>
                                <th>{{trans('v3/new_tabla.numerodepedido')}}</th>
                                <th>{{trans('v3/new_tabla.numerodefactura')}}</th>
                                <th>{{trans('v3/new_tabla.realizadopor')}}</th>
                                <th>{{trans('v3/new_tabla.tipo')}}</th>
                                <th>{{trans('v3/new_tabla.accion')}}</th>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> {{trans('v3/new_varios.cerrar')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>