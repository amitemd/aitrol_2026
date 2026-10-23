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
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">{{trans('v3/new_tabla.inicio')}}</a></li>
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
                    <form id="frm_">
                        <div class="card-header">
                            <h3 class="card-title">{{ config('data.titulo') }}</h3>
                            <div>
                                <div style="position: absolute; right: 5px;top:7px">
                                    <a href="javascript:;" class="btn btn-success btn-sm" onclick="agregarRetencion();"><i class="fas fa-receipt"></i> {{trans('v3/new_varios.retenciones')}}</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body hero-factura">
                            <div class="ibox-content">
                                {{ csrf_field() }}
                                <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveTransferenciaBancaria" />
                                <input type="hidden" id="d" name="d" />
                                <div class="row hero-datosfactura">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-success">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_tabla.informacionGeneral')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <label class="font-normal">{{trans('v3/new_tabla.estado')}}</label>
                                                        <input id="estado" type="text" class="form-control" name="estado" value="Activa" readonly autofocus>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <label class="font-normal">{{trans('v3/new_tabla.identificacion')}}</label>
                                                        <input type="text" class="form-control text-right" name="id" id="id" value="" readonly autofocus>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_tabla.secuencial')}}</label>
                                                        <input type="text" class="form-control text-right" name="id" id="id" value="" readonly autofocus>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <label class="font-normal">{{trans('v3/new_tabla.asiento')}}</label>
                                                        <input type="text" class="form-control text-right" name="id" id="id" value="" readonly autofocus>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <label class="font-normal">{{trans('v3/new_tabla.tipo')}}</label>
                                                        <input type="text" class="form-control text-center" name="tipo" id="tipo" value="BAN-TR" readonly autofocus>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_tabla.fecha')}}</label>
                                                        <input type="date" class="form-control text-right" name="fecha_asiento" id="fecha_asiento" onblur="validarCierre(this);" min="<?= config('data.fechaCierreContable') !== null ? date('Y-m-d', strtotime(config('data.fechaCierreContable') . '+1 day')) : ''; ?>" value="<?= date('Y-m-d'); ?>">
                                                    </div>
                                                    <div class="col-md-4"> 
                                                        <label class="font-normal">{{trans('v3/new_tabla.concepto')}}</label>
                                                        <input type="text" class="form-control" name="concepto" id="concepto" value="" autofocus>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_tabla.nocheque')}}</label>
                                                        <input type="date" class="form-control text-right" name="fecha_cheque" id="fecha_cheque" value="" autofocus>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_tabla.beneficiario')}}</label>
                                                        <input type="text" class="form-control" name="beneficiario" id="beneficiario" value="" autofocus>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="font-normal">{{trans('v3/new_tabla.direccion')}}</label>
                                                        <input type="text" class="form-control" name="direccion" id="direccion" value="" autofocus>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_tabla.ci_Ruc')}}</label>
                                                        <input type="text" class="form-control text-right" name="ruc" id="ruc" value="" autofocus>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_tabla.sucursal')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="sucursal" name="sucursal">
                                                            <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                                                            <?php
                                                            if (config('data.sucursales') != '' && count(config('data.sucursales')) > 0) {
                                                                foreach (config('data.sucursales') as $value) {
                                                                    echo '<option value="' . $value->id . '">' . $value->codigo_sucursal . ' | ' . $value->nombre_sucursal . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_tabla.cuentaOrigen')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="id_cuenta_origen" name="id_cuenta_origen">
                                                            <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                                                            <?php
                                                            if (config('data.bancos') != '' && count(config('data.bancos')) > 0) {
                                                                foreach (config('data.bancos') as $value) {
                                                                    echo '<option value="' . $value->cuenta_mayor . '">' . $value->nombre . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_tabla.divisa')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="id_divisa_origen" name="id_divisa_origen">
                                                            <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                                                            <?php
                                                            if (config('data.divisas') != '' && count(config('data.divisas')) > 0) {
                                                                foreach (config('data.divisas') as $value) {
                                                                    $selected = '';
                                                                    if ($value->id == 1)
                                                                        $selected = 'selected';
                                                                    echo '<option value="' . $value->id . '" ' . $selected . '>' . $value->descripcion . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <label class="font-normal">{{trans('v3/new_tabla.cambio')}}</label>
                                                        <input type="text" class="form-control text-right" name="id_cambio_origen" id="id_cambio_origen" value="1.00" readonly autofocus>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_tabla.valorOrigen')}}</label>
                                                        <input type="text" class="form-control text-right" name="valor_origen" id="valor_origen" value="0.00" onchange="calcularRetencionesTransferencia();" autofocus>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_tabla.noIncrementeCheque')}}</label>
                                                        <br />
                                                        <center>
                                                            <div class="icheck-success d-inline">
                                                                <input type="checkbox" class="cheCheque" id="cheCheque">
                                                                <label for="cheCheque"></label>
                                                            </div>
                                                        </center>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_tabla.cuentaDestino')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="id_cuenta_destino" name="id_cuenta_destino">
                                                            <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                                                            <?php
                                                            if (config('data.bancos') != '' && count(config('data.bancos')) > 0) {
                                                                foreach (config('data.bancos') as $value) {
                                                                    echo '<option value="' . $value->cuenta_mayor . '">' . $value->nombre . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_tabla.divisa')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="id_divisa_destino" name="id_divisa_destino">
                                                            <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                                                            <?php
                                                            if (config('data.divisas') != '' && count(config('data.divisas')) > 0) {
                                                                foreach (config('data.divisas') as $value) {
                                                                    $selected = '';
                                                                    if ($value->id == 1)
                                                                        $selected = 'selected';
                                                                    echo '<option value="' . $value->id . '" ' . $selected . '>' . $value->descripcion . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <label class="font-normal">{{trans('v3/new_tabla.cambio')}}</label>
                                                        <input type="text" class="form-control text-right" name="id_cambio_destino" id="id_cambio_destino" value="1.00" readonly autofocus>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_tabla.valorDestino')}}</label>
                                                        <input type="text" class="form-control text-right" name="valor_destino" id="valor_destino" value="0.00" readonly autofocus>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-datosfactura">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_tabla.informacionAdicional')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="font-normal">{{trans('v3/new_tabla.glosa')}}</label>
                                                        <textarea id="glosa" name="glosa" class="form-control" rows="6"></textarea>
                                                        <br />
                                                        <div style='text-align: center'>
                                                            <button type="button" class="btn btn-danger" onclick="goBack('transferenciabancaria');"><i class="fas fa-times-circle"></i> {{trans('v3/new_varios.cancelar')}}</button>
                                                            <button type="button" class="btn btn-primary" id="btnGuardarTransferenciaBancaria"><i class="fas fa-save"></i> {{trans('v3/new_varios.guardar')}}</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <label class="font-normal">{{trans('v3/new_tabla.proveedor')}}</label>
                                                                <select class="form-control" id="id_proveedor" name="id_proveedor" style="width: 100%;" onchange="cargar_autorizacion(this)">
                                                                    <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12" id="div_autorizacion">
                                                                <label class="font-normal">{{trans('v3/new_tabla.autorizacion')}}</label>
                                                                <input type="text" class="form-control" name="autorizacion" id="autorizacion" value="" readonly autofocus>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label class="font-normal">{{trans('v3/new_tabla.comision')}}</label>
                                                                <select class="form-control select2" name="id_comision" id="id_comision" onchange="calcularRetencionesTransferencia();">
                                                                    <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                                                                    <option value="5">5 %</option>
                                                                    <option value="7">7 %</option>
                                                                    <option value="8">8 %</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="font-normal">&nbsp;</label>
                                                                <input type="text" class="form-control text-right" name="valor_comision" id="valor_comision" value="0.00" readonly autofocus>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="font-normal">{{trans('v3/new_tabla.comisionTC')}}</label>
                                                                <input type="text" class="form-control text-right" name="comision_tarjeta" id="comision_tarjeta" onchange="calcularRetencionesTransferencia();" value="0.00" autofocus>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label class="font-normal">{{trans('v3/new_tabla.baseIva')}}</label>
                                                                <input type="text" class="form-control text-right" name="base_iva" id="base_iva" value="0.00" autofocus>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="font-normal" for="id_base_iva">&nbsp;</label>
                                                                <select class="form-control select2" name="id_base_iva" id="id_base_iva" onchange="calcularRetencionesTransferencia();">
                                                                    <option value="0">{{ trans('contableM.ningun_') }}</option>
                                                                    <?php
                                                                    if (config('data.retenciones') != '' && count(config('data.retenciones')) > 0) {
                                                                        foreach (config('data.retenciones') as $value) {
                                                                            echo '<option value="' . $value->cuenta_deudora . '">' . $value->valor . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="font-normal">{{trans('v3/new_tabla.ivaRetenido')}}</label>
                                                                <input type="text" class="form-control text-right" name="iva_retenido" id="iva_retenido" value="0.00" readonly autofocus>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label class="font-normal">{{trans('v3/new_tabla.baseRenta')}}</label>
                                                                <input type="text" class="form-control text-right" name="base_ret_fuente" id="base_ret_fuente" value="0.00" autofocus>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="font-normal" for="id_base_ret_fuente">&nbsp;</label>
                                                                <select class="form-control select2" name="id_base_ret_fuente" id="id_base_ret_fuente" onchange="calcularRetencionesTransferencia();">
                                                                    <option value="0">{{ trans('contableM.ningun_') }}</option>
                                                                    <?php
                                                                    if (config('data.retencionesFue') != '' && count(config('data.retencionesFue')) > 0) {
                                                                        foreach (config('data.retencionesFue') as $value) {
                                                                            echo '<option value="' . $value->cuenta_deudora . '">' . $value->valor . ' | ' . $value->nombre . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="font-normal">{{trans('v3/new_tabla.rentaretenida')}}</label>
                                                                <input type="text" class="form-control text-right" name="retencion_fuente" id="retencion_fuente" value="0.00" readonly autofocus>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label class="font-normal">&nbsp;</label>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="font-normal">{{trans('v3/new_tabla.valorAdicional')}}</label>
                                                                <input type="text" class="form-control text-right" name="valores_adicionales" id="valores_adicionales" onchange="calcularRetencionesTransferencia()" value="0.00" autofocus>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="font-normal">{{trans('v3/new_tabla.total')}}</label>
                                                                <input type="text" class="form-control text-right" name="total" id="total" onchange="calcularRetencionesTransferencia()" value="0.00" readonly autofocus>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@include('v3.contable.cajabancos.view_modal_retenciones')