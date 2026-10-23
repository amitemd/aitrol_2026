<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ ucwords(strtolower(trans('v3/new_submodulo.'.config('data.submodulo')))) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">{{ ucwords(strtolower(trans('v3/new_submodulo.'.config('data.submodulo')))) }}</li>
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
                            <input type="hidden" id="d" name="d" value="|" />
                            <input type="hidden" id="id" name="id" value="" />
                            <input type="hidden" id="action" value="{{ url('v3/') }}/{{ config('data.controlador') }}/saveFactura" />
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
                                            <label class="font-normal">{{trans('v3/new_tabla.ordenimportacion')}}</label>
                                            <div class="input-group mb-3">
                                                <select class="form-control select2 select2_cuentas select2-hidden-accessible" name="id_proveedor" id="selecOrden" style="width: 100%;" onchange="selecFactura()">
                                                    <option value="">{{trans('v3/new_varios.seleccionar')}}</option>
                                                    <?php
                                                    if (config('data.cab')->cruce != '' && count(config('data.cab')->cruce) > 0) {
                                                        foreach (config('data.cab')->cruce as $row) {
                                                            foreach ($row->compra->detalles as $det) {
                                                                echo '<option data-id_estado="' . $row->id . '" data-proveedor="' . $row->compra->proveedor . '" data-gasto="' . $det->id_gasto . '" data-precio="' . $det->precio . '" data-fecha="' . $row->compra->fecha . '" data-descuento="' . $det->descuento . '" data-total="' . $det->total . '" value="' . $row->compra->id . '">' . $row->compra->proveedor_da->nombrecomercial . ' | ' . $cab->observacion . '</option>';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ibox-content">
                                <div class="row hero-datoscompra">
                                    <div class="col-md-2">
                                        <label class="font-normal">{{trans('v3/new_tabla.fecha')}}</label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d'); ?>" />
                                    </div>
                                    <div class="col-md-1">
                                        <label class="font-normal">Tipo</label>
                                        <input type="text" class="form-control" id="tipo" name="tipo" value="FACT-IMP" readonly />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="font-normal">{{trans('v3/new_tabla.sucursal(empresa)')}}</label>
                                        <select class="form-control select2" style="width: 100%;" id="establecimiento" name="sucursal" onchange="obtener_caja()">
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
                                        <select class="form-control select2" style="width: 100%;" id="emision" name="punto_emision">
                                            <option value="">{{trans('v3/new_tabla.--seleccione--')}}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="font-normal">Id</label>
                                        <input type="text" class="form-control" id="id" name="id" value="" readonly />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="font-normal">Asiento</label>
                                        <input type="text" class="form-control" id="id_asiento" name="id_asiento" value="" readonly />
                                    </div>
                                    <div class="col-md-2">
                                        <label for="archivo_sri" class="font-normal">{{trans('v3/new_tabla.aparecearchivosri')}}
                                        </label>
                                        <div class="form-group clearfix">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="archivo_sri"checked>
                                                <label for="archivo_sri">
                                                </label>
                                            </div>
                                            <input type="hidden" id="archivosri" name="archivosri" value="1" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.proveedor')}}</label>
                                        <select class="form-control select2_provi" style="width: 100%;" id="proveedor" name="proveedor">
                                            <option value="">{{trans('v3/new_tabla.--seleccione--')}}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="font-normal">{{trans('v3/new_tabla.serie')}}</label>
                                        <input type="text" class="form-control text-right" id="serie" name="serie" maxlength="7" onkeypress="return soloNumeros(event);" placeholder={{trans('v3/new_tabla.seriedeldocumento')}} />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="font-normal">{{trans('v3/new_tabla.secuenciafactura')}}</label>
                                        <input type="text" class="form-control text-right" id="secuencia_factura" name="secuencia_factura" onkeypress="return soloNumeros(event);" maxlength="9" placeholder={{trans('v3/new_tabla.secuenciadefactura')}} />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="font-normal">{{trans('v3/new_tabla.autorizacion:')}}</label>
                                        <input type="text" class="form-control text-right" id="autorizacion" name="autorizacion" maxlength="49" onkeypress="return soloNumeros(event);" placeholder="{{trans('v3/new_tabla.numerodeautorizacion')}}" />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="font-normal">{{trans('v3/new_tabla.fechafacturacion')}}</label>
                                        <input type="date" class="form-control text-right" id="f_autorizacion" name="f_autorizacion" value="<?= date('Y-m-d'); ?>" />
                                    </div>
                                </div>
                                <div class="row">
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
                                    <div class="col-md-2">
                                        <label class="font-normal">{{trans('v3/new_tabla.termino')}}</label>
                                        <select class="form-control select2" style="width: 100%;" id="termino" name="termino">
                                            <option value="">{{trans('v3/new_tabla.--seleccione--')}}</option>
                                            <?php
                                            if (config('data.terminos') != '' && count(config('data.terminos')) > 0) {
                                                foreach (config('data.terminos') as $value) {
                                                    echo '<option value="' . $value->dias . '" >' . $value->nombre . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="font-normal">{{trans('v3/new_tabla.fechacaducidad')}}</label>
                                        <input type="date" class="form-control" id="f_caducidad" name="f_caducidad" value="<?= date('Y-m-d'); ?>" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="font-normal">{{trans('v3/new_tabla.concepto')}}</label>
                                        <input type="text" class="form-control" id="observacion" name="observacion" placeholder="{{trans('v3/new_tabla.observacionadicionaldelafactura')}}" />
                                    </div>
                                </div>
                                <div style="margin-top: 2%">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="javascript:;" id="agregarProveedor" class="btn btn-primary hero-agregarProveedor"><i class="fa-solid fa-user-plus"></i> {{trans('v3/new_tabla.agregarproveedor')}}</a>
                                    </div>
                                </div>
                                <div style="margin-top: 2%">
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered table-hover" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 18%;">{{trans('v3/new_tabla.producto')}}</th>
                                                    <th style="width: 8%;">{{trans('v3/new_tabla.cantidad')}}</th>
                                                    <th style="width: 8%;">{{trans('v3/new_tabla.preciounitario')}}</th>
                                                    <th style="width: 8%;">{{trans('v3/new_tabla.descuento')}}</th>
                                                    <th style="width: 8%;">{{trans('v3/new_tabla.(%)descuento')}}</th>
                                                    <th style="width: 8%;">Total</th>
                                                    <th style="text-align: center;width: 5%;">{{trans('v3/new_tabla.iva')}}</th>
                                                    <th style="text-align: center;width: 5%;">
                                                        <button type="button" id="agregarLinea" class="btn"><i class="fas fa-plus-square fa-2x" style="cursor: pointer;color:green;"></i></button>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="detalleCompra"></tbody>
                                        </table>
                                        <table style="width: 100%;">
                                            <tr>
                                                <td style="width: 60%;" valign="top">
                                                    <a href="javascript:;" class="btn btn-danger" onclick="window.history.back();"><i class="fa-solid fa-circle-xmark"></i>{{trans('v3/new_tabla.cancelar')}}</a>
                                                    <a href="javascript:;" class="btn btn-primary" id="btnagregarAdicional"><i class="fa-solid fa-plus-large"></i>{{trans('v3/new_tabla.adicional')}}</a>
                                                    <a href="javascript:;" class="btn btn-success" id="btnguardarFactura"><i class="fa-solid fa-save"></i>{{trans('v3/new_tabla.guardar')}}</a>
                                                </td>
                                                <td style="width: 40%;" align="right">
                                                    <table cellspacing="0">
                                                        <tr>
                                                            <td>{{trans('v3/new_tabla.subtotal')}} <?= config('data.impuestoIvaPorcentaje'); ?>%</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" id="subtotal_12" name="subtotal_12" readonly /></td>
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
                                                            <td>{{trans('v3/new_tabla.impuesto')}}<?= config('data.impuestoIvaPorcentaje'); ?>%</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" readonly id="iva" name="iva" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{trans('v3/new_tabla.totalapagar')}}</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" readonly id="total_pagar" /></td>
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
                                    <th>{{trans('v3/new_tabla.proveedor')}}</th>
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
                                <th>{{trans('v3/new_tabla.proveedor')}}</th>
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