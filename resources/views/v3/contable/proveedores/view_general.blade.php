<div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>{{trans('v3/new_varios.tipoIdentificacion')}}</label>
                <div class="input-group mb-3">
                    <select class="form-control select2" id="tipo" name="tipo" style="width: 100%;">
                        <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                        <?php
                        if (config('data.tipo_identificacion') != null && count(config('data.tipo_identificacion')) > 0) {
                            foreach (config('data.tipo_identificacion') as $row) {
                                echo '<option value="' . $row->cod_sri_tipo_identificacion . '">' . $row->tipo_identificacion . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>{{trans('v3/new_varios.identificacion')}}</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-address-card-o"></i></span>
                    </div>
                    <input type="text" id="id" name="id" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="{{trans('v3/new_varios.identificacionProveedor')}}">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>{{trans('v3/new_tabla.razonSocial')}}</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                    </div>
                    <input type="text" id="razonsocial" name="razonsocial" class="form-control" placeholder="{{trans('v3/new_varios.razonSocialProveedor')}}">
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>{{trans('v3/new_tabla.nombreComercial')}}</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                    </div>
                    <input type="text" id="nombrecomercial" name="nombrecomercial" class="form-control" placeholder="{{trans('v3/new_varios.nombreComercialProveedor')}}">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>Email</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    </div>
                    <input type="text" id="email" name="email" class="form-control" placeholder="{{trans('v3/new_varios.emailProveedor')}}">
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>Email 2</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    </div>
                    <input type="text" id="email2" name="email2" class="form-control" placeholder="{{trans('v3/new_varios.emailProveedor')}} 2">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>{{trans('v3/new_tabla.telefono')}}</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    </div>
                    <input type="text" id="telefono1" name="telefono1" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="{{trans('v3/new_varios.telefonoProveedor')}}">
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>{{trans('v3/new_tabla.celular')}}</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                    </div>
                    <input type="text" id="telefono2" name="telefono2" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="{{trans('v3/new_varios.celularProveedor')}}">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>{{trans('v3/new_varios.cuentaContable')}}</label>
                <div class="input-group mb-3">
                    <select class="form-control select2" id="id_cuentas" name="id_cuentas" style="width: 100%;">
                        <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>Tipo proveedor</label>
                <div class="input-group mb-3">
                    <select class="form-control select2" id="id_tipoproveedor" name="id_tipoproveedor" style="width: 100%;">
                        <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                        <?php
                        if (config('data.tipo_proveedor') != null && count(config('data.tipo_proveedor')) > 0) {
                            foreach (config('data.tipo_proveedor') as $row) {
                                echo '<option value="' . $row->id . '">' . $row->nombre . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label>Provisión</label>
                <div class="input-group mb-3">
                    <select class="form-control select2_multiple" id="provisiones" name="provisiones[]" multiple style="width: 100%;">
                        <?php
                        if (config('data.provisiones') != null && count(config('data.provisiones')) > 0) {
                            foreach (config('data.provisiones') as $row) {
                                echo '<option value="' . $row->id_catalogo . '">' . $row->catalogo . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label for="cheestado">{{trans('v3/new_tabla.estado')}}</label>
                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox" class="custom-control-input" id="cheestado">
                    <label class="custom-control-label" for="cheestado"></label>
                </div>
            </div>
        </div>
    </div>
</div>