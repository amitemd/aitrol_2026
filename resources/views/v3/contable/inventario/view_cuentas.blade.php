<div class="card card-default">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/inventario.ctaMerGasto')}}</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2" id="cta_gastos" name="cta_gastos" style="width: 100%;">
                            <option value="">--{{trans('v3/new_varios.seleccione')}}--</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/inventario.ctaMerVentas')}}</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2" id="cta_ventas" name="cta_ventas" style="width: 100%;">
                            <option value="">--{{trans('v3/new_varios.seleccione')}}--</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/inventario.ctaMerCostos')}}</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2" id="cta_costos" name="cta_costos" style="width: 100%;">
                            <option value="">--{{trans('v3/new_varios.seleccione')}}--</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/inventario.ctaMerDevolucion')}}</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2" id="cta_devolucion" name="cta_devolucion" style="width: 100%;">
                            <option value="">--{{trans('v3/new_varios.seleccione')}}--</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/inventario.impIvaCompras')}}</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2" id="impuesto_iva_compras" name="impuesto_iva_compras" style="width: 100%;">
                            <option value="">--{{trans('v3/new_varios.seleccione')}}--</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/inventario.impIvaVentas')}}</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2" id="impuesto_iva_ventas" name="impuesto_iva_ventas" style="width: 100%;">
                            <option value="">--{{trans('v3/new_varios.seleccione')}}--</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>{{trans('v3/inventario.impServ')}}</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2" id="impuesto_servicio" name="impuesto_servicio" style="width: 100%;">
                            <option value="">--{{trans('v3/new_varios.seleccione')}}--</option>
                            <?php
                            if (config('data.porcentajes_iva') != null && count(config('data.porcentajes_iva')) > 0) {
                                foreach (config('data.porcentajes_iva') as $row) {
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
                    <label>{{trans('v3/inventario.impIce')}}</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2" id="impuesto_ice" name="impuesto_ice" style="width: 100%;">
                            <option value="">--{{trans('v3/new_varios.seleccione')}}--</option>
                            <?php
                            if (config('data.porcentajes_iva') != null && count(config('data.porcentajes_iva')) > 0) {
                                foreach (config('data.porcentajes_iva') as $row) {
                                    echo '<option value="' . $row->id_catalogo . '">' . $row->catalogo . '</option>';
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
                    <label>{{trans('v3/inventario.clasImpIce')}}</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2" id="clasificacion_impuesto_ice" name="clasificacion_impuesto_ice" style="width: 100%;">
                            <option value="">--{{trans('v3/new_varios.seleccione')}}--</option>
                            <?php
                            if (config('data.porcentajes_iva') != null && count(config('data.porcentajes_iva')) > 0) {
                                foreach (config('data.porcentajes_iva') as $row) {
                                    echo '<option value="' . $row->id_catalogo . '">' . $row->catalogo . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
