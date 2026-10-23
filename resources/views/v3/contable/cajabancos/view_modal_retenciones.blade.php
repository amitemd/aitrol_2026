<div class=" modal fade modalTotal" id="myModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal_retencion"><i class="fas fa-edit"></i> Editar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_retencion">
                    {{ csrf_field() }}
                    <input type="hidden" id="d" name="d" value="|" />
                    <input type="hidden" id="action_retencion" value="{{url('v3/')}}/{{config('data.controlador')}}/saveRentencion" />
                    <div class="card card-default">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Proveedor</label>
                                        <div class="input-group mb-3 hero-tipo_identificacion">
                                            <select class="form-control" id="id_proveedor_modal" name="id_proveedor_modal" style="width: 100%;">
                                                <option value="">--<?= trans('v3/new_varios.seleccionar'); ?>--</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Autorización</label>
                                        <div class="input-group mb-3">
                                            <input type="text" id="autorizacion_modal" name="autorizacion_modal" onkeypress="return isNumberKey(event)" class="form-control text-right" maxlength="49" placeholder="No. de autorización">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group mb-3">
                                        <label>Base fuente</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                            </div>
                                            <input type="text" id="base_fuente_modal" name="base_fuente_modal" onkeypress="return isNumberKey(event)" class="form-control text-right" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Porcentaje</label>
                                        <div class="input-group mb-3">
                                            <select class="form-control select2" id="porcentaje_fuente_modal" name="porcentaje_fuente_modal" style="width: 100%;height: 35px !important;">
                                                <option value="">--<?= trans('v3/new_varios.seleccionar'); ?>--</option>
                                                <?php
                                                if (config('data.retencionesFue') != '' && count(config('data.retencionesFue')) > 0) {
                                                    foreach (config('data.retencionesFue') as $value) {
                                                        echo '<option value="' . $value->cuenta_deudora . '|' . $value->valor . '">' . $value->valor . ' | ' . $value->nombre . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group mb-3">
                                        <label>Fuente retenida</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                            </div>
                                            <input type="text" id="fuente_retenida_modal" name="fuente_retenida_modal" class="form-control text-right" placeholder="0.00" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group mb-3">
                                        <label>Base iva</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                            </div>
                                            <input type="text" id="base_iva_modal" name="base_iva_modal" onkeypress="return isNumberKey(event)" class="form-control text-right" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Porcentaje</label>
                                        <div class="input-group mb-3">
                                            <select class="form-control select2" id="porcentaje_iva_modal" name="porcentaje_iva_modal" style="width: 100%;height: 35px !important;">
                                                <option value="">--<?= trans('v3/new_varios.seleccionar'); ?>--</option>
                                                <?php
                                                if (config('data.retenciones') != '' && count(config('data.retenciones')) > 0) {
                                                    foreach (config('data.retenciones') as $value) {
                                                        echo '<option value="' . $value->cuenta_deudora . '|' . $value->valor . '">' . $value->valor . ' | ' . $value->nombre . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group mb-3">
                                        <label>Iva retenido</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                            </div>
                                            <input type="text" id="iva_retenido_modal" name="iva_retenido_modal" class="form-control text-right" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarRetencion"><i class="fas fa-save"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>